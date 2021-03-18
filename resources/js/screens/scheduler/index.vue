<script>
    import Vue from 'vue'
    import VueCronEditorBuefy from 'vue-cron-editor-buefy';

    // Custom Components
    import CreateEditSchedule from './create-edit-schedule';
    import CustomAccordion from './custom-accordion';
    import DeleteSchedule from './delete-schedule';
    import CustomSelect from './custom-select';

    export default {
        components: {
            CreateEditSchedule,
            CustomAccordion,
            DeleteSchedule,
            CustomSelect
        },
        data() {
            return {
                scheduler: [],
                selected: {
                    method: null,
                    project: null,
                    category: null
                },
                showEditCreate: false,
                showDelete: false,
                loaded: false,
                tag: null,
            };
        },
        computed: {
            projects() {
                if (this.scheduler.length === 0) {
                    return [];
                }

                const projects = [...new Set(this.scheduler.map(item => item.project))];

                return projects.sort((a, b) => this.sortAlphabetically(a, b));
            },
            schedules() {
                const { project } = this.selected;

                if (project === null) { return []; }
                
                const jobs = this.scheduler.filter(schedule => schedule.project === project);

                return jobs.sort((a, b) => {
                    const sort = this.sortAlphabetically(a.category, b.category);
                    
                    if (sort !== 0) {
                        return sort;
                    }

                    return this.sortAlphabetically(a.method, b.method);
                });
            }
        },
        created() {
            this.$http.get(Horizon.basePath + '/api/scheduler')
                .then(response => {
                    this.parseScheduler(response.data);
                    this.loading(false);
                });
        },
        mounted() {
            document.title = "Horizon - Scheduler";
        },
        methods: {
            sortAlphabetically(a, b) {
                return a.localeCompare(b, 'en', { sensitivity: 'base' });
            },
            showEditCreateModal(show = true) {
                this.showEditCreate = show;
            },
            showDeleteModal(show = true) {
                this.showDelete = show;
            },
            handleCreate() {
                this.clearSelectedKeys();
                this.closeCollapse();
                this.showEditCreateModal();
            },
            handleEdit(schedule) {
                this.closeCollapse();
                this.selected = { ...schedule };
                this.showEditCreateModal();
            },
            handleDelete(schedule) {
                this.closeCollapse();
                this.selected = { ...schedule }
                this.showDeleteModal();
            },
            saveScheduler(scheduler = this.scheduler) {
                this.showEditCreateModal(false);
                this.showDeleteModal(false);
                this.loading();

                this.$http.post(Horizon.basePath + '/api/scheduler', { scheduler })
                    .then(response =>{
                        this.parseScheduler(response.data);
                        this.clearSelectedKeys();
                        this.loading(false);
                    });
            },
            parseScheduler(scheduler) {
                this.scheduler = scheduler === '' ? [] : scheduler;
            },
            selectProject({ tag, value }) {
                this.clearSelectedKeys(Object.keys(this.selected).filter(k => k !== 'project'));
                this.selected.project = this.selected.project === value ? null : value;
                this.tag = this.tag === tag ? null : tag;
            },
            closeCollapse() {
                if (this.tag !== null) {
                    $(`#${this.tag}`).collapse('hide');
                    this.tag = null;
                }
            },
            clearSelectedKeys(keys = null) {
                if (keys === null) {
                    keys = Object.keys(this.selected);
                }

                keys.forEach(key => this.selected[key] = null);
            },
            explanation(frequency) {
                if (frequency === null) return '';

                const VueCronEditorClass = Vue.extend(VueCronEditorBuefy);

                return (new VueCronEditorClass({
                    propsData: { value: frequency }
                })).explanation;
            },
            showTable(item) {
                return this.schedules.length > 0 &&
                    this.selected.project !== null &&
                    this.selected.project === item
            },
            loading(loading = true) {
                this.loaded = !loading;
            }
        }
    }
</script>
<template>
    <div>
        <CreateEditSchedule
            :show="showEditCreate"
            :scheduler="scheduler"
            :selected="selected"
            @save="saveScheduler($event)"
            @close="showEditCreate = false"
        />
        <DeleteSchedule
            :show="showDelete"
            :scheduler="scheduler"
            :selected="selected"
            @close="showDelete = false"
            @save="saveScheduler($event)"
        />
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Scheduler</h5>
                
                <button type="button" class="btn btn-primary" @click="handleCreate()">
                    <i class="bi bi-plus-circle"></i>
                </button>
            </div>
            <div 
                v-if="scheduler.length === 0 || !loaded" 
                class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius"
            >
                <span v-if="!loaded">Loading schedules...</span>
                <span v-else>There aren't any schedule.</span>
            </div>
            <div v-else class="card-body">
                <CustomAccordion
                    :items="projects"
                    tag="schedule"
                    @change="selectProject($event)"
                >
                    <template #default="{ tag, item }">
                        <div 
                            v-if="showTable(item)"
                            class="table-responsive-lg"
                        >
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Job</th>
                                        <th scope="col">Frequency</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(schedule, index) in schedules"
                                        :key="`${tag}_job_${index}`"
                                    >
                                        <td>
                                            <strong class="text-wrapper">{{ schedule.category }}</strong>
                                            <span class="text-wrapper">{{ schedule.method }}</span>
                                        </td>
                                        <td>{{ explanation(schedule.frequency) }}</td>
                                        <td>
                                            <button
                                                class="btn btn-secondary btn-sm mr-1"
                                                @click="handleEdit(schedule)"
                                            >
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <button
                                                class="btn btn-danger btn-sm"
                                                @click="handleDelete(schedule)"
                                            >
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </CustomAccordion>
            </div>
        </div>
    </div>
</template>
<style lang="scss" scoped>
.table {
    th {
        border-bottom: 2px solid rgba(0, 0, 0, 0.125);
        background-color: transparent;
        padding: 0.75rem 1.25rem;
        border-top: none;
        font-weight: bold;
    }
}

.text-wrapper {
    padding: 2px 8px;
    border-radius: 4px;
    background-color: rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

::v-deep .accordion .card-body {
    padding: 0;
}
</style>
