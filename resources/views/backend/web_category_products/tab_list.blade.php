<ul class="nav nav-tabs">
    <li v-bind:class="category_hierarchy_content.content_type == 'P' ?'active':''"><a data-toggle="tab"
            href="#content_type_p">商品</a></li>
    <li v-bind:class="category_hierarchy_content.content_type == 'M' ?'active':''"><a data-toggle="tab"
            href="#content_type_m">賣場</a></li>
</ul>
<div class="tab-content">
    <div id="content_type_p" class="tab-pane fade in "
        v-bind:class="category_hierarchy_content.content_type == 'P' ?'active':''">
        <div class="panel-body col-sm-12">

            <div class="row">
                <div class="col-sm-2">
                    <button type="button" class="btn btn-block btn-warning btn-sm" data-toggle="modal"
                        data-target="#row_detail"><i class="fa-solid fa-plus"></i> 新增商品</button>
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
                        <th>毛利(%)</th>
                        <th>功能</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(category_products_list, category_products_list_key) in category_products_list ">
                        <td>@{{ category_products_list_key + 1 }}</td>
                        <td>@{{ category_products_list.product_no }}</td>
                        <td>@{{ category_products_list.product_name }}</td>
                        <td>@{{ category_products_list.selling_price }}</td>
                        <td>@{{ category_products_list.start_launched_at }} ~ @{{ category_products_list.end_launched_at }}</td>
                        <td>@{{ category_products_list.launched_status_desc }}</td>
                        <td>@{{ category_products_list.gross_margin }}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" type="button"
                                @click="del_category_products_list(category_products_list_key)"><i
                                    class="fa-solid fa-trash-can"></i> 刪除</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="content_type_m" class="tab-pane fade in"
        v-bind:class="category_hierarchy_content.content_type == 'M' ?'active':''">
        <div class="panel-body">
            <form class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-1">行銷活動<span class="text-red">*</span></label>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-block btn-warning btn-sm" data-toggle="modal"
                            data-target="#promotion_campaign_model">挑選賣場</button>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" id="campaign_brief" name="campaign_brief" v-model="category_hierarchy_content.campaign_brief" class="form-control" readonly>
                        <input type="hidden" name="promotion_campaign_id" v-model="category_hierarchy_content.promotion_campaign_id" class="form-control" readonly>
                    </div>
                    <div class="col-sm-1" v-show="category_hierarchy_content.promotion_campaign_id !== null">
                        <button type="button" class="btn btn-block btn-danger btn-sm" @click="del_promotion_campaign_id">刪除</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
