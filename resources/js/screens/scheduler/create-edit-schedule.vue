<script type="text/ecmascript-6">
    import VueCronEditorBuefy from 'vue-cron-editor-buefy';
    import CustomSelect from './custom-select';

    export default {
        components: {
            VueCronEditorBuefy,
            CustomSelect
        },
        name: 'CreateEditSchedule',
        props: {
            show: Boolean
        },
        watch: {
            show(value) {
                $('#createEditSchedule').modal(value ? 'show' : 'hide');
            }
        },
        data () {
            return {
                projects: null,
                schedule: {
                    category: null,
                    project: null,
                    job: null,
                }
            };
        },
        computed: {
            jobs() {
                const { project, category } = this.schedule;

                if (project === null || category === null) {
                    return [];
                }

                return this.projects[project][category].jobs;
            }
        },
        created() {
            this.projects = JSON.parse(JSON.stringify(Horizon.controllers));
        }
    }
</script>
<template>
    <div class="modal fade w-100" id="createEditSchedule" tabindex="-1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Job Scheduler Editor</h5>
                <button type="button" class="close" @click="$emit('close')">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row pb-3">
                    <div class="col-6">
                        <CustomSelect
                            v-model="schedule.project"
                            :disabled="projects === null"
                            :options="projects"
                            label="Project"
                        />
                    </div>
                    <div class="col-6">
                        <CustomSelect
                            v-model="schedule.category"
                            :disabled="schedule.project === null"
                            :options="projects[schedule.project]"
                            label="Category"
                        />
                    </div>
                </div>
                <div class="row pb-3">
                    <div class="col">
                        <CustomSelect
                            v-model="schedule.job"
                            :disabled="jobs.length === 0"
                            :options="jobs"
                            label="Job"
                        />
                    </div>
                </div>
                <div
                    v-if="schedule.job"
                    class="row pb-3"
                >   
                    <div class="col">
                        <label for="cronEditor">Frequency</label>            
                        <VueCronEditorBuefy
                            id="cronEditor"
                            v-model="schedule.frequency"
                            class="w-100"
                        />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="$emit('close')">Close</button>
                <button type="button" class="btn btn-primary" :disabled="!schedule.frequency" @click="$emit('save', schedule)">Save changes</button>
            </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
#cronEditor {
    .card {
        box-shadow: none !important;
    }
    .input {
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem !important;
    }

    .dropdown-menu {
        padding: 0 !important;
    }

    .dropdown-content {
        padding: .5rem !important;
    }

    .centered-text {
        white-space: nowrap !important;
    }

    .b-checkbox.checkbox:not(.button) {
        margin-bottom: 0 !important;
    }

    .tabs {
        a:hover, .is-active a {
            border-bottom-color: var(--primary) !important;
            color: var(--primary) !important;
        }
    }
}
</style>