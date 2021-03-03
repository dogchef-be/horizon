<script type="text/ecmascript-6">
    export default {
        name: 'DeleteSchedule',
        props: {
            show: Boolean,
            scheduler: {
                type: Array,
                default: () => []
            },
            schedule: {
                type: Object,
                default: null
            }
        },
        watch: {
            show(value) {
                $('#deleteSchedule').modal(value ? 'show' : 'hide');
            }
        },
        methods: {
            deleteSchedule() {
                const scheduler = this.scheduler.filter(schedule => schedule.project !== this.schedule.project &&
                    schedule.category !== this.schedule.category && schedule.job !== this.schedule.job);

                this.$emit('save', scheduler);
            }
        }
    }
</script>
<template>
    <div class="modal fade" id="deleteSchedule" tabindex="-1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" v-if="show">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ schedule.project }} / {{ schedule.category }} / {{ schedule.job }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span>Do you really want to delete this job?</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="$emit('close')">Close</button>
                    <button type="button" class="btn btn-danger" @click="deleteSchedule()">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>