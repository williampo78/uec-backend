<div class="modal fade" :id="modal.supplier.id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> @{{ modal.supplier.title }}</h4>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-nowrap">功能</th>
                                <th class="text-nowrap">項次</th>
                                <th class="text-nowrap">供應商</th>
                                <th class="text-nowrap">申請狀態</th>
                                <th class="text-nowrap">申請總量</th>
                                <th class="text-nowrap">申請總金額</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in modal.supplier.list" :key="index">
                                <td>
                                    <button type="button" class="btn btn-info btn-sm"
                                        @click="viewRequestSupplierDetail(item.id, item.supplierName)">
                                        明細
                                    </button>
                                </td>
                                <td>@{{ index + 1 }}</td>
                                <td>@{{ item.supplierName }}</td>
                                <td>@{{ item.statusCode }}</td>
                                <td>@{{ item.expectedQty }}</td>
                                <td>@{{ item.expectedAmount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr style="border-top: 1px solid gray;">

                <div v-if="modal.supplier.detail.isShow">
                    <h4>【@{{ modal.supplier.detail.supplierName }}】商品明細</h4>
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
                                <tr v-for="(item, index) in modal.supplier.detail.items" :key="index">
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
                    <hr style="border-top: 1px solid gray;">

                    <h4>【@{{ modal.supplier.detail.supplierName }}】簽核結果</h4>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">簽核時間</label>
                                <p class="form-control-static">@{{ modal.supplier.detail.reviewAt }}</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">簽核人員</label>
                                <p class="form-control-static">@{{ modal.supplier.detail.reviewerName }}</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">簽核結果</label>
                                <p class="form-control-static">@{{ modal.supplier.detail.reviewResult }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">簽核備註</label>
                                <p class="form-control-static" style="white-space: pre-wrap">@{{ modal.supplier.detail.reviewRemark }}</p>
                            </div>
                        </div>
                    </div>
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
