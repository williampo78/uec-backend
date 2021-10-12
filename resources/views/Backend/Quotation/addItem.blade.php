<script>
    function AddItemRow(get_type, position)
    {
        cc
        var process_plan = $("#process_plan").val();
        var ShowWarehouseTitle = "返回倉庫";
        if(position == "input")
            ShowWarehouseTitle = "來源倉庫";

        if(process_plan == "")
        {
            alert("請先選擇計畫");
            return;
        }

        var curRow = parseInt($('#'+position+'_row_number').val());
        var newRow = curRow + 1;
        if(curRow == 0)
        {
            $(" <div class='add_row'>" +
                "<div class='row'>" +
                "<div class='col-sm-4 text-left'>品項</div>" +
                "<div class='col-sm-1 text-left'>庫存</div>" +
                "<div class='col-sm-1 text-left'>數量</div>" +
                "<div class='col-sm-2 text-left'>批號</div>" +
                "<div class='col-sm-1 text-left'>單位</div>" +
                "<div class='col-sm-2 text-left'>" + ShowWarehouseTitle +" </div>" +
                "<div class='col-sm-1 text-left'>功能</div>" +
                "</div>" +
                " </div>").appendTo($('#'+position+'ItemDiv'));
        }

        $(" <div class='add_row' id='div-addrow-" + position + newRow + "'>" +
            "<div class='row'>" +
            "<input class='form-control' name='" + position + "itemid-" + newRow + "' id='" + position + "itemid-" + newRow + "' type='hidden'>" +
            "<input class='form-control' name='" + position + "itemnumber-" + newRow + "' id='" + position + "itemnumber-" + newRow + "' type='hidden'>" +
            "<input class='form-control' name='" + position + "itembrand-" + newRow + "' id='" + position + "itembrand-" + newRow + "' type='hidden'>" +
            "<input class='form-control' name='" + position + "itemname-" + newRow + "' id='" + position + "itemname-" + newRow + "' type='hidden'>" +
            "<input class='form-control' name='" + position + "itemspec-" + newRow + "' id='" + position + "itemspec-" + newRow + "' type='hidden'>" +
            "<input class='form-control' name='" + position + "itemprice-" + newRow + "' id='" + position + "itemprice-" + newRow + "' type='hidden'>" +
            "<div class='col-sm-4' >" +
            "<div class='input-group'>" +
            "<select class='form-control js-select2-item' name='" + position + "item-" + newRow + "' id='" + position + "item-" + newRow + "' onchange=\"getItemInfo(" + newRow + " , '" + get_type + "', '" + position + "')\" >" +
            "<option value=''></option>" +
            "</select>" +
            "<span class='input-group-btn'>"+
            "<button class='btn copy_btn' type='button' onclick=\"copy_text(" + newRow + ")\" ><i class='fa fa-copy'></i></button>" +
            "</span>" +
            "</div>" +
            "</div>" +
            "<div class='col-sm-1' >" +
            "<input class='form-control' name='" + position + "stockqty-" + newRow + "' id='" + position + "stockqty-" + newRow + "' readonly >" +
            "</div>" +
            "<div class='col-sm-1' >" +
            "<input class='form-control qty' name='" + position + "qty-" + newRow + "' id='" + position + "qty-" + newRow + "'  type='number'>" +
            "</div>" +
            "<div class='col-sm-2' id='" + position + "div_lot_number_" + newRow + "'>" +
            "<input class='form-control' name='" + position + "lotnumber-" + newRow + "' id='" + position + "lotnumber-" + newRow + "'>" +
            "</div>" +
            "<div class='col-sm-1' >" +
            "<input class='form-control' name='" + position + "unit-" + newRow + "' id='" + position + "unit-" + newRow + "' readonly >" +
            "</div>" +
            "<div class='col-sm-2' >" +
            "<select class='form-control js-select2' name='" + position + "warehouse-" + newRow + "' id='" + position + "warehouse-" + newRow + "' >" +
            "</select>" +
            "</div>" +
            "<div class='col-sm-1'>" +
            "<button class='btn btn-danger btn_close' id='btn-delete-" + position + newRow + "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
            "</div>" +
            "</div>" +
            "</div>"
        ).appendTo($('#'+position+'ItemDiv'));

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
                url: "ajax/get_db_info.php",
                type: "POST",
                data: {'get_type': "itemlist"},
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

        // 即時取出倉庫資訊，放到 select 中
        $.ajax(
            {
                url: "ajax/get_db_info.php",
                type: "POST",
                data: {'get_type': "warehouselist"},
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
                        var text_value = value.name;
                        $("#" + position + "warehouse-" + newRow).append($("<option></option>").attr("value", value.id).text(text_value));
                    });


                    $(".js-select2").select2(
                        {
                            allowClear: true ,
                            theme: "bootstrap",
                            placeholder: "請選擇"
                        });
                }
            });

        $('#'+position+'_row_number').val(newRow);
    }
</script>
