<script>
import vSelect from "vue-select";

vSelect.props.placeholder.default = "請選擇";

export default vSelect;
</script>

<style scoped>
* >>> {
    --vs-search-input-placeholder-color: #999;

    --vs-font-size: 1.4rem;
    --vs-line-height: 1.6;

    --vs-dropdown-option-padding: 6px 12px;
}

.v-select .vs__dropdown-toggle {
    background: #fff;
    padding-left: 3px;
}
</style>
