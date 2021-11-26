<div class="modal fade" id="slot_content_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i>廣告上架資料</h4>
            </div>

            <div class="modal-body">
                <div id="slot-block">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>版位</label>
                                <p id="modal-slot"></p>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>上架時間</label>
                                <p id="modal-start-at-end-at"></p>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>狀態</label>
                                <p id="modal-active"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>版位主色</label>
                                <p id="modal-slot-color-code"></p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>版位icon</label>
                                <div id="modal-slot-icon-name"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>版位標題</label>
                                <p id="modal-slot-title"></p>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid gray;" />
                </div>

                <div id="image-block" style="display: none;">
                    <div class="table-responsive">
                        <table class='table table-striped table-bordered table-hover' style='width:100%'>
                            <thead>
                                <tr>
                                    <th class="text-nowrap">排序</th>
                                    <th class="text-nowrap">圖片</th>
                                    <th class="text-nowrap">alt</th>
                                    <th class="text-nowrap">標題</th>
                                    <th class="text-nowrap">摘要</th>
                                    <th class="text-nowrap">連結內容</th>
                                    <th class="text-nowrap">另開視窗</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <hr style="border-top: 1px solid gray;" />
                </div>

                <div id="text-block" style="display: none;">
                    <div class="table-responsive">
                        <table class='table table-striped table-bordered table-hover' style='width:100%'>
                            <thead>
                                <tr>
                                    <th class="text-nowrap">排序</th>
                                    <th class="text-nowrap">文字</th>
                                    <th class="text-nowrap">連結內容</th>
                                    <th class="text-nowrap">另開視窗</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <hr style="border-top: 1px solid gray;" />
                </div>

                <div id="product-block" style="display: none;">
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
                                                    <th class="text-nowrap">排序</th>
                                                    <th class="text-nowrap">商品</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-category">
                                    <div class="table-responsive">
                                        <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap">排序</th>
                                                    <th class="text-nowrap">分類</th>
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
