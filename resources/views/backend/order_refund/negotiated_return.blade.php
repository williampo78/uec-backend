{{--退貨協商回報--}}
<div class="modal fade" id="negotiated_return" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 退貨協商回報</h4>
            </div>
            <div class="modal-body">
                <div class="row form-horizontal">
                    <div class="col-sm-12">
                        <div class="panel panel-default no-border-bottom">
                            <div class="panel-body">
                                <form id="negotiated-return-form" autocomplete="off">
                                    <input type="hidden" id="return_examination_id" name="return_examination_id">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label">退貨申請單號</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static"
                                                       id="negotiated-return-request-no"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label">退貨檢驗單號</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static"
                                                       id="negotiated-return-examination-no"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label">協商結果</label>
                                                    <span class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="nego_result" value="1">允許退貨
                                                    </label>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="nego_result" value="0">不允許退貨
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label">退款金額</label>
                                                    <span class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input id="nego_refund_amount" type="text"
                                                           class="form-control search-limit-group" maxlength="6"
                                                           name="nego_refund_amount" placeholder="※ 填寫500，表示可退款500元">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label">協商內容備註</label>
                                                    <span class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <textarea id="nego_remark" class="form-control" rows="5"
                                                              maxlength="250"
                                                              name="nego_remark" placeholder="最多輸入250個字"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-12 text-right">
                                                    {{--                                                @if ($share_role_auth['auth_query'])--}}
                                                    <button class="btn btn-success" id="negotiated-return-save">
                                                        <i class="fa-solid fa-check"></i> 儲存
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                                        <i class="fa-solid fa-xmark"></i> 放棄
                                                    </button>
                                                    {{--                                                @endif--}}
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
