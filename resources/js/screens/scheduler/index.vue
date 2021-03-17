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
                loaded: false
            };
        },
        computed: {
            projects() {
                if (this.scheduler.length === 0) {
                    return [];
                }

                return [...new Set(this.scheduler.map(item => item.project))].sort((a, b) => this.sortArray(a, b));
            },
            categories() {
                const { project } = this.selected;

                if (project === null) { return []; }

                const filtered = this.scheduler.filter(item => item.project === project);

                return [...new Set(filtered.map(item => item.category))].sort((a, b) => this.sortArray(a, b));
            },
            schedules() {
                const { project, category } = this.selected;

                if (project === null || category === null) { return []; }
                
                return this.scheduler.filter(schedule => schedule.project === project && schedule.category === category);
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
            sortArray(a, b) {
                if (a > b) {
                    return -1;
                }

                if (a < b) {
                    return 1;
                }

                return 0;
            },
            showEditCreateModal(show = true) {
                this.showEditCreate = show;
            },
            showDeleteModal(show = true) {
                this.showDelete = show;
            },
            handleCreate() {
                this.closeProjectCollapse();
                this.clearSelectedKeys();
                this.showEditCreateModal();
            },
            handleEdit(schedule) {
                this.closeProjectCollapse();
                this.selected = { ...schedule };
                this.showEditCreateModal();
            },
            handleDelete(schedule) {
                this.closeProjectCollapse();
                this.selected = { ...schedule }
                this.showDeleteModal();
            },
            saveScheduler(scheduler = this.scheduler, deleted = null) {
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
            select(key, collapse) {
                this.clearSelectedKeys(Object.keys(this.selected).filter(k => k !== key && k !== 'project'));

                if (key === 'project'){
                    this.closeCategoriesCollapse()
                }

                this.selected[key] = this.selected[key] === collapse.value ? null : collapse.value;
            },
            closeProjectCollapse(action = 'hide') {
                this.closeCategoriesCollapse(action);
                $(`.collapse-schedule`).collapse(action);
            },
            closeCategoriesCollapse(action = 'hide') {
                if (this.selected.project !== null) {
                    $(`.collapse-schedule-${this.selected.project}`).collapse(action);
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
                    @change="select('project', $event)"
                >
                    <template #default="{ tag }">
                        <CustomAccordion
                            :items="categories"
                            :tag="tag"
                            @change="select('category', $event)"
                        >
                            <div
                                v-for="schedule in schedules"
                                :key="`${schedule.project}_${schedule.category}_${schedule.method}`"
                                class="row method-row"
                            >
                                <div class="col-5 d-flex align-items-center">{{ schedule.method }}</div>
                                <div class="col-5 d-flex align-items-center">{{ explanation(schedule.frequency) }}</div>
                                <div class="col-2 d-flex justify-content-end">
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
                                </div>
                            </div>
                        </CustomAccordion>
                    </template>
                </CustomAccordion>
            </div>
        </div>
    </div>
</template>
<style lang="scss" scoped>
.method-row {
    margin: 0 -10px;

    &:not(:last-child) {
        padding-bottom: 15px;
    }
}
</style>
