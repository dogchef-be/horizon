<script type="text/ecmascript-6">
    export default {
        name: 'CustomSelect',
        props: {
            value: {
                type: [String],
                default: null
            },
            options: {
                type: [Array, Object],
                default: () => []
            },
            label: {
                type: [String],
                default: 'Label'
            },
            disabled: Boolean
        },
        data () {
            return {
                selected: this.value
            }
        },
        watch: {
            selected(value) {
                this.$emit('input', value);
            },
            value(value) {
                this.selected = value;
            }
        }
    }
</script>
<template>
    <fieldset>
        <label :for="`${label}Select`">{{ label }}</label>
        <select
            :id="`${label}Select`"
            v-model="selected"
            :disabled="disabled"
            class="custom-select"
            required
            >
            <option v-show="false" :value="null">Select...</option>
            <template v-if="!disabled">
                <template v-if="Array.isArray(options)">
                    <option                    
                        v-for="(option, index) in options"
                        :key="`option_index_${index}`"
                        :value="option"
                    >{{ option }}</option>
                </template>
                <template v-else>
                    <option
                        v-for="(option, key, index) in options"
                        :key="`option_index_${index}`"
                        :value="key"
                    >{{ key }}</option>
                </template>
            </template>
        </select>
    </fieldset>
</template>
