<div class="modal fade overflow-auto" id="order_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 訂單管理 檢視資料</h4>
            </div>
            <div class="modal-body">
                <div style="font-weight:bold" class="row">
                    <div class="col-lg-3 d-lg-flex flex-lg-column flex-grow-1 d-block">
                        <h5 style="font-weight: bold; font-size:16px">訂單資訊</h5>
                        <table class="table table-bordered flex-grow-1">
                            <tr>
                                <th class="active align-middle">訂單編號</th>
                                <td id="modal-order-no" class="align-middle"></td>
                            </tr>
                            <tr>
                                <th class="active align-middle">訂單狀態</th>
                                <td id="modal-order-status-code" class="align-middle"></td>
                            </tr>
                            <tr>
                                <th class="active align-middle">付款狀態</th>
                                <td id="modal-pay-status" class="align-middle"></td>
                            </tr>
                            <tr>
                                <th class="active align-middle">訂單時間</th>
                                <td id="modal-ordered-date" class="align-middle"></td>
                            </tr>
                            <tr>
                                <th class="active align-middle">付款方式</th>
                                <td id="modal-payment-method" class="align-middle"></td>
                            </tr>
                            <tr>
                                <th class="active align-middle">免運門檻</th>
                                <td id="modal-shipping-free-threshold" class="align-middle"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-5">
                        <h5 style="font-weight: bold; font-size:16px">訂購人</h5>
                        <table style="table-layout: fixed" class="table table-bordered">
                            <tr>
                                <th class="active">訂購人姓名</th>
                                <td id="modal-buyer-name"></td>
                                <th class="active">會員帳號</th>
                                <td id="modal-member-account"></td>
                            </tr>
                            <tr>
                                <th class="active">訂購人Email</th>
                                <td colspan="3" id="modal-buyer-email"></td>
                            </tr>
                        </table>
                        <h5 style="font-weight: bold; font-size:16px">收件人</h5>
                        <table style="table-layout: fixed" class="table table-bordered">
                            <tr>
                                <th class="active">收件者</th>
                                <td id="modal-receiver-name"></td>
                                <th class="active">收件手機</th>
                                <td id="modal-receiver-mobile"></td>
                            </tr>
                            <tr>
                                <th class="active">收件地址</th>
                                <td colspan="3" id="modal-receiver-address"></td>
                            </tr>
                        </table>
                        <h5 style="font-weight: bold; font-size:16px">物流</h5>
                        <table style="table-layout: fixed" class="table table-bordered">
                            <tr>
                                <th class="active">物流方式</th>
                                <td colspan="3" id="modal-lgst-method"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-4">
                        <h5 style="font-weight: bold; font-size:16px">
                        金額</h5>
                        <table style="table-layout: fixed" class="table table-bordered">
                            <tr>
                                <th colspan="4" class="active">{{ config('uec.cart_p_discount_split') == 1 ? '折後商品總價' : '商品總價' }} (A)：</th>
                                <td class="text-right" colspan="3" id="modal-total-amount"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="active">滿額折抵 (B)：</th>
                                <td class="text-right" colspan="3" id="modal-cart-campaign-discount"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="active">點數折抵 (C)：</th>
                                <td class="text-right" colspan="3" id="modal-point-discount"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="active">運費 (D)：</th>
                                <td class="text-right" colspan="3" id="modal-shipping-fee"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="active">結帳金額 (=A+B+C+D)：</th>
                                <td class="text-right" colspan="3" id="modal-paid-amount"></td>
                            </tr>
                        </table>
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
                    <li>
                        <a href="#tab-return-success" data-toggle="tab">退貨成功</a>
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
                                        <th class="text-nowrap">單品<br>活動折抵</th>
                                        @if(config('uec.cart_p_discount_split') == 1)
                                            <th class="text-nowrap">購物車<br>滿額折抵</th>
                                            <th class="text-nowrap">小計</th>
                                        @else
                                            <th class="text-nowrap">小計</th>
                                            <th class="text-nowrap">購物車<br>滿額折抵</th>
                                        @endif
                                        <th class="text-nowrap">點數折抵</th>
                                        <th class="text-nowrap">訂單身份</th>
                                        <th class="text-nowrap">廠商貨號</th>
                                        <th class="text-nowrap">廠商料號</th>

                                        <th class="text-nowrap">託運單號</th>
                                        <th class="text-nowrap">供應商</th>
                                        <th class="text-nowrap">商品類型</th>
                                        <th class="text-nowrap">已退數量</th>
                                        <th class="text-nowrap">已退活動折抵</th>

                                        <th class="text-nowrap">已退小計</th>
                                        <th class="text-nowrap">已退點數折抵</th>
                                        <th class="text-nowrap">出貨單號</th>
                                        <th class="text-nowrap">出貨單狀態</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-invoice-info">
                        <div class="form-horizontal">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label class="control-label">發票用途</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="form-control-static" id="modal-invoice-usage"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label class="control-label">載具類型</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="form-control-static" id="modal-carrier-type"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label class="control-label">載具號碼</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="form-control-static" id="modal-carrier-no"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label class="control-label">統一編號</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="form-control-static" id="modal-buyer-gui-number"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label class="control-label">抬頭</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="form-control-static" id="modal-buyer-title"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label class="control-label">捐贈機構</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <p class="form-control-static" id="modal-donated-institution-name"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />

                        <div class="table-responsive">
                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">日期</th>
                                        <th class="text-nowrap">類型</th>
                                        <th class="text-nowrap">發票號碼</th>
                                        <th class="text-nowrap">課稅別</th>
                                        <th class="text-nowrap">金額</th>
                                        <th class="text-nowrap"></th>
                                        <th class="text-nowrap">備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-payment-info">
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
                                        <th class="text-nowrap">狀態變更時間</th>
                                        <th class="text-nowrap">備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-campaign-discount">
                        <div class="table-responsive">
                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">活動群組</th>
                                        <th class="text-nowrap">活動層級</th>
                                        <th class="text-nowrap">活動名稱</th>
                                        <th class="text-nowrap">Item編號</th>
                                        <th class="text-nowrap">商品名稱</th>
                                        <th class="text-nowrap">規格一</th>
                                        <th class="text-nowrap">規格二</th>
                                        <th class="text-nowrap">身份</th>
                                        <th class="text-nowrap">折抵金額</th>
                                        <th class="text-nowrap">作廢</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-lgst-info">
                        <div class="table-responsive">
                            <table class='table table-striped table-bordered table-hover' style='width:40%'>
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">類型</th>
                                        <th class="text-nowrap">時間</th>
                                        <th class="text-nowrap">備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>取消 / 作廢時間</th>
                                        <td id="modal-cancelled-voided-at"></td>
                                        <td id="modal-cancelled-reason"></td>
                                    </tr>
                                    <tr>
                                        <th>出貨時間</th>
                                        <td id="modal-shipped-at"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>到店時間</th>
                                        <td id="modal-arrived-store-at"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>(宅配)配達時間</th>
                                        <td id="modal-home-dilivered-at"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>(超取)取件時間</th>
                                        <td id="modal-cvs-completed-at"></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-return-success">
                        <div class="table-responsive">
                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">退貨申請單號</th>
                                        <th class="text-nowrap">明細類型</th>
                                        <th class="text-nowrap">明細描述</th>
                                        <th class="text-nowrap">售價</th>
                                        <th class="text-nowrap">數量</th>
                                        <th class="text-nowrap">小計</th>
                                        <th class="text-nowrap">歸還點數</th>
                                        <th class="text-nowrap">退款金額</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.modal-body -->


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
