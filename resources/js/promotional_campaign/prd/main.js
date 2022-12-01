import PrdCampaignProductModal from "../../components/PrdCampaignProductModal.vue";

Vue.component("prd-campaign-product-modal", PrdCampaignProductModal);

// 比較商品售價與折扣
jQuery.validator.addMethod(
    "compareDiscountAndSellingPrice",
    function (value, element, form) {
        let productNo = '';
        let errorCount = 0;
        let products = form.products; //產品類型
        let campaignType = form.campaignType; //活動類型
        let N = form.nValue; //滿額活動
        let errorMessage = '';
        if (campaignType == 'PRD02') { // (單品)第N件(含)以上，折X元 
            if (N == 1) {
                products.forEach((product) => { //N = 1：X 須小於 商品售價  
                    if (Number(product.sellingPrice) <= Number(value)) {
                        if (errorCount >= 10) {
                            productNo += '...';
                            return;
                        }

                        if (errorCount > 0 && errorCount < 10) {
                            productNo += ', ';
                        }
                        errorMessage = '須小於商品售價';
                        productNo += product.productNo;
                        errorCount++;
                    }
                });

            } else if (N > 1) {
                products.forEach((product) => { //N > 1：X 須小於等於  商品售價  
                    if (Number(product.sellingPrice) < Number(value)) {
                        if (errorCount >= 10) {
                            productNo += '...';
                            return;
                        }

                        if (errorCount > 0 && errorCount < 10) {
                            productNo += ', ';
                        }
                        errorMessage = '須小於等於商品售價';
                        productNo += product.productNo;
                        errorCount++;
                    }
                });

            }

        } else if (campaignType == 'PRD04') { // (單品)滿N件，每件折X元
            products.forEach((product) => {
                if (Number(product.sellingPrice) <= Number(value)) {
                    if (errorCount >= 10) {
                        productNo += '...';
                        return;
                    }
    
                    if (errorCount > 0 && errorCount < 10) {
                        productNo += ', ';
                    }
                    errorMessage = '須小於商品售價';
                    productNo += product.productNo;
                    errorCount++;
                }
            });
        }

        if (!errorCount) {
            return true;
        }

        $.validator.messages.compareDiscountAndSellingPrice = `${errorMessage}(${productNo})`;

        return false;
    },
    $.validator.messages.compareDiscountAndSellingPrice
);
