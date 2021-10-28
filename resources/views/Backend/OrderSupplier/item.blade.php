{{-- <script>

    function copy_text(row_id)
    {
        console.log(row_id)
        var name = $("#item-" + row_id).val();

        new Clipboard('.copy_btn',
            {
                text: function(trigger)
                {
                    return name;
                }
            });
    }

    function ajaxGetItem(requisitions_purchase_id){
        $('#ItemDiv').html('');

        $(" <div class='add_row'>" +
            "<div class='row'>" +
            "<div class='col-sm-4 text-left'>品項</div>" +
            "<div class='col-sm-1 text-left'>贈品</div>" +
            "<div class='col-sm-1 text-left'>單價</div>" +
            "<div class='col-sm-1 text-left'>請購量</div>" +
            "<div class='col-sm-1 text-left'>採購量</div>" +
            "<div class='col-sm-1 text-left'>單位</div>" +
            "<div class='col-sm-1 text-left'>最小採購量</div>" +
            "<div class='col-sm-1 text-left'>原幣小計</div>" +
            "<div class='col-sm-1 text-left'>功能</div>" +
            "</div>" +
            " </div>").appendTo($('#ItemDiv'));

        var curRow = parseInt($('#rowNo').val());
        var newRow = curRow + 1;
        $.ajax({
            url: "/backend/order_supplier/ajax",
            type: "POST",
            data: {'get_type': "requisitions_purchase_detail" ,'requisitions_purchase_id': requisitions_purchase_id , _token: '{{csrf_token()}}'},
            enctype: 'multipart/form-data',
        })
            .done(function( data ){

                var data_array = data.split('@@');
                if(data_array[0] == "OK")
                {
                    var obj = jQuery.parseJSON(data_array[1]);

                    $.each( obj, function( key, value )
                    {
                        var giveawayChecked = '';
                        if (value.is_gift==1){
                            giveawayChecked = 'checked';
                        }
                        $(" <div class='add_row' id='div-addrow-" + newRow + "'>" +
                            "<input name='order_supplier_detail_id[]' type='hidden' value='"+value.id+"'>" +
                            "<input name='requisitions_purchase_detail_id[]' type='hidden' value='"+value.id+"'>" +
                            "<div class='row'>" +
                            "<div class='col-sm-4' >" +
                            "<div class='input-group'>" +
                            "<input class='form-control' name='item[]' id='" + "item-" + newRow + "' value='"+value.item_name+"' readonly>" +
                            "<span class='input-group-btn'>"+
                            "<button class='btn copy_btn' type='button' onclick=\"copy_text(" + newRow + ")\" ><i class='fa fa-copy'></i></button>" +
                            "</span>" +
                            "</div>" +
                            "</div>" +
                            "<div class='col-sm-1'>" +
                            "<p>" +
                                "<input type='checkbox' class='big-checkbox' "+giveawayChecked+"/>" +
                                // <input type="hidden" name="test_checkbox[]" value="0" />
                                "<input type='hidden' name='is_giveaway[]' value='"+value.is_gift+"'>" +

                            "</p>" +
                            "</div>" +
                            "<input type='hidden' name='item_id[]' value='"+value.item_id+"'>" +
                            "<div class='col-sm-1'>" +
                            "<input class='form-control' id='" + "price-" + newRow + "'  type='number' value='"+value.item_price+"' readonly>" +
                            "</div>" +
                            "<div class='col-sm-1'>" +
                            "<input class='form-control' id='" + "item_qty-" + newRow + "'  type='number' value='"+value.item_qty+"' readonly>" +
                            "</div>" +
                            "<div class='col-sm-1'>" +
                            "<input class='form-control' name='order_supplier_qty[]' id='" + "order_supplier_qty-" + newRow + "'  type='number' value='"+value.order_supplier_qty+"'>" +
                            "</div>" +
                            "<div class='col-sm-1'>" +
                            "<input class='form-control id='" + "item_unit-" + newRow + "' value='"+value.item_unit+"' readonly>" +
                            "</div>" +
                            "<div class='col-sm-1'>" +
                            "<input class='form-control id='" + "price-" + newRow + "' value='"+'最小採購量'+"' readonly>" +
                            "</div>" +
                            "<div class='col-sm-1'>" +
                            "<input class='form-control id='" + "original_subtotal_price-" + newRow + "'  type='number' value='"+value.original_subtotal_price+"' readonly>" +
                            "</div>" +
                            "<div class='col-sm-1'>" +
                            "<button type='button' data-details='"+value.order_supplier_detail_id+"' class='btn btn-danger btn_close' id='btn-delete-" + newRow + "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
                            "</div>" +
                            "</div>" +
                            "</div>"
                        ).appendTo($('#ItemDiv'));

                        $('#rowNo').val(newRow);
                    });

                }

                $('button[id^=btn-delete-]').click(function()
                {
                    var id = this.dataset.details;

                    if(confirm("確定要刪除?")){
                        $.ajax({
                            url: "/backend/order_supplier/ajaxDelItem",
                            type: "POST",
                            data: {'id': id, _token: '{{csrf_token()}}'},
                            enctype: 'multipart/form-data',
                        })

                        $("#div-addrow-" + $(this).val()).remove();
                    }
                    return false;
                });

                $('input[type=checkbox]').on("change",function(){
                    let is_checked = $(this).parent().find('input[type=checkbox]').is(':checked');
                    let target = 0;
                    if(is_checked == true)
                    {
                        target = 1;
                    }
                    else
                    {
                        target = 0;
                    }
                    $(this).parent().find('input[type=hidden]').val(target);
                });
            });
    }
</script> --}}
