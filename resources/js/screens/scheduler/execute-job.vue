<script type="text/ecmascript-6">
    import CustomSelect from './custom-select';

    export default {
        components: {
            CustomSelect
        },
        name: 'ExecuteJob',
        props: {
            show: Boolean,
            jobs: {
                type: Array,
                default: () => []
            }
        },
        watch: {
            show(value) {
                $('#executeJob').modal(value ? 'show' : 'hide');
            },
        },
        data () {
            return {
                projects: JSON.parse(JSON.stringify(Horizon.methodsForScheduling)),
                job: {
                    category: null,
                    project: null,
                    method: null,
                }
            };
        },
        computed: {
            methods() {
                const { project, category } = this.job;
                
                if (project === null || category === null) {
                    return [];
                }
                
                return this.projects[project][category].methods;
            }
        },
        methods: {
            handleSelectProject(project) {
                if (this.job.project !== project) {
                    this.handleSelectCategory(null);
                }

                this.job.project = project;
            },
            handleSelectCategory(category) {
                if (this.job.category !== category) {
                    this.handleSelectMethod(null);
                }

                this.job.category = category;
            },
            handleSelectMethod(method) {
                this.job.method = method;
            }
        }
    }
</script>
<template>
    <div class="modal fade w-100" id="executeJob" tabindex="-1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" v-if="show">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Launch job manually</h5>
                    <button type="button" class="close" @click="$emit('close')">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pb-3">
                        <div class="col-6">
                            <CustomSelect
                                :value="job.project"                                
                                :options="projects"
                                label="Project"
                                @input="handleSelectProject($event)"
                            />
                        </div>
                        <div class="col-6">
                            <CustomSelect
                                :value="job.category"
                                :disabled="!job.project"
                                :options="projects[job.project]"
                                label="Category"
                                @input="handleSelectCategory($event)"
                            />
                        </div>
                    </div>
                    <div class="row pb-3">
                        <div class="col">
                            <CustomSelect
                                :value="job.method"
                                :disabled="methods.length === 0"
                                :options="methods"
                                label="Method"
                                @input="handleSelectMethod($event)"
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
                        Close
                    </button>
                    <button
                        :disabled="!job.project || !job.category || !job.method"
                        class="btn btn-primary"
                        type="button"
                        @click="$emit('run', job)"
                    >
                        Run Now
                    </button>
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
