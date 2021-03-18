<script type="text/ecmascript-6">
    export default {
        name: 'CustomAccordion',
        props: {
            tag: {
                type: String,
                required: true
            },
            items: {
                type: Array,
                required: true
            }
        }
    }
</script>
<template>
    <div
        :id="`accordion-${tag}`"
        class="accordion"
    >
        <div
            v-for="item in items"
            :key="`key-${tag}-${item}`"
            class="card"
        >
            <div
                :id="`heading-${tag}-${item}`"
                class="card-header"
            >
                <h2 class="mb-0">
                    <button
                        :data-target="`#collapse-${tag}-${item}`"
                        :aria-controls="`collapse-${tag}-${item}`"
                        class="btn btn-link collapsed"
                        data-toggle="collapse"
                        aria-expanded="false"
                        type="button"
                        @click="$emit('change', { tag: `collapse-${tag}-${item}`, value:item })"
                    >
                        {{ item }}
                    </button>
                </h2>
            </div>
            <div 
                :id="`collapse-${tag}-${item}`"
                :aria-labelledby="`heading-${tag}-${item}`"
                :data-parent="`#accordion-${tag}`"
                :class="`collapse`"
            >
                <div class="card-body">
                    <slot :tag="`${tag}-${item}`" :item="item" name="default"></slot>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss" scoped>
.card {
    border: 1px solid rgba(0,0,0,.125);
    box-shadow: none;
}

.card-header {
    background-color: rgba(0,0,0,.03);
    border-bottom: 1px solid rgba(0,0,0,.125);
}
</style>
