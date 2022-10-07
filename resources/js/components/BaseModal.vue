<template>
    <modal
        :name="name"
        :resizable="true"
        :adaptive="true"
        :scrollable="true"
        :reset="true"
        :width="$attrs.width ? $attrs.width : '90%'"
        height="auto"
        :shiftY="$attrs['shift-y'] ? $attrs['shift-y'] : 0.1"
        :clickToClose="$attrs['click-to-close'] ? $attrs['click-to-close'] : false"
        v-on="$listeners"
        v-bind="$attrs"
    >
        <div class="panel-primary">
            <div class="modal-header panel-heading">
                <button
                    type="button"
                    class="close"
                    @click="$modal.hide(name)"
                    v-if="hasCloseButton"
                >
                    &times;
                </button>

                <h4 class="modal-title">
                    <slot name="title"></slot>
                </h4>
            </div>

            <div class="modal-body">
                <slot name="body"></slot>
            </div>

            <div class="modal-footer" v-if="hasFooter">
                <slot name="footer">
                    <button
                        type="button"
                        class="btn btn-warning"
                        @click="$modal.hide(name)"
                    >
                        <i class="fa-solid fa-xmark"></i> 關閉視窗
                    </button>
                </slot>
            </div>
        </div>
    </modal>
</template>

<script>
export default {
    name: "BaseModal",
    props: {
        name: {
            type: String,
            default: "modal",
        },
        hasFooter: {
            type: Boolean,
            default: true,
        },
        hasCloseButton: {
            type: Boolean,
            default: true,
        },
    },
    methods: {},
};
</script>

<style>
.vm--modal {
    height: auto !important;
    margin-top: 1rem;
    margin-bottom: 1rem !important;
}
</style>
