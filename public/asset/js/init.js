
  // var get_url = location.search.substring(1).replace(/\%/g, "Percent");
  // var json_url = JSON.parse('{"' + decodeURI(get_url.replace(/&/g, "\",\"").replace(/=/g,"\":\"")) + '"}');

  // var use_buy_price = ["order_client", "sales", "shipping", "return_purchase"];
  // var use_sell_price = ["requisitions_purchase", "order_supplier", "purchase", "purchase_acceptance", "return_sales"];

  // var use_lotnumber_input = ["return_purchase", "return_sales", "order_supplier", "purchase", "purchase_acceptance"];
  // var use_lotnumber_select = ["order_client", "shipping", "sales", "adjust", "transfer", "requisition"];

  // $(document).ready(function()
  // {
  //   $('#currency').change(function ()
  //   {
  //     changeCurrency($("#currency").val(), $("#trade_date").val());
  //   });

  //   $('.table_list').DataTable({
  //     "bSort": true,
  //     aaSorting: [] ,
  //     "pageLength": 50,
  //     dom: 'Bfrtip',
  //     lengthMenu:
  //     [
  //         [ 25, 50, 100, -1 ],
  //         [ '25 筆', '50 筆', '100 筆', '全部顯示' ]
  //     ],
  //     buttons:
  //     [
  //       {
  //         'extend': 'pageLength',
  //         'text': '顯示筆數'
  //       },
  //       {
  //         'extend': 'excel',
  //         'text': '<i class="fa fa-fw fa-file-excel-o"></i> 匯出Excel'
  //       },
  //       {
  //         'extend': 'print',
  //         'text': '<i class="fa fa-fw fa-print"></i> 列印'
  //       }
  //     ]
  //   });

  //   $('#table_list').DataTable({
  //     "bSort": true,
  //     aaSorting: [] ,
  //     "pageLength": 50,
  //     stateSave: true,
  //     dom: 'Bfrtip',
  //     lengthMenu:
  //     [
  //         [ 25, 50, 100, -1 ],
  //         [ '25 筆', '50 筆', '100 筆', '全部顯示' ]
  //     ],
  //     buttons:
  //     [
  //       {
  //         'extend': 'pageLength',
  //         'text': '顯示筆數'
  //       },
  //       {
  //         'extend': 'excel',
  //         'text': '<i class="fa fa-fw fa-file-excel-o"></i> 匯出Excel'
  //       },
  //       {
  //         'extend': 'print',
  //         'text': '<i class="fa fa-fw fa-print"></i> 列印'
  //       }
  //     ]
  //   });

  //   $('#table_list_sort').DataTable({
  //     "bSort": true,
  //     aaSorting: [] ,
  //     "order": [[ 1, "desc" ]],
  //     "pageLength": 50,
  //     dom: 'Bfrtip',
  //     lengthMenu:
  //     [
  //         [ 25, 50, 100, -1 ],
  //         [ '25 筆', '50 筆', '100 筆', '全部顯示' ]
  //     ],
  //     buttons:
  //     [
  //       {
  //         'extend': 'pageLength',
  //         'text': '顯示筆數'
  //       },
  //       {
  //         'extend': 'excel',
  //         'text': '<i class="fa fa-fw fa-file-excel-o"></i> 匯出Excel'
  //       },
  //       {
  //         'extend': 'print',
  //         'text': '<i class="fa fa-fw fa-print"></i> 列印'
  //       }
  //     ]
  //   });

  //   $('#table_list3').DataTable({
  //     "bSort": true,
  //     aaSorting: [] ,
  //     "pageLength": 25,
  //     stateSave: true,
  //     dom: 'Bfrtip',
  //     lengthMenu:
  //     [
  //         [ 25, 50, 100, -1 ],
  //         [ '25 筆', '50 筆', '100 筆', '全部顯示' ]
  //     ],
  //     buttons:
  //     [
  //       {
  //         'extend': 'pageLength',
  //         'text': '顯示筆數'
  //       },
  //       {
  //         'extend': 'excel',
  //         'text': '<i class="fa fa-fw fa-file-excel-o"></i> 匯出Excel'
  //       },
  //       {
  //         'extend': 'print',
  //         'text': '<i class="fa fa-fw fa-print"></i> 列印'
  //       }
  //     ]
  //   });

  //   $('#table_list_100').DataTable({
  //     "bSort": true,
  //     "pageLength": 100,
  //     aaSorting: [],
  //     dom: 'Bfrtip',
  //     lengthMenu:
  //     [
  //         [ 25, 50, 100, -1 ],
  //         [ '25 筆', '50 筆', '100 筆', '全部顯示' ]
  //     ],
  //     buttons:
  //     [
  //       {
  //         'extend': 'pageLength',
  //         'text': '顯示筆數'
  //       },
  //       {
  //         'extend': 'excel',
  //         'text': '<i class="fa fa-fw fa-file-excel-o"></i> 匯出Excel'
  //       },
  //       {
  //         'extend': 'print',
  //         'text': '<i class="fa fa-fw fa-print"></i> 列印'
  //       }
  //     ]
  //   });

  //   $('#table_list2 tr').click(function(event)
  //   {
  //     if (event.target.type !== 'checkbox')
  //     {
  //       $(':checkbox', this).trigger('click');
  //     }
  //   });

  //   $.validator.addMethod("not_empty", function(value, element)
  //   {
  //      return value != "";
  //   });

  //   // 表單驗證
  //   $("#new-form").validate(
  //   {
  //     errorPlacement: function(error, element)
  //     {
  //       $(element)
  //       .closest( "form" )
  //       .find( "label[for='" + element.attr( "id" ) + "']" )
  //       .append(error);

  //       $(element)
  //       .closest( "form" )
  //       .find("div[id='div_" + element.attr( "id" ) + "']" )
  //       .addClass("has-error");
  //     },
  //     rules:
  //     {
  //       total_price:
  //       {
  //         required: true ,
  //         not_empty : true
  //       },
  //       // bank_account:            { required: function (e){ if($("#bank_price").val() != "0"){return true} } },
  //       note_number:            { required: function (e){ if($("#note_price").val() != "0"){return true} } },
  //       note_expiry_date:        { required: function (e){ if($("#note_price").val() != "0"){return true} } },
  //       // note_bank_account:      { required: function (e){ if($("#note_price").val() != "0"){return true} } },
  //       vacation_type:          { required: true },
  //       supervisor_user:        { required: true },
  //       agent:                  { required: true },
  //       buy_price:              { required: true },
  //       sell_price1:            { required: true },
  //       sell_price2:            { required: true },
  //       // large_unit:              { required: true },
  //       // small_unit:              { required: true },
  //       account:                { required: true },
  //       item_number:            { required: true },
  //       property_number:        { required: true },
  //       name:                    { required: true },
  //       // company_number:          { required: true },
  //       // contact_name:            { required: true },
  //       warehouse_name:          { required: true },
  //       category_name:          { required: true },
  //       department_name:        { required: true },
  //       number:                  { required: true },
  //       receiver_name:          { required: true },
  //       receiver_address:        { required: true },
  //       invoice_address:        { required: true },
  //       warehouse:              { required: true },
  //       sales:                  { required: true },
  //       purchase:                { required: true },
  //       order_client:            { required: true },
  //       order_supplier:          { required: true },
  //       client:                 { required: true },
  //       supplier:                { required: true },
  //       employee_type:          { required: true },
  //       employee_type_code:      { required: true },
  //       employee_type_name:      { required: true },
  //       employee_number:        { required: true },
  //       department:              { required: true },
  //       trade_date:              { required: true },
  //       client_type:            { required: true },
  //       supplier_type:          { required: true },
  //       expiry_date:            { required: true }
  //     },
  //     errorElement: "span",
  //     messages:
  //     {
  //       total_price:
  //       {
  //         required : "金額錯誤，請重新整理",
  //         not_empty : "請新增品項"
  //       },
  //       agent:                  "請選擇公司",
  //       buy_price:              "請填寫金額",
  //       sell_price1:            "請填寫金額",
  //       sell_price2:            "請填寫金額",
  //       // large_unit:              "請填寫單位",
  //       // small_unit:              "請填寫單位",
  //       account:                "請填寫帳號",
  //       item_number:            "請填寫編號",
  //       property_number:        "請填寫編號",
  //       name:                    "請填寫名稱",
  //       // company_number:          "請填寫統編",
  //       // contact_name:            "請填寫聯絡人名稱",
  //       warehouse_name:          "請填寫倉庫名稱",
  //       category_name:          "請填寫分類名稱",
  //       department_name:        "請填寫部門名稱",
  //       number:                  "單號錯誤，請重新整理",
  //       receiver_name:          "請填寫收件人名稱",
  //       receiver_address:        "請填寫收件地址",
  //       invoice_address:        "請填寫發票地址",
  //       warehouse:              "請選擇倉庫",
  //       sales:                  "請選擇銷貨單",
  //       purchase:                "請選擇進貨單",
  //       order_client:            "請選擇訂購單",
  //       order_supplier:          "請選擇採購單",
  //       client:                 "請選擇客戶",
  //       supplier:                "請選擇供應商",
  //       department:              "請選擇部門",
  //       employee_type:          "請選擇員工類別",
  //       trade_date:              "請填寫日期" ,
  //       expiry_date:            "請填寫有效日期" ,
  //       employee_type_code:      "請填寫類別代碼" ,
  //       employee_type_name:      "請填寫類別名稱" ,
  //       employee_number:        "請填寫員工編號" ,
  //       // bank_account:            "請選擇銀行",
  //       note_number:            "請填寫票號",
  //       // note_bank_account:      "請選擇銀行",
  //       note_expiry_date:        "請填寫到期日" ,
  //       vacation_type:          "請選擇假別",
  //       client_type:            "請選擇類型",
  //       supplier_type:          "請選擇類型",
  //       supervisor_user:        "請選擇審核主管"
  //     },
  //     submitHandler:function(form)
  //     {
  //       $("#btn-save").attr('disabled', true);
  //       return true;
  //     }
  //   });

  //   // 防止 enter 送出表單
  //   $('#new-form').on('keyup keypress', function(e)
  //   {
  //     var target_id = e.target.id;
  //     var keyCode = e.keyCode || e.which;
  //     if (keyCode === 13 && target_id != "remark" && target_id != "common_word_name")
  //     {
  //       e.preventDefault();
  //       return false;
  //     }
  //   });

  //   $('#datetimepicker_year').datetimepicker({    format: 'YYYY' });
  //   $('#datetimepicker_month').datetimepicker({    format: 'YYYY-MM' });
  //   $('#datetimepicker_month2').datetimepicker({    format: 'YYYY-MM' });
  //   $('#datetimepicker_month3').datetimepicker({    format: 'YYYY-MM' });
  //   $('#datetimepicker_month4').datetimepicker({    format: 'YYYY-MM' });
  //   $('#datetimepicker').datetimepicker({    format: 'YYYY-MM-DD' });
  //   $('#datetimepicker1').datetimepicker({  format: 'YYYY-MM-DD' });
  //   $('#datetimepicker2').datetimepicker({   format: 'YYYY-MM-DD' });
  //   $('#datetimepicker3').datetimepicker({   format: 'YYYY-MM-DD' });
  //   $('#datetimepicker4').datetimepicker({   format: 'YYYY-MM-DD' });
  //   $('#datetimepicker5').datetimepicker({   format: 'YYYY-MM-DD' });
  //   $('#datetimepicker_time1').datetimepicker({ format: 'YYYY-MM-DD HH:mm' });
  //   $('#datetimepicker_time2').datetimepicker({ format: 'YYYY-MM-DD HH:mm' });

  //   $(".js-select2").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇"
  //   });

  //   $(".js-select2-category").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇分類"
  //   });

  //   $(".js-select2-item").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇品項"
  //   });

  //   $(".js-select2-agent").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇公司"
  //   });

  //   $(".js-select2-stock_status").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇存貨狀態"
  //   });

  //   $(".js-select2-client").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇客戶"
  //   });

  //   $(".js-select2-supplier").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇供應商"
  //   });

  //   $(".js-select2-warehouse").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇倉庫"
  //   });

  //   $(".js-select2-purchase").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇進貨單"
  //   });

  //   $(".js-select2-sales").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇銷貨單"
  //   });

  //   $(".js-select2-order-client").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇訂購單"
  //   });

  //   $(".js-select2-order-supplier").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇採購單"
  //   });

  //   $(".js-select2-bank-account").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇轉帳帳號"
  //   });

  //   $('#tax').change(function()
  //   {
  //     if($("#tax").val() == "0")
  //     {
  //       // $(".subtotaltaxprice").val("0");
  //       $(".totaltaxprice ").val("0");
  //     }

  //     $.each($(".subtotalprice"), function(e)
  //     {
  //       var data_array = $(this).attr("id").split('-');
  //       var row_id = data_array[1];
  //       $("#qty-" + row_id).val(parseInt($("#qty-" + row_id).val(),10));
  //       $("#price-" + row_id).val(parseFloat($("#price-" + row_id).val()));

  //       var subtotalprice = (accMul(parseInt($("#qty-" + row_id).val(), 10) , parseFloat($("#price-" + row_id).val(), 10)));
  //       var currency_price = parseFloat($("#currency_price").val());
  //       $("#subtotalprice-" + row_id).val(Math.round(subtotalprice * currency_price));
  //       $("#originalsubtotalprice-" + row_id).val(subtotalprice);
  //       SumItemPrice();
  //     });
  //   });

  //   // 快速新增員工類別
  //   $('#btn-save-new-employee-type').click(function ()
  //   {
  //     var agent_id = $("#ModalAgentID").val();
  //     var name = $("#ModalEmployeeTypeName").val();
  //     var code = $("#ModalEmployeeTypeCode").val();
  //     new_employee_type(agent_id, code , name);
  //   });

  //   // 快速新增部門
  //   $('#btn-save-new-department').click(function ()
  //   {
  //     var agent_id = $("#ModalAgentID").val();
  //     var name = $("#ModalDepartmentName").val();
  //     new_department(agent_id, name);
  //   });
  // });

  // function changeCurrency(currency_id, trade_date)
  // {
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"exchange_rate_info" , "id": currency_id  , "trade_date": trade_date },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var currency_price = obj.currency_price;
  //       if(jQuery.inArray(json_url.func, use_buy_price ) >= 0)
  //       {
  //         currency_price = obj.currency_price;
  //         // console.log(json_url.func + " : in_use_buy_price");
  //       }
  //       else if(jQuery.inArray(json_url.func, use_sell_price ) >= 0)
  //       {
  //         currency_price = obj.currency_sell_price;
  //         // console.log(json_url.func + " : in_use_sell_price");
  //       }

  //       // console.log(obj.currency_price + " , " + obj.currency_sell_price);

  //       $("#currency_price").val(currency_price);
  //       $("#currency_code").val(obj.original_currency_code);
  //       SumItemPrice();
  //     }
  //   });
  // }

  // // for select2 match on start 重頭開始搜尋
  // function matchStart (term, text)
  // {
  //   if (text.toUpperCase().indexOf(term.toUpperCase()) == 0)
  //   {
  //     return true;
  //   }
  //   return false;
  // }

  // // arg1 乘以 arg2 的精準小數計算
  // function accMul(arg1, arg2)
  // {
  //   var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
  //   try { m += s1.split(".")[1].length } catch (e) { }
  //   try { m += s2.split(".")[1].length } catch (e) { }
  //   return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m)
  // }

  // // arg1 除以 arg2 的精準小數計算
  // function accDiv(arg1, arg2)
  // {
  //   var t1 = 0, t2 = 0, r1, r2;
  //   try { t1 = arg1.toString().split(".")[1].length } catch (e) { }
  //   try { t2 = arg2.toString().split(".")[1].length } catch (e) { }
  //   with (Math)
  //   {
  //     r1 = Number(arg1.toString().replace(".", ""))
  //     r2 = Number(arg2.toString().replace(".", ""))
  //     return (r1 / r2) * pow(10, t2 - t1);
  //   }
  // }

  // // arg1 加 arg2 的精準小數計算
  // function accAdd(arg1, arg2)
  // {
  //   var r1, r2, m, c;
  //   try { r1 = arg1.toString().split(".")[1].length } catch (e) { r1 = 0 }
  //   try { r2 = arg2.toString().split(".")[1].length } catch (e) { r2 = 0 }
  //   c = Math.abs(r1 - r2);
  //   m = Math.pow(10, Math.max(r1, r2))
  //   if (c > 0)
  //   {
  //     var cm = Math.pow(10, c);
  //     if (r1 > r2)
  //     {
  //       arg1 = Number(arg1.toString().replace(".", ""));
  //       arg2 = Number(arg2.toString().replace(".", "")) * cm;
  //     }
  //     else
  //     {
  //       arg1 = Number(arg1.toString().replace(".", "")) * cm;
  //       arg2 = Number(arg2.toString().replace(".", ""));
  //     }
  //   }
  //   else
  //   {
  //     arg1 = Number(arg1.toString().replace(".", ""));
  //     arg2 = Number(arg2.toString().replace(".", ""));
  //   }
  //   return (arg1 + arg2) / m
  //  }

  // // arg1 減 arg2 的精準小數計算
  // function accSub(arg1,arg2)
  // {
  //   var r1,r2,m,n;
  //   try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
  //   try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
  //   m=Math.pow(10,Math.max(r1,r2));
  //   //last modify by deeka
  //   //動態控制精度長度
  //   n=(r1>=r2)?r1:r2;
  //   return ((arg1*m-arg2*m)/m).toFixed(n);
  // }

  // // 列印揀貨單
  // function open_print_picking_list(id , get_type)
  // {
  //   window.open("/print/print_picking_list.php?table_name=" + get_type + "&table_id=" + id);
  // };

  // // 列印折讓單
  // function open_print_discount(id , get_type)
  // {
  //   window.open("/print/print_discount.php?table_name=" + get_type + "&table_id=" + id);
  // };

  // // 列印報價單
  // function open_print_quotes(type, id)
  // {
  //   window.open("/print/print_quotes.php?id=" + id + "&type=" + type);
  // }

  // // 列印銷貨單
  // function open_print(id , get_type)
  // {
  //   window.open("/print/print_order.php?table_name=" + get_type + "&table_id=" + id);
  // }

  // // 列印銷貨單與PDF檔
  // function open_print_pdf(id , get_type)
  // {
  //   window.open("/print/print_order_pdf.php?table_name=" + get_type + "&table_id=" + id);
  // }

  // // 列印對帳單
  // function open_print_reconciliation_statement(client_id , start_time , end_time , sum_getable_price , sum_discount_price , sum_balance_price , last_advnace_price , last_getable_price , last_reconciliation_price , in_data_id)
  // {
  //   window.open("/print/print_reconciliation_statement.php?client_id=" + client_id + "&start_time=" + start_time + "&end_time=" + end_time + "&sum_getable_price=" + sum_getable_price + "&sum_discount_price=" + sum_discount_price + "&sum_balance_price=" + sum_balance_price + "&last_advnace_price=" + last_advnace_price + "&last_getable_price=" + last_getable_price + "&last_reconciliation_price=" + last_reconciliation_price + "&in_data_id=" + in_data_id);
  // }

  // // 列印對帳單
  // function open_print_reconciliation_statement_envelope(client_id , start_time , end_time , sum_getable_price , sum_discount_price , sum_balance_price , last_advnace_price , last_getable_price , last_reconciliation_price , in_data_id)
  // {
  //   window.open("/print/print_reconciliation_statement_envelope.php?client_id=" + client_id + "&start_time=" + start_time + "&end_time=" + end_time + "&sum_getable_price=" + sum_getable_price + "&sum_discount_price=" + sum_discount_price + "&sum_balance_price=" + sum_balance_price + "&last_advnace_price=" + last_advnace_price + "&last_getable_price=" + last_getable_price + "&last_reconciliation_price=" + last_reconciliation_price + "&in_data_id=" + in_data_id);
  // }

  // // 新增部門
  // function new_department(agent_id, name)
  // {
  //   $.ajax(
  //   {
  //     url: "ajax/insert_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "new_department" ,'agent_id': agent_id ,'name': name},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     if(data == "OK")
  //     {
  //       location.reload();
  //       alert("新增成功");
  //       $('#new_department').modal('hide');
  //     }
  //   });
  // }

  // // 新增員工類別
  // function new_employee_type(agent_id, code , name)
  // {
  //   $.ajax(
  //   {
  //     url: "ajax/insert_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "new_employee_type" ,'agent_id': agent_id ,'code': code ,'name': name},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     if(data == "OK")
  //     {
  //       location.reload();
  //       alert("新增成功")
  //       $('#new_employee_type').modal('hide');
  //     }
  //   });
  // }

  // // 補登發票
  // function show_update_invoice(id , get_type)
  // {
  //   $("#btn-save-update-invoice").off("click");

  //   var data_id = id;
  //   $("#get_modal_id").val(data_id);
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":get_type , "id": data_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       $("#ModaInvoicelNumber").val(obj.invoice_number);
  //       $("#ModaInvoicelDate").val(obj.invoice_date);
  //     }
  //   });

  //   $('#btn-save-update-invoice').click(function ()
  //   {
  //     update_invoice(data_id , get_type);
  //   });
  // }

  // // 更新發票資訊
  // function update_invoice(id , get_type)
  // {
  //   var invoice_number = $("#ModaInvoicelNumber").val();
  //   var invoice_date = $("#ModaInvoicelDate").val();

  //   $.ajax(
  //   {
  //     url: "ajax/update_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "update_" + get_type + "_invoice" ,'id': id ,'invoice_number': invoice_number,'invoice_date': invoice_date},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     if(data == "OK")
  //     {
  //       if(get_type == "contract_record")
  //       {
  //         send_line_message("contract_record_message", id);
  //       }
  //       location.reload();
  //       alert("發票已補登");
  //       $('#update').modal('hide');
  //     }
  //   });
  // }

  // // 作廢單
  // function de_active(id , get_type)
  // {
  //   $("#btn-deactivate").attr('disabled', true);
  //   $("#btn-deactivate-salary").attr('disabled', true);

  //   $.ajax(
  //   {
  //     url: "ajax/update_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "update_" + get_type + "_active" ,'id': id},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     if(get_type = "order_client" && data == "OK")
  //     {
  //       location.reload();
  //       alert("此單已結案");
  //       $('#row_detail').modal('hide');
  //     }
  //     else if(data == "OK")
  //     {
  //       location.reload();
  //       alert("此單已作廢");
  //       $('#row_detail').modal('hide');
  //     }
  //     else if(data == "over_time")
  //     {
  //       alert("生產加工作業已開始，不得作廢!");
  //       $('#row_detail').modal('hide');
  //     }
  //     else if(data == "ERROR")
  //     {
  //       alert("此單已沖帳或沖帳中，不得作廢!");
  //       $('#row_detail').modal('hide');
  //     }

  //     $("#btn-deactivate").attr('disabled', false);
  //     $("#btn-deactivate-salary").attr('disabled', false);
  //   });
  // }

  // // 新增商品品項，動態生成view ItemDiv ，在 puchase sales order_client order_supplier adjust 用到
  // function AddItemRow(get_type)
  // {
  //   var supplier_id = $("#supplier").val();
  //   var client_id = $("#client").val();
  //   if(get_type == "purchase")
  //   {
  //     if(supplier_id == "")
  //     {
  //       supplier_id = 0;
  //       // alert("請先選擇供應商");
  //       // return;
  //     }
  //   }

  //   if(get_type == "sales" || get_type == "quotes")
  //   {
  //     if(client_id == "")
  //     {
  //       alert("請先選擇客戶");
  //       return;
  //     }
  //   }

  //   var curRow = parseInt($('#rowNo').val());
  //   var newRow = curRow + 1;
  //   if(curRow == 0)
  //   {
  //     if(get_type == "shipping")
  //     {
  //       $(" <div class='add_row'>" +
  //           "<div class='row'>" +
  //             "<div class='col-sm-6 text-left'>品項</div>" +
  //             "<div class='col-sm-1 text-left'>庫存</div>" +
  //             "<div class='col-sm-2 text-left'>批號</div>" +
  //             "<div class='col-sm-1 text-left'>數量</div>" +
  //             "<div class='col-sm-1 text-left'>單位</div>" +
  //             "<div class='col-sm-1 text-left' style='display:none;'>上次價格</div>" +
  //             "<div class='col-sm-1 text-left' style='display:none;'>單價</div>" +
  //             "<div class='col-sm-1 text-left' style='display:none;'>原幣小計</div>" +
  //             "<div class='col-sm-1 text-left' style='display:none;'>小計</div>" +
  //             "<div class='col-sm-1 text-left' style='display:none;'>原幣金額</div>" +
  //             "<div class='col-sm-1 text-left' style='display:none;'>總金額</div>" +
  //             "<div class='col-sm-1 text-left'>功能</div>" +
  //           "</div>" +
  //       " </div>").appendTo($('#ItemDiv'));
  //     }
  //     else
  //     {
  //       $(" <div class='add_row'>" +
  //           "<div class='row'>" +
  //             "<div class='col-sm-2 text-left'>品項</div>" +
  //             "<div class='col-sm-1 text-left'>庫存</div>" +
  //             "<div class='col-sm-2 text-left'>批號</div>" +
  //             "<div class='col-sm-1 text-left'>數量</div>" +
  //             "<div class='col-sm-1 text-left'>單位</div>" +
  //             "<div class='col-sm-1 text-left'>上次價格</div>" +
  //             "<div class='col-sm-1 text-left'>單價</div>" +
  //             "<div class='col-sm-1 text-left'>原幣小計</div>" +
  //             "<div class='col-sm-1 text-left' style='display:none;'>小計</div>" +
  //             "<div class='col-sm-1 text-left' style='display:none;'>原幣金額</div>" +
  //             "<div class='col-sm-1 text-left'>總金額</div>" +
  //             "<div class='col-sm-1 text-left'>功能</div>" +
  //           "</div>" +
  //       " </div>").appendTo($('#ItemDiv'));
  //     }
  //   }
  //   if(get_type == "shipping")
  //   {
  //     $(" <div class='add_row' id='div-addrow-" + newRow + "'>" +
  //           "<div class='row'>" +
  //             "<input class='form-control' name='itemid-" + newRow + "' id='itemid-" + newRow + "' type='hidden'>" +
  //             "<input class='form-control' name='itemnumber-" + newRow + "' id='itemnumber-" + newRow + "' type='hidden'>" +
  //             "<input class='form-control' name='itembrand-" + newRow + "' id='itembrand-" + newRow + "' type='hidden'>" +
  //             "<input class='form-control' name='itemname-" + newRow + "' id='itemname-" + newRow + "' type='hidden'>" +
  //             "<input class='form-control' name='itemspec-" + newRow + "' id='itemspec-" + newRow + "' type='hidden'>" +
  //             "<div class='col-sm-6' >" +
  //               "<div class='input-group'>" +
  //                 "<select class='form-control js-select2-item' name='item-" + newRow + "' id='item-" + newRow + "' onchange=\"getItemInfo(" + newRow + " , '" + get_type + "' , " + supplier_id + " , " + client_id + ")\">" +
  //                   "<option value=''></option>" +
  //                 "</select>" +
  //                 "<span class='input-group-btn'>"+
  //                   "<button class='btn copy_btn' type='button' onclick=\"copy_text(" + newRow + ")\" ><i class='fa fa-copy'></i></button>" +
  //                 "</span>" +
  //               "</div>" +
  //             "</div>" +
  //             "<div class='col-sm-1' >" +
  //               "<input class='form-control' name='stockqty-" + newRow + "' id='stockqty-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-2' id='div_lot_number_" + newRow + "'>" +
  //               "<input class='form-control' name='lotnumber-" + newRow + "' id='lotnumber-" + newRow + "' >" +
  //             "</div>" +
  //             "<div class='col-sm-1' >" +
  //               "<input class='form-control qty' name='qty-" + newRow + "' id='qty-" + newRow + "'  type='number'>" +
  //             "</div>" +
  //             "<div class='col-sm-1' >" +
  //               "<input class='form-control' name='unit-" + newRow + "' id='unit-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1'  style='display:none;'>" +
  //               "<input class='form-control'  name='lastprice-" + newRow + "' id='lastprice-" + newRow + "'  readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1'  style='display:none;'>" +
  //               "<input class='form-control price' name='price-" + newRow + "' id='price-" + newRow + "'  type='number'>" +
  //             "</div>" +

  //             "<div class='col-sm-1' style='display:none;'>" +
  //               "<input class='form-control original_subtotalprice' name='originalsubtotalprice-" + newRow + "' id='originalsubtotalprice-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1'  style='display:none;'>" +
  //               "<input class='form-control subtotalprice' name='subtotalprice-" + newRow + "' id='subtotalprice-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1'  style='display:none;'>" +
  //               "<input class='form-control original_totalprice' name='originaltotalprice-" + newRow + "' id='originaltotalprice-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1'  style='display:none;'>" +
  //               "<input class='form-control totalprice' style='display:none;' name='totalprice-" + newRow + "' id='totalprice-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1'>" +
  //               "<button class='btn btn-danger btn_close' id='btn-delete-" + newRow + "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
  //             "</div>" +
  //           "</div>" +
  //         "</div>"
  //     ).appendTo($('#ItemDiv'));
  //   }
  //   else
  //   {
  //     $(" <div class='add_row' id='div-addrow-" + newRow + "'>" +
  //           "<div class='row'>" +
  //             "<input class='form-control' name='itemid-" + newRow + "' id='itemid-" + newRow + "' type='hidden'>" +
  //             "<input class='form-control' name='itemnumber-" + newRow + "' id='itemnumber-" + newRow + "' type='hidden'>" +
  //             "<input class='form-control' name='itembrand-" + newRow + "' id='itembrand-" + newRow + "' type='hidden'>" +
  //             "<input class='form-control' name='itemname-" + newRow + "' id='itemname-" + newRow + "' type='hidden'>" +
  //             "<input class='form-control' name='itemspec-" + newRow + "' id='itemspec-" + newRow + "' type='hidden'>" +
  //             "<div class='col-sm-2' >" +
  //               "<div class='input-group'>" +
  //                 "<select class='form-control js-select2-item' name='item-" + newRow + "' id='item-" + newRow + "' onchange=\"getItemInfo(" + newRow + " , '" + get_type + "' , " + supplier_id + " , " + client_id + ")\">" +
  //                   "<option value=''></option>" +
  //                 "</select>" +
  //                 "<span class='input-group-btn'>"+
  //                   "<button class='btn copy_btn' type='button' onclick=\"copy_text(" + newRow + ")\" ><i class='fa fa-copy'></i></button>" +
  //                 "</span>" +
  //               "</div>" +
  //             "</div>" +
  //             "<div class='col-sm-1' >" +
  //               "<input class='form-control' name='stockqty-" + newRow + "' id='stockqty-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-2' id='div_lot_number_" + newRow + "'>" +
  //               "<input class='form-control' name='lotnumber-" + newRow + "' id='lotnumber-" + newRow + "' >" +
  //             "</div>" +
  //             "<div class='col-sm-1' >" +
  //               "<input class='form-control qty' name='qty-" + newRow + "' id='qty-" + newRow + "'  type='number'>" +
  //             "</div>" +
  //             "<div class='col-sm-1' >" +
  //               "<input class='form-control' name='unit-" + newRow + "' id='unit-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1' >" +
  //               "<input class='form-control' name='lastprice-" + newRow + "' id='lastprice-" + newRow + "'  readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1' >" +
  //               "<input class='form-control price' name='price-" + newRow + "' id='price-" + newRow + "'  type='number'>" +
  //             "</div>" +
  //             "<div class='col-sm-1'>" +
  //               "<input class='form-control original_subtotalprice' name='originalsubtotalprice-" + newRow + "' id='originalsubtotalprice-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1'  style='display:none;'>" +
  //               "<input class='form-control subtotalprice' name='subtotalprice-" + newRow + "' id='subtotalprice-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1'  style='display:none;'>" +
  //               "<input class='form-control original_totalprice' name='originaltotalprice-" + newRow + "' id='originaltotalprice-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1' >" +
  //               "<input class='form-control totalprice' name='totalprice-" + newRow + "' id='totalprice-" + newRow + "' readonly >" +
  //             "</div>" +
  //             "<div class='col-sm-1'>" +
  //               "<button class='btn btn-danger btn_close' id='btn-delete-" + newRow + "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
  //             "</div>" +
  //           "</div>" +
  //         "</div>"
  //     ).appendTo($('#ItemDiv'));
  //   }
  //   $('#rowNo').val(newRow);

  //   // if(get_type == "requisition")
  //   // {
  //     // $("#price-" + newRow).attr("readonly",true);
  //   // }

  //   $(".js-select2-item").select2(
  //   {
  //       allowClear: true ,
  //       theme: "bootstrap",
  //       placeholder: "請選擇品項"
  //   });

  //   $('button[id^=btn-delete-]').click(function()
  //   {
  //     $("#div-addrow-" + $(this).val()).remove();
  //     SumItemPrice();
  //     return false;
  //   });

  //   add_item_event();

  //   // 即時取出物品資訊，放到 select 中
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "itemlist"},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);
  //       $.each( obj, function( key, value )
  //       {
  //         var text_value = value.number + "-" + value.brand + "-" + value.name + "-" + value.spec;
  //         $("#item-" + newRow).append($("<option></option>").attr("value", value.id).text(text_value));
  //       });
  //     }
  //   });
  // }

  // function getStockLotNumber(newRow, item_id)
  // {
  //   // console.log(newRow + " => " + item_id);
  //   $("#div_lot_number_" + newRow).html("<select class='form-control' name='lotnumber-" + newRow + "' id='lotnumber-" + newRow + "' ></select>");
  //   $("#lotnumber-" + newRow).html("");
  //   // 即時取出物品資訊，放到 select 中
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "lotnumber_list", "item_id" : item_id},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);
  //       $.each( obj, function( key, value )
  //       {
  //         var text_value = value.item_lot_number + " (" + value.sum_item_qty + ")";
  //         if(value.item_lot_number == "")
  //           text_value = "無資料";
  //         $("#lotnumber-" + newRow).append($("<option></option>").attr("value", value.item_lot_number).text(text_value));
  //       });
  //     }
  //   });
  // }

  // // 帶出 table_detail 的品項列表，給 編輯 用 (order_client order_supplier)
  // function InitEditOrderItem(order_type, order_id)
  // {
  //   var newRow = 0;
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {'get_type': order_type, "id" : order_id},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     var item_id_select = [];
  //     var item_qty_select = [];

  //     if(data_array[0] == "OK")
  //     {
  //       $('#ItemDiv').html("");
  //       var html_value = "";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       $.each( obj, function( key, value )
  //       {
  //         console.log(value.item_id);
  //         var subtotaltaxprice = 0;
  //         if($("#tax").val() == "1")
  //           subtotaltaxprice = Math.round(parseFloat(value.subtotal_price, 10) * 0.05 , 10);

  //         newRow++;
  //         item_id_select.push(value.item_id);
  //         item_qty_select.push(value.item_qty);

  //         if(newRow == 1)
  //         {
  //           html_value +=  " <div class='add_row'>" +
  //                             "<div class='row'>" +
  //                               "<div class='col-sm-2 text-left'>品項</div>" +
  //                               "<div class='col-sm-1 text-left'>庫存</div>" +
  //                               "<div class='col-sm-2 text-left'>批號</div>" +
  //                               "<div class='col-sm-1 text-left'>數量</div>" +
  //                               "<div class='col-sm-1 text-left'>單位</div>" +
  //                               "<div class='col-sm-1 text-left'>上次價格</div>" +
  //                               "<div class='col-sm-1 text-left'>單價</div>" +
  //                               "<div class='col-sm-1 text-left'>原幣小計</div>" +
  //                               "<div class='col-sm-1 text-left' style='display:none;'>小計</div>" +
  //                               "<div class='col-sm-1 text-left' style='display:none;'>原幣金額</div>" +
  //                               "<div class='col-sm-1 text-left'>總金額</div>" +
  //                               "<div class='col-sm-1 text-left'>功能</div>" +
  //                             "</div>" +
  //                         " </div>";
  //         }
  //         html_value +=  " <div class='add_row' id='div-addrow-" + newRow + "' >" +
  //                           "<div class='row'>" +

  //                             "<input class='form-control' name='itemid-" + newRow + "' id='itemid-" + newRow + "'  value='" + value.item_id + "'  type='hidden'>" +
  //                             "<input class='form-control' name='itemnumber-" + newRow + "' id='itemnumber-" + newRow + "'  value='" + value.item_number + "' type='hidden'>" +
  //                             "<input class='form-control' name='itembrand-" + newRow + "' id='itembrand-" + newRow + "'  value='" + value.item_brand + "' type='hidden'>" +
  //                             "<input class='form-control' name='itemname-" + newRow + "' id='itemname-" + newRow + "'  value='" + value.item_name + "' type='hidden'>" +
  //                             "<input class='form-control' name='itemspec-" + newRow + "' id='itemspec-" + newRow + "'  value='" + value.item_spec + "' type='hidden'>" +

  //                             "<div class='col-sm-2' >" +
  //                               "<div class='input-group'>" +
  //                                 "<input class='form-control' name='item-" + newRow + "' id='item-" + newRow + "'  value='" + value.item_number + " - " + value.item_brand + " - " + value.item_name + " - " + value.item_spec + "' readonly >" +
  //                                 "<span class='input-group-btn'>"+
  //                                   "<button class='btn copy_btn' type='button' onclick=\"copy_text(" + newRow + ")\" ><i class='fa fa-copy'></i></button>" +
  //                                 "</span>" +
  //                               "</div>" +
  //                             "</div>" +

  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control' name='stockqty-" + newRow + "' id='stockqty-" + newRow + "'  value='" + value.item_stock_qty + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-2' id='div_lot_number_" + newRow + "'>" +
  //                               "<input class='form-control' name='lotnumber-" + newRow + "' id='lotnumber-" + newRow + "'  value='" + value.item_lot_number + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control qty' name='qty-" + newRow + "' id='qty-" + newRow + "'  value='" + parseInt(value.item_qty) + "' type='number'>" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control' name='unit-" + newRow + "' id='unit-" + newRow + "' value='" + value.item_unit + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +

  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control price' name='price-" + newRow + "' id='price-" + newRow + "' value='" + parseFloat(value.item_price) + "' >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1'>" +
  //                               "<input class='form-control original_subtotalprice' name='originalsubtotalprice-" + newRow + "' value='" + value.original_subtotal_price + "'  id='originalsubtotalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1'  style='display:none;'>" +
  //                               "<input class='form-control subtotalprice' name='subtotalprice-" + newRow + "' value='" + value.subtotal_price + "'  id='subtotalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1'  style='display:none;'>" +
  //                               "<input class='form-control original_totalprice' name='originaltotalprice-" + newRow + "' value='" + value.original_total_price + "'  id='originaltotalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control totalprice' name='totalprice-" + newRow + "' value='" + value.total_price + "' id='totalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1'>" +
  //                               "<button class='btn btn-danger btn_close' id='btn-delete-" + newRow + "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
  //                             "</div>" +
  //                           "</div>" +
  //                         "</div>";
  //       });
  //       html_value += "<input type='hidden' name='rowNo' id='rowNo' value='" + newRow + "'>";
  //       $(html_value).appendTo($('#ItemDiv'));

  //       $('button[id^=btn-delete-]').click(function()
  //       {
  //         $("#div-addrow-" + $(this).val()).remove();
  //         SumItemPrice();
  //         return false;
  //       });

  //       SumItemPrice();
  //       add_item_event();

  //       $('#rowNo').val(newRow);
  //     }
  //     else
  //       alert("查無資料");
  //   });

  //   $('#rowNo').val(newRow);
  // }


  // // 帶出 table_detail 的品項列表，給 編輯 用 (purchase)
  // function InitEditItemPrice(order_type, order_id)
  // {
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {'get_type': order_type, "id" : order_id},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     var item_id_select = [];
  //     var item_qty_select = [];
  //     var newRow = 0;

  //     if(data_array[0] == "OK")
  //     {
  //       $('#ItemDiv').html("");
  //       var html_value = "";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       $.each( obj, function( key, value )
  //       {
  //         var subtotaltaxprice = 0;
  //         if($("#tax").val() == "1")
  //           subtotaltaxprice = Math.round(parseFloat(value.subtotal_price, 10) * 0.05 , 10);

  //         newRow++;
  //         item_id_select.push(value.item_id);
  //         item_qty_select.push(value.item_qty);

  //         if(newRow == 1)
  //         {
  //           html_value +=  " <div class='add_row'>" +
  //                             "<div class='row'>" +
  //                               "<div class='col-sm-5 text-left'>品項</div>" +
  //                               "<div class='col-sm-2 text-left'>批號</div>" +
  //                               "<div class='col-sm-1 text-left'>數量</div>" +
  //                               "<div class='col-sm-1 text-left'>單位</div>" +
  //                               "<div class='col-sm-1 text-left'>單價</div>" +
  //                               "<div class='col-sm-1 text-left'>原幣小計</div>" +
  //                               "<div class='col-sm-1 text-left' style='display:none;'>小計</div>" +
  //                               "<div class='col-sm-1 text-left' style='display:none;'>原幣金額</div>" +
  //                               "<div class='col-sm-1 text-left'>總金額</div>" +
  //                             "</div>" +
  //                         " </div>";
  //         }
  //         html_value +=  " <div class='add_row' id='div-addrow-" + newRow + "' >" +
  //                           "<div class='row'>" +

  //                             "<input class='form-control' name='orderid-" + newRow + "' id='orderid-" + newRow + "'  value='" + value.id + "'  type='hidden'>" +
  //                             "<input class='form-control' name='itemid-" + newRow + "' id='itemid-" + newRow + "'  value='" + value.item_id + "'  type='hidden'>" +
  //                             "<input class='form-control' name='itemnumber-" + newRow + "' id='itemnumber-" + newRow + "'  value='" + value.item_number + "' type='hidden'>" +
  //                             "<input class='form-control' name='itembrand-" + newRow + "' id='itembrand-" + newRow + "'  value='" + value.item_brand + "' type='hidden'>" +
  //                             "<input class='form-control' name='itemname-" + newRow + "' id='itemname-" + newRow + "'  value='" + value.item_name + "' type='hidden'>" +
  //                             "<input class='form-control' name='itemspec-" + newRow + "' id='itemspec-" + newRow + "'  value='" + value.item_spec + "' type='hidden'>" +

  //                             "<div class='col-sm-5' >" +
  //                               "<div class='input-group'>" +
  //                                 "<input class='form-control' name='item-" + newRow + "' id='item-" + newRow + "'  value='" + value.item_number + " - " + value.item_brand + " - " + value.item_name + " - " + value.item_spec + "' readonly >" +
  //                                 "<span class='input-group-btn'>"+
  //                                   "<button class='btn copy_btn' type='button' onclick=\"copy_text(" + newRow + ")\" ><i class='fa fa-copy'></i></button>" +
  //                                 "</span>" +
  //                               "</div>" +
  //                             "</div>" +
  //                             "<div class='col-sm-2' id='div_lot_number_" + newRow + "'>" +
  //                               "<input class='form-control' name='lotnumber-" + newRow + "' id='lotnumber-" + newRow + "'  value='" + value.item_lot_number + "' readonly>" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control qty' name='qty-" + newRow + "' id='qty-" + newRow + "'  value='" + parseInt(value.item_qty) + "' type='number' readonly>" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control' name='unit-" + newRow + "' id='unit-" + newRow + "' value='" + value.item_unit + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control price' name='price-" + newRow + "' id='price-" + newRow + "' value='" + parseFloat(value.item_price) + "' >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1'>" +
  //                               "<input class='form-control original_subtotalprice' name='originalsubtotalprice-" + newRow + "' value='" + value.original_subtotal_price + "' id='originalsubtotalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1'  style='display:none;'>" +
  //                               "<input class='form-control subtotalprice' name='subtotalprice-" + newRow + "' value='" + value.subtotal_price + "' id='subtotalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1'  style='display:none;'>" +
  //                               "<input class='form-control original_totalprice' name='originaltotalprice-" + newRow + "' value='" + value.original_total_price + "' id='originaltotalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control totalprice' name='totalprice-" + newRow + "' value='" + value.total_price + "' id='totalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                           "</div>" +
  //                         "</div>";
  //       });
  //       html_value += "<input type='hidden' name='rowNo' id='rowNo' value='" + newRow + "'>";
  //       $(html_value).appendTo($('#ItemDiv'));

  //       $('button[id^=btn-delete-]').click(function()
  //       {
  //         $("#div-addrow-" + $(this).val()).remove();
  //         SumItemPrice();
  //         return false;
  //       });

  //       SumItemPrice();
  //       add_item_event();

  //       $('#rowNo').val(newRow);
  //     }
  //     else
  //       alert("查無資料");
  //   });

  //   $('#rowNo').val(newRow);
  // }

  // // 帶入上次品項，動態生成view ItemDiv ，在 purchase sales order_client order_supplier 用到
  // function InputItemList(get_type, order_type)
  // {
  //   if(order_type != "purchase" && order_type != "sales")
  //     return;

  //   var supplier_id = $("#supplier").val();
  //   var client_id = $("#client").val();
  //   var objects_type = "";
  //   var objects_id = "";

  //   if(order_type == "purchase")
  //   {
  //     if(supplier_id == "")
  //     {
  //       alert("請先選擇供應商");
  //       return;
  //     }

  //     order_type = "purchase";
  //     objects_type = "supplier_id";
  //     objects_id = supplier_id;
  //   }

  //   if(order_type == "sales")
  //   {
  //     if(client_id == "")
  //     {
  //       alert("請先選擇客戶");
  //       return;
  //     }

  //     order_type = "sales";
  //     objects_type = "client_id";
  //     objects_id = client_id;
  //   }

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {'get_type': get_type, "order_type" : order_type, "objects_type" : objects_type, "objects_id" : objects_id},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     var item_id_select = [];
  //     var item_qty_select = [];
  //     var newRow = 0;

  //     if(data_array[0] == "OK")
  //     {
  //       $('#ItemDiv').html("");
  //       var html_value = "";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       $.each( obj, function( key, value )
  //       {
  //         var subtotaltaxprice = 0;
  //         if($("#tax").val() == "1")
  //           subtotaltaxprice = Math.round(parseFloat(value.subtotal_price, 10) * 0.05 , 10);

  //         newRow++;
  //         item_id_select.push(value.item_id);
  //         item_qty_select.push(value.item_qty);

  //         var show_qty_title = "數量";
  //         if(get_type == "common_item_list")
  //           show_qty_title = "均量";

  //         var show_price_title = "單價";
  //         if(get_type == "common_item_list")
  //           show_price_title = "均價";

  //         if(newRow == 1)
  //         {
  //           html_value +=  " <div class='add_row'>" +
  //                             "<div class='row'>" +
  //                               "<div class='col-sm-2 text-left'>品項</div>" +
  //                               "<div class='col-sm-1 text-left'>庫存</div>" +
  //                               "<div class='col-sm-2 text-left'>批號</div>" +
  //                               "<div class='col-sm-1 text-left'>" + show_qty_title + "</div>" +
  //                               "<div class='col-sm-1 text-left'>單位</div>" +
  //                               "<div class='col-sm-1 text-left'>上次價格</div>" +
  //                               "<div class='col-sm-1 text-left'>" + show_price_title + "</div>" +
  //                               "<div class='col-sm-1 text-left'>原幣小計</div>" +
  //                               "<div class='col-sm-1 text-left' style='display:none;'>小計</div>" +
  //                               "<div class='col-sm-1 text-left' style='display:none;'>原幣金額</div>" +
  //                               "<div class='col-sm-1 text-left'>總金額</div>" +
  //                               "<div class='col-sm-1 text-left'>功能</div>" +
  //                             "</div>" +
  //                         " </div>";
  //         }
  //         html_value +=  " <div class='add_row' id='div-addrow-" + newRow + "' >" +
  //                           "<div class='row'>" +

  //                             "<input class='form-control' name='itemid-" + newRow + "' id='itemid-" + newRow + "'  value='" + value.item_id + "'  type='hidden'>" +
  //                             "<input class='form-control' name='itemnumber-" + newRow + "' id='itemnumber-" + newRow + "'  value='" + value.item_number + "' type='hidden'>" +
  //                             "<input class='form-control' name='itembrand-" + newRow + "' id='itembrand-" + newRow + "'  value='" + value.item_brand + "' type='hidden'>" +
  //                             "<input class='form-control' name='itemname-" + newRow + "' id='itemname-" + newRow + "'  value='" + value.item_name + "' type='hidden'>" +
  //                             "<input class='form-control' name='itemspec-" + newRow + "' id='itemspec-" + newRow + "'  value='" + value.item_spec + "' type='hidden'>" +

  //                             "<div class='col-sm-2' >" +
  //                               "<div class='input-group'>" +
  //                                 "<input class='form-control' name='item-" + newRow + "' id='item-" + newRow + "'  value='" + value.item_number + " - " + value.item_brand + " - " + value.item_name + " - " + value.item_spec + "' readonly >" +
  //                                 "<span class='input-group-btn'>"+
  //                                   "<button class='btn copy_btn' type='button' onclick=\"copy_text(" + newRow + ")\" ><i class='fa fa-copy'></i></button>" +
  //                                 "</span>" +
  //                               "</div>" +
  //                             "</div>" +

  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control' name='stockqty-" + newRow + "' id='stockqty-" + newRow + "'  value='" + value.item_stock_qty + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-2' id='div_lot_number_" + newRow + "'>" +
  //                               "<input class='form-control' name='lotnumber-" + newRow + "' id='lotnumber-" + newRow + "'  value='" + value.item_lot_number + "' >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control qty' name='qty-" + newRow + "' id='qty-" + newRow + "'  value='" + parseInt(value.item_qty) + "' type='number'>" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control' name='unit-" + newRow + "' id='unit-" + newRow + "' value='" + value.item_unit + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +

  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control price' name='price-" + newRow + "' id='price-" + newRow + "' value='" + parseFloat(value.item_price) + "' >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1'>" +
  //                               "<input class='form-control original_subtotalprice' name='originalsubtotalprice-" + newRow + "' value='" + value.original_subtotal_price + "' id='originalsubtotalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' style='display:none;'>" +
  //                               "<input class='form-control subtotalprice' name='subtotalprice-" + newRow + "' value='" + value.subtotal_price + "' id='subtotalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' style='display:none;'>" +
  //                               "<input class='form-control original_totalprice' name='originaltotalprice-" + newRow + "' value='" + value.original_total_price + "' id='originaltotalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1' >" +
  //                               "<input class='form-control totalprice' name='totalprice-" + newRow + "' value='" + value.total_price + "' id='totalprice-" + newRow + "' readonly >" +
  //                             "</div>" +
  //                             "<div class='col-sm-1'>" +
  //                               "<button class='btn btn-danger btn_close' id='btn-delete-" + newRow + "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
  //                             "</div>" +
  //                           "</div>" +
  //                         "</div>";
  //       });
  //       html_value += "<input type='hidden' name='rowNo' id='rowNo' value='" + newRow + "'>";
  //       $(html_value).appendTo($('#ItemDiv'));

  //       $('button[id^=btn-delete-]').click(function()
  //       {
  //         $("#div-addrow-" + $(this).val()).remove();
  //         SumItemPrice();
  //         return false;
  //       });

  //       SumItemPrice();
  //       add_item_event();

  //       $('#rowNo').val(newRow);
  //     }
  //     else
  //       alert("查無資料");
  //   });

  //   $('#rowNo').val(newRow);
  // }

  function copy_text(row_id)
  {
    var name = $("#itemname-" + row_id).val();
    new Clipboard('.copy_btn',
    {
      text: function(trigger)
      {
        return name;
      }
    });
  }

  // function add_item_return_event(item_qty_select)
  // {
  //   $('.qty , .price').change(function()
  //   {
  //     var data_array = $(this).attr("id").split('-');
  //     var row_id = data_array[1];

  //     if(parseInt($("#qty-" + row_id).val(),10) > item_qty_select[row_id-1])
  //     {
  //       alert("退貨數量不得超過原銷貨數量 " + item_qty_select[row_id-1]);
  //       $("#qty-" + row_id).val(item_qty_select[row_id-1]);
  //     }
  //     else
  //     {
  //       $("#qty-" + row_id).val(parseInt($("#qty-" + row_id).val(),10));
  //       $("#price-" + row_id).val(parseFloat($("#price-" + row_id).val()));
  //     }

  //     var subtotalprice = (accMul(parseInt($("#qty-" + row_id).val(), 10) , parseFloat($("#price-" + row_id).val(), 10)));
  //     var currency_price = parseFloat($("#currency_price").val());
  //     $("#subtotalprice-" + row_id).val(Math.round(subtotalprice));
  //     $("#originalsubtotalprice-" + row_id).val(Math.round(subtotalprice));
  //     SumItemPrice();
  //   });
  // }

  // function add_item_event()
  // {
  //   $('.qty , .price').change(function()
  //   {
  //     var data_array = $(this).attr("id").split('-');
  //     var row_id = data_array[1];

  //     $("#qty-" + row_id).val(parseInt($("#qty-" + row_id).val(),10));
  //     $("#price-" + row_id).val(parseFloat($("#price-" + row_id).val()));

  //     var subtotalprice = (accMul(parseInt($("#qty-" + row_id).val(), 10) , parseFloat($("#price-" + row_id).val(), 10)));
  //     var currency_price = parseFloat($("#currency_price").val());
  //     $("#subtotalprice-" + row_id).val(Math.round(subtotalprice));
  //     $("#originalsubtotalprice-" + row_id).val(Math.round(subtotalprice));
  //     // subtotaltaxprice

  //     SumItemPrice();
  //   });
  // }

  // // 計算 item 總金額
  // function SumItemPrice()
  // {
  //   var totalsum = 0;
  //   var originaltotalsum = 0;
  //   var totaltaxsum = 0;
  //   var originaltotaltaxsum = 0;

  //   var showtotalsum = 0;
  //   var showtotaltaxsum = 0;

  //   var currency_price = $("#currency_price").val();
  //   if(currency_price == "" || currency_price == null)
  //   {
  //     currency_price = 1;
  //   }
  //   else
  //   {
  //     currency_price = parseFloat(currency_price, 10);
  //   }

  //   $.each($(".subtotalprice"), function()
  //   {
  //     var data_array = $(this).attr("id").split('-');
  //     var row_id = data_array[1];
  //     var subtotalprice = (accMul(parseInt($("#qty-" + row_id).val(), 10) , parseFloat($("#price-" + row_id).val(), 10)));
  //     $("#subtotalprice-" + row_id).val(Math.round(subtotalprice * currency_price));

  //     totalsum = accAdd(totalsum , parseFloat($(this).val(), 10));
  //   });
  //   totalsum = Math.round(totalsum);

  //   $.each($(".original_subtotalprice"), function()
  //   {
  //     var data_array = $(this).attr("id").split('-');
  //     var row_id = data_array[1];
  //     var subtotalprice = (accMul(parseInt($("#qty-" + row_id).val(), 10) , parseFloat($("#price-" + row_id).val(), 10)));
  //     $("#originalsubtotalprice-" + row_id).val(Math.round(subtotalprice * 100) / 100);

  //     originaltotalsum = accAdd(originaltotalsum , parseFloat($(this).val(), 10));
  //     // console.log('1:'+ originaltotalsum);
  //   });
  //   originaltotalsum = Math.round(originaltotalsum * 100) / 100;
  //   // console.log('2:'+ originaltotalsum);


  //   if($("#tax").val() == "1") // 應稅
  //   {
  //     totaltaxsum = Math.round(accMul(totalsum , 0.05));
  //     originaltotaltaxsum = Math.round(accMul(originaltotalsum , 0.05) * 100) / 100;

  //     showtotaltaxsum = parseInt(totaltaxsum, 10);
  //     showtotalsum = accAdd(parseInt(totalsum, 10) , parseInt(totaltaxsum, 10));

  //     showoriginaltotaltaxsum = originaltotaltaxsum;
  //     showoriginaltotalsum = accAdd(originaltotalsum , originaltotaltaxsum);
  //   }
  //   else if($("#tax").val() == "2") // 內含
  //   {
  //     totaltaxsum = accSub(parseInt(totalsum, 10) , Math.round(accDiv(totalsum , 1.05)));
  //     originaltotaltaxsum = accSub(originaltotalsum , Math.round(accDiv(originaltotalsum , 1.05) * 100) / 100);

  //     showtotaltaxsum = parseInt(totaltaxsum, 10);
  //     showtotalsum = parseInt(totalsum, 10);

  //     showoriginaltotaltaxsum = originaltotaltaxsum;
  //     showoriginaltotalsum = originaltotalsum
  //   }
  //   else // 免稅
  //   {
  //     totaltaxsum = 0;
  //     originaltotaltaxsum = 0;

  //     showtotaltaxsum = parseInt(totaltaxsum, 10);
  //     showtotalsum = parseInt(totalsum, 10);

  //     showoriginaltotaltaxsum = originaltotaltaxsum;
  //     showoriginaltotalsum = originaltotalsum;
  //   }

  //   // alert(showtotaltaxsum);

  //   $(".totaltaxprice").val(Math.round(showtotaltaxsum));
  //   $(".totalprice").val(Math.round(showtotalsum));
  //   $(".original_totaltaxprice").val(showoriginaltotaltaxsum);
  //   $(".original_totalprice").val(showoriginaltotalsum);
  // }

  // // for SumItemPrice
  // function getItemInfo(row_id , get_type , supplier_id , client_id)
  // {
  //   var item_id = $("#item-" + row_id).val();

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "iteminfo" ,'item_id': item_id ,'type': get_type ,'supplier_id': supplier_id ,'client_id': client_id},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var ShowPrice = 0;
  //       if(get_type == "purchase" || get_type == "order_supllier" || get_type == "return_purchase")
  //         ShowPrice = obj.buy_price;
  //       else
  //         ShowPrice = obj.sell_price1;

  //       var ShowSubPrice = Math.round(parseFloat(ShowPrice, 10));

  //       var currency_price = parseFloat($("#currency_price").val());

  //       $("#itemlable-" + row_id).text("品項 = " + obj.name + " - " + obj.spec);
  //       $("#itemid-" + row_id).val(obj.id);
  //       $("#itemnumber-" + row_id).val(obj.number);
  //       $("#itembrand-" + row_id).val(obj.brand);
  //       $("#itemname-" + row_id).val(obj.name);
  //       $("#itemspec-" + row_id).val(obj.spec);
  //       $("#qty-" + row_id).val("1");
  //       $("#unit-" + row_id).val(obj.small_unit);
  //       $("#price-" + row_id).val(ShowPrice);
  //       $("#subtotalprice-" + row_id).val(Math.round(ShowSubPrice * currency_price));
  //       $("#originalsubtotalprice-" + row_id).val(ShowSubPrice);
  //       $("#totalprice-" + row_id).val(ShowSubPrice);
  //       $("#stockqty-" + row_id).val(obj.stock_qty);

  //       if(get_type == "sales")
  //         get_price_limit(client_id, item_id , row_id);

  //       // 即時取出物品資訊，放到 select 中
  //       // $.ajax(
  //       // {
  //       //   url: "ajax/get_db_info.php",
  //       //   type: "POST",
  //       //   data: {'get_type': "item_lot_number" , "item_id" : item_id},
  //       //   enctype: 'multipart/form-data',
  //       // })
  //       // .done(function( data )
  //       // {
  //       //   var data_array = data.split('@@');
  //       //   if(data_array[0] == "OK")
  //       //   {
  //       //     var obj = jQuery.parseJSON(data_array[1]);
  //       //     $.each( obj, function( key, value )
  //       //     {
  //       //       var text_value = value.number + "-" + value.brand + "-" + value.name + "-" + value.spec;
  //       //       $("#item-" + newRow).append($("<option></option>").attr("value", value.id).text(text_value));
  //       //     });
  //       //   }
  //       // });

  //       SumItemPrice();

  //       if(jQuery.inArray(json_url.func, use_lotnumber_select ) >= 0)
  //       {
  //         getStockLotNumber(row_id, obj.id);
  //       }
  //       else if(get_type == "return_purchase")
  //       {
  //         getStockLotNumber(row_id, obj.id);
  //       }
  //       else if(get_type == "return_sales")
  //       {
  //         getStockLotNumber(row_id, obj.id);
  //       }
  //     }


  //     $("#lastprice-" + row_id).val(data_array[2]);
  //   });
  // }

  // // 取得品項售價設定
  // function get_price_limit(client_id, item_id, row_id)
  // {
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "price_limit" ,'item_id': item_id ,'client_id': client_id},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       $("#price-" + row_id).val(obj.price);
  //       $("#subtotalprice-" + row_id).val(obj.price);
  //       $("#originalsubtotalprice-" + row_id).val(obj.price);
  //       $("#price-" + row_id).attr('readonly', true);
  //     }
  //     else
  //     {
  //       $("#price-" + row_id).attr('readonly', false);
  //     }
  //     SumItemPrice();
  //   });
  // }

  // // 新增分類帳，動態生成view
  // function AddGeneralLedgerRow()
  // {
  //   var curRow = parseInt($('#rowNo').val());
  //   var newRow = curRow + 1;
  //   $(" <div class='' id='div-addrow-" + newRow + "'>" +
  //         "<div class='row'>" +
  //           "<div class='col-sm-2' >" +
  //             "<label>會計科目</label>" +
  //             "<select class='form-control' name='ledger-" + newRow + "' id='ledger-" + newRow + "'>" +
  //             "</select>" +
  //           "</div>" +
  //           "<div class='col-sm-1' >" +
  //             "<label>借貸別</label>" +
  //             "<select class='form-control' name='ledgertype-" + newRow + "' id='ledgertype-" + newRow + "'>" +
  //               "<option value='lend'>借方</option>" +
  //               "<option value='borrow'>貸方</option>" +
  //             "</select>" +
  //           "</div>" +
  //           "<div class='col-sm-2' >" +
  //             "<label>銀行</label>" +
  //             "<select class='form-control' name='ledgerbank-" + newRow + "' id='ledgerbank-" + newRow + "'>" +
  //             "</select>" +
  //           "</div>" +
  //           "<div class='col-sm-5' >" +
  //             "<label>摘要</label>" +
  //             "<input class='form-control' name='ledgerremark-" + newRow + "' id='ledgerremark-" + newRow + "'>" +
  //           "</div>" +
  //           "<div class='col-sm-1' >" +
  //             "<label>金額</label>" +
  //             "<input class='form-control ledgerprice' name='ledgerprice-" + newRow + "' id='ledgerprice-" + newRow + "'   type='number' value='0' >" +
  //           "</div>" +
  //           "<div class='col-sm-1'>" +
  //             "<br>" +
  //             "<button class='btn btn-danger btn_close' id='btn-delete-" + newRow + "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
  //           "</div>" +
  //         "</div>" +
  //       "</div>"
  //   ).appendTo($('#DivAddRow'));

  //   $('button[id^=btn-delete-]').click(function()
  //   {
  //     $("#div-addrow-" + $(this).val()).remove();
  //     sumPrice();
  //     return false;
  //   });

  //   // 即時取出 會計科目 ，放到 select 中
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "ledgerlist"},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       $("#ledger-" + newRow).append(data_array[1]);
  //     }
  //   });

  //   // 即時取出 銀行 ，放到 select 中
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "banklist"},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);
  //       $.each( obj, function( key, value )
  //       {
  //         var text_value = value.name;
  //         $("#ledgerbank-" + newRow).append($("<option></option>").attr("value", value.id).text(text_value));
  //       });
  //     }
  //   });

  //   $('#rowNo').val(newRow);
  // }

  // // 金額格式化，2000 => 2,000
  // function formatNumber(str, glue)
  // {
  //   // 如果傳入必需為數字型參數，不然就噴 isNaN 回去
  //   if(isNaN(str))
  //   {
  //     return NaN;
  //   }
  //   // 決定三個位數的分隔符號
  //   var glue= (typeof glue== 'string') ? glue: ',';
  //   var digits = str.toString().split('.'); // 先分左邊跟小數點
  //   var integerDigits = digits[0].split(""); // 獎整數的部分切割成陣列
  //   var threeDigits = []; // 用來存放3個位數的陣列

  //   // 當數字足夠，從後面取出三個位數，轉成字串塞回 threeDigits
  //   while (integerDigits.length > 3)
  //   {
  //     threeDigits.unshift(integerDigits.splice(integerDigits.length - 3, 3).join(""));
  //   }
  //   threeDigits.unshift(integerDigits.join(""));
  //   digits[0] = threeDigits.join(glue);
  //   return digits.join(".");
  // }

  // // 加工單詳細 dialog
  // function process_detail(id)
  // {
  //   $('#ProcessDivAddRow').html("");
  //   var data_id = id;

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"process" , "id": data_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var ShowStatus = "";
  //       if(obj.status == "created")
  //         ShowStatus = "<span class='btn btn-success btn-block' >已建單</span>";
  //       else if(obj.status == "processing")
  //         ShowStatus = "<span class='btn btn-warning btn-block' >生產加工中</span>";
  //       else if(obj.status == "finished")
  //         ShowStatus = "<span class='btn btn-success btn-block' >已完成</span>";


  //       var ShowAutoStorage = "";
  //       if(obj.auto_storage == "1")
  //         ShowAutoStorage = "是";
  //       else
  //         ShowAutoStorage = "否";

  //       $("#ProcessModalNumber").html(obj.number);
  //       $("#ProcessModalProcessPlan").html(obj.process_plan_name);
  //       $("#ProcessModalStartTime").html(obj.start_time);
  //       $("#ProcessModalEndTime").html(obj.end_time);
  //       $("#ProcessModalAutoStorage").html(ShowAutoStorage);
  //       $("#ProcessModalStatus").html(ShowStatus);
  //       $("#ProcessModalRemark").html(obj.remark);
  //     }
  //   });

  //   $("<hr>").appendTo($('#ProcessDivAddRow'));

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"process_detail" , "id": data_id , "type": "in"},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<div class='col-sm-12'><i class='fa fa-th-large'></i> 來源品項</div>" +
  //                         "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th class='col-sm-1'>編號</th>" +
  //                               "<th class='col-sm-1'>品牌</th>" +
  //                               "<th class='col-sm-5'>名稱</th>" +
  //                               "<th class='col-sm-3'>規格</th>" +
  //                               "<th class='col-sm-1'>數量</th>" +
  //                               "<th class='col-sm-1'>單位</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);
  //       $.each( obj, function( key, value )
  //       {
  //         html_value +=  "<tr>" +
  //                         "<td>" + value.item_number + "</td>" +
  //                         "<td>" + value.item_brand + "</td>" +
  //                         "<td>" + value.item_name + "</td>" +
  //                         "<td>" + value.item_spec + "</td>" +
  //                         "<td>" + value.item_qty + "</td>" +
  //                         "<td>" + value.item_unit + "</td>" +
  //                       "</tr>";
  //       });

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#ProcessDivAddRow'));
  //     }
  //   });

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"process_detail" , "id": data_id , "type": "out"},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<div class='col-sm-12'><i class='fa fa-th-large'></i> 產出品項</div>" +
  //                         "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th class='col-sm-1'>編號</th>" +
  //                               "<th class='col-sm-1'>品牌</th>" +
  //                               "<th class='col-sm-5'>名稱</th>" +
  //                               "<th class='col-sm-3'>規格</th>" +
  //                               "<th class='col-sm-1'>數量</th>" +
  //                               "<th class='col-sm-1'>單位</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);
  //       $.each( obj, function( key, value )
  //       {
  //         html_value +=  "<tr>" +
  //                         "<td>" + value.item_number + "</td>" +
  //                         "<td>" + value.item_brand + "</td>" +
  //                         "<td>" + value.item_name + "</td>" +
  //                         "<td>" + value.item_spec + "</td>" +
  //                         "<td>" + value.item_qty + "</td>" +
  //                         "<td>" + value.item_unit + "</td>" +
  //                       "</tr>";
  //       });

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#ProcessDivAddRow'));
  //     }
  //   });
  // }

  // function order_detail(get_type , order_id)
  // {
  //   $('#OrderDivAddRow').html("");

  //   $("#get_order_modal_id").val(order_id);

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":get_type , "id": order_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var ShowOrderName = obj.client_company_number + " - " + obj.client_name;
  //       if(get_type == "purchase" || get_type == "purchase_return")
  //         ShowOrderName = obj.supplier_company_number + " - " + obj.supplier_name;

  //       $("#OrderModalNumber").html(obj.number);
  //       $("#OrderModalName").html(ShowOrderName);
  //       $("#OrderModalTax").html(obj.total_tax_price);
  //       $("#OrderModalTotalPrice").html(obj.total_price);
  //       $("#OrderModalReceiverName").html(obj.receiver_name);
  //       $("#OrderModalReceiverAddress").html(obj.receiver_address);
  //       $("#OrderModalInvoiceName").html(obj.invoice_name);
  //       $("#OrderModalInvoiceAddress").html(obj.invoice_address);
  //       $("#OrderModalInvoiceNumber").html(obj.invoice_number);
  //       $("#OrderModalInvoiceDate").html(obj.invoice_date);
  //       $("#OrderModalRemark").html(obj.remark);
  //     }
  //   });

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type": get_type + "_detail" , "id": order_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th>編號</th>" +
  //                               "<th>品牌</th>" +
  //                               "<th>名稱</th>" +
  //                               "<th>規格</th>" +
  //                               "<th>批號</th>" +
  //                               "<th>數量</th>" +
  //                               "<th>單位</th>" +
  //                               "<th>單價</th>" +
  //                               "<th>匯率</th>" +
  //                               "<th>小計</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var sum_subtotal_price = 0;
  //       $.each( obj, function( key, value )
  //       {
  //         html_value +=  "<tr>" +
  //                         "<td>" + value.item_number + "</td>" +
  //                         "<td>" + value.item_brand + "</td>" +
  //                         "<td>" + value.item_name + "</td>" +
  //                         "<td>" + value.item_spec + "</td>" +
  //                         "<td>" + value.item_lot_number + "</td>" +
  //                         "<td>" + value.item_qty + "</td>" +
  //                         "<td>" + value.item_unit + "</td>" +
  //                         "<td>" + value.item_price + "</td>" +
  //                         "<td>" + value.currency_price + "</td>" +
  //                         "<td>" + value.subtotal_price + "</td>" +
  //                       "</tr>";
  //         sum_subtotal_price = parseFloat(sum_subtotal_price) + parseFloat(value.subtotal_price);
  //       });

  //       html_value +=  "<tr>" +
  //                         "<td colspan='6'></td>" +
  //                         "<td colspan='2'>合計</td>" +
  //                         "<td colspan='1'>" + Math.round(sum_subtotal_price) + "</td>" +
  //                       "</tr>";

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#OrderDivAddRow'));
  //     }
  //   });
  // }
  // function getLocation()
  // {
  //   var geo_options =
  //   {
  //     enableHighAccuracy  : true,
  //     maximumAge          : 30000,
  //     timeout             : 27000
  //   };

  //   if (navigator.geolocation)
  //   {
  //     navigator.geolocation.getCurrentPosition(get_position, show_geo_error, geo_options);
  //   }
  //   else
  //   {
  //     // Geolocation is not supported by this browser.
  //     alert("不支援定位");
  //   }
  // }

  // function get_position(position)
  // {
  //   var lat, lng, address;

  //   lat = position.coords.latitude;
  //   lng = position.coords.longitude;

  //   $("#get_lat").val(lat);
  //   $("#get_lng").val(lng);

  //   var geocoder = new google.maps.Geocoder();

  //   // google.maps.LatLng 物件
  //   var coord = new google.maps.LatLng(lat, lng);

  //   // 傳入 latLng 資訊至 geocoder.geocode
  //   geocoder.geocode({'latLng': coord }, function(results, status)
  //   {
  //     if (status === google.maps.GeocoderStatus.OK) // 如果有資料就會回傳
  //     {
  //       if (results)
  //       {
  //         address = results[0].formatted_address;

  //         $("#get_address").val(address);

  //         insert_sign($("#get_location_type").val() , lat , lng , address, $("#SignModalRemark").val());
  //         // console.log(address);
  //         // $("#mapholder").append(address + "<br/>");
  //       }
  //     }
  //     else // 經緯度資訊錯誤
  //     {
  //       alert("Reverse Geocoding failed because: " + status);
  //     }
  //   });
  // }

  // function insert_sign(type , lat , lng , address , remark)
  // {
  //   // alert(type + " : " + lat + " : " + lng + " : " + address);
  //   $.ajax(
  //   {
  //     url: "ajax/update_db_info.php",
  //     type: "POST",
  //     data: {'get_type': "sign" , 'type':type , 'lat':lat , 'lng':lng , 'address':address, 'remark':remark},
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       location.reload();
  //     }
  //   });
  // }

  // function show_geo_error(error)
  // {
  //   var error_msg = "";
  //   switch(error.code)
  //   {
  //       case error.PERMISSION_DENIED:
  //           error_msg = "User denied the request for Geolocation."
  //           break;
  //       case error.POSITION_UNAVAILABLE:
  //           error_msg = "Location information is unavailable."
  //           break;
  //       case error.TIMEOUT:
  //           error_msg = "The request to get user location timed out."
  //           break;
  //       case error.UNKNOWN_ERROR:
  //           error_msg = "An unknown error occurred."
  //           break;
  //   }
  //   alert(error_msg);
  // }

  // function getContractTypeName(getValue)
  // {
  //   var value = "";
  //   switch(getValue)
  //   {
  //     case "lease":
  //       value = "租賃";
  //       break;
  //     case "product_project":
  //       value = "產品專案";
  //       break;
  //     case "project":
  //       value = "一般專案";
  //       break;
  //     case "other":
  //       value = "其他";
  //       break;
  //     default :
  //       value = "其他";
  //       break;
  //   }
  //   return value;
  // }

  // function getContractPaymentName(getValue)
  // {
  //   var value = "";
  //   switch(getValue)
  //   {
  //     case "installments":
  //       value = "分期繳";
  //       break;
  //     case "monthly":
  //       value = "月繳";
  //       break;
  //     case "quarterly":
  //       value = "季繳";
  //       break;
  //     case "half-year'":
  //       value = "半年繳";
  //       break;
  //     case "year":
  //       value = "年繳";
  //       break;
  //     case "two-years'":
  //       value = "兩年繳";
  //       break;
  //     case "three-years'":
  //       value = "三年繳";
  //       break;
  //     case "other'":
  //       value = "其他";
  //       break;
  //     default :
  //       value = "其他";
  //       break;
  //   }

  //   return value
  // }

  // function open_contract_pdf(annex_file_name)
  // {
  //   if(annex_file_name == "")
  //     alert("無附件");
  //   else
  //     window.open('contract_file/pdf/' + annex_file_name, '_blank');

  //   return false;
  // }

  // function open_resigned_image(annex_file_name)
  // {
  //   if(annex_file_name == "")
  //     alert("無附件");
  //   else
  //     window.open('resigned_file/image/' + annex_file_name, '_blank');

  //   return false;
  // }

  // function open_vacation_image(annex_file_name)
  // {
  //   if(annex_file_name == "")
  //     alert("無附件");
  //   else
  //     window.open('vacation_file/image/' + annex_file_name, '_blank');

  //   return false;
  // }

  // function contract_record_detail(id)
  // {
  //   $('#ContractRecordDivAddRow').html("");
  //   var data_id = id;

  //   $("#get_modal_contract_record_id").val(data_id);

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"contract_record_detail" , "id": data_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th>護字號</th>" +
  //                               "<th>姓名</th>" +
  //                               "<th>性別</th>" +
  //                               "<th>生日</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);
  //       var count_obj = 0;
  //       $.each( obj, function( key, value )
  //       {
  //         count_obj++;
  //         var ShowGender = "其他";
  //         if(value.gender == "male")
  //           ShowGender = "男";
  //         else if(value.gender == "female")
  //           ShowGender = "女";

  //         html_value +=  "<tr>" +
  //                         "<td>" + value.hospital_number_display + "</td>" +
  //                         "<td>" + value.name + "</td>" +
  //                         "<td>" + ShowGender + "</td>" +
  //                         "<td>" + value.birthday + "</td>" +
  //                       "</tr>";
  //       });


  //       html_value +=  "<tr>" +
  //                       "<td colspan='2'></td>" +
  //                       "<td>總床數</td>" +
  //                       "<td>" + count_obj + "</td>" +
  //                     "</tr>";

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#ContractRecordDivAddRow'));
  //     }
  //     else
  //     {
  //       $('#contract_record_detail').modal('toggle');
  //       alert("查無資料");
  //     }
  //   });
  // }

  // // 薪資異動明細
  // function salary_setting_log(user_id)
  // {
  //   $("#salary_setting_log_list").html("");
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"salary_setting_log" , "user_id": user_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');

  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th width='20%'>異動時間</th>" +
  //                               "<th>異動說明</th>" +
  //                               "<th width='10%'>經辦人</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var sum_subtotal_price = 0;
  //       $.each( obj, function( key, value )
  //       {
  //         html_value +=  "<tr>" +
  //                         "<td>" + value.created_at + "</td>" +
  //                         "<td>" + value.remark.replace(/\n/g, "<br />") + "</td>" +
  //                         "<td>" + value.execute_user_name + "</td>" +
  //                       "</tr>";
  //         sum_subtotal_price = parseFloat(sum_subtotal_price) + parseFloat(value.subtotal_price);
  //       });
  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#salary_setting_log_list'));
  //     }
  //     else if(data_array[0] == "no_data")
  //     {
  //       var html_value = "無異動資料";
  //       $("#salary_setting_log_list").html(html_value);
  //     }
  //   });
  //   return false;
  // }

  // function client_detail(id)
  // {
  //   $('#ModalClientDetailDivAddRow').html("");
  //   var data_id = id;

  //   $("#get_modal_client_detail_id").val(data_id);

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"clientinfo" , "id": data_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       $("#ModalClientDetailDisplayNumber").html(obj.display_number);

  //       $("#ModalClientDetailCompanyNumber").html(obj.company_number);
  //       $("#ModalClientDetailName").html(obj.name);
  //       $("#ModalClientDetailShortName").html(obj.short_name);
  //       $("#ModalClientDetailAddress").html("(" + obj.postal_code + ")" + obj.address);
  //       $("#ModalClientDetailTelephone").html(obj.telephone);
  //       $("#ModalClientDetailCellPhone").html(obj.cell_phone);
  //       $("#ModalClientDetailFax").html(obj.fax);
  //       $("#ModalClientDetailEmail").html(obj.email);
  //       $("#ModalClientDetailContactName").html(obj.contact_name);
  //       $("#ModalClientDetailPayConditionName").html(obj.pay_condition_name);
  //       $("#ModalClientDetailEmployeeName").html(obj.employee_name);
  //       $("#ModalClientDetailAreaName").html(obj.area_name);
  //       $("#ModalClientDetailRemark").html(obj.remark.replace(/\n/g, "<br />"));

  //       $("#ModalClientDetailInvoiceCompanyNumber").html(obj.invoice_company_number);
  //       $("#ModalClientDetailInvoiceName").html(obj.invoice_name);
  //       $("#ModalClientDetailInvoiceAddress").html(obj.invoice_address);
  //       $("#ModalClientDetailInvoiceNumber").html(obj.invoice_number);
  //       $("#ModalClientDetailInvoiceEmail").html(obj.invoice_email);
  //       $("#ModalClientDetailInvoiceCellPhone").html(obj.invoice_cell_phone);
  //     }
  //   });

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"contact_list" , "id": data_id , "type": "Client" },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<table class='table table-striped table-bordered table-hover' style='width:98%' id='table_list2' align='center'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th colspan='6' class='text-center'>其他聯絡人</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th>姓名</th>" +
  //                               "<th>電話</th>" +
  //                               "<th>手機</th>" +
  //                               "<th>傳真</th>" +
  //                               "<th>信箱</th>" +
  //                               "<th>備註</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var sum_subtotal_price = 0;
  //       $.each( obj, function( key, value )
  //       {
  //         html_value +=  "<tr>" +
  //                         "<td>" + value.name + "</td>" +
  //                         "<td>" + value.telephone + "</td>" +
  //                         "<td>" + value.cell_phone + "</td>" +
  //                         "<td>" + value.fax + "</td>" +
  //                         "<td>" + value.email + "</td>" +
  //                         "<td>" + value.remark + "</td>" +
  //                       "</tr>";
  //       });

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#ModalClientDetailDivAddRow'));
  //     }
  //   });
  // }

  // function supplier_detail(id)
  // {
  //   $('#ModalSupplierDetailDivAddRow').html("");
  //   var data_id = id;

  //   $("#get_modal_supplier_detail_id").val(data_id);

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"supplierinfo" , "id": data_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       $("#ModalSupplierDetailDisplayNumber").html(obj.display_number);

  //       $("#ModalSupplierDetailCompanyNumber").html(obj.company_number);
  //       $("#ModalSupplierDetailName").html(obj.name);
  //       $("#ModalSupplierDetailShortName").html(obj.short_name);
  //       $("#ModalSupplierDetailAddress").html("(" + obj.postal_code + ")" + obj.address);
  //       $("#ModalSupplierDetailTelephone").html(obj.telephone);
  //       $("#ModalSupplierDetailCellPhone").html(obj.cell_phone);
  //       $("#ModalSupplierDetailFax").html(obj.fax);
  //       $("#ModalSupplierDetailEmail").html(obj.email);
  //       $("#ModalSupplierDetailContactName").html(obj.contact_name);
  //       $("#ModalSupplierDetailPayConditionName").html(obj.pay_condition_name);
  //       $("#ModalSupplierDetailEmployeeName").html(obj.employee_name);
  //       $("#ModalSupplierDetailAreaName").html(obj.area_name);
  //       $("#ModalSupplierDetailRemark").html(obj.remark.replace(/\n/g, "<br />"));

  //       $("#ModalSupplierDetailBankName").html(obj.bank_name);
  //       $("#ModalSupplierDetailBankBranch").html(obj.bank_branch);
  //       $("#ModalSupplierDetailBankAccountName").html(obj.bank_account_name);
  //       $("#ModalSupplierDetailBankNumber").html(obj.bank_number);

  //     }
  //   });

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"contact_list" , "id": data_id , "type": "Supplier" },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<table class='table table-striped table-bordered table-hover' style='width:98%' id='table_list2' align='center'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th colspan='6' class='text-center'>其他聯絡人</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th>姓名</th>" +
  //                               "<th>電話</th>" +
  //                               "<th>手機</th>" +
  //                               "<th>傳真</th>" +
  //                               "<th>信箱</th>" +
  //                               "<th>備註</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var sum_subtotal_price = 0;
  //       $.each( obj, function( key, value )
  //       {
  //         html_value +=  "<tr>" +
  //                         "<td>" + value.name + "</td>" +
  //                         "<td>" + value.telephone + "</td>" +
  //                         "<td>" + value.cell_phone + "</td>" +
  //                         "<td>" + value.fax + "</td>" +
  //                         "<td>" + value.email + "</td>" +
  //                         "<td>" + value.remark + "</td>" +
  //                       "</tr>";
  //       });

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#ModalSupplierDetailDivAddRow'));
  //     }
  //   });
  // }

  // function salary_record_detail(id)
  // {
  //   $('#DivAddInsuranceRow').html("");
  //   $('#DivAddFamilyRow').html("");
  //   $('#DivAddSalaryRow').html("");

  //   var data_id = id;
  //   $("#get_salary_record_modal_id").val(data_id);

  //   // 基本資料
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"salary_record_info" , "salary_record_id": data_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var ShowActive = "";
  //       if(obj.active == "1")
  //       {
  //         ShowActive = "<span class='btn btn-success' >正常</span>";
  //         $("#btn-deactivate").show();
  //       }
  //       else
  //       {
  //         ShowActive = "<span class='btn btn-danger btn-block' >已作廢</span>";
  //         $("#btn-deactivate").hide();
  //       }

  //       var ShowSalaryType = "";
  //       if(obj.salary_type == "monthly")
  //         ShowSalaryType = "月薪";
  //       else if(obj.salary_type == "hourly")
  //         ShowSalaryType = "月薪";

  //       $("#ModalSalaryRecordName").html(obj.receiver_user_name);
  //       $("#ModalSalaryRecordMonth").html(obj.salary_month);
  //       $("#ModalSalaryRecordType").html(ShowSalaryType);
  //       $("#ModalSalaryRecordTradeDate").html(obj.trade_date);
  //       $("#ModalSalaryRecordBasicPrice").html(formatNumber(obj.basic_price,','));
  //       $("#ModalSalaryRecordBasicPriceCount").html(obj.basic_price_count);
  //       $("#ModalSalaryRecordOvertimeHours").html(obj.overtime_hours);
  //       $("#ModalSalaryRecordVacationHours").html(obj.vacation_hours);
  //       $("#ModalSalaryRecordOvertimePrice").html(obj.overtime_price);
  //       $("#ModalSalaryRecordVacationPrice").html(obj.vacation_price);
  //       $("#ModalSalaryRecordTotalPrice").html("<span style='font-size:18px;'>"+formatNumber(obj.total_price,',')+"</span>");
  //       $("#ModalSalaryRecordActive").html(ShowActive);
  //       $("#ModalSalaryRecordRemark").html(obj.remark);
  //     }
  //   });

  //   $('#DivAddVacationRow').html("");
  //   // 請假/加班紀錄
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"salary_record_vacation_list" , "salary_record_id": data_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th>類別</th>" +
  //                               "<th>事由</th>" +
  //                               "<th>起始時間</th>" +
  //                               "<th>結束時間</th>" +
  //                               "<th>核定時數</th>" +
  //                               "<th>申請時間</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var sum_subtotal_price = 0;
  //       $.each( obj, function( key, value )
  //       {
  //         html_value +=  "<tr>" +
  //                         "<td>" + value.vacation_type + "</td>" +
  //                         "<td>" + value.cause_text + "</td>" +
  //                         "<td>" + value.start_time + "</td>" +
  //                         "<td>" + value.end_time + "</td>" +
  //                         "<td>" + value.allow_times + "</td>" +
  //                         "<td>" + value.created_at + "</td>" +
  //                       "</tr>";
  //       });

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#DivAddVacationRow'));
  //     }
  //   });

  //   $('#DivAddInsuranceRow').html("");
  //   // 保險費
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"salary_record_detail_list" , "salary_record_id": data_id , "table_name": "salary_setting_insurance" },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th>類型</th>" +
  //                               "<th>名稱</th>" +
  //                               "<th>個人負擔</th>" +
  //                               "<th>公司負擔</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var sum_subtotal_price = 0;
  //       $.each( obj, function( key, value )
  //       {
  //         html_value +=  "<tr>" +
  //                         "<td>" + value.insurance_type_name + "</td>" +
  //                         "<td>" + value.insurance_name + "</td>" +
  //                         "<td>" + formatNumber(value.price,',') + "</td>" +
  //                         "<td>" + formatNumber(value.company_price,',') + "</td>" +
  //                       "</tr>";
  //       });

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#DivAddInsuranceRow'));
  //     }
  //   });

  //   $('#DivAddFamilyRow').html("");
  //   // 眷屬加保
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"salary_record_detail_list" , "salary_record_id": data_id , "table_name": "salary_setting_insurance_family" },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th>關係</th>" +
  //                               "<th>姓名</th>" +
  //                               "<th>金額</th>" +
  //                               "<th>備註</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var sum_subtotal_price = 0;
  //       $.each( obj, function( key, value )
  //       {
  //         html_value +=  "<tr>" +
  //                         "<td>" + value.relationship + "</td>" +
  //                         "<td>" + value.name + "</td>" +
  //                         "<td>" + formatNumber(value.subtotal_price,',') + "</td>" +
  //                         "<td>" + value.remark + "</td>" +
  //                       "</tr>";
  //       });

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#DivAddFamilyRow'));
  //     }
  //   });

  //   $('#DivAddSalaryRow').html("");
  //   // 薪資項目
  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":"salary_record_detail_list" , "salary_record_id": data_id , "table_name": "salary_item" },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var html_value = "<table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>" +
  //                           "<thead>" +
  //                             "<tr>" +
  //                               "<th>項目</th>" +
  //                               "<th>稅別</th>" +
  //                               "<th>金額</th>" +
  //                               "<th>數量</th>" +
  //                               "<th>小計</th>" +
  //                               "<th>備註</th>" +
  //                             "</tr>" +
  //                           "</thead>" +
  //                           "<tbody>";

  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var sum_subtotal_price = 0;
  //       $.each( obj, function( key, value )
  //       {
  //         var ShowTax = '未稅';
  //         if(value.tax == '0')
  //           ShowTax = '未稅';
  //         else
  //           ShowTax = '應稅';

  //         html_value +=  "<tr>" +
  //                         "<td>" + value.name + "</td>" +
  //                         "<td>" + ShowTax + "</td>" +
  //                         "<td>" + formatNumber(value.price,',') + "</td>" +
  //                         "<td>" + value.qty + "</td>" +
  //                         "<td>" + formatNumber(value.subtotal_price,',') + "</td>" +
  //                         "<td>" + value.remark + "</td>" +
  //                       "</tr>";
  //       });

  //       html_value += "</tbody></table>";
  //       $(html_value).appendTo($('#DivAddSalaryRow'));
  //     }
  //   });

  //   return false;
  // }

  // function submit_invoice(table_id, get_type)
  // {
  //   var data_id = table_id;
  //   $("#get_modal_invoice_id").val(data_id);

  //   $("#btn-checkInvoice").html("<i class='fa fa-fw fa-save'></i> 確認開立");
  //   $("#btn-checkInvoice").attr("disabled", false);

  //   $.ajax(
  //   {
  //     url: "ajax/get_db_info.php",
  //     type: "POST",
  //     data: {"get_type":get_type , "id": data_id },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var ShowInvoiceBtn = 1;

  //       var ShowCompanyNumber = "";
  //       if(obj.client_type_name != "個人")
  //         ShowCompanyNumber = obj.client_invoice_company_number;
  //       else  if(obj.client_type == "個人")
  //         ShowCompanyNumber = "";

  //       var ShowClientAddress = "";
  //       if(obj.client_invoice_address == "")
  //       {
  //         ShowInvoiceBtn = 0;
  //         ShowClientAddress = "<span class='text-danger'>(必填)請先將客戶資料填寫完整</span>";
  //       }
  //       else
  //         ShowClientAddress = "<input type='text' name='api_invoice_client_address' id='api_invoice_client_address' value='" + obj.client_invoice_address + "'>";

  //       var ShowClientEmail = "";
  //       if(obj.client_invoice_email == "")
  //         ShowClientEmail = "<input type='text' name='api_invoice_client_email' id='api_invoice_client_email' value='" + $("#default_email").val() + "'><span class='text-danger'>此為預設email</span>";
  //       else
  //         ShowClientEmail = "<input type='text' name='api_invoice_client_email' id='api_invoice_client_email' value='" + obj.client_invoice_email + "'>";

  //       var ShowClientName = "";
  //       if(obj.client_invoice_name == "")
  //       {
  //         ShowInvoiceBtn = 0;
  //         ShowClientName = "<span class='text-danger'>(必填)請先將客戶資料填寫完整</span>";
  //       }
  //       else
  //         ShowClientName = "<input type='text' name='api_invoice_client_name' id='api_invoice_client_name' value='" + obj.client_invoice_name + "'>";

  //       $("#SubmitInvoiceModalClientCompanyNumber").html("<input type='text' name='api_invoice_company_number' id='api_invoice_company_number' value='" + ShowCompanyNumber + "'>");
  //       $("#SubmitInvoiceModalClientName").html(ShowClientName);
  //       $("#SubmitInvoiceModalClientAddress").html(ShowClientAddress);
  //       $("#SubmitInvoiceModalClientPhone").html(obj.client_invoice_cell_phone);
  //       $("#SubmitInvoiceModalClientEmail").html(ShowClientEmail);
  //       $("#SubmitInvoiceModalClientRemark").html(obj.client_remark);
  //       $("#SubmitInvoiceModalTotalPrice").html(obj.total_price);

  //       if(ShowInvoiceBtn)
  //         $("#btn-checkInvoice").show();
  //       else
  //         $("#btn-checkInvoice").hide();
  //     }
  //   });
  // }

  // function send_invoice_api(get_type, table_id, company_number, client_name, client_address, client_phone, client_email, invoice_remark)
  // {
  //   $.ajax(
  //   {
  //     url: "ajax/api_ecpay.php",
  //     type: "POST",
  //     data: {  "get_type"        : get_type,
  //             "id"              : table_id,
  //             "company_number"  : company_number,
  //             "client_name"      : client_name,
  //             "client_address"  : client_address,
  //             "client_phone"    : client_phone,
  //             "client_email"    : client_email,
  //             "invoice_remark"  : invoice_remark
  //           },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     // $("#api_invoice_remark").val(data);

  //     $("#btn-checkInvoice").html("<i class='fa fa-fw fa-save'></i> 確認開立");
  //     $("#btn-checkInvoice").attr("disabled", false);

  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       if(get_type == "contract_record_submit")
  //       {
  //         send_line_message("contract_record_message", table_id);
  //       }
  //       alert("發票開立成功");
  //       location.reload();
  //     }
  //     else
  //     {
  //       alert(data_array[1]);
  //     }
  //   });
  // }

  // function send_line_message(get_type, table_id)
  // {
  //   $.ajax(
  //   {
  //     url: "ajax/send_line.php",
  //     type: "POST",
  //     data: {  "get_type"  : get_type,
  //             "table_id"  : table_id
  //           },
  //     enctype: 'multipart/form-data',
  //     async: false
  //   })
  //   .done(function( data )
  //   {
  //     console.log(data);
  //     // var data_array = data.split('@@');
  //     // if(data_array[0] == "OK")
  //     // {
  //     // }
  //   });
  // }

  // function close_invoice_api(table_id, table_name, close_remark)
  // {
  //   $.ajax(
  //   {
  //     url: "ajax/api_ecpay.php",
  //     type: "POST",
  //     data: {  "get_type"        :"close_invoice",
  //             "table_id"        : table_id,
  //             "table_name"      : table_name,
  //             "close_remark"    : close_remark
  //           },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);
  //       $("#btn-closeInvoice").attr("disabled" , true);
  //       alert("發票已作廢")
  //       location.reload();
  //     }
  //     else
  //     {
  //       alert(data_array[1]);
  //       $("#btn-closeInvoice").attr("disabled" , false);
  //     }
  //   });
  // }

  // function invoice_detail_api(table_id, table_name)
  // {
  //   $("#get_modal_invoice_detail_id").val(table_id);

  //   var ShowLoadingApi = "<i class='fa fa-fw fa-spinner fa-spin'></i> 連線中..";

  //   $("#InvoiceDetailModalInvoiceNumber").html(ShowLoadingApi); // 發票號碼
  //   $("#InvoiceDetailModalClientCompanyNumber").html(ShowLoadingApi); // 買方統編
  //   $("#InvoiceDetailModalClientName").html(ShowLoadingApi); // 客戶名稱
  //   $("#InvoiceDetailModalClientPhone").html(ShowLoadingApi); // 客戶電話
  //   $("#InvoiceDetailModalClientEmail").html(ShowLoadingApi); // 客戶信箱
  //   $("#InvoiceDetailModalClientAddress").html(ShowLoadingApi); // 客戶地址
  //   $("#InvoiceDetailModalTaxPrice").html(ShowLoadingApi); // 發票金額
  //   $("#InvoiceDetailModalTotalPrice").html(ShowLoadingApi); // 發票金額
  //   $("#InvoiceDetailModalRandomNumber").html(ShowLoadingApi); // 發票隨機碼
  //   $("#InvoiceDetailModalInvoiceCategory").html(ShowLoadingApi); // 發票類別 B2B：有統編 B2C：無統編
  //   $("#InvoiceDetailModalCreateDate").html(ShowLoadingApi); // 開立時間
  //   $("#InvoiceDetailModalCreateStatus").html(ShowLoadingApi); // 開立狀態 1:發票開立 0:發票註銷
  //   $("#InvoiceDetailModalInvalidStatus").html(ShowLoadingApi); // 作廢狀態 1:已作廢時 0:未作廢
  //   $("#InvoiceDetailModalUploadDate").html(ShowLoadingApi); // 上傳時間
  //   $("#InvoiceDetailModalUploadStatus").html(ShowLoadingApi); // 上傳狀態 1:已上傳 0:未上傳
  //   $("#InvoiceDetailModalTurnkeyStatus").html(ShowLoadingApi); // 上傳狀態(國稅局)C:成功 E:失敗 G:處理中
  //   $("#InvoiceDetailModalRemark").html(ShowLoadingApi); // 發票備註

  //   $.ajax(
  //   {
  //     url: "ajax/api_ecpay.php",
  //     type: "POST",
  //     data: {  "get_type"        :"query_invoice",
  //             "table_id"        : table_id,
  //             "table_name"      : table_name
  //           },
  //     enctype: 'multipart/form-data',
  //   })
  //   .done(function( data )
  //   {
  //     var data_array = data.split('@@');
  //     if(data_array[0] == "OK")
  //     {
  //       var obj = jQuery.parseJSON(data_array[1]);

  //       var Show_IIS_Issue_Status = "";
  //       if(obj.IIS_Issue_Status == "1") Show_IIS_Issue_Status = "已開立";
  //       else if(obj.IIS_Issue_Status == "2") Show_IIS_Issue_Status = "已註銷";

  //       var Show_IIS_Invalid_Status = "";
  //       if(obj.IIS_Invalid_Status == "1") Show_IIS_Invalid_Status = "已作廢";
  //       else if(obj.IIS_Invalid_Status == "2") Show_IIS_Invalid_Status = "未作廢";

  //       var Show_IIS_Upload_Status = "";
  //       if(obj.IIS_Upload_Status == "1") Show_IIS_Upload_Status = "已上傳";
  //       else if(obj.IIS_Upload_Status == "2") Show_IIS_Upload_Status = "未上傳";

  //       var Show_IIS_Turnkey_Status = "";
  //       if(obj.IIS_Turnkey_Status == "C") Show_IIS_Turnkey_Status = "成功";
  //       else if(obj.IIS_Turnkey_Status == "E") Show_IIS_Turnkey_Status = "失敗";
  //       else if(obj.IIS_Turnkey_Status == "G") Show_IIS_Turnkey_Status = "處理中";


  //       $("#InvoiceDetailModalInvoiceNumber").html(obj.IIS_Number); // 發票號碼
  //       $("#InvoiceDetailModalClientCompanyNumber").html(obj.IIS_Identifier); // 買方統編
  //       $("#InvoiceDetailModalClientName").html(obj.IIS_Customer_Name); // 客戶名稱
  //       $("#InvoiceDetailModalClientPhone").html(obj.IIS_Customer_Phone); // 客戶電話
  //       $("#InvoiceDetailModalClientEmail").html(obj.IIS_Customer_Email); // 客戶信箱
  //       $("#InvoiceDetailModalClientAddress").html(obj.IIS_Customer_Addr); // 客戶地址
  //       $("#InvoiceDetailModalTaxPrice").html(obj.IIS_Tax_Amount); // 發票金額
  //       $("#InvoiceDetailModalTotalPrice").html(obj.IIS_Sales_Amount); // 發票金額
  //       $("#InvoiceDetailModalRandomNumber").html(obj.IIS_Random_Number); // 發票隨機碼
  //       $("#InvoiceDetailModalInvoiceCategory").html(obj.IIS_Category); // 發票類別 B2B：有統編 B2C：無統編
  //       $("#InvoiceDetailModalCreateDate").html(obj.IIS_Create_Date); // 開立時間
  //       $("#InvoiceDetailModalCreateStatus").html(Show_IIS_Issue_Status); // 開立狀態 1:發票開立 0:發票註銷
  //       $("#InvoiceDetailModalInvalidStatus").html(Show_IIS_Invalid_Status); // 作廢狀態 1:已作廢時 0:未作廢
  //       $("#InvoiceDetailModalUploadDate").html(obj.IIS_Upload_Date); // 上傳時間
  //       $("#InvoiceDetailModalUploadStatus").html(Show_IIS_Upload_Status); // 上傳狀態 1:已上傳 0:未上傳
  //       $("#InvoiceDetailModalTurnkeyStatus").html(Show_IIS_Turnkey_Status); // 上傳狀態(國稅局)C:成功 E:失敗 G:處理中
  //       $("#InvoiceDetailModalRemark").html(obj.InvoiceRemark); // 發票備註

  //       if(obj.IIS_Invalid_Status != "1")
  //         $("#btn-closeInvoice").attr("disabled" , false);
  //     }
  //     else
  //     {
  //       alert(data_array[1]);

  //       $("#btn-closeInvoice").attr("disabled" , true);
  //     }
  //   });
  // }
