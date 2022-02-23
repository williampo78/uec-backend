<div id="product-block" style="display: none;">
    <div class="row">
        <div class="col-sm-6">
            <label class="radio-inline">
                <input type="radio" name="product_assigned_type" id="product_assigned_type_product" checked
                    value="P" />指定商品
            </label>
            <label class="radio-inline">
                <input type="radio" name="product_assigned_type" id="product_assigned_type_category" value="C" />指定分類
            </label>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-sm-12">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="product-block-tab">
                <li class="active">
                    <a href="#tab-product" data-toggle="tab">商品</a>
                </li>
                <li>
                    <a href="#tab-category" data-toggle="tab">分類</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab-product">
                    <div class="table-responsive">
                        <table class='table table-striped table-bordered table-hover' style='width:100%'>
                            <thead>
                                <tr>
                                    <th class="text-nowrap">排序 <span style="color:red;">*</span></th>
                                    <th class="text-nowrap">商品 <span style="color:red;">*</span></th>
                                    <th class="text-nowrap">功能</th>
                                </tr>
                            </thead>
                            <tbody>
                                <input type="hidden" id="product-block-product-row-no" value="0" />
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-warning" id="btn-new-product-product">
                        <i class="fa-solid fa-plus"></i> 新增商品
                    </button>
                </div>
                <div class="tab-pane fade" id="tab-category">
                    <div class="table-responsive">
                        <table class='table table-striped table-bordered table-hover' style='width:100%'>
                            <thead>
                                <tr>
                                    <th class="text-nowrap">排序 <span style="color:red;">*</span></th>
                                    <th class="text-nowrap">分類 <span style="color:red;">*</span></th>
                                    <th class="text-nowrap">功能</th>
                                </tr>
                            </thead>
                            <tbody>
                                <input type="hidden" id="product-block-category-row-no" value="0" />
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-warning" id="btn-new-product-category">
                        <i class="fa-solid fa-plus"></i> 新增分類
                    </button>
                </div>
            </div>
        </div>
    </div>
    <hr style="border-top: 1px solid gray;" />
</div>
