<script>
    import CreateEditSchedule from './create-edit-schedule';
    import DeleteSchedule from './delete-schedule';
    import CustomSelect from './custom-select';

    export default {
        components: {
            CreateEditSchedule,
            DeleteSchedule,
            CustomSelect
        },
        data() {
            return {
                scheduler: [],
                selectedProject: null,
                selectedCategory: null,
                selectedSchedule: null,
                showEditCreate: false,
                showDelete: false,
            };
        },
        computed: {
            projects() {
                if (this.scheduler.length === 0) {
                    return [];
                }

                return [...new Set(this.scheduler.map(item => item.project))];
            },
            categories() {
                if (this.selectedProject === null) {
                    return [];
                }

                const project = this.scheduler.filter(item => item.project === this.selectedProject);

                return [...new Set(project.map(item => item.category))];
            },
            jobs() {
                if (this.selectedProject === null || this.selectedCategory === null) {
                    return [];
                }

                return this.scheduler.filter(item => item.project === this.selectedProject && item.category === this.selectedCategory);
            }
        },
        
        watch: {
            project() {
                this.selectedJob = null;
                this.selectedCategory = null;
            }
        },
        created() {
            this.$http.get(Horizon.basePath + '/api/scheduler')
                .then(response => this.parseScheduler(response.data));
        },
        mounted() {
            document.title = "Horizon - Scheduler";

            $('#exampleModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var recipient = button.data('whatever') // Extract info from data-* attributes
                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                var modal = $(this)
                modal.find('.modal-title').text('New message to ' + recipient)
                modal.find('.modal-body input').val(recipient)
            });
        },
        methods: {
            deleteJobSchedule() {
                this.scheduler = this.scheduler.filter(schedule => schedule.project !== selectedProject &&
                    schedule.category !== selectedCategory && schedule.job !== selectedJob);

                this.postScheduler();
            },
            handleSave(schedule) {
                this.scheduler.push(schedule);
                this.selectedProject = schedule.project;
                this.selectedCategory = schedule.category;    
                this.saveScheduler();
            },
            showEditCreateModal(show = true) {
                this.showEditCreate = show;
            },
            handleCreate() {
                this.selectedSchedule = null;
                this.showEditCreateModal();
            },
            handleEdit(job, frequency) {
                this.selectedSchedule = {
                    project: this.selectedProject,
                    category: this.selectedCategory,
                    frequency,
                    job
                };

                this.showEditCreateModal();
            },
            handleDelete(job, frequency) {
                this.selectedSchedule = {
                    project: this.selectedProject,
                    category: this.selectedCategory,
                    frequency,
                    job
                }

                this.showDelete = true;
            },
            saveScheduler(scheduler = this.scheduler) {
                this.showEditCreateModal(false);
                this.showDelete = false;

                this.$http.post(Horizon.basePath + '/api/scheduler', { scheduler })
                    .then(response => this.parseScheduler(response.data));
            },
            parseScheduler(scheduler) {
                this.scheduler = scheduler === '' ? [] : scheduler;
            }
        }
    }
</script>
<template>
    <div>
        <CreateEditSchedule :show="showEditCreate" :schedule="selectedSchedule" @close="showEditCreate = false" @save="handleSave($event)" />
        <DeleteSchedule :show="showDelete" :scheduler="scheduler" :schedule="selectedSchedule" @close="showDelete = false" @save="saveScheduler($event)" />
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Scheduler</h5>
                
                <button type="button" class="btn btn-primary" @click="handleCreate()">
                    <i class="bi bi-plus-circle"></i>
                </button>
            </div>
            <div v-if="scheduler.length === 0" class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                <span>There aren't any schedule.</span>
            </div>
            <div v-else class="card-body">
                <div class="row">
                    <div class="col-6">
                        <CustomSelect
                            v-model="selectedProject"
                            :disabled="projects.length === 0"
                            :options="projects"
                            label="Project"
                        />
                    </div>
                    <div class="col-6">
                        <CustomSelect
                            v-model="selectedCategory"
                            :disabled="categories.length === 0"
                            :options="categories"
                            label="Category"
                        />
                    </div>
                    <div class="col-12 pt-3" v-if="jobs.length > 0">
                        <label for="jobs-list">Jobs</label>
                        <ul
                            id="jobs-list"
                            class="list-group"
                        >
                            <li
                                v-for="({ job, frequency }, index) in jobs"
                                :key="`cron_index_${index}`"
                                class="list-group-item py-1"
                            >
                                <div class="row">
                                    <div class="col-5 d-flex align-items-center">{{ job }}</div>
                                    <div class="col-5 d-flex align-items-center">{{ frequency }}</div>
                                    <div class="col-2 d-flex justify-content-end">
                                        <button type="button" class="btn btn-secondary btn-sm mr-1" @click="handleEdit(job, frequency)">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button
                                            class="btn btn-danger btn-sm"
                                            data-target="#exampleModal"
                                            data-toggle="modal"
                                            type="button"
                                            @click="handleDelete(job, frequency)"
                                        >
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss" scoped>
.link {
  -webkit-user-select: none;
  -ms-user-select: none;
  user-select: none;
  cursor: pointer;
}
</style>
