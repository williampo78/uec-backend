@extends('Backend.master')
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
                                            <label for="supplier">供應商<span class="redtext">*</span></label>
                                            <select class="form-control js-select2-department" name="supplier_id"
                                                id="supplier_id">
                                                @foreach ($supplier as $v)
                                                    <option value='{{ $v['id'] }}'
                                                        {{ isset($quotation['trade_date']) && $quotation['trade_date'] == $v['id'] ? 'selected' : '' }}>
                                                        {{ $v['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group" id="div_trade_date">
                                            <label for="trade_date">報價日期<span class="redtext">*</span></label>
                                            <div class='input-group date' id='datetimepickera'>
                                                <input type='text' class="form-control" name="trade_date" id="trade_date"
                                                    value="{{ $quotation['trade_date'] ?? '' }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
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
                                            <label for="tax_div">稅別<span class="redtext">*</span></label>
                                            <select class="form-control js-select2-department" name="tax" id="tax">
                                                <option value="1"
                                                    {{ isset($quotation['tax']) && $quotation['tax'] == '1' ? 'selected' : '' }}>
                                                    應稅</option>
                                                <option value="0"
                                                    {{ isset($quotation['tax']) && $quotation['tax'] == '0' ? 'selected' : '' }}>
                                                    未稅</option>
                                                <option value="2"
                                                    {{ isset($quotation['tax']) && $quotation['tax'] == '2' ? 'selected' : '' }}>
                                                    內含</option>
                                                <option value="3"
                                                    {{ isset($quotation['tax']) && $quotation['tax'] == '3' ? 'selected' : '' }}>
                                                    零稅率</option>
                                            </select>
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

                                <hr>
                                <h4><i class="fa fa-th-large"></i> 品項</h4>
                                <div id="ItemDiv">
                                    <input type="hidden" name="rowNo" id="rowNo" value="0">
                                    <div class='add_row'>
                                        <div class='row'>
                                            <div class='col-sm-6 text-left'>品項<span class='redtext'>*</span></div>
                                            <div class='col-sm-2 text-left'>單價<span class='redtext'>*</span></div>
                                            <div class='col-sm-3 text-left'>最小採購量</div>
                                            <div class='col-sm-1 text-left'>功能</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <a class="btn btn-warning" id="btn-addNewRow"><i class="fa fa-plus"></i>
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
                                            <button class="btn btn-success save_data" type="button" data-type="saveDraft"><i
                                                    class="fa fa-save"></i> 儲存草稿</button>
                                            <button class="btn btn-success save_data" type="button"
                                                data-type="saveReview"><i class="fa fa-save"></i> 儲存並送審</button>
                                            <button class="btn btn-danger save_data" type="button" data-type="cancel"><i
                                                    class="fa fa-ban"></i> 取消</button>
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
            $('#btn-addNewRow').click(function() {
                AddItemRow("process", "input");
            });

            $('#supplier_id').select2();
            $('#tax').select2();

            var quotation_id = '{{ $id ?? '' }}';
            if (quotation_id != '') {
                getItem(quotation_id);
            }
            $('#datetimepickera').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $('#new-form').validate({
                // debug: true,
                submitHandler: function(form) {
                    form.submit();
                },
                rules: {
                    submitted_at: {
                        required: true
                    },
                },
                messages: {
                    submitted_at: "請輸入報價日期",
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
                var curRow = parseInt($('#rowNo').val());
                var newRow = curRow + 1;
                $(" <div class='add_row product_item_count' id='div-addrow-" + position + newRow + "'>" +
                    "<div class='row'>" +
                    "<input class='form-control' name='itemname[]' id='" + position + "itemname-" + newRow +
                    "' type='hidden'>" +
                    "<div class='col-sm-6' >" +
                    "<div class='input-group'>" +
                    "<select class='form-control js-select2-item product_item_va' name='item[" + newRow +
                    "]' id='" + position + "item-" + newRow + "' data-key='" + newRow + "'>" +
                    "<option value=''></option>" +
                    "</select>" +
                    "<span class='input-group-btn'>" +
                    "<button class='btn copy_btn' type='button' data-key='" + newRow +
                    "'><i class='fa fa-copy'></i></button>" +
                    "</span>" +
                    "</div>" +
                    "</div>" +
                    "<div class='col-sm-2' >" +
                    "<input class='form-control qty price_va' name='price[" + newRow + "]' id='" + position +
                    "price-" + newRow + "'  type='number' min='0'>" +
                    "</div>" +
                    "<div class='col-sm-3' >" +
                    "<input class='form-control' name='minimum_purchase_qty[" + newRow + "]' id='" + position +
                    "minimum_purchase_qty-" + newRow + "' readonly >" +
                    "</div>" +
                    "<div class='col-sm-1'>" +
                    "<button class='btn btn-danger btn_close' id='btn-delete-" + position + newRow +
                    "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
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
                let product = @json($products_item);
                $.each(product, function(key, value) {
                    let text_value = value.item_no + "-" + value.brands_name + "-" + value.product_name +
                        "-" + value.spec_1_value + "-" + value.spec_2_value;
                    $("#" + position + "item-" + newRow).append($("<option></option>").attr("value", value
                        .id).text(text_value));
                });
                $('#rowNo').val(newRow);
            }



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
                        itemOption += "<option value=" + itemVal.id + " " + selected + ">" + value
                            .product_items_no + "-" + itemVal.brands_name + "-" + itemVal
                            .product_name + "-" + itemVal.spec_1_value + "-" + itemVal
                            .spec_2_value + "</option>";
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
                        position + "item-" + newRow + "' data-key='" + newRow + "'>" +
                        itemOption +
                        "</select>" +
                        "<span class='input-group-btn'>" +
                        "<button class='btn copy_btn' type='button' data-key='" + newRow +
                        "'><i class='fa fa-copy'></i></button>" +
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
                        "' value='" + newRow + "'><i class='fa fa-ban'></i> 刪除</button>" +
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

            $(document).on("click", ".copy_btn", function() {
                let key = $(this).data('key');
                let copytext = $('#inputitem-' + key).find('option:selected').text() ; 
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
