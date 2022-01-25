@extends('Backend.master')
@section('title', '進貨單')
@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-sign-in"></i> 進貨單</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->

                    <div class="panel-heading">
                        <form role="form" class="form-horizontal" id="select-form" method="GET" action=""
                            enctype="multipart/form-data">
                            <br>
                            <div class="row">
                                {{-- row 1 start --}}
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"><label class="control-label"> 供應商 </label></div>
                                        <div class="col-sm-9">
                                            <div class='input-group' id='supplier_deliver_date_dp'>
                                                <select class="form-control js-select2-department" name="supplier"
                                                    id="supplier" value="{{ request()->input('supplier') }}">
                                                    <option value=""></option>
                                                    @foreach ($supplier as $v)
                                                        <option value='{{ $v['id'] }}'
                                                            {{ request()->input('supplier') && $v['id'] == request()->input('supplier') ? 'selected' : '' }}>
                                                            {{ $v['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4"><label class="control-label">供應商統編</label></div>
                                        <div class="col-sm-8">
                                            <div class='input-group'>
                                                <input class="form-control" name="company_number" id="company_number"
                                                    value="{{ request()->input('company_number') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4"><label class="control-label">採購單號</label></div>
                                        <div class="col-sm-8">
                                            <div class='input-group'>
                                                <input class="form-control" name="order_supplier_number"
                                                    id="order_supplier_number"
                                                    value="{{ request()->input('order_supplier_number') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{-- row 1 end --}}
                            {{-- row 2 start --}}
                            <div class="row">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"><label class="control-label">進貨日期</label></div>
                                        <div class="col-sm-4">
                                            <div class="form-group">

                                                <div class="input-group date" id="trade_date_start_box">
                                                    <input type="text" class="form-control" name="trade_date_start"
                                                        id="trade_date_start"
                                                        value="{{ request()->input('trade_date_start') }}">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <div class="form-group">
                                                <label class="control-label">　～</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class="input-group date" id="trade_date_end_box">
                                                    <input type="text" class="form-control" name="trade_date_end"
                                                        id="trade_date_end"
                                                        value="{{ request()->input('trade_date_end') }}">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4"><label class="control-label">進貨單號</label></div>
                                        <div class="col-sm-8">
                                            <div class='input-group'>
                                                <input class="form-control" name="number" id="number"
                                                    value="{{ request()->input('number') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <div class="col-sm-12">
                                        <button class="btn btn-warning"><i class="fa fa-search"></i> 查詢</button>
                                    </div>
                                </div>
                                {{-- row 2 end --}}
                            </div>
                        </form>

                    </div>

                </div>

                <!-- Table list -->
                <div class="panel-body">

                    <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                        <thead>
                            <tr>
                                <th>功能</th>
                                <th>進貨日期</th>
                                <th>進貨單號</th>
                                <th>採購單號</th>
                                <th>總金額</th>
                                <th>發票號碼</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($purchase))
                                @foreach ($purchase as $obj)
                                    <tr>
                                        <td>
                                            {{-- @if ($share_role_auth['auth_query']) --}}
                                            <button type="button" class="btn btn-info btn-sm show-btn" data-toggle="modal"
                                                data-target="#show_data" data-id="{{ $obj->id }}"><i
                                                    class="fa fa-search"></i></button>
                                            {{-- @endif --}}

                                            {{-- @if ($share_role_auth['auth_update'] && $v['status_code'] == 'DRAFTED' && $v['created_by'] == Auth::user()->id) --}}
                                            <button type="button" class="btn btn-warning btn-sm update_invoice"
                                                data-toggle="modal" data-target="#update_invoice"
                                                data-obj="{{ $obj }}">補登發票</button>
                                            {{-- @endif --}}
                                        </td>
                                        <td>{{ $obj->trade_date }}</td>
                                        <td>{{ $obj->number }}</td>
                                        <td>{{ $obj->order_supplier_number }}</td>
                                        <td>{{ $obj->total_price }}</td>
                                        <td>{{ $obj->invoice_number }}</td>
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('Backend.Purchase.detail')
        @include('Backend.Purchase.update_invoice')

    </div>
    </div>

@section('js')
    <script>
        $(document).ready(function() {

            $('#supplier').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $('#trade_date_start_box').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $('#trade_date_end_box').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $('#invoice_date_box').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $('.show-btn').click(function() {
                var id = $(this).data('id');
                axios.post('/backend/purchase/ajax', {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        type: 'showPurchase',
                        id: id,
                    })
                    .then(function(response) {
                        $('#show_blade_init').html('');
                        $('#show_blade_init').html(response.data);
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
            $('.update_invoice').click(function() {
                var obj = $(this).data('obj');
                $('#show_number').html(obj.number)
                $('#show_order_supplier_number').html(obj.order_supplier_number);
                $('#invoice_date').val(obj.invoice_date);
                $('#invoice_number').val(obj.invoice_number);
                $('#purchase_id').val(obj.id);

            });

            $("#select-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    // $('#save_data').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    trade_date_start: {
                        required: {
                            depends: function(element) {
                                if($('#order_supplier_number').val() !=='' || $('#number').val() !== ''){
                                    return false ;
                                }else{
                                    return true ;
                                }
                            }
                        },
                        monthIntervalVerify: function() {
                            isExecution = true
                            if($('#order_supplier_number').val() !=='' || $('#number').val() !== ''){
                                isExecution = false ;
                            }
                            let obj = {
                                startTime: $('#trade_date_start').val(),
                                endTime: $('#trade_date_end').val(),
                                monthNum: 6,
                                isExecution:isExecution,
                            }
                            return obj;
                        },
                    },
                    trade_date_end: {
                        required: {
                            depends: function(element) {
                                if($('#order_supplier_number').val() !=='' || $('#number').val() !== ''){
                                    return false ;
                                }else{
                                    return true ;
                                }
                            }
                        },
                    },

                },
                messages: {
                    end_launched_at: {
                        greaterThan: "結束時間必須大於開始時間",
                    },
                },
                errorClass: "help-block",
                errorElement: "span",
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
                    $(element).closest(".form-group").addClass("has-error");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
                success: function(label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });

            $("#update_invoice_form").validate({
                // debug: true,
                submitHandler: function(form) {
                    var check = confirm('確定要修改嗎?');

                    if (check) {
                        var invoice_date = $('#invoice_date').val();
                        var invoice_number = $('#invoice_number').val();
                        var id = $('#purchase_id').val();
                        axios.post('/backend/purchase/ajax', {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                type: 'update_invoice',
                                id: id,
                                invoice_number: invoice_number,
                                invoice_date: invoice_date
                            })
                            .then(function(response) {
                                alert('更新成功')
                                history.go(0)

                            })
                            .catch(function(error) {
                                console.log(error);
                            });
                    }

                },
                rules: {
                    invoice_number: {
                        required: true
                    },
                    invoice_date: {
                        required: true
                    },

                },
                messages: {

                },
                errorClass: "help-block",
                errorElement: "span",
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
                    $(element).closest(".form-group").addClass("has-error");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
                success: function(label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });


        });
    </script>
@endsection
@endsection
