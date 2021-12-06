<div class="modal fade" id="prd_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group display-flex-center">
                                    <div class="col-sm-3">
                                        <label for="supplier_id">供應商</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-supplier-id" name="supplier_id" id="supplier_id">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group display-flex-center">
                                    <div class="col-sm-3">
                                        <label for="product_no">商品序號</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_no" id="product_no" placeholder="模糊查詢" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group display-flex-center">
                                    <div class="col-sm-3">
                                        <label for="product_name">商品名稱</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_name" id="product_name" placeholder="模糊查詢，至少輸入4碼" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group display-flex-center">
                                    <div class="col-sm-3">
                                        <label>售價</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="selling_price_min" id="selling_price_min">
                                    </div>
                                    <div class="col-sm-1 text-center">
                                        ~
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="selling_price_max" id="selling_price_max">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group display-flex-center">
                                    <div class="col-sm-3">
                                        <label>建檔日</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='input-group date' id='datetimepicker_start_created_at'>
                                            <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker_start_created_at"
                                                name="start_created_at" id="start_created_at" value="" autocomplete="off" />
                                            <span class="input-group-addon" data-target="#datetimepicker_start_created_at" data-toggle="datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 text-center">
                                        ~
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='input-group date' id='datetimepicker_end_created_at'>
                                            <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker_end_created_at"
                                                name="end_created_at" id="end_created_at" value="" autocomplete="off" />
                                            <span class="input-group-addon" data-target="#datetimepicker_end_created_at" data-toggle="datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group display-flex-center">
                                    <div class="col-sm-3">
                                        <label>上架日期</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='input-group date' id='datetimepicker_start_launched_at'>
                                            <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker_start_launched_at"
                                                name="start_launched_at" id="start_launched_at" value="" autocomplete="off" />
                                            <span class="input-group-addon" data-target="#datetimepicker_start_launched_at" data-toggle="datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 text-center">
                                        ~
                                    </div>
                                    <div class="col-sm-4">
                                        <div class='input-group date' id='datetimepicker_end_launched_at'>
                                            <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker_end_launched_at"
                                                name="end_launched_at" id="end_launched_at" value="" autocomplete="off" />
                                            <span class="input-group-addon" data-target="#datetimepicker_end_launched_at" data-toggle="datetimepicker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group display-flex-center">
                                    <div class="col-sm-3">
                                        <label for="product_type">商品類型</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-product-type" name="product_type" id="product_type">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group display-flex-center">
                                    <div class="col-sm-3">
                                        <label>筆數限制</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" name="limit" id="limit" value="100" max="100" min="0" readonly>
                                    </div>
                                    <div class="col-sm-5 text-right">
                                        <button type="button" class="btn btn-warning" id="btn-search-product"><i class="fa fa-search"></i> 查詢</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-success" @click="productsForCategory"><i class="fa fa-save"></i> 儲存</button>
                                <button type="button" class="btn btn-success" @click="productsForCategory" data-dismiss="modal"><i class="fa fa-save"></i> 儲存並關閉</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-ban"></i> 取消</button>
                            </div>
                            <div class="col-sm-12">
                                <hr/>
                            </div>
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-primary" @click="check_all('allon')"><i class="fa fa-check"></i> 全勾選</button>
                                <button type="button" class="btn btn-primary" @click="check_all('alloff')"><i class="fa fa-close"></i> 全取消</button>
                            </div>
                        </div>
                        <br/>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" style="width:100%">
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
                                    <input type="hidden" id="prd-modal-row-no" value="0" />

                                    <tr>
                                        <td></td>
                                        <td>
                                            <div class="text-center">
                                                <input type="checkbox" class="big-checkbox"
                                                    style="width: 20px;height: 20px;" />
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
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
