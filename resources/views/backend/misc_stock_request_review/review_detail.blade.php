<div class="modal fade" :id="modal.reviewDetail.id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> @{{ modal.reviewDetail.title }}</h4>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-nowrap">商品序號</th>
                                <th class="text-nowrap">商品名稱</th>
                                <th class="text-nowrap">Item編號</th>
                                <th class="text-nowrap">規格一</th>
                                <th class="text-nowrap">規格二</th>
                                <th class="text-nowrap">單價</th>
                                <th class="text-nowrap">可退量</th>
                                <th class="text-nowrap">申請量</th>
                                <th class="text-nowrap">申請小計</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in modal.reviewDetail.list" :key="index">
                                <td>@{{ item.productNo }}</td>
                                <td>@{{ item.productName }}</td>
                                <td>@{{ item.itemNo }}</td>
                                <td>@{{ item.spec1Value }}</td>
                                <td>@{{ item.spec2Value }}</td>
                                <td>@{{ item.unitPrice }}</td>
                                <td>@{{ item.stockQty }}</td>
                                <td>@{{ item.expectedQty }}</td>
                                <td>@{{ item.expectedSubtotal }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> 關閉視窗
                </button>
            </div>
        </div>
    </div>
</div>
