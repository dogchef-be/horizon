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
            show: Boolean,
            schedule: {
                type: Object,
                default: null
            }
        },
        watch: {
            show(value) {
                this.current = this.getCurrentValues();
                $('#createEditSchedule').modal(value ? 'show' : 'hide');
            }
        },
        data () {
            return {
                projects: JSON.parse(JSON.stringify(Horizon.controllers)),
                current: {
                    category: null,
                    project: null,
                    job: null,
                    frequency: '*\/1 * * * *',
                }
            };
        },
        computed: {
            jobs() {
                const { project, category } = this.current;

                if (project === null || category === null) {
                    return [];
                }

                return this.projects[project][category].jobs;
            }
        },
        methods: {
            getCurrentValues() {
                if (this.schedule !== null) {
                    return { ...this.schedule };
                }

                return {
                    frequency: '*\/1 * * * *',
                    category: null,
                    project: null,
                    job: null,
                }
            }
        }
    }
</script>
<template>
    <div class="modal fade w-100" id="createEditSchedule" tabindex="-1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" v-if="show">
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
                                v-model="current.project"
                                :disabled="projects === null"
                                :options="projects"
                                label="Project"
                            />
                        </div>
                        <div class="col-6">
                            <CustomSelect
                                v-model="current.category"
                                :disabled="current.project === null"
                                :options="projects[current.project]"
                                label="Category"
                            />
                        </div>
                    </div>
                    <div class="row pb-3">
                        <div class="col">
                            <CustomSelect
                                v-model="current.job"
                                :disabled="jobs.length === 0"
                                :options="jobs"
                                label="Job"
                            />
                        </div>
                    </div>
                    <div
                        v-if="current.job"
                        class="row pb-3"
                    >   
                        <div class="col">
                            <label for="cronEditor">Frequency</label>            
                            <VueCronEditorBuefy
                                id="cronEditor"
                                v-model="current.frequency"
                                class="w-100"
                            />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="$emit('close')">Close</button>
                    <button type="button" class="btn btn-primary" :disabled="!current.frequency" @click="$emit('save', current)">Save changes</button>
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
        box-shadow: inset 0 1px 2px rgba(10,10,10,.1);
    }

    .dropdown-menu {
        padding: 0 !important;
    }

    .dropdown-content {
        padding: .5rem !important;

        .select > select {
            border-radius: 0.25rem !important;
        }

        .control.is-colon {
            margin: 0 2px !important;
        }
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

    .b-checkbox.checkbox {
        &:hover input[type="checkbox"]:not(:disabled) + .check {
            border-color: var(--primary) !important;
        }
        
        input[type="checkbox"]:checked + .check {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }
    }
    
    .centered-checkbox-group {
        flex-wrap: nowrap !important;
    }

    .field.is-grouped-centered.is-grouped {
        background-color: #ffffff !important;
    }
}
</style>