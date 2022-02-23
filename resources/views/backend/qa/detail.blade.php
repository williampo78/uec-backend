<div class="modal fade" id="qa_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 檢視資料</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>類別</label>
                            <p class="form-control-static" id="modal-description"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>排序</label>
                            <p class="form-control-static" id="modal-sort"></p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>狀態</label>
                            <p class="form-control-static" id="modal-active"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>問題描述</label>
                            <p class="form-control-static" id="modal-content-name"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>問題解答</label>
                            <p class="form-control-static" id="modal-content-text"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa-solid fa-xmark"></i> 關閉視窗</button>
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
