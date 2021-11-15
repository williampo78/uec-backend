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
                            <form role="form" id="select-form" method="GET" action="" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>供應商</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-department" name="supplier"
                                                id="supplier">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>商品序號</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <input placeholder="模糊查詢" class="form-control" name="company_number"
                                                id="company_number" value="">
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
                                            <input placeholder="模糊查詢，至少輸入4碼" class="form-control"
                                                name="company_number" id="company_number" value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <h5>售價</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-control" name="company_number" id="company_number"
                                                value="">
                                        </div>
                                        <div class="col-sm-1" style="">
                                            <h5>~</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-control" name="company_number" id="company_number"
                                                value="">
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
                                                <div class='input-group date' id='select_start_date'>
                                                    <input type='text' class="form-control" name="select_start_date"
                                                        id="select_start_date" value="" />
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
                                            <div class='input-group date' id='select_end_date'>
                                                <input type='text' class="form-control" name="select_end_date"
                                                    id="select_end_date" value="" />
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
                                                <div class='input-group date' id='select_start_date'>
                                                    <input type='text' class="form-control" name="select_start_date"
                                                        id="select_start_date" value="" />
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
                                            <div class='input-group date' id='select_end_date'>
                                                <input type='text' class="form-control" name="select_end_date"
                                                    id="select_end_date" value="" />
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
                                            <h5>筆數限制</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-control" name="company_number"
                                                id="company_number" type="number" value="100"  max="100" min="0" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-6"></div>
                                </div>
                            </form>
                        </div>

                        <!-- Table list -->
                        <div class="panel-body">
                            <div class="row">
                                    <button type="button" class="btn btn-warning" data-dismiss="modal">全勾選</button>
                                    <button type="button" class="btn btn-warning" data-dismiss="modal">全取消</button>
                                    <button type="button" class="btn btn-warning" data-dismiss="modal">儲存</button>
                                    <button type="button" class="btn btn-warning" data-dismiss="modal">儲存並關閉</button>
                                    <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-fw fa-close"></i>關閉</button>
                            </div>
                            <hr>
                            <table class="table table-striped table-bordered table-hover" style="width:100%" id="products_model_list" data-page-length='100'>
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
                                    @for ($i = 0; $i < 50 ; $i++)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @endfor
                                  
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

<script>

</script>
