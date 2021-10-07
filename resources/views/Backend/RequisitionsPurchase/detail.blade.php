
<!-- 請購單明細 -->
<div class="modal fade" id="row_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 請購單明細</h4>
                <input type='hidden' name="get_modal_id"  id="get_modal_id" value=""/>
            </div>
            <form id="productModal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 單號</label></div>
                                <div class="col-sm-4" id="RPModalNumber"></div>
                                <div class="col-sm-2"><label> 供應商</label></div>
                                <div class="col-sm-4" id="RPModalSupplierName"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 請購部門</label></div>
                                <div class="col-sm-4" id="RPModalDepartmentName"></div>
                                <div class="col-sm-2"><label> 倉庫</label></div>
                                <div class="col-sm-4" id="RPModalWarehouse"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 原幣稅額</label></div>
                                <div class="col-sm-4" id="RPModalOriginalTax"></div>
                                <div class="col-sm-2"><label> 原幣總金額</label></div>
                                <div class="col-sm-4" id="RPModalOriginalTotalPrice"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 稅額</label></div>
                                <div class="col-sm-4" id="RPModalTax"></div>
                                <div class="col-sm-2"><label> 總金額</label></div>
                                <div class="col-sm-4" id="RPModalTotalPrice"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 備註</label></div>
                                <div class="col-sm-10" id="RPModalRemark"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="DivAddRow">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-fw fa-close"></i> 關閉視窗</button>
                    <button type="button" class="btn btn-danger" id="btn-deactivate"><i class="fa fa-fw fa-ban"></i> 作廢</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
    $(document).ready(function ()
    {
        $('#btn-select').click(function ()
        {
            window.location.href="index.php?func=requisitions_purchase&select_start_date=" + $("#select_start_date").val() + "&select_end_date=" + $("#select_end_date").val() + "&department=" + $("#department").val() + "&active=" + $("#active").val();
            return false;
        });

        $('#btn-deactivate').click(function ()
        {
            var data_id = $("#get_modal_id").val();
            if(confirm("確認要作廢此單?"))
            {
                de_active(data_id , "requisitions_purchase");
            }
        });

    });

    function row_detail(id)
    {
        $('#DivAddRow').html("");
        var data_id = id;

        $("#get_modal_id").val(data_id);

        $.ajax(
            {
                url: "/backend/requisitions_purchase/ajax",
                type: "POST",
                data: {"get_type":"requisitions_purchase" , "id": data_id , _token: '{{csrf_token()}}'},
                enctype: 'multipart/form-data',
            })
            .done(function( data )
            {
                var data_array = data.split('@@');
                if(data_array[0] == "OK")
                {
                    var obj = jQuery.parseJSON(data_array[1]);

                    var ShowActive = "";
                    if(obj.active == "1")
                    {
                        ShowActive = "<span class='btn btn-success btn-block' >正常</span>";
                        $("#btn-deactivate").show();
                    }
                    else
                    {
                        ShowActive = "<span class='btn btn-danger btn-block' >已作廢</span>";
                        $("#btn-deactivate").hide();
                    }
                    var ShowInvoiceType = "";
                    if(obj.invoice_type == "1")
                        ShowInvoiceType = "三聯式收銀機發票";
                    else if(obj.invoice_type == "2")
                        ShowInvoiceType = "二聯式發票";
                    else if(obj.invoice_type == "3")
                        ShowInvoiceType = "三聯式發票";
                    else if(obj.invoice_type == "4")
                        ShowInvoiceType = "三聯式電子計算機發票";
                    else if(obj.invoice_type == "5")
                        ShowInvoiceType = "二聯式收銀機發票";
                    else if(obj.invoice_type == "6")
                        ShowInvoiceType = "二聯式收銀機載有稅額之其他憑證";
                    else if(obj.invoice_type == "7")
                        ShowInvoiceType = "電子計算機發票";
                    else if(obj.invoice_type == "8")
                        ShowInvoiceType = "海關代徵營業稅";
                    else if(obj.invoice_type == "9")
                        ShowInvoiceType = "免用統一發票";
                    else if(obj.invoice_type == "10")
                        ShowInvoiceType = "一般稅額計算之電子發票";

                    $("#RPModalNumber").html(obj.number);
                    $("#RPModalDepartmentName").html(obj.department_name);
                    $("#RPModalSupplierName").html(obj.supplier_name);
                    $("#RPModalWarehouse").html(obj.warehouse_name);
                    $("#RPModalOriginalTax").html(obj.original_total_tax_price);
                    $("#RPModalOriginalTotalPrice").html(obj.original_total_price);
                    $("#RPModalTax").html(obj.total_tax_price);
                    $("#RPModalTotalPrice").html(obj.total_price);
                    $("#RPModalActive").html(ShowActive);
                    $("#RPModalRemark").html(obj.remark);
                }
            });

        $.ajax(
            {
                url: "/backend/requisitions_purchase/ajax",
                type: "POST",
                data: {"get_type":"requisitions_purchase_detail" , "id": data_id , _token: '{{csrf_token()}}' },
                enctype: 'multipart/form-data',
            })
            .done(function( data )
            {
                var data_array = data.split('@@');
                if(data_array[0] == "OK")
                {
                    var html_value = "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
                        "<thead>" +
                        "<tr>" +
                        "<th>編號</th>" +
                        "<th>品牌</th>" +
                        "<th>名稱</th>" +
                        "<th>規格</th>" +
                        "<th>批號</th>" +
                        "<th>數量</th>" +
                        "<th>單位</th>" +
                        "<th>單價</th>" +
                        "<th>匯率</th>" +
                        "<th>小計</th>" +
                        "</tr>" +
                        "</thead>" +
                        "<tbody>";

                    var obj = jQuery.parseJSON(data_array[1]);

                    var sum_subtotal_price = 0;
                    $.each( obj, function( key, value )
                    {
                        html_value +=  "<tr>" +
                            "<td>" + value.item_number + "</td>" +
                            "<td>" + value.item_brand + "</td>" +
                            "<td>" + value.item_name + "</td>" +
                            "<td>" + value.item_spec + "</td>" +
                            "<td>" + value.item_lot_number + "</td>" +
                            "<td>" + value.item_qty + "</td>" +
                            "<td>" + value.item_unit + "</td>" +
                            "<td>" + value.item_price + "</td>" +
                            "<td>" + value.currency_price + "</td>" +
                            "<td>" + value.subtotal_price + "</td>" +
                            "</tr>";
                        sum_subtotal_price = parseFloat(sum_subtotal_price) + parseFloat(value.subtotal_price);
                    });

                    html_value +=  "<tr>" +
                        "<td colspan='7'></td>" +
                        "<td colspan='2'>合計</td>" +
                        "<td colspan='1'>" + Math.round(sum_subtotal_price) + "</td>" +
                        "</tr>";

                    html_value += "</tbody></table>";
                    $(html_value).appendTo($('#DivAddRow'));
                }
            });
    }
    $('#btn-new').click(function() { window.location.href='index.php?func=requisitions_purchase&action=new'; });
    $('#btn-get-new').click(function() { window.location.href='index.php?func=requisitions_purchase&action=getnew'; });
    $('button[id^=btn-edit-]').click(function() { window.location.href='index.php?func=requisitions_purchase&action=edit&id='+$(this).val(); });
</script>

