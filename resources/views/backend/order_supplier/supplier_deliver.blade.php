<!-- 報價單明細 -->
<div class="modal fade" id="row_supplier_deliver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 補登預進日</h4>
                <input type='hidden' id="get_order_supplier_id" value="" />
            </div>
            <form id="supplier_deliver_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3"><label> 採購單號</label></div>
                                <div class="col-sm-9 show_number"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3"><label class="control-label"> 採購日期</label></div>
                                <div class="col-sm-9">
                                    <p class="form-control-static" id="trade_date"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3"><label class="control-label"> 廠商交貨日</label></div>
                                <div class="col-sm-9">
                                    <div class="input-group" id="supplier_deliver_date_flatpickr">
                                        <input type="text" class="form-control" name="supplier_deliver_date" id="supplier_deliver_date" value="" autocomplete="off" data-input />
                                        <span class="input-group-btn" data-toggle>
                                            <button class="btn btn-default" type="button">
                                                <i class="fa-solid fa-calendar-days"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">

                            <div class="form-group">

                                <div class="col-sm-3">
                                    <label class="control-label"> 預計進貨日</label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="input-group" id="expect_deliver_date_flatpickr">
                                        <input type="text" class="form-control" name="expect_deliver_date" id="expect_deliver_date" value="" autocomplete="off" data-input />
                                        <span class="input-group-btn" data-toggle>
                                            <button class="btn btn-default" type="button">
                                                <i class="fa-solid fa-calendar-days"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk"></i> 儲存並關閉
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> 關閉視窗
                    </button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
