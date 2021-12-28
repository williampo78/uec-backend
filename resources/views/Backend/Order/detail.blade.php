<div class="modal fade" id="order_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i>訂單管理 檢視資料</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="panel panel-default no-border-bottom">
                            <div class="panel-heading text-center">訂單資訊</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">訂單編號</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-order-no"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">訂單時間</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-ordered-date"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">訂單狀態</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-order-status-code"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">付款方式</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-payment-method"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">付款狀態</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-pay-status"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">免運門檻</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-shipping-free-threshold"></p>
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
                                                <label class="control-label">會員帳號</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-member-account"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">訂購人姓名</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-buyer-name"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">訂購人eMail</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-buyer-email"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default no-border-bottom">
                            <div class="panel-heading text-center">收件人</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">收件者</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-receiver-name"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">收件手機</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-receiver-mobile"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">收件地址</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-receiver-address"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">物流</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">物流方式</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-lgst-method"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="control-label">出貨單狀態</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <p id="modal-shipment-status-code"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="panel panel-default amount-panel">
                            <div class="panel-heading"></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-9 text-right">
                                            <label class="control-label">商品總價 (A)：</label>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <p id="modal-total-amount"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-9 text-right">
                                            <label class="control-label">滿額折抵 (B)：</label>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <p id="modal-cart-campaign-discount"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-9 text-right">
                                            <label class="control-label">點數折抵 (C)：</label>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <p id="modal-point-discount"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-9 text-right">
                                            <label class="control-label">運費 (D)：</label>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <p id="modal-shipping-fee"></p>
                                        </div>
                                    </div>
                                </div>

                                <hr style="border-top: 1px solid gray;" />

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-sm-9 text-right">
                                            <label class="control-label">結帳金額 (=A+B+C+D)：</label>
                                        </div>
                                        <div class="col-sm-3 text-right">
                                            <p id="modal-paid-amount"></p>
                                        </div>
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
                        <a href="#tab-order-detail" data-toggle="tab">訂單明細</a>
                    </li>
                    <li>
                        <a href="#tab-invoice-info" data-toggle="tab">發票資訊</a>
                    </li>
                    <li>
                        <a href="#tab-payment-info" data-toggle="tab">金流資訊</a>
                    </li>
                    <li>
                        <a href="#tab-campaign-discount" data-toggle="tab">活動折抵</a>
                    </li>
                    <li>
                        <a href="#tab-lgst-info" data-toggle="tab">物流資訊</a>
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
                                        <th class="text-nowrap">Item編號</th>
                                        <th class="text-nowrap">商品名稱</th>
                                        <th class="text-nowrap">規格一</th>
                                        <th class="text-nowrap">規格二</th>
                                        <th class="text-nowrap">售價</th>
                                        <th class="text-nowrap">商品活動價</th>
                                        <th class="text-nowrap">數量</th>
                                        <th class="text-nowrap">活動折抵</th>
                                        <th class="text-nowrap">小計</th>
                                        <th class="text-nowrap">點數折抵</th>
                                        <th class="text-nowrap">備註</th>
                                        <th class="text-nowrap">託運單號</th>
                                        <th class="text-nowrap">已退數量</th>
                                        <th class="text-nowrap">已退活動折抵</th>
                                        <th class="text-nowrap">已退小計</th>
                                        <th class="text-nowrap">已退點數折抵</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-invoice-info">

                    </div>
                    <div class="tab-pane fade" id="tab-payment-info">

                    </div>
                    <div class="tab-pane fade" id="tab-campaign-discount">

                    </div>
                    <div class="tab-pane fade" id="tab-lgst-info">

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal"><i
                        class="fa fa-fw fa-close"></i>關閉視窗</button>
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
