<div class="modal fade" id="update_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 補登發票</h4>
                <input type='hidden' id="purchase_id" value="" />
            </div>
            <form id="update_invoice_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3"><label> 進貨單號</label></div>
                                <div class="col-sm-9" id="show_number"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3"><label> 採購單號</label></div>
                                <div class="col-sm-9" id="show_order_supplier_number"></div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">

                                <div class="col-sm-3"><label class="control-label"> 發票號碼</label> <span class="text-red">*</span></div>
                                <div class="col-sm-9">
                                        <input type='text' class="form-control" name="invoice_number" id="invoice_number" onkeyup="value=value.replace(/[^\w=@#]|_/ig,'')" value="" />

                                </div>

                            </div>
                        </div>

                        <div class="col-sm-6">

                            <div class="form-group">

                                <div class="col-sm-3">
                                    <label class="control-label"> 發票日期 </label><span class="text-red">*</span>
                                </div>
                                <div class="col-sm-9">
                                    <div class="input-group" id="invoice_date_flatpickr">
                                        <input type="text" class="form-control" name="invoice_date" id="invoice_date" value="" autocomplete="off" data-input />
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
                        <i class="fa-solid fa-floppy-disk"></i> 更新
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
