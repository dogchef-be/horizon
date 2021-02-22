<script>
    import CreateEditSchedule from './create-edit-schedule';

    export default {
        components: {
            CreateEditSchedule
        },
        data() {
            return {
                scheduler: null,
                showEditCreate: false,
            };
        },
        created() {
            this.$http.get(Horizon.basePath + '/api/scheduler')
                .then(response => {
                    const data = response.data === '' ? null : response.data;
                    
                    if (data) {
                        this.scheduler = response.data;
                    }
                });
        },
        mounted() {
            document.title = "Horizon - Scheduler";
        },
        methods: {
            handleSave(schedule) {
                this.showEditCreate = false;

                if (!this.scheduler) {
                    this.scheduler = {};
                }

                if (!this.scheduler[schedule.project]) {
                    this.scheduler[schedule.project] = {};
                }


                if (!this.scheduler[schedule.project][schedule.category]) {
                    this.scheduler[schedule.project][schedule.category] = {};
                }

                this.scheduler[schedule.project][schedule.category] = {
                    ...this.scheduler[schedule.project][schedule.category],
                    [schedule.job]: schedule.frequency
                }
                

                this.$http.post(Horizon.basePath + '/api/scheduler', { 'scheduler': this.scheduler })
                    .then(response => {
                        const data = response.data === '' ? null : response.data;
                        
                        if (data) {
                            this.scheduler = response.data;
                        }
                    })
            }
        }
    }
</script>
<template>
    <div>
        <CreateEditSchedule :show="showEditCreate" @close="showEditCreate = false" @save="handleSave($event)"></CreateEditSchedule>
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Scheduler</h5>
                
                <button type="button" class="btn btn-primary" @click="showEditCreate = true">+</button>
            </div>
            <div v-if="scheduler === null" class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
                <span>There aren't any schedule.</span>
            </div>
            <div v-else class="card-body">
                <div v-for="(schedule, key, index) in scheduler" :key="`schedule_index_${index}`">
                    <button 
                        class="btn btn-link btn-block text-left my-2"
                        type="button"
                        data-toggle="collapse" 
                        aria-expanded="true"
                        :data-target="`#collapse${key}`"
                        :aria-controls="`collapse${key}`"
                    >
                        {{ key }}
                    </button>
                    <div :id="`collapse${key}`" class="collapse show">
                        <div 
                            v-for="(category, catkey, catIndex) in scheduler[key]" :key="`category_index_${catIndex}`" 
                            class="row px-4"
                        >
                            <button 
                                class="btn btn-link btn-block text-left"
                                type="button"
                                data-toggle="collapse" 
                                aria-expanded="true"
                                :data-target="`#collapse${key}${catkey}`"
                                :aria-controls="`collapse${key}${catkey}`"
                            >
                                {{ catkey }}
                            </button>
                            <div :id="`collapse${key}${catkey}`" class="collapse show w-100 my-1">
                                <ul class="list-group">
                                    <li
                                        v-for="(subCategory, subkey, subIndex) in scheduler[key][catkey]" :key="`subcategory_index_${subIndex}`"
                                        class="list-group-item"
                                    >
                                        <div class="row px-6">
                                            <div class="col-6">{{ subkey }}</div>
                                            <div class="col-6">{{ subCategory }}</div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>