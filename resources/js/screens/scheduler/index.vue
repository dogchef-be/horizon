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
            deleteJobSchedule() {

            },
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
        <CreateEditSchedule :show="showEditCreate" @close="showEditCreate = false" @save="handleSave($event)" />
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Scheduler</h5>
                
                <button type="button" class="btn btn-primary" @click="showEditCreate = true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                    </svg>
                </button>
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
                                        v-for="(cron, subkey, subIndex) in scheduler[key][catkey]" :key="`subcategory_index_${subIndex}`"
                                        class="list-group-item"
                                    >
                                        <div class="row px-6">
                                            <div class="col-6">{{ subkey }}</div>
                                            <div class="col-5">{{ cron }}</div>
                                            <div class="col-1">
                                                <button type="button" class="btn btn-danger" @click="deleteJobSchedule($cat)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                    </svg>
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
        </div>
    </div>
</template>