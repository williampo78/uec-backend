<div class="row" v-if="!isNowGreaterThanOrEqualToStartAt">
    <div class="col-sm-1">
        <button type="button" class="btn btn-warning" @click="addGiveaway(threshold)">
            <i class="fa-solid fa-plus"></i> 新增贈品
        </button>
    </div>
</div>
<br>
<div class="table-responsive">
    <table class='table table-striped table-bordered table-hover' style='width:100%'>
        <thead>
            <tr>
                <th class="text-nowrap">項次</th>
                <th class="text-nowrap">商品序號</th>
                <th class="text-nowrap">商品名稱</th>
                <th class="text-nowrap">數量</th>
                <th class="text-nowrap">庫存類型</th>
                <th class="text-nowrap">商品類型</th>
                <th class="text-nowrap">供應商</th>
                <th class="text-nowrap">庫存數</th>
                <th class="text-nowrap">上架狀態</th>
                <th class="text-nowrap" v-if="!isNowGreaterThanOrEqualToStartAt">功能</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(giveaway, index) in threshold.giveaways" :key="index">
                <input type="hidden" :name="`thresholds[${thresholdIndex}][giveaways][${index}][id]`" :value="giveaway.id">
                <input type="hidden" :name="`thresholds[${thresholdIndex}][giveaways][${index}][product_id]`" :value="giveaway.productId">
                <td>@{{ index + 1 }}</td>
                <td>@{{ giveaway.productNo }}</td>
                <td>@{{ giveaway.productName }}</td>
                <td>
                    <div class="form-group">
                        <input type="number" class="form-control giveaway-assigned-qty" :name="`thresholds[${thresholdIndex}][giveaways][${index}][assigned_qty]`" min="1"
                            v-model="giveaway.assignedQty" :disabled="isNowGreaterThanOrEqualToStartAt">
                    </div>
                </td>
                <td>@{{ giveaway.stockType }}</td>
                <td>@{{ giveaway.productType }}</td>
                <td>@{{ giveaway.supplier }}</td>
                <td>@{{ giveaway.stockQty }}</td>
                <td>@{{ giveaway.launchStatus }}</td>
                <td v-if="!isNowGreaterThanOrEqualToStartAt">
                    <button type="button" class="btn btn-danger" @click="deleteGiveaway(threshold, index)">
                        <i class="fa-solid fa-trash-can"></i> 刪除
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
