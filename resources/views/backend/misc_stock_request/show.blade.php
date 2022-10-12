<div class="modal fade" :id="modal.show.id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> @{{ modal.show.title }}</h4>
            </div>

            <div class="modal-body">
                <div class="stock-viewer d-block d-md-grid">
                    <div class="form-group viewer-title mb-0">
                        <label class="control-label label-title">退出單號</label>
                        <p class="form-control-static label-content">@{{ modal.show.requestNo }}</p>
                    </div>
                    <div class="form-group viewer-title mb-0">
                        <label class="control-label label-title">庫別</label>
                        <p class="form-control-static label-content">@{{ modal.show.warehouseName }}</p>
                    </div>
                    <div class="form-group viewer-title mb-0">
                        <label class="control-label label-title">申請總量</label>
                        <p class="form-control-static label-content">@{{ modal.show.expectedQty }}</p>
                    </div>
                        <div class="form-group viewer-title mb-0">
                            <label class="control-label label-title">申請時間</label>
                            <p class="form-control-static label-content">@{{ modal.show.requestDate }}</p>
                        </div>
                        <div class="form-group viewer-title mb-0">
                            <label class="control-label label-title">送審時間</label>
                            <p class="form-control-static label-content">@{{ modal.show.submittedAt }}</p>
                        </div>
                        <div class="form-group viewer-title mb-0">
                            <label class="control-label label-title">預計出庫日</label>
                            <p class="form-control-static label-content">@{{ modal.show.expectedDate }}</p>
                        </div>
                        <div class="form-group viewer-title mb-0">
                            <label class="control-label label-title">稅別</label>
                            <p class="form-control-static label-content">@{{ modal.show.tax }}</p>
                        </div>
                        <div class="form-group viewer-title mb-0">
                            <label class="control-label label-title">申請稅額</label>
                            <p class="form-control-static label-content">@{{ modal.show.expectedTaxAmount }}</p>
                        </div>
                        <div class="form-group viewer-title mb-0">
                            <label class="control-label label-title">申請總金額</label>
                            <p class="form-control-static label-content">@{{ modal.show.expectedAmount }}</p>
                        </div>
                        <div class="form-group viewer-title mb-0 column-full border-0">
                            <label class="control-label label-title">備註</label>
                            <p class="form-control-static label-content">@{{ modal.show.remark }}</p>
                        </div>
                </div>

                <hr style="border-top: 1px solid gray;">

                <div class="viewer-outer d-block d-md-grid">
                    <div>
                        <h5 class="fs-4 fw-bold">物流資訊</h5>
                        <div class="viewer-inner d-block d-md-grid">
                            <div class="form-group viewer-title mb-0">
                                <label class="control-label label-title">收件人</label>
                                <p class="form-control-static label-content">@{{ modal.show.shipToName }}</p>
                            </div>
                            <div class="form-group viewer-title mb-0">
                                <label class="control-label label-title">聯絡電話</label>
                                <p class="form-control-static label-content">@{{ modal.show.shipToMobile }}</p>
                            </div>
                            <div class="form-group viewer-title mb-0 border-0" style="grid-column: 1/3;">
                                <label class="control-label label-title">地址</label>
                                <p class="form-control-static label-content">@{{ modal.show.shipToAddress }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h5 class="fs-4 fw-bold">倉庫回報資訊</h5>
                        <div class="viewer-inner d-block d-md-grid">
                            <div class="form-group viewer-title mb-0">
                                <label class="control-label  label-title">實際出庫日</label>
                                <p class="form-control-static label-content">@{{ modal.show.actualDate }}</p>
                            </div>
                            <div class="form-group viewer-title mb-0">
                                <label class="control-label  label-title">實退稅額</label>
                                <p class="form-control-static label-content">@{{ modal.show.actualTaxAmount }}</p>
                            </div>
                            <div class="form-group viewer-title mb-0 border-0" style="grid-column: 1/3;">
                                <label class="control-label label-title">實退總金額</label>
                                <p class="form-control-static label-content">@{{ modal.show.actualAmount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 1px solid gray;">

                <h5 class="fs-4 fw-bold">商品明細</h5>
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
                                <th class="text-nowrap">供應商</th>
                                <th class="text-nowrap">實退量</th>
                                <th class="text-nowrap">實退小計</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in modal.show.items" :key="index">
                                <td>@{{ item.productNo }}</td>
                                <td>@{{ item.productName }}</td>
                                <td>@{{ item.itemNo }}</td>
                                <td>@{{ item.spec1Value }}</td>
                                <td>@{{ item.spec2Value }}</td>
                                <td>@{{ item.unitPrice }}</td>
                                <td>@{{ item.stockQty }}</td>
                                <td>@{{ item.expectedQty }}</td>
                                <td>@{{ item.expectedSubtotal }}</td>
                                <td>@{{ item.supplierName }}</td>
                                <td>@{{ item.actualQty }}</td>
                                <td>@{{ item.actualSubtotal }}</td>
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
