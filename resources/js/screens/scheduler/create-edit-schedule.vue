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
            selected: {
                type: Object,
                required: true
            },
            scheduler: {
                type: Array,
                default: () => []
            }
        },
        watch: {
            show(value) {
                this.schedule = value ? this.getSelectedValues() : null;
                $('#createEditSchedule').modal(value ? 'show' : 'hide');
            }
        },
        data () {
            return {
                projects: JSON.parse(JSON.stringify(Horizon.methodsForScheduling)),
                schedule: null
            };
        },
        computed: {
            methods() {
                const { project, category } = this.schedule;
                
                if (project === null || category === null) {
                    return [];
                }
                
                return this.projects[project][category].methods;
            }
        },
        methods: {
            getSelectedValues() {
                const hasValues = Object.values(this.selected).some(value => value !== null);

                if (hasValues) {
                    return { ...this.selected };
                }

                return {
                    frequency: '*\/1 * * * *',
                    category: null,
                    project: null,
                    method: null,
                }
            },
            handleSeletecMethod() {
                const { project, category, method } = this.schedule;

                if (method !== null) {
                    const jobs = this.scheduler.filter(schedule => (
                        schedule.category === category &&
                        schedule.project === project && 
                        schedule.method === method
                    ));

                    if (jobs.length > 0) {
                        this.schedule.frequency = jobs[0].frequency;
                    }
                }
            },
            saveSchedule() {
                const { project, category, method } = this.schedule;

                const scheduler = this.scheduler.filter(schedule => (
                    schedule.project !== project || schedule.category !== category || schedule.method !== method
                ));
                
                scheduler.push(this.schedule);

                this.$emit('save', scheduler);
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
                                v-model="schedule.method"
                                :disabled="methods.length === 0"
                                :options="methods"
                                label="Method"
                                @input="handleSeletecMethod()"
                            />
                        </div>
                    </div>
                    <div
                        v-if="schedule.method"
                        class="row pb-3"
                    >   
                        <div class="col">
                            <label for="cronEditor">Frequency</label>            
                            <VueCronEditorBuefy
                                id="cronEditor"
                                v-model="schedule.frequency"
                                class="w-100"
                                preserveStateOnSwitchToAdvanced
                            />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-secondary"
                        type="button"
                        @click="$emit('close')"
                    >
                        <span>Close</span>
                    </button>
                    <button
                        :disabled="schedule.frequency === null"
                        class="btn btn-primary"
                        type="button"
                        @click="saveSchedule()"
                    >Save</button>
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
