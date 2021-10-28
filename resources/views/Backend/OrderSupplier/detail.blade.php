<!-- 報價單明細 -->
<div class="modal fade" id="row_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 採購單明細</h4>
                <input type='hidden' name="get_modal_id"  id="get_modal_id" value=""/>
            </div>
            <form id="productModal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row form-group">
                                <div class="col-sm-2 text-right"><label> 採購單號</label></div>
                                <div class="col-sm-4" id="RPModalNumber"></div>
                                <div class="col-sm-2 text-right"><label> 供應商</label></div>
                                <div class="col-sm-4" id="RPModalSupplierName"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2 text-right"><label> 幣別</label></div>
                                <div class="col-sm-4" id="RPModalCurrencyCode"></div>
                                <div class="col-sm-2 text-right"><label> 狀態</label></div>
                                <div class="col-sm-4" id="RPModalStatus"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2 text-right"><label> 原幣稅額</label></div>
                                <div class="col-sm-4" id="RPModalOriginalTotalTaxPrice"></div>
                                <div class="col-sm-2 text-right"><label> 原幣總金額</label></div>
                                <div class="col-sm-4" id="RPModalOriginalTotalPrice"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2 text-right"><label> 稅額</label></div>
                                <div class="col-sm-4" id="RPModalTax"></div>
                                <div class="col-sm-2 text-right"><label> 總金額</label></div>
                                <div class="col-sm-4" id="RPModalTotalPrice"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2 text-right"><label> 採購單號</label></div>
                                <div class="col-sm-4" id="RPModalNumber2"></div>
                                <div class="col-sm-2 text-right"><label> 發票抬頭</label></div>
                                <div class="col-sm-4" id="RPModalInvoiceName"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2 text-right"><label> 發票統編</label></div>
                                <div class="col-sm-4" id="RPModalInvoiceCompanyNumber"></div>
                                <div class="col-sm-2 text-right"><label> 發票地址</label></div>
                                <div class="col-sm-4" id="RPModalReceiverAddress"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2 text-right"><label> 備註</label></div>
                                <div class="col-sm-4" id="RPModalRemark"></div>
                                <div class="col-sm-2 text-right"><label> 倉庫</label></div>
                                <div class="col-sm-4" id="RPModalWarehouse"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2 text-right"><label> 廠商交貨日</label></div>
                                <div class="col-sm-4" id="RPModalSupplierDeliverDate"></div>
                                <div class="col-sm-2 text-right"><label> 預計進貨日</label></div>
                                <div class="col-sm-4" id="RPModalExpectDeliverDate"></div>
                            </div>

                        </div>
                    </div>
                    <div id="DivAddRow">
                    </div>
                </div>
                <div class="modal-footer">
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
    function row_detail(id)
    {
        $('#DivAddRow').html("");
        var data_id = id;

        $("#get_modal_id").val(data_id);

        $.ajax(
            {
                url: "/backend/order_supplier/ajax",
                type: "POST",
                data: {"get_type":"order_supplier" , "id": data_id, _token:'{{ csrf_token() }}' },
                enctype: 'multipart/form-data',
            })
            .done(function( data )
            {
                var data_array = data.split('@@');
                if(data_array[0] == "OK")
                {
                    var obj = jQuery.parseJSON(data_array[1]);

                    $("#RPModalNumber").html(obj.number);
                    $("#RPModalSupplierName").html(obj.supplier_name);
                    $("#RPModalCurrencyCode").html(obj.currency_code);
                    $("#RPModalStatus").html(obj.status);
                    $("#RPModalOriginalTotalTaxPrice").html(obj.original_total_tax_price);
                    $("#RPModalOriginalTotalPrice").html(obj.original_total_price);
                    $("#RPModalTax").html(obj.tax);
                    $("#RPModalTotalPrice").html(obj.total_price);
                    $("#RPModalNumber2").html(obj.number);
                    $("#RPModalInvoiceName").html(obj.invoice_name);
                    $("#RPModalInvoiceCompanyNumber").html(obj.invoice_company_number);
                    $("#RPModalReceiverAddress").html(obj.receiver_address);
                    $("#RPModalRemark").html(obj.remark);
                    $("#RPModalWarehouse").html(obj.warehouse_name);
                    $("#RPModalSupplierDeliverDate").html(obj.supplier_deliver_date);
                    $("#RPModalExpectDeliverDate").html(obj.expect_deliver_date);

                }
            });

        $.ajax(
            {
                url: "/backend/order_supplier/ajax",
                type: "POST",
                data: {"get_type":"order_supplier_detail" , "id": data_id, _token:'{{ csrf_token() }}' },
                enctype: 'multipart/form-data',
            })
            .done(function( data )
            {
                var data_array = data.split('@@');
                if(data_array[0] == "OK")
                {
                    var obj = jQuery.parseJSON(data_array[1]);

                    var html_value = "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
                        "<thead>" +
                        "<tr>" +
                        "<th>商品編號</th>" +
                        "<th>商品名稱</th>" +
                        "<th>單價</th>" +
                        "<th>採購量</th>" +
                        "<th>單位</th>" +
                        "<th>小計</th>" +
                        "<th>贈品</th>" +
                        "<th>最小採購量</th>" +
                        "<th>進貨量</th>" +
                        "</tr>" +
                        "</thead>" +
                        "<tbody>";

                    $.each( obj, function( key, value )
                    {
                        var is_giveaway = '否';
                        if (value.is_giveaway == 1){
                            is_giveaway = '是';
                        }
                        html_value +=  "<tr>" +
                            "<td>" + value.item_number + "商品編號</td>" +
                            "<td>" + value.item_name + "商品名稱</td>" +
                            "<td>" + value.item_price + "單價</td>" +
                            "<td>" + value.item_qty + "採購量</td>" +
                            "<td>" + value.item_unit + "單位</td>" +
                            "<td>" + value.subtotal_price + "小計</td>" +
                            "<td>" + is_giveaway + "贈品</td>" +
                            "<td>" + '最小採購量' + "最小採購量</td>" +
                            "<td>" + value.purchase_qty + "進貨量</td>" +
                            "</tr>";
                    });

                    html_value += "</tbody></table>";
                    $(html_value).appendTo($('#DivAddRow'));
                }
            });
    }
</script>
