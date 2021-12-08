// 加入贈品區塊欄位驗證
function validateGiftBlock() {
    $(`#gift-product-table > tbody [name^="gift_block_assigned_qty"]`).each(function () {
        $(this).rules("add", {
            required: true,
            digits: true,
        });
    });
}
