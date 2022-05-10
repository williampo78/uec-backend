<div class="modal fade" id="promotion_campaign_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="width:100%;">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"> 挑選賣場</h4>
                <input type='hidden' name="get_modal_id" id="get_modal_id" value="" />
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <!-- 功能按鈕(新增) -->
                        <div class="panel-heading">
                            <input type="hidden" id="now_row_num">
                            <div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <h5>資料範圍</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-department"
                                                name="promotional_campaigns_time_type"
                                                id="promotional_campaigns_time_type">
                                                <option value="not_expired" selected>尚未過期的活動</option>
                                                <option value="all">所有活動</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <h5>活動名稱/文案</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <input placeholder="模糊查詢" class="form-control"
                                                name="promotional_campaigns_key_word"
                                                id="promotional_campaigns_key_word">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-6">
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                        </div>
                                        <div class="col-sm-4">
                                        </div>
                                        <div class="col-sm-5 text-right">
                                            <button type="button" class="btn btn-warning search_btn" data-type="promotion_campaign">
                                                <i class="fa-solid fa-magnifying-glass"></i> 查詢
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
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                            class="fa-solid fa-ban"></i> 取消</button>
                                </div>
                            </div>
                            <br>
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                            id="products_model_list" data-page-length='100'>
                            <thead id="promotion_campaign_model_list">
                                <tr>
                                    <th>功能</th>
                                    <th>活動文案</th>
                                    <th>上架時間</th>
                                    <th>活動ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2</td>
                                    <td>3</td>
                                    <td>4</td>
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
