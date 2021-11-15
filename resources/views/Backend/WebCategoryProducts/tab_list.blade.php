<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#content_type_p">商品</a></li>
    {{-- <li><a data-toggle="tab" href="#content_type_m">賣場</a></li> --}}
</ul>
<div class="tab-content">
    <div id="content_type_p" class="tab-pane fade in active ">
        <div class="panel-body col-sm-12">
            
            <div class="row">
                <div class="col-sm-2">
                    <button type="button" class="btn btn-block btn-warning btn-sm" data-toggle="modal"
                    data-target="#row_detail" ><i class="fa fa-plus"></i> 新增商品</button>
                </div>
            </div>
            <hr>
            <table class="table table-striped table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>項次</th>
                        <th>商品序號</th>
                        <th>商品名稱</th>
                        <th>售價(含稅)</th>
                        <th>上架日期</th>
                        <th>上架狀態</th>
                        <th>毛利</th>
                        <th>功能</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                    v-for="(category_product, category_products_key) in category_products " >
                        <form id="del" method="post">
                            @method('DELETE') 
                            @csrf
                        </form>
                        <td>@{{category_products_key}}</td>
                        <td>@{{category_product.id}}</td>
                        <td>@{{category_product.product_no}}</td>
                        <td>@{{category_product.product_name}}</td>
                        <td>@{{category_product.start_launched_at}} ~ @{{category_product.end_launched_at}}</td>
                        <td>@{{category_product.launched_status}}</td>
                        <td>@{{category_product.gross_margin}}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" type="button">刪除</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @include('Backend.WebCategoryProducts.detail')
    {{-- <div id="content_type_m" class="tab-pane fade in">
        <div style="height: 500px">
            賣場內容
        </div>
    </div> --}}
</div>
<input type="hidden" id="status" v-model="status" name="status">
