<div class="modal fade" :id="modal.show.id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> @{{ modal.show.title }}</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">退出單號</label>
                            <p class="form-control-static">@{{ modal.show.requestNo }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">庫別</label>
                            <p class="form-control-static">@{{ modal.show.warehouseName }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">申請總量</label>
                            <p class="form-control-static">@{{ modal.show.expectedQty }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">申請時間</label>
                            <p class="form-control-static">@{{ modal.show.requestDate }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">送審時間</label>
                            <p class="form-control-static">@{{ modal.show.submittedAt }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">預計出庫日</label>
                            <p class="form-control-static">@{{ modal.show.expectedDate }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">稅別</label>
                            <p class="form-control-static">@{{ modal.show.tax }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">申請稅額</label>
                            <p class="form-control-static">@{{ modal.show.expectedTaxAmount }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">申請總金額</label>
                            <p class="form-control-static">@{{ modal.show.expectedAmount }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">備註</label>
                            <p class="form-control-static" style="white-space: pre-wrap">@{{ modal.show.remark }}</p>
                        </div>
                    </div>
                </div>
                <hr style="border-top: 1px solid gray;">

                <h4>物流資訊</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">收件人</label>
                            <p class="form-control-static">@{{ modal.show.shipToName }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">聯絡電話</label>
                            <p class="form-control-static">@{{ modal.show.shipToMobile }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">地址</label>
                            <p class="form-control-static">@{{ modal.show.shipToAddress }}</p>
                        </div>
                    </div>
                </div>
                <hr style="border-top: 1px solid gray;">

                <h4>倉庫回報資訊</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">實際出庫日</label>
                            <p class="form-control-static">@{{ modal.show.actualDate }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">實退稅額</label>
                            <p class="form-control-static">@{{ modal.show.actualTaxAmount }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">實退總金額</label>
                            <p class="form-control-static">@{{ modal.show.actualAmount }}</p>
                        </div>
                    </div>
                </div>
                <hr style="border-top: 1px solid gray;">

                <h4>商品明細</h4>
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
