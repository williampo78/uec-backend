{{--退貨協商回報--}}
<div class="modal fade" id="manual-refund-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 人工退款回報</h4>
            </div>
            <div class="modal-body">
                <div class="row form-horizontal">
                    <div class="col-sm-12">
                        <div class="panel panel-default no-border-bottom">
                            <div class="panel-body">
                                <form id="manual-refund-form" autocomplete="off">
                                    <input type="hidden" name="return_request_id">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label">退貨申請單號</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static" data-target="request_no"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label"> 實際退款日期 </label><span
                                                        class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="input-group" id="refund_at_flatpickr">
                                                        <input type="text" class="form-control" name="refund_at" data-input>
                                                        <span class="input-group-btn" data-toggle>
                                                            <button class="btn btn-default" type="button">
                                                                <i class="fa-solid fa-calendar-days"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label">退款備註</label>
                                                    <span class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" rows="5" maxlength="250"
                                                              name="manually_refund_remark" placeholder="最多輸入250個字"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-12 text-right">
                                                    <button class="btn btn-success">
                                                        <i class="fa-solid fa-check"></i> 儲存
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                                        <i class="fa-solid fa-xmark"></i> 放棄
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <hr style="border-top: 1px solid gray;"/>
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
