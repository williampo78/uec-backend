<div class="modal fade overflow-auto" id="invoice_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 發票資訊</h4>
            </div>

            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">發票號碼</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="invoice-modal-invoice-no"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">發票日期</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="invoice-modal-transaction-date"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">隨機碼</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="invoice-modal-random-no"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">訂單編號</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="invoice-modal-order-no"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">銷售額</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="invoice-modal-selling-price"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">課稅別</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="invoice-modal-tax-type"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">總稅額</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="invoice-modal-total-tax"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">總金額</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="invoice-modal-amount"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 1px solid gray;" />

                <div class="row">
                    <div class="col-sm-12">
                        <p>商品明細</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class='table table-striped table-bordered table-hover' style='width:100%'
                        id="invoice-modal-invoice-info-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap">項次</th>
                                <th class="text-nowrap">商品名稱</th>
                                <th class="text-nowrap">單價</th>
                                <th class="text-nowrap">數量</th>
                                <th class="text-nowrap">小計</th>
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
