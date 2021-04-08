<script type="text/ecmascript-6">
    export default {
        name: 'ConfirmDelete',
        props: {
            show: Boolean,
            selected: {
                type: Object,
                required: true
            }
        },
        watch: {
            show(value) {
                $('#deleteFailedJob').modal(value ? 'show' : 'hide');
            }
        },
    }
</script>
<template>
    <div class="modal fade" id="deleteFailedJob" tabindex="-1" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" v-if="show">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Do you really want to delete this failed job?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span>{{ jobBaseName(selected.name) }}</span>
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
                        @click="$emit('save')"
                    >
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
