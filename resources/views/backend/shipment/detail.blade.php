<div class="modal fade" id="shipment_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 出貨單管理 檢視資料</h4>
            </div>

            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">出貨單號</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-shipment-no"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">建單時間</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-created-at"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">出貨單狀態</label>
                                </div>
                                <div class="col-sm-8">
                                    <div class="col-sm-4">
                                        <p class="form-control-static" id="modal-status-code"></p>
                                    </div>
                                    <div class="col-sm-8">
                                        <p class="form-control-static" id="modal-progress-log">
                                            <button type="button" class="btn btn-primary btn-sm progress_log_detail" data-shipment="{{ $shipment['shipments_id'] }}">出貨配送歷程</button>
                                        </p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
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
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">物流廠商</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-lgst-company"></p>
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
                                    <label class="control-label">收件者</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-ship-to-name"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">收件手機</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-ship-to-mobile"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">收件地址</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-ship-to-address"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">會員帳號</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-member-account"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">EDI轉出時間</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-edi-exported-at"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">託運單號</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-package-no"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">出貨時間</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-shipped-at"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">到店時間</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-arrived-store-at"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">(超取)取件時間</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-cvs-completed-at"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">(宅配)配達時間</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-home-dilivered-at"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">客拒收 / 未取時間</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-overdue-confirmed-at"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">取消 / 作廢時間</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-cancelled-voided-at"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 1px solid gray;" />

                <div class="table-responsive">
                    <table class='table table-striped table-bordered table-hover' style='width:100%'
                        id="modal-product-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap">項次</th>
                                <th class="text-nowrap">Item編號</th>
                                <th class="text-nowrap">商品名稱</th>
                                <th class="text-nowrap">規格一</th>
                                <th class="text-nowrap">規格二</th>
                                <th class="text-nowrap">數量</th>
                                <th class="text-nowrap">廠商料號</th>
                                <th class="text-nowrap">廠商貨號</th>
                                <th class="text-nowrap">供應商</th>
                            </tr>
                        </thead>
                        <tbody>
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
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
