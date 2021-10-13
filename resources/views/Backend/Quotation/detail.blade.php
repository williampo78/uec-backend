<!-- 報價單明細 -->
<div class="modal fade" id="row_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 報價單</h4>
                <input type='hidden' name="get_modal_id"  id="get_modal_id" value=""/>
            </div>
            <form id="productModal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row form-group">
                                <div class="col-sm-1"><label> 報價單號</label></div>
                                <div class="col-sm-3" id="RPModalDocNumber"></div>
                                <div class="col-sm-1"><label> 供應商</label></div>
                                <div class="col-sm-3" id="RPModalSupplierName"></div>
                                <div class="col-sm-1"><label> 狀態</label></div>
                                <div class="col-sm-3" id="RPModalStatus"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-1"><label> 幣別</label></div>
                                <div class="col-sm-3" id="RPModalCurrencyCode"></div>
                                <div class="col-sm-1"><label> 匯率</label></div>
                                <div class="col-sm-3" id="RPModalExchangeRate"></div>
                                <div class="col-sm-1"><label> 稅別</label></div>
                                <div class="col-sm-3" id="RPModalTax"></div>
                            </div>

                            <div class="row form-group">
                                <div class="col-sm-1"><label> 備註</label></div>
                                <div class="col-sm-10" id="RPModalRemark"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="DivAddRow">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-fw fa-close"></i> 關閉視窗</button>
{{--                    <button type="button" class="btn btn-danger" id="btn-deactivate"><i class="fa fa-fw fa-ban"></i> 作廢</button>--}}
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
                url: "/backend/quotation/ajax",
                type: "POST",
                data: {"get_type":"quotation" , "id": data_id, _token:'{{ csrf_token() }}' },
                enctype: 'multipart/form-data',
            })
            .done(function( data )
            {
                var data_array = data.split('@@');
                if(data_array[0] == "OK")
                {
                    var obj = jQuery.parseJSON(data_array[1]);

                    $("#RPModalDocNumber").html(obj.doc_number);
                    $("#RPModalSupplierName").html(obj.supplier_name);
                    $("#RPModalStatus").html(obj.status_code);
                    $("#RPModalCurrencyCode").html(obj.currency_code);
                    $("#RPModalExchangeRate").html(obj.exchange_rate);
                    $("#RPModalTax").html(obj.tax);
                    $("#RPModalRemark").html(obj.remark);
                }
            });

        $.ajax(
            {
                url: "/backend/quotation/ajax",
                type: "POST",
                data: {"get_type":"quotation_detail" , "id": data_id, _token:'{{ csrf_token() }}' },
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
                        "<th>商品編號</th>" +
                        "<th>商品名稱</th>" +
                        "<th>國際條碼</th>" +
                        "<th>單價</th>" +
                        "<th>最小採購量</th>" +
                        "</tr>" +
                        "</thead>" +
                        "<tbody>";

                    var obj = jQuery.parseJSON(data_array[1]);

                    $.each( obj, function( key, value )
                    {
                        html_value +=  "<tr>" +
                            "<td>" + value.item_number + "</td>" +
                            "<td>" + value.item_name + "</td>" +
                            "<td>" + '國際條碼' + "</td>" +
                            "<td>" + value.original_unit_price + "</td>" +
                            "<td>" + '最小' + "</td>" +
                            "</tr>";
                    });

                    html_value += "</tbody></table>";
                    $(html_value).appendTo($('#DivAddRow'));
                }
            });

        $.ajax(
            {
                url: "/backend/quotation/ajax",
                type: "POST",
                data: {"get_type":"quotation_view_log" , "id": data_id, _token:'{{ csrf_token() }}' },
                enctype: 'multipart/form-data',
            })
            .done(function( data )
            {
                var data_array = data.split('@@');
                if(data_array[0] == "OK")
                {
                    var html_value =
                        "<label>簽核紀錄</label>" +
                        "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
                        "<thead>" +
                        "<tr>" +
                        "<th>次序</th>" +
                        "<th>簽核人員</th>" +
                        "<th>簽核時間</th>" +
                        "<th>簽核結果</th>" +
                        "<th>簽核備註</th>" +
                        "</tr>" +
                        "</thead>" +
                        "<tbody>";

                    var obj = jQuery.parseJSON(data_array[1]);

                    $.each( obj, function( key, value )
                    {
                        html_value +=  "<tr>" +
                            "<td>" + value.item_number + "</td>" +
                            "<td>" + value.item_name + "</td>" +
                            "<td>" + '國際條碼' + "</td>" +
                            "<td>" + value.original_unit_price + "</td>" +
                            "<td>" + '最小' + "</td>" +
                            "</tr>";
                    });

                    html_value += "</tbody></table>";
                    $(html_value).appendTo($('#DivAddRow'));
                }
            });
    }
</script>
