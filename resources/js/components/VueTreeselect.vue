<script>
import VueTreeselect from "@riophae/vue-treeselect";

export default {
    name: "treeselect",
    extends: VueTreeselect,
    props: {
        clearValueText: {
            default: "清空",
        },
        loadingText: {
            default: "載入中...",
        },
        noChildrenText: {
            default: "沒有子項目",
        },
        noOptionsText: {
            default: "沒有可使用的項目",
        },
        noResultsText: {
            default: "沒有找到任何項目",
        },
        placeholder: {
            default: "請選擇...",
        },
        showCount: {
            default: true,
        },
    },
};
</script>
