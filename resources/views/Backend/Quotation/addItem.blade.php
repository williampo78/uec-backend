<script>
    function AddItemRow(get_type, position)
    {
        var curRow = parseInt($('#rowNo').val());
        var newRow = curRow + 1;
        if(curRow == 0)
        {
            $(" <div class='add_row'>" +
                "<div class='row'>" +
                "<div class='col-sm-6 text-left'>品項</div>" +
                "<div class='col-sm-2 text-left'>單價</div>" +
                "<div class='col-sm-3 text-left'>最小採購量</div>" +
                "<div class='col-sm-1 text-left'>功能</div>" +
                "</div>" +
                " </div>").appendTo($('#ItemDiv'));
        }

        $(" <div class='add_row' id='div-addrow-" + position + newRow + "'>" +
            "<div class='row'>" +
            // "<input class='form-control' name='itemid[]' id='" + position + "itemid-" + newRow + "' type='hidden'>" +
            "<input class='form-control' name='itemname[]' id='" + position + "itemname-" + newRow + "' type='hidden'>" +
            // "<input class='form-control' name='itemprice[]' id='" + position + "itemprice-" + newRow + "' type='hidden'>" +
            "<div class='col-sm-6' >" +
            "<div class='input-group'>" +
            "<select class='form-control js-select2-item' name='item[]' id='" + position + "item-" + newRow + "' onchange=\"getItemInfo(" + newRow + " , '" + get_type + "', '" + position + "')\" >" +
            "<option value=''></option>" +
            "</select>" +
            "<span class='input-group-btn'>"+
            "<button class='btn copy_btn' type='button' onclick=\"copy_text(" + newRow + ")\" ><i class='fa fa-copy'></i></button>" +
            "</span>" +
            "</div>" +
            "</div>" +
            "<div class='col-sm-2' >" +
            "<input class='form-control qty' name='price[]' id='" + position + "price-" + newRow + "'  type='number'>" +
            "</div>" +
            "<div class='col-sm-3' >" +
            "<input class='form-control' name='minimum_purchase_qty[]' id='" + position + "minimum_purchase_qty-" + newRow + "' readonly >" +
            "</div>" +
            "<div class='col-sm-1'>" +
            "<button class='btn btn-danger btn_close' id='btn-delete-" + position + newRow + "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
            "</div>" +
            "</div>" +
            "</div>"
        ).appendTo($('#ItemDiv'));

        $(".js-select2-item").select2(
            {
                allowClear: true ,
                theme: "bootstrap",
                placeholder: "請選擇品項"
            });

        $('button[id^=btn-delete-]').click(function()
        {
            $("#div-addrow-" + position + $(this).val()).remove();
            return false;
        });

        // 即時取出物品資訊，放到 select 中
        $.ajax(
            {
                url: "/backend/quotation/ajax",
                type: "POST",
                data: {'get_type': "itemlist", _token: '{{csrf_token()}}'},
                enctype: 'multipart/form-data',
            })
            .done(function( data )
            {
                var data_array = data.split('@@');
                if(data_array[0] == "OK")
                {
                    var obj = jQuery.parseJSON(data_array[1]);
                    $.each( obj, function( key, value )
                    {
                        var text_value = value.number + "-" + value.brand + "-" + value.name + "-" + value.spec;
                        $("#" + position + "item-" + newRow).append($("<option></option>").attr("value", value.id).text(text_value));
                    });
                }
            });

        $('#rowNo').val(newRow);
    }

    function copy_text(row_id)
    {
        var name = $("#inputitemname-" + row_id).val();

        new Clipboard('.copy_btn',
            {
                text: function(trigger)
                {
                    return name;
                }
            });
    }

    function getItemInfo(row_id , get_type, position)
    {
        var item_id = $("#"+position+"item-" + row_id).val();

        $.ajax(
            {
                url: "/backend/quotation/ajax",
                type: "POST",
                data: {'get_type': "iteminfo" ,'item_id': item_id ,'type': get_type, _token: '{{csrf_token()}}' },
                enctype: 'multipart/form-data',
            })
            .done(function( data )
            {
                var data_array = data.split('@@');
                if(data_array[0] == "OK")
                {
                    var obj = jQuery.parseJSON(data_array[1]);

                    $("#"+position+"itemlable-" + row_id).text("品項 = " + obj.name + " - " + obj.spec);
                    $("#"+position+"itemname-" + row_id).val(obj.name);
                    $("#"+position+"itemid-" + row_id).val(obj.id);
                    // $("#"+position+"buy_price-" + row_id).val(obj.buy_price);
                    $("#"+position+"minimum_purchase_qty-" + row_id).val(obj.minimum_sales_qty);

                }
            });
    }

    function ajaxGetItem(quotation_id){
        var curRow = parseInt($('#rowNo').val());
        var newRow = curRow + 1;
        var position = 'input';
        var get_type = '';
        $.ajax({
            url: "/backend/quotation/ajax",
            type: "POST",
            data: {'get_type': "quotation_detail" ,'id': quotation_id , _token: '{{csrf_token()}}','action':'upd' },
            enctype: 'multipart/form-data',
        })
        .done(function( data ){

            var data_array = data.split('@@');
            if(data_array[0] == "OK")
            {
                var obj = jQuery.parseJSON(data_array[1]);
                var item = jQuery.parseJSON(data_array[2]);


                $.each( obj, function( key, value )
                {
                    var itemOption = "<option value=''></option>";

                    $.each( item, function (itemKey,itemVal){
                        var selected = '';
                        if (itemVal.id == value.item_id){
                            selected = 'selected';
                        }
                        itemOption += "<option value="+itemVal.id+" "+selected+">"+itemVal.number + "-" + itemVal.brand + "-" + itemVal.name + "-" + itemVal.spec+"</option>";
                    });

                    $(" <div class='add_row' id='div-addrow-" + position + newRow + "'>" +
                        "<input name='quotation_details_id[]' type='hidden' value='"+value.quotation_details_id+"'>" +
                        "<input class='form-control' id='" + position + "itemname-" + newRow + "' type='hidden'>" +
                        "<div class='row'>" +
                        "<div class='col-sm-6' >" +
                        "<div class='input-group'>" +
                        "<select class='form-control js-select2-item' name='item[]' id='" + position + "item-" + newRow + "' onchange=\"getItemInfo(" + newRow + " , '" + get_type + "', '" + position + "')\" >" +
                            itemOption +
                        "</select>" +
                        "<span class='input-group-btn'>"+
                        "<button class='btn copy_btn' type='button' onclick=\"copy_text(" + newRow + ")\" ><i class='fa fa-copy'></i></button>" +
                        "</span>" +
                        "</div>" +
                        "</div>" +
                        "<div class='col-sm-2' >" +
                        "<input class='form-control qty' name='price[]' id='" + position + "price-" + newRow + "'  type='number' value='"+value.original_unit_price+"'>" +
                        "</div>" +
                        "<div class='col-sm-3' >" +
                        "<input class='form-control' name='minimum_purchase_qty[]' id='" + position + "minimum_purchase_qty-" + newRow + "' readonly >" +
                        "</div>" +
                        "<div class='col-sm-1'>" +
                        "<button type='button' data-details='"+value.quotation_details_id+"' class='btn btn-danger btn_close' id='btn-delete-" + position + newRow + "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
                        "</div>" +
                        "</div>" +
                        "</div>"
                    ).appendTo($('#ItemDiv'));

                    $('#rowNo').val(newRow);
                });

            }

            $(".js-select2-item").select2(
                {
                    allowClear: true ,
                    theme: "bootstrap",
                    placeholder: "請選擇品項"
                });

            $('button[id^=btn-delete-]').click(function()
            {
                var id = this.dataset.details;

                if(confirm("確定要刪除?")){
                    $.ajax({
                        url: "/backend/quotation/ajaxDelItem",
                        type: "POST",
                        data: {'id': id, _token: '{{csrf_token()}}'},
                        enctype: 'multipart/form-data',
                    })

                    $("#div-addrow-" + position + $(this).val()).remove();
                }
                return false;
            });
        });
    }
</script>
