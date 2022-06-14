import PrdCampaignProductModal from "../../components/PrdCampaignProductModal.vue";

Vue.component("prd-campaign-product-modal", PrdCampaignProductModal);

// 比較商品售價與折扣
jQuery.validator.addMethod(
    "compareDiscountAndSellingPrice",
    function (value, element, products) {
        let productNo = '';
        let errorCount = 0;

        products.forEach((product) => {
            if (product.sellingPrice <= value) {
                if (errorCount >= 10) {
                    productNo += '...';
                    return;
                }

                if (errorCount > 0 && errorCount < 10) {
                    productNo += ', ';
                }

                productNo += product.productNo;
                errorCount++;
            }
        });

        if (!errorCount) {
            return true;
        }

        $.validator.messages.compareDiscountAndSellingPrice = `不可大於等於商品售價 (${productNo})`;

        return false;
    },
    $.validator.messages.compareDiscountAndSellingPrice
);
