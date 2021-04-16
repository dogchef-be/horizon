<script type="text/ecmascript-6">
    export default {
        name: 'DeleteSchedule',
        props: {
            show: Boolean,
            scheduler: {
                type: Array,
                default: () => []
            },
            selected: {
                type: Object,
                required: true
            }
        },
        watch: {
            show(value) {
                $('#deleteSchedule').modal(value ? 'show' : 'hide');
            }
        },
        methods: {
            deleteSchedule() {
                const { project, category, frequency, method } = this.selected;

                const scheduler = this.scheduler.filter(schedule => (
                    schedule.project !== project || schedule.category !== category ||
                    schedule.frequency !== frequency || schedule.method !== method
                ));

                this.$emit('save', scheduler);
            }
        }
    }
</script>
<template>
    <div class="modal fade" id="deleteSchedule" tabindex="-1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content" v-if="show">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Delete job from schedule:</strong></p>
                    <p>{{ selected.project }} / {{ selected.category }} / {{ selected.method }}</p>
                </div>
                <div class="modal-footer">
                    <button 
                        class="btn btn-secondary"
                        data-dismiss="modal"
                        type="button"
                        @click="$emit('close')"
                    >
                        <span>Close</span>
                    </button>
                    <button
                        class="btn btn-danger"
                        type="button"
                        @click="deleteSchedule()"
                    >
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
