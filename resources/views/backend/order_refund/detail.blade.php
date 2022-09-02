<div class="modal fade" id="order_refund_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 退貨申請單管理 檢視資料</h4>
            </div>

            <div class="modal-body">
                <div class="row form-horizontal">
                    <div class="col-sm-12">
                        <div class="panel panel-default no-border-bottom">
                            <div class="panel-heading text-center">退貨單資訊</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">退貨申請單號</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-request-no"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">退貨申請時間</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-request-date"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">訂單編號</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-order-no"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">退貨單狀態</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-status-code"></p>
                                                <button type="button" class="btn btn-warning">人工退款</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">退貨完成時間</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-completed-at"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">物流方式</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-lgst-method"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default no-border-bottom">
                            <div class="panel-heading text-center">訂購人</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">會員編號</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-member-account"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">訂購人</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-buyer-name"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default no-border-bottom">
                            <div class="panel-heading text-center">取件資訊</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">取件聯絡人</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-req-name"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">取件聯絡手機</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-req-mobile"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">取件聯絡電話</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-req-telephone"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">取件地址</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-req-fulladdress"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">退貨說明</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">退貨原因</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static" id="modal-req-reason-description"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">退貨備註</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p class="form-control-static modal-req-remark"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 1px solid gray;" />

                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab-order-detail" data-toggle="tab">退貨明細</a>
                    </li>
                    <li>
                        <a href="#tab-invoice-info" data-toggle="tab">退款資訊</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab-order-detail">
                        <div class="table-responsive">
                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">功能</th>
                                        <th class="text-nowrap">Item編號</th>
                                        <th class="text-nowrap">商品名稱</th>
                                        <th class="text-nowrap">規格一</th>
                                        <th class="text-nowrap">規格二</th>
                                        <th class="text-nowrap">申請數量</th>
                                        <th class="text-nowrap">檢驗合格數量</th>
                                        <th class="text-nowrap">檢驗不合格數</th>
                                    </tr>
                                </thead>
                                <tbody id="return_details_content">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-invoice-info">
                        <div class="table-responsive">
                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">時間</th>
                                        <th class="text-nowrap">類型</th>
                                        <th class="text-nowrap">對象</th>
                                        <th class="text-nowrap">金額</th>
                                        <th class="text-nowrap">狀態</th>
                                        <th class="text-nowrap">備註</th>
                                    </tr>
                                </thead>
                                <tbody id="return_information_content">
                                </tbody>
                            </table>
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
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
