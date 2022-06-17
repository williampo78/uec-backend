<template>
    <div class="input-group">
        <flat-pickr
            :config="defaultConfig"
            class="form-control"
            :name="name"
            autocomplete="off"
            data-input
            :value="value"
            @input="test"
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
    name: "VueFlatPickr",
    props: {
        name: String,
        value: String,
    },
    data() {
        return {
            defaultConfig: {
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
        console.log('created');
        console.log(this.value);
    },
    methods: {
        onChange(selectedDates, dateStr, instance) {
            // console.log(selectedDates, dateStr);
            // console.log(this.value);
            // this.$emit("on-change", this.value);
        },
        test() {
            console.log($event.target.value);
            // this.$emit('input', $event.target.value)
        },
    },
};
</script>
