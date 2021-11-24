<div class="modal fade" id="slot_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">

            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i>廣告版位資料</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>適用頁面</label>
                            <input class="form-control" id="modal_applicable_page" readonly value="">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>代碼</label>
                            <input class="form-control" id="modal_slot_code" readonly value="">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>名稱</label>
                            <input class="form-control" id="modal_slot_desc" readonly value="">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Mobile適用</label>
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="radio" name="modal_is_mobile_applicable" id="modal_is_mobile_applicable_enabled" disabled>
                                    <label>是</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="radio"  name="modal_is_mobile_applicable" id="modal_is_mobile_applicable_disabled" disabled>
                                    <label>否</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Desktop適用</label>
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="radio" name="modal_is_desktop_applicable" id="modal_is_desktop_applicable_enabled" disabled>
                                    <label>是</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="radio" name="modal_is_desktop_applicable" id="modal_is_desktop_applicable_disabled" disabled>
                                    <label>否</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>上架類型</label>
                            <input class="form-control" id="modal_slot_type" readonly value="">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>狀態</label>
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="radio" name="modal_active" id="modal_active_enabled" disabled>
                                    <label>是</label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="radio"  name="modal_active" id="modal_active_disabled" disabled>
                                    <label>否</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <label>備註</label>
                        <textarea class="form-control" rows="3" id="modal_remark" readonly></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-fw fa-close"></i>關閉視窗</button>
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
