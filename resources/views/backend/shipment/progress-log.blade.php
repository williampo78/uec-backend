<div class="modal fade" id="progress_log_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 出貨配送歷程</h4>
            </div>

            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">出貨單號</label>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="modal-log-shipment-no"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class='table table-striped table-bordered table-hover' style='width:100%'
                        id="modal-log-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap">操作時間</th>
                                <th class="text-nowrap">配送狀態</th>
                                <th class="text-nowrap">配送訊息</th>
                                <th class="text-nowrap">約定配送日</th>
                                <th class="text-nowrap">操作人員</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> 關閉視窗
                </button>
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
