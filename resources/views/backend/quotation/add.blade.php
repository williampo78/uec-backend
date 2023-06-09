@extends('backend.layouts.master')
@section('title', $act == 'upd' ? '編輯報價單' : '新增報價單')
@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body">
                    <form role="form" id="new-form" method="post"
                        action="{{ $act == 'upd' ? route('quotation.update', $id) : route('quotation.store') }}"
                        enctype="multipart/form-data">
                        @if ($act == 'upd')
                            @method('PUT')
                        @endif

                        @csrf
                        <div class="row">
                            <!-- 欄位 -->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group" id="supplier">
                                            <label for="supplier">供應商<span class="text-red">*</span></label>
                                            <select class="form-control js-select2-department" name="supplier_id"
                                                id="supplier_id">
                                                @if(!isset($quotation['supplier_id']))
                                                <option disabled selected> </option>
                                                @endif
                                                @foreach ($supplier as $v)
                                                    <option value='{{ $v['id'] }}'
                                                        {{ isset($quotation['supplier_id']) && $quotation['supplier_id'] == $v['id'] ? 'selected' : '' }}>
                                                        {{ $v['name'] }}</option>
                                                @endforeach
                                            </select>
                                            <input style="display: none;" type="text" name="old_supplier_id" id="old_supplier_id"  value="{{ $quotation['supplier_id'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group" id="div_trade_date">
                                            <label for="trade_date">報價日期<span class="text-red">*</span></label>
                                            <div class="input-group" id="trade_date_flatpickr">
                                                <input type="text" class="form-control" name="trade_date" id="trade_date" value="{{ $quotation['trade_date'] ?? date('Y-m-d') }}" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group" id="div_doc_number">
                                            <label for="doc_number">報價單號</label>
                                            <input class="form-control" name="doc_number" id="doc_number"
                                                value="{{ $quotation['doc_number'] ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group" id="div_currency_code">
                                            <label for="currency_code">幣別</label>
                                            <select class="form-control js-select2" name="currency_code" id="currency_code">
                                                <option value='TWD'>新台幣</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label for="total_tax_price">匯率</label>
                                            <input class="form-control" name="exchange_rate" id="exchange_rate" value="1"
                                                readonly>
                                            <input type="hidden" name="exchange_rate" id="exchange_rate" value="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group" id="tax_div">
                                            <label for="tax_div">稅別<span class="text-red">*</span></label>
                                            <select class="form-control js-select2-department" name="tax" id="tax">
                                                @foreach ($taxList as $key => $tax)
                                                    <option value="{{ $key }}"
                                                    @if(isset($quotation['tax']))
                                                    {{ $quotation['tax'] == $key ? 'selected' : '' }}
                                                    @else
                                                    {{$key == 2 ? 'selected' : '' }}
                                                    @endif>
                                                        {{ $tax }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="tax_div">報價含稅否<span class="text-red">*</span></label>
                                        <div class="radio">
                                            <div class="col-sm-6">
                                                <label>
                                                    <input type="radio" name="is_tax_included" value="1"
                                                        {{ isset($quotation['is_tax_included']) && $quotation['is_tax_included'] == '1' ? 'checked' : '' }}>
                                                    含稅
                                                </label>
                                            </div>
                                            <div class="col-sm-6">

                                                <label>
                                                    <input type="radio" name="is_tax_included" value="0"
                                                        {{ isset($quotation['is_tax_included']) && $quotation['is_tax_included'] == '0' ? 'checked' : '' }}>
                                                    未稅
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" id="div_remark">
                                            <label for="remark">備註</label>
                                            <textarea class="form-control" rows="3" name="remark"
                                                id="remark">{{ $quotation['remark'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <h4><i class="fa-solid fa-table-cells-large"></i> 品項</h4>
                                <div id="ItemDiv">
                                    <input type="hidden" name="rowNo" id="rowNo" value="0">
                                    <div class='add_row'>
                                        <div class='row'>
                                            <div class='col-sm-6 text-left'>品項<span class='text-red'>*</span></div>
                                            <div class='col-sm-2 text-left'>進貨成本<span class='text-red'>*</span></div>
                                            <div class='col-sm-3 text-left'>最小採購量</div>
                                            <div class='col-sm-1 text-left'>功能</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <a class="btn btn-warning" id="btn-addNewRow"><i class="fa-solid fa-plus"></i>
                                            新增品項</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <hr>
                                    </div>
                                </div>
                                <input type="hidden" name="status_code" id="status_code" value="">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success save_data" type="button" data-type="saveDraft">
                                                <i class="fa-solid fa-floppy-disk"></i> 儲存草稿
                                            </button>
                                            <button class="btn btn-success save_data" type="button"
                                                data-type="saveReview">
                                                <i class="fa-solid fa-floppy-disk"></i> 儲存並送審
                                            </button>
                                            <button class="btn btn-danger save_data" type="button" data-type="cancel">
                                                <i class="fa-solid fa-ban"></i> 取消
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@section('js')
    <script>
        $(document).ready(function() {

            is_tax_included_status();

            $('#btn-addNewRow').click(function() {
                AddItemRow("process", "input");
            });
            $('#supplier_id').select2();
            $('#tax').select2();
            $('#supplier_id').on('change', function() {
                var this_supplier_id = $(this).find(":selected").val() ;
                if($('.js-select2-item').length > 0){
                    if($('#old_supplier_id').val() !== ''){
                        if($('#old_supplier_id').val() !== $(this).find(":selected").val()){
                            alert('請先將品項刪除再切換供應商')
                            $("#supplier_id").val($('#old_supplier_id').val()).trigger('change');
                        }
                    }
                }else{
                    $('#old_supplier_id').val(this_supplier_id);
                }
            });

            var quotation_id = '{{ $id ?? '' }}';
            if (quotation_id != '') {
                getItem(quotation_id);
            }

            flatpickr("#trade_date_flatpickr", {
                dateFormat: "Y-m-d",
            });

            $('#new-form').validate({
                // debug: true,
                submitHandler: function(form) {
                    /**
                    *報價單送審時，需先檢查明細裡面的每個item都不可存在未結案的報價單，若有，不允許送審
                    */
                    var product_items = $(".product_item_va").map(function(){
                        return $(this).val();
                    }).get()
                    if($('#status_code').val() == 'REVIEWING'){
                        axios.post('/backend/quotation/ajax', {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                get_type: 'check_quotation_items',
                                product_items:product_items,
                            })
                            .then(function(response) {
                                if(!response.data.status){
                                    alert('下列品項仍有未結案報價單，不允許送審！'+response.data.error_msg) ;
                                }else{
                                    form.submit();
                                }
                            })
                            .catch(function(error) {
                                console.log(error);
                            });
                    }else{
                        form.submit();
                    }

                },
                rules: {
                    trade_date: {
                        required: true
                    },
                    is_tax_included: {
                        required: $('#tax').val() !== 0
                    },
                    supplier_id: {
                        required: true
                    },
                },
                messages: {
                    trade_date: "請輸入報價日期",
                    supplier_id: "請選取供應商"
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    if (element.parent('.input-group').length || element.is(':radio')) {
                        error.insertAfter(element.parent());
                        return;
                    }

                    if (element.is('select')) {
                        element.parent().append(error);
                        return;
                    }

                    error.insertAfter(element);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            function AddItemRow(get_type, position) {
                $("#btn-addNewRow").prop('disabled', true);
                var supplier_id = $('#supplier_id').val();
                if (supplier_id == '' || supplier_id == null) {
                    alert('請先選擇供應商才能新增品項');
                    $("#btn-addNewRow").prop('disabled', false);
                    return false;
                }
                var curRow = parseInt($('#rowNo').val());
                var newRow = curRow + 1;

                // let product = @json($products_item);
                $.ajax({
                        url: "/backend/quotation/ajax",
                        type: "POST",
                        data: {
                            "get_type": "supplierGetProducts",
                            "supplier_id": $('#supplier_id').val(),
                            _token: '{{ csrf_token() }}'
                        },
                        enctype: 'multipart/form-data',
                    })
                    .done(function(data) {
                        $(" <div class='add_row product_item_count' id='div-addrow-" + position + newRow +
                            "'>" +
                            "<div class='row'>" +
                            "<input class='form-control' name='itemname[]' id='" + position + "itemname-" +
                            newRow +
                            "' type='hidden'>" +
                            "<div class='col-sm-6' >" +
                            "<div class='input-group'>" +
                            "<select class='form-control js-select2-item product_item_va' name='item[" +
                            newRow +
                            "]' id='" + position + "item-" + newRow + "' data-key='" + newRow + "' data-va='product_item_va'>" +
                            "<option value=''></option>" +
                            "</select>" +
                            "<span class='input-group-btn'>" +
                            "<button class='btn copy_btn' type='button' data-key='" + newRow +
                            "'><i class='fa-solid fa-copy'></i></button>" +
                            "</span>" +
                            "</div>" +
                            "</div>" +
                            "<div class='col-sm-2' >" +
                            "<input class='form-control qty price_va' name='price[" + newRow + "]' id='" +
                            position +
                            "price-" + newRow + "'  type='number' min='0'>" +
                            "</div>" +
                            "<div class='col-sm-3' >" +
                            "<input class='form-control' name='minimum_purchase_qty[" + newRow + "]' id='" +
                            position +
                            "minimum_purchase_qty-" + newRow + "' readonly >" +
                            "</div>" +
                            "<div class='col-sm-1'>" +
                            "<button class='btn btn-danger btn_close' id='btn-delete-" + position + newRow +
                            "' value='" + newRow + "'><i class='fa-solid fa-trash-can'></i> 刪除</button>" +
                            "</div>" +
                            "</div>" +
                            "</div>"
                        ).appendTo($('#ItemDiv'));
                        $(".js-select2-item").select2({
                            allowClear: true,
                            theme: "bootstrap",
                            placeholder: "請選擇品項"
                        });

                        $('button[id^=btn-delete-]').click(function() {
                            $("#div-addrow-" + position + $(this).val()).remove();
                            return false;
                        });
                        $.each(data.products, function(key, value) {
                            let text_value = value.item_no + "-" + value.brand_name + "-" + value
                                .product_name;
                            if (value.spec_1_value !== '') {
                                text_value += "-" + value.spec_1_value;
                            }
                            if (value.spec_2_value !== '') {
                                text_value += "-" + value.spec_2_value;
                            }
                            $("#" + position + "item-" + newRow).append($("<option></option>").attr(
                                "value", value
                                .id).text(text_value));
                        });
                        $('#rowNo').val(newRow);
                        $("#btn-addNewRow").prop('disabled', false);
                    });


            }


            //update才會執行該function
            function getItem(quotation_id) {
                var curRow = parseInt($('#rowNo').val());
                var position = 'input';
                var get_type = '';
                var obj = @json($quotation_details ?? '');
                var item = @json($products_item ?? '');
                $.each(obj, function(key, value) {
                    var newRow = curRow++;
                    var itemOption = "<option value=''></option>";
                    $.each(item, function(itemKey, itemVal) {
                        var selected = '';
                        if (itemVal.id == value.product_items_id) {
                            selected = 'selected';
                        }
                        itemOption += "<option value=" + itemVal.id + " " + selected + ">" + value.product_items_no + "-" + itemVal.brand_name + "-" + itemVal.product_name ;
                        if(itemVal.spec_1_value !== ''){
                            itemOption += "-" +  itemVal.spec_1_value ;
                        }
                        if(itemVal.spec_2_value !== ''){
                            itemOption += "-" +  itemVal.spec_2_value ;
                        }

                        itemOption += "</option>" ;

                        let text_value = value.item_no + "-" + value.brands_name + "-" + value
                            .product_name + "-" + value.spec_1_value + "-" + value.spec_2_value;
                    });

                    $(" <div class='add_row product_item_count' id='div-addrow-" + position + newRow +
                        "'>" +
                        "<input name='quotation_details_id[]' type='hidden' value='" + value
                        .quotation_details_id + "'>" +
                        "<input class='form-control' id='" + position + "itemname-" + newRow +
                        "' type='hidden'>" +
                        "<div class='row'>" +
                        "<div class='col-sm-6' >" +
                        "<div class='input-group'>" +
                        "<select class='form-control js-select2-item product_item_va' name='item[]' id='" +
                        position + "item-" + newRow + "' data-key='" + newRow + "' data-va='product_item_va'>" +
                        itemOption +
                        "</select>" +
                        "<span class='input-group-btn'>" +
                        "<button class='btn copy_btn' type='button' data-key='" + newRow +
                        "'><i class='fa-solid fa-copy'></i></button>" +
                        "</span>" +
                        "</div>" +
                        "</div>" +
                        "<div class='col-sm-2' >" +
                        "<input class='form-control qty price_va' name='price[]' id='" + position +
                        "price-" + newRow + "'  type='number' value='" + value.original_unit_price +
                        "'>" +
                        "</div>" +
                        "<div class='col-sm-3' >" +
                        "<input class='form-control' name='minimum_purchase_qty[]' value='" + value
                        .min_purchase_qty + "' id='" + position + "minimum_purchase_qty-" + newRow +
                        "' readonly >" +
                        "</div>" +
                        "<div class='col-sm-1'>" +
                        "<button type='button' data-details='" + value.quotation_details_id +
                        "' class='btn btn-danger btn_close' id='btn-delete-" + position + newRow +
                        "' value='" + newRow + "'><i class='fa-solid fa-trash-can'></i> 刪除</button>" +
                        "</div>" +
                        "</div>" +
                        "</div>"
                    ).appendTo($('#ItemDiv'));
                    $('#rowNo').val(newRow);
                });


                $(".js-select2-item").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇品項"
                });
                $('button[id^=btn-delete-]').click(function() {
                    var id = this.dataset.details;

                    if (confirm("確定要刪除?")) {
                        $.ajax({
                            url: "/backend/quotation/ajaxDelItem",
                            type: "POST",
                            data: {
                                'id': id,
                                _token: '{{ csrf_token() }}'
                            },
                            enctype: 'multipart/form-data',
                        })

                        $("#div-addrow-" + position + $(this).val()).remove();
                    }
                    return false;
                });
            }
            $(document).on("click", ".save_data", function() {
                let type = $(this).data('type');
                if (type !== 'cancel') {
                    $(".product_item_va").each(function() {
                        $(this).rules("add", {
                            required: true,
                            notRepeating:true,
                            messages:{
                                notRepeating:'重複已選取的品項'
                            }
                        });
                    })
                    $(".price_va").each(function() {
                        $(this).rules("add", {
                            required: true,
                        });
                    })
                    var curRow = $('.product_item_count').length;
                    if (curRow == 0) {
                        alert('至少要填入一個品項才能送出');
                        return false;
                    }
                }
                switch (type) {

                    case 'cancel':
                        if (confirm("確認放棄存檔?")) {
                            window.location.href = "{{ route('quotation') }}";
                        }
                        break;
                    case 'saveDraft':
                        if (confirm("確定要儲存為草稿？")) {
                            $('#status_code').val('DRAFTED');
                            $('#new-form').submit();
                        }
                        return false;
                        break;
                    case 'saveReview':
                        if (confirm("單據送審後無法再修改，確定要送審？")) {
                            $('#status_code').val('REVIEWING');
                            $('#new-form').submit();
                        }
                        return false;
                        break;
                    default:
                        break;
                }
            })
            $(document.body).on("change", ".js-select2-item", function() {
                var item = @json($products_item ?? '');
                let find_this_id = $(this).val();
                let fund_this_key = $(this).data('key');
                let inputminimum_purchase_qty = '#inputminimum_purchase_qty-' + fund_this_key;
                let new_item = item.filter(function(obj, index) {
                    return obj.id == find_this_id;
                })
                $(inputminimum_purchase_qty).val(new_item[0].min_purchase_qty);
            });
            $(document.body).on("change", "#tax", function() {
                is_tax_included_status();
            });
            //報價含稅否
            function is_tax_included_status() {
                let taxtype = $('#tax').val();
                switch (taxtype) {
                    case '0': // 免稅
                        $('input[name=is_tax_included]').attr("disabled", true);
                        break;
                    case '2': //應稅內含
                        $('input[name=is_tax_included]').attr("disabled", false);
                        break;
                    case '3': //零稅率
                        $('input[name=is_tax_included]').attr("disabled", false);
                        break;
                    default:
                        break;
                }
            }
            $(document).on("click", ".copy_btn", function() {
                let key = $(this).data('key');
                let copytext = $('#inputitem-' + key).find('option:selected').text();
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(copytext).select();
                document.execCommand("copy");
                $temp.remove();
            })
        });
    </script>
@endsection

@endsection
