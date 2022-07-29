<script>
import VueTreeselect from "@riophae/vue-treeselect";
import { warning } from "@riophae/vue-treeselect/src/utils";

export default {
    extends: VueTreeselect,
    props: {
        clearValueText: {
            type: String,
            default: "清空",
        },
        loadingText: {
            type: String,
            default: "載入中...",
        },
        noChildrenText: {
            type: String,
            default: "沒有子項目",
        },
        noOptionsText: {
            type: String,
            default: "沒有可使用的項目",
        },
        noResultsText: {
            type: String,
            default: "沒有找到任何項目",
        },
        placeholder: {
            type: String,
            default: "請選擇",
        },
        showCount: {
            type: Boolean,
            default: true,
        },
    },
    methods: {
        verifyProps() {
            warning(
                () => (this.async ? this.searchable : true),
                () =>
                    'For async search mode, the value of "searchable" prop must be true.'
            );

            if (this.options == null && !this.loadOptions) {
                warning(
                    () => false,
                    () =>
                        'Are you meant to dynamically load options? You need to use "loadOptions" prop.'
                );
            }
            /*
            if (this.flat) {
                warning(
                () => this.multiple,
                () => 'You are using flat mode. But you forgot to add "multiple=true"?',
                )
            }
            */
            if (!this.flat) {
                const propNames = [
                    "autoSelectAncestors",
                    "autoSelectDescendants",
                    "autoDeselectAncestors",
                    "autoDeselectDescendants",
                ];

                propNames.forEach((propName) => {
                    warning(
                        () => !this[propName],
                        () => `"${propName}" only applies to flat mode.`
                    );
                });
            }
        },
    },
};
</script>

<style>
.vue-treeselect__menu {
    line-height: 230%;
}

.vue-treeselect__label-container {
    font-size: 14px;
    color: #000000;
}

.vue-treeselect__count {
    color: #ff0000;
}

.vue-treeselect__option-arrow-container:hover .vue-treeselect__option-arrow,
.vue-treeselect--branch-nodes-disabled
    .vue-treeselect__option:hover
    .vue-treeselect__option-arrow {
    color: #000000;
}

.vue-treeselect__option-arrow {
    width: 11px;
    height: 11px;
}

.vue-treeselect.treeselect-invalid:not(.vue-treeselect--open)
    .vue-treeselect__control {
    border-color: #a94442;
}
</style>
