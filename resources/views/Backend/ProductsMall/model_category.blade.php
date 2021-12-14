<div class="modal fade" id="model_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="width:100%;">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"> 新增分類</h4>
                <input type='hidden' name="get_modal_id" id="get_modal_id" value="" />
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-2">
                                    <h5>分類名稱</h5>
                                </div>
                                <div class="col-sm-4">
                                    <input placeholder="分類名稱" class="form-control" v-model="SelectCategoryName">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" style="width:100%"
                            id="products_model_list" data-page-length='100'>
                            <thead>
                                <tr>
                                    {{-- <th>項次</th> --}}
                                    <th>ID</th>
                                    <th>分類名稱</th>
                                    <th>功能</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(CategoryList, key) in CategoryList">
                                    <td>@{{ CategoryList . id }}</td>
                                    <td>@{{ CategoryList . name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success"
                                            @click="addContentToProductsCategory(CategoryList.id  , key)">加入
                                        </button>
                                    </td>
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
