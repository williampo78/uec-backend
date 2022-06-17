<div class="modal fade" id="buyout-stock-in-request-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 檢視寄售商品入庫申請</h4>
            </div>

            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">申請單號</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.requestNo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">申請時間</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.requestDate }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">供應商</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.supplier }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">預計入庫日</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.expectedDate }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">總申請量</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.expectedQty }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">備註</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static" style="white-space: pre-wrap">@{{ modal.remark }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">狀態</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.statusCode }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">實際入庫時間</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.actualDate }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr style="border-top: 1px solid gray;">

                <h4>品項</h4>
                <table class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-nowrap">項次</th>
                            <th class="text-nowrap">商品Item</th>
                            <th class="text-nowrap">規格一</th>
                            <th class="text-nowrap">規格二</th>
                            <th class="text-nowrap">最小入庫量</th>
                            <th class="text-nowrap">申請數量</th>
                            <th class="text-nowrap">實際入庫量</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in modal.items" :key="index">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ item.productItem }}</td>
                            <td>@{{ item.spec1Value }}</td>
                            <td>@{{ item.spec2Value }}</td>
                            <td>@{{ item.minPurchaseQty }}</td>
                            <td>@{{ item.expectedQty }}</td>
                            <td>@{{ item.actualQty }}</td>
                        </tr>
                    </tbody>
                </table>
                <hr style="border-top: 1px solid gray;">

                <h4>簽核記錄</h4>
                <table class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-nowrap">次序</th>
                            <th class="text-nowrap">簽核人員</th>
                            <th class="text-nowrap">簽核時間</th>
                            <th class="text-nowrap">簽核結果</th>
                            <th class="text-nowrap">簽核結果備註</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(reviewLog, index) in modal.reviewLogs" :key="index">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ reviewLog.reviewer }}</td>
                            <td>@{{ reviewLog.reviewAt }}</td>
                            <td>@{{ reviewLog.reviewResult }}</td>
                            <td>@{{ reviewLog.reviewRemark }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> 關閉視窗
                </button>
            </div>
        </div>
    </div>
</div>
