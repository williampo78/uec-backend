<div class="modal fade" id="gift-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">新增贈品</h4>
            </div>

            <div class="modal-body">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading form-horizontal">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label for="gift-modal-supplier-id" class="control-label">供應商</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select class="form-control select2-supplier-id" id="gift-modal-supplier-id">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label for="gift-modal-product-no" class="control-label">商品序號</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="gift-modal-product-no" placeholder="模糊查詢" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label for="gift-modal-product-name" class="control-label">商品名稱</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input class="form-control" id="gift-modal-product-name" placeholder="模糊查詢，至少輸入4碼" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label class="control-label">售價</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="gift-modal-selling-price-min">
                                    </div>
                                    <div class="col-sm-2 text-center">
                                        <label class="control-label">~</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="gift-modal-selling-price-max">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label class="control-label">建檔日</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='input-group date' id='datetimepicker-gift-modal-start-created-at'>
                                            <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker-gift-modal-start-created-at" id="gift-modal-start-created-at" autocomplete="off" />
                                            <span class="input-group-addon" data-target="#datetimepicker-gift-modal-start-created-at" data-toggle="datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 text-center">
                                        <label class="control-label">~</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='input-group date' id='datetimepicker-gift-modal-end-created-at'>
                                            <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker-gift-modal-end-created-at" id="gift-modal-end-created-at" autocomplete="off" />
                                            <span class="input-group-addon" data-target="#datetimepicker-gift-modal-end-created-at" data-toggle="datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label class="control-label">上架日期</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='input-group date' id='datetimepicker-gift-modal-start-launched-at-start'>
                                            <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker-gift-modal-start-launched-at-start" id="gift-modal-start-launched-at-start" autocomplete="off" />
                                            <span class="input-group-addon" data-target="#datetimepicker-gift-modal-start-launched-at-start" data-toggle="datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 text-center">
                                        <label class="control-label">~</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='input-group date' id='datetimepicker-gift-modal-start-launched-at-end'>
                                            <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker-gift-modal-start-launched-at-end" id="gift-modal-start-launched-at-end" autocomplete="off" />
                                            <span class="input-group-addon" data-target="#datetimepicker-gift-modal-start-launched-at-end" data-toggle="datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label for="gift-modal-product-type" class="control-label">商品類型</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select class="form-control select2-product-type" id="gift-modal-product-type">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label for="gift-modal-limit" class="control-label">筆數限制</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="gift-modal-limit" value="100" max="100" min="0" readonly>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <button type="button" class="btn btn-warning" id="gift-modal-btn-search"><i class="fa fa-search"></i> 查詢</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-success" id="gift-modal-btn-save"><i class="fa fa-save"></i> 儲存</button>
                                <button type="button" class="btn btn-success" id="gift-modal-btn-save-and-close" data-dismiss="modal"><i class="fa fa-save"></i> 儲存並關閉</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-ban"></i> 取消</button>
                            </div>
                            <div class="col-sm-12">
                                <hr/>
                            </div>
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-primary" id="gift-modal-btn-check-all"><i class="fa fa-check"></i> 全勾選</button>
                                <button type="button" class="btn btn-primary" id="gift-modal-btn-cancel-all"><i class="fa fa-close"></i> 全取消</button>
                            </div>
                        </div>
                        <br/>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" style="width:100%" id="gift-modal-product-table">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap"></th>
                                        <th class="text-nowrap">商品序號</th>
                                        <th class="text-nowrap">商品名稱</th>
                                        <th class="text-nowrap">售價(含稅)</th>
                                        <th class="text-nowrap">上架日期</th>
                                        <th class="text-nowrap">上架狀態</th>
                                        <th class="text-nowrap">毛利(%)</th>
                                        <th class="text-nowrap">供應商</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
