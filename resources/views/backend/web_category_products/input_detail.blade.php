<!-- 使用者明細 -->
<div class="modal fade" id="row_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="width:100%;">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"> 新增商品</h4>
                <input type='hidden' name="get_modal_id" id="get_modal_id" value="" />
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <!-- 功能按鈕(新增) -->
                        <div class="panel-heading">
                            <div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>供應商</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-department" name="supplier"
                                                id="supplier">
                                                <option value=""></option>
                                                @foreach ($supplier as $val)
                                                    <option value='{{ $val['id'] }}'>{{ $val['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>商品序號</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <input placeholder="商品序號" class="form-control" name="product_no"
                                                id="product_no" v-model="select_req.product_no">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>商品名稱</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <input placeholder="商品名稱，至少輸入四個字" class="form-control" name="product_name"
                                                id="product_name" v-model="select_req.product_name">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>售價</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <input placeholder="最低售價" class="form-control" name="selling_price_min"
                                                id="selling_price_min" type="number" value="">
                                        </div>
                                        <div class="col-sm-1" style="">
                                            <h5>~</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <input placeholder="最高售價" class="form-control" name="selling_price_max"
                                                id="selling_price_max" value="" type="number">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>建檔日</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="">
                                                <div class='input-group date' id='start_created_at'>
                                                    <input type='text' class="form-control" name="start_created_at"
                                                        value="" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <h5>~</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class='input-group date' id='end_created_at'>
                                                <input type='text' class="form-control" name="end_created_at"
                                                    value="" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>上架日期</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="">
                                                <div class='input-group date' id='start_launched_at_start'>
                                                    <input type='text' class="form-control"
                                                        name="start_launched_at_start" value="" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <h5>~</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class='input-group date' id='start_launched_at_end'>
                                                <input type='text' class="form-control" name="start_launched_at_end"
                                                    value="" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>商品類型</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-department" name="product_type"
                                                id="product_type" disabled>
                                                <option value="N" selected >一般品</option>
                                                <option value="G">贈品</option>
                                                <option value="A">加購品</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>筆數限制</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-control" name="limit" id="limit" type="number"
                                                value="100" max="100" min="0" readonly>
                                        </div>
                                        <div class="col-sm-5 text-right">
                                            <button type="button" class="btn btn-warning" @click="productsGetAjax">
                                                <i class="fa fa-search"></i> 查詢
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table list -->
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-success"
                                        @click="productsForCategory"><i class="fa fa-save"></i> 儲存</button>
                                    <button type="button" class="btn btn-success" @click="productsForCategory"
                                        data-dismiss="modal"><i class="fa fa-save"></i> 儲存並關閉</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-ban"></i> 取消</button>
                                    {{-- <button type="button" @click="TESTFUNCTION">測試按鈕</button> --}}
                                </div>
                                <div class="col-sm-12">
                                    <hr>
                                </div>
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-primary"
                                        @click="check_all('allon')"><i class="fa fa-check"></i> 全勾選</button>
                                    <button type="button" class="btn btn-primary"
                                        @click="check_all('alloff')"><i class="fa fa-close"></i> 全取消</button>
                                </div>
                            </div>
                            <br>

                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="products_model_list" data-page-length='100'>
                                <thead>
                                    <tr>
                                        <th>項次</th>
                                        <th></th>
                                        <th>商品序號</th>
                                        <th>商品名稱</th>
                                        <th>售價(含稅)</th>
                                        <th>上架日期</th>
                                        <th>上架狀態</th>
                                        <th>毛利(%)</th>
                                        <th>供應商</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, result_products_key) in result_products">
                                        <td>@{{ result_products_key + 1 }}</td>
                                        <td>
                                            <div class="text-center">
                                                <input type="checkbox" class="big-checkbox"
                                                    style="width: 20px;height: 20px;" v-model="product.check_use"
                                                    :true-value="1" :false-value="0">
                                            </div>
                                        </td>
                                        <td>@{{ product.product_no }}</td>
                                        <td>@{{ product.product_name }}</td>
                                        <td>@{{ product.selling_price }}</td>
                                        <td>@{{ product.start_launched_at }} ~ @{{ product.end_launched_at }}</td>
                                        <td>@{{ product.launched_status }}</td>
                                        <td>@{{ product.gross_margin }}</td>
                                        <td>@{{ product.supplier_name }}</td>
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
