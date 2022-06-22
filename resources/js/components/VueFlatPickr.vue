<template>
    <div class="input-group">
        <flat-pickr
            data-input
            class="form-control"
            autocomplete="off"
            :name="name"
            v-model="date"
            :config="config"
            @on-change="onChange"
        >
        </flat-pickr>
        <div class="input-group-btn">
            <button class="btn btn-default" type="button" data-toggle>
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
        'flat-pickr': flatPickr,
    },
    name: "vue-flat-pickr",
    props: {
        setting: {
            type: Object,
            default: () => ({
                name: "",
                date: "",
                config: {},
            }),
        },
    },
    data() {
        return {
            name: "date",
            date: "",
            config: {
                allowInput: true,
                wrap: true,
                clickOpens: false,
                time_24hr: true,
                locale: MandarinTraditional,
                disableMobile: "true",
            },
        };
    },
    created() {
        if (this.setting) {
            if (this.setting.name) {
                this.name = this.setting.name;
            }
        }
    },
    watch: {
        "setting.date": {
            handler(date) {
                this.date = date;
            },
            immediate: true,
        },
        "setting.config": {
            handler(config) {
                this.config = Object.assign({}, this.config, config);
            },
            deep: true,
            immediate: true,
        },
    },
    methods: {
        onChange(selectedDates, dateStr, instance) {
            this.setting.date = dateStr;
            this.$emit("on-change", selectedDates, dateStr, instance);
        },
    },
};
</script>
