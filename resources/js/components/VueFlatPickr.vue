<template>
    <div class="input-group">
        <flat-pickr
            data-input
            class="form-control"
            autocomplete="off"
            :name="name"
            v-model="valueLocal"
            :config="configLocal"
            @on-change="onChange"
            :disabled="disabled"
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
import { MandarinTraditional } from "flatpickr/dist/l10n/zh-tw.js";

export default {
    components: {
        "flat-pickr": flatPickr,
    },
    name: "vue-flat-pickr",
    props: {
        name: {
            type: String,
            default: "",
        },
        value: {
            type: String,
            default: "",
        },
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
    computed: {
        valueLocal: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit("update:value", value);
            },
        },
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
    methods: {
        onChange(selectedDates, dateStr, instance) {
            this.$emit("on-change", selectedDates, dateStr, instance);
        },
    },
};
</script>
