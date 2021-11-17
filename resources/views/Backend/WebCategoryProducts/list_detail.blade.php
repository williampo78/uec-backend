<div class="modal fade" id="row_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
aria-hidden="true">

<div class="modal-dialog">
    <div class="modal-content modal-primary panel-primary">
        <div class="modal-header panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 報價單</h4>
            <input type='hidden' name="get_modal_id" id="get_modal_id" value="" />
        </div>
        <div id="productModal">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row form-group">
                            <div class="col-sm-2"><label> 分類</label></div>
                            <div class="col-sm-4">@{{category_hierarchy_content.name}}</div>
                            <div class="col-sm-2"><label> 狀態</label></div>
                            <div class="col-sm-4">@{{category_hierarchy_content.active}}</div>
      
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-2"><label> 網頁標題</label></div>
                            <div class="col-sm-4">@{{category_hierarchy_content.meta_title}}</div>
                            <div class="col-sm-2"><label> 網頁描述</label></div>
                            <div class="col-sm-4">@{{category_hierarchy_content.meta_description}}</div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-2"><label> 網頁關鍵字</label></div>
                            <div class="col-sm-4">@{{category_hierarchy_content.meta_keywords}}</div>
                            <div class="col-sm-2"><label> 內容類型</label></div>
                            <div class="col-sm-4">@{{category_hierarchy_content.content_type}}</div>
                        </div>
                    </div>
                </div>
                <hr>
                <div id="DivAddRow">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#content_type_p">商品</a></li>
                    </ul>
                    <div class="col-ms-12">
                        <table class="table table-striped table-bordered table-hover" style="width:100%"
                            id="table_list2">
                            <thead>
                                <tr>
                                    <th>項次</th>
                                    <th>商品序號</th>
                                    <th>商品名稱</th>
                                    <th>售價(含稅)</th>
                                    <th>上架日期</th>
                                    <th>上架狀態</th>
                                    <th>毛利</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(category_products_list, category_products_list_key) in category_products_list ">
                                    <td>@{{category_products_list_key +1}}</td>
                                    <td>@{{category_products_list.product_no}}</td>
                                    <td>@{{category_products_list.product_name}}</td>
                                    <td>@{{category_products_list.selling_price}}</td>
                                    <td>@{{category_products_list.start_launched_at}} ~ @{{category_products_list.end_launched_at}}</td>
                                    <td>@{{category_products_list.launched_status}}</td>
                                    <td>@{{category_products_list.gross_margin}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal"><i
                        class="fa fa-fw fa-close"></i> 關閉視窗</button>
            </div>
        </div>
    </div>
    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>