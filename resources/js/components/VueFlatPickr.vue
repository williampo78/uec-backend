<template>
    <div class="input-group">
        <flat-pickr
            data-input
            class="form-control"
            autocomplete="off"
            :config="configLocal"
            :disabled="disabled"
            v-bind="attrs"
            v-on="$listeners"
        >
        </flat-pickr>
        <div class="input-group-btn">
            <button
                class="btn btn-default"
                type="button"
                :disabled="disabled"
                data-toggle
            >
                <i class="fa-solid fa-calendar-days"></i>
            </button>
        </div>
    </div>
</template>

<script>
import flatPickr from "vue-flatpickr-component";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import { MandarinTraditional } from "flatpickr/dist/l10n/zh-tw";

export default {
    components: {
        "flat-pickr": flatPickr,
    },
    name: "vue-flat-pickr",
    inheritAttrs: false,
    props: {
        config: {
            type: Object,
            default: () => ({}),
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            configLocal: {
                allowInput: true,
                wrap: true,
                clickOpens: false,
                time_24hr: true,
                locale: MandarinTraditional,
                disableMobile: true,
            },
        };
    },
    watch: {
        config: {
            handler(config) {
                this.configLocal = Object.assign({}, this.configLocal, config);
            },
            deep: true,
            immediate: true,
        },
    },
    computed: {
        attrs() {
            const attrs = { ...this.$attrs };

            attrs.class = this.$vnode.data.staticClass;
            attrs.style = this.$vnode.data.staticStyle;

            return attrs;
        },
    },
};
</script>
