import CartV2CampaignProductModal from "../../components/CartV2CampaignProductModal.vue";

Vue.component("cart-v2-campaign-product-modal", CartV2CampaignProductModal);

// 至少指定一組門檻
jQuery.validator.addMethod(
    "atLeastOneThreshold",
    function (value, element, params) {
        if (params && params.length) {
            return true;
        }

        return false;
    },
    "至少指定一組門檻"
);

// 至少指定一個商品
jQuery.validator.addMethod(
    "atLeastOneProduct",
    function (value, element, params) {
        if (params && params.length) {
            return true;
        }

        return false;
    },
    "至少指定一個商品"
);

// 每個門檻需至少指定一個贈品
jQuery.validator.addMethod(
    "eachThresholdAtLeastOneGiveaway",
    function (value, element, params) {
        if (!params || !params.length) {
            return false;
        }

        if (params.some((threshold) => !threshold.giveaways.length)) {
            return false;
        }

        return true;
    },
    "每個門檻需至少指定一個贈品"
);
