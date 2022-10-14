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
                                                    <p class="form-control-static" data-target="request-no"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label">退貨檢驗單號</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static" data-target="examination-no"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <label class="control-label">退貨商品</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th >商品名稱</th>
                                                                <th >規格一</th>
                                                                <th >規格二</th>
                                                                <th >Item編號</th>
                                                                <th >售價</th>
                                                                <th >數量</th>
                                                                <th >活動折抵</th>
                                                                <th >小計</th>
                                                                <th >訂單身份</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody data-target="return_items">
                                                        </tbody>
                                                    </table>
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
                                                    <label class="control-label">協商退款金額</label>
                                                    <span class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control search-limit-group"
                                                           maxlength="6" min="1" max="999999"
                                                           name="nego_refund_amount" placeholder="※ 填寫500，表示可退款500元">
                                                    <span class="text-danger">※ 可退款金額：<span data-target="refundable_amount"></span>元</span>
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
                                                    <textarea class="form-control" rows="5" maxlength="250"
                                                              name="nego_remark" placeholder="最多輸入250個字"></textarea>
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
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
