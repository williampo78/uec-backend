<template>
    <select>
        <slot></slot>
    </select>
</template>

<script>
export default {
    props: {
        options: {
            type: Array,
            default: () => [],
        },
        value: [String, Number],
        allowClear: {
            type: Boolean,
            default: true,
        },
    },
    mounted: function () {
        var vm = this;

        $(this.$el)
            .select2({
                data: this.options,
                allowClear: this.allowClear,
            })
            .val(this.value)
            .trigger("change")
            .on("change", function () {
                vm.$emit("input", this.value);
                vm.$emit("select2-change", this.value);
            })
            .on("select2:selecting", function (event) {
                vm.$emit("select2-selecting", event);
            });
    },
    watch: {
        value: function (value) {
            $(this.$el).val(value).trigger("change");
        },
        options: {
            handler: function (options) {
                $(this.$el)
                    .empty()
                    .select2({
                        data: options,
                        allowClear: this.allowClear,
                    })
                    .val(this.value)
                    .trigger("change");
            },
            deep: true,
        },
    },
    destroyed: function () {
        $(this.$el).off().select2("destroy");
    },
};
</script>
