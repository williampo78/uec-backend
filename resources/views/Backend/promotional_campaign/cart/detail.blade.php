<div class="modal fade" id="promotional_campaign_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i>滿額活動 檢視資料</h4>
            </div>

            <div class="modal-body">
                <div id="campaign-block" class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">活動名稱</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static" id="modal-campaign-name"></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">狀態</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static" id="modal-active"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">活動類型</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static" id="modal-campaign-type"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label class="control-label">N (滿額) = </label>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="form-control-static" id="modal-n-value"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label class="control-label">X (折扣) = </label>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="form-control-static" id="modal-x-value"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">上架時間起</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static" id="modal-start-at"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">上架時間訖</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static" id="modal-end-at"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid gray;" />
                </div>

                <div id="applicable-target-block">
                    <div class="form-group">
                        <label class="control-label">適用對象</label>

                        <div class="row" style="margin-left: 1rem;">
                            <div class="col-sm-2">
                                <p id="modal-target-groups"></p>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid gray;" />
                </div>

                <div id="prd-block" style="display: none;">
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="control-label">單品清單</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class='table table-striped table-bordered table-hover' style='width:100%' id="prd-product-table">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">項次</th>
                                    <th class="text-nowrap">商品序號</th>
                                    <th class="text-nowrap">商品名稱</th>
                                    <th class="text-nowrap">售價(含稅)</th>
                                    <th class="text-nowrap">上架日期</th>
                                    <th class="text-nowrap">上架狀態</th>
                                    <th class="text-nowrap">毛利(%)</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <hr style="border-top: 1px solid gray;" />
                </div>

                <div id="gift-block" style="display: none;">
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="control-label">贈品清單</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class='table table-striped table-bordered table-hover' style='width:100%' id="gift-product-table">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">項次</th>
                                    <th class="text-nowrap">商品序號</th>
                                    <th class="text-nowrap">商品名稱</th>
                                    <th class="text-nowrap">贈品數量</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <hr style="border-top: 1px solid gray;" />
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
