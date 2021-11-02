<!-- 報價單明細 -->
<div class="modal fade" id="row_supplier_deliver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 補登預進日</h4>
                <input type='hidden' name="get_modal_id"  id="get_modal_id" value=""/>
            </div>
            <form id="productModal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-2 text-right"><label> 採購單號</label></div>
                            <div class="col-sm-4">@{{order_supplier.number}}</div>
                        </div>
                        <br>
                        <div class="col-sm-12">
                            <div class="col-sm-2 text-right"><label> 廠商交貨日</label></div>
                            <div class="col-sm-4">
                                <div class='input-group date' id='supplier_deliver_date_dp'>
                                    <input type='text' class="form-control" name="supplier_deliver_date" id="supplier_deliver_date" value=""/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-2 text-right"><label> 預計進貨日</label></div>
                            <div class="col-sm-4">
                                <div class='input-group date' id='expect_deliver_date_dp'>
                                    <input type='text' class="form-control" name="expect_deliver_date" id="expect_deliver_date" value=""/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="saveDate();" data-dismiss="modal"><i class="fa fa-fw fa-save"></i> 儲存並關閉</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-fw fa-close"></i> 關閉視窗</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>

    function row_supplier_deliver(id)
    {
        $('#supplier_deliver_date_dp').datetimepicker({
            format:'YYYY-MM-DD',
        });
        $('#expect_deliver_date_dp').datetimepicker({
            format:'YYYY-MM-DD',
        });

        $.ajax(
        {
            url: "/backend/order_supplier/ajax",
            type: "POST",
            data: {"get_type":"order_supplier" , "id": id, _token:'{{ csrf_token() }}' },
            enctype: 'multipart/form-data',
        })
        .done(function( data )
        {
            var data_array = data.split('@@');
            if(data_array[0] == "OK") {
                var obj = jQuery.parseJSON(data_array[1]);
                $('#OPModalNumber2').html(obj.number);
                $('#supplier_deliver_date').val(obj.supplier_deliver_date);
                $('#expect_deliver_date').val(obj.expect_deliver_date);
            }
        })
    }

    function saveDate(){

    }

</script>
