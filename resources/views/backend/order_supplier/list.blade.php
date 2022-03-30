@extends('backend.master')
@section('title', '採購單')
@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-arrow-right-to-bracket"></i> 採購單</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form role="form" id="select-form" method="GET" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>供應商</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-department" name="supplier" id="supplier">
                                            <option value=""></option>
                                            @foreach ($data['supplier'] as $v)
                                                <option value='{{ $v['id'] }}'
                                                    {{ isset($data['getData']['supplier']) && $v['id'] == $data['getData']['supplier'] ? 'selected' : '' }}>
                                                    {{ $v['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>供應商統編</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="company_number" id="company_number"
                                            value="{{ $data['getData']['company_number'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>狀態</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="status" id="status">
                                            <option value=''></option>
                                            <option value='drafted'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'drafted' ? 'selected' : '' }}>
                                                草稿</option>
                                            <option value='reviewing'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'reviewing' ? 'selected' : '' }}>
                                                簽核中</option>
                                            <option value='approved'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'approved' ? 'selected' : '' }}>
                                                已核准</option>
                                            <option value='rejected'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'rejected' ? 'selected' : '' }}>
                                                已駁回</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>採購日期：</h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_start_date">
                                            <div class="input-group" id="select_start_date_flatpickr">
                                                <input type="text" class="form-control" name="select_start_date" id="select_start_date" value="{{ $data['getData']['select_start_date'] ?? '' }}" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <h5>～</h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_end_date">
                                            <div class="input-group" id="select_end_date_flatpickr">
                                                <input type="text" class="form-control" name="select_end_date" id="select_end_date" value="{{ $data['getData']['select_end_date'] ?? '' }}" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>採購單號</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="order_number" id="order_number"
                                            value="{{ $data['getData']['order_number'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>請購單號</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="requisitions_purchase_number" id="order_number"
                                            value="{{ $data['getData']['requisitions_purchase_number'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-sm-9">
                                </div>

                                <div class="col-sm-3 text-right">
                                    <div class="col-sm-12">
                                        {{-- @if ($share_role_auth['auth_query']) --}}
                                        <button class="btn btn-warning"><i class="fa-solid fa-magnifying-glass"></i></i> 查詢</button>
                                        {{-- @endif --}}
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                @if ($share_role_auth['auth_create'])
                                    <a class="btn btn-block btn-warning btn-sm"
                                        href="{{ route('order_supplier.create') }}"><i class="fa-solid fa-plus"></i>
                                        由請購單帶入</a>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>採購日期</th>
                                    <th>採購單號</th>
                                    <th>供應商</th>
                                    <th>總金額</th>
                                    <th>狀態</th>
                                    <th>請購單號</th>
                                    <th>預計進貨日</th>
                                    <th>預進表拋出時間</th>
                                </tr>
                            </thead>
                            <tbody>
                                <button style="display:none;" class="btn btn-info btn-sm toggle-show-model"
                                    data-toggle="modal" data-target="#row_detail">SHOW
                                </button>
                                @foreach ($data['order_supplier'] as $k => $v)
                                    <tr>
                                        <form id="del-{{ $v['id'] }}"
                                            action="{{ route('order_supplier.destroy', $v['id']) }}" method="post">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                        <td>
                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-info btn-sm"
                                                    @click="showBtn({{ $v['id'] }})">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            @endif
                                            @if ($share_role_auth['auth_update'] && $v['status'] == 'DRAFTED' && $v['created_by'] == $data['user_id'])
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('order_supplier.edit', $v['id']) }}">編輯</a>
                                            @endif
                                            @if ($share_role_auth['auth_update'] && $v['status'] == 'APPROVED' && $v['created_by'] == $data['user_id'])

                                            <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                data-target="#row_supplier_deliver"
                                                @click="supplier_deliver({{ $v }})">預進日
                                            </button>
                                            @endif
                                            @if ($share_role_auth['auth_delete'] && $v['status'] == 'DRAFTED' && $v['created_by'] == $data['user_id'])
                                                <button class="btn btn-danger btn-sm" type="button"
                                                    @click="delBtn({{ $v['id'] }})">刪除</button>
                                            @endif
                                        </td>
                                        <td>{{ $v['trade_date'] }}</td>
                                        <td>{{ $v['number'] }}</td>
                                        <td>{{ $data['supplier'][$v['supplier_id']]['name'] ?? '' }}</td>
                                        <td>{{ $v['total_price'] }}</td>
                                        <td>{{ $data['status_code'][$v['status']] ?? '' }}</td>
                                        <td>{{ $v['requisitions_purchase_number'] }}</td>
                                        <td>{{ $v['expect_deliver_date'] }}</td>
                                        <td>{{ $v['edi_exported_at'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- 報價單明細 --}}
        @include('backend.order_supplier.detail')
        {{-- 補登預進日 --}}
        @include('backend.order_supplier.supplier_deliver')
    </div>

@section('js')
    <script>
        var showRequisitions = Vue.extend({
            data: function() {
                return {
                    order_supplier: {},
                    show_supplier:{} ,
                    show_detail:{} ,
                }
            },
            methods: {
                showBtn(id) {
                    var vm = this;
                    axios.post('/backend/order_supplier/ajax', {
                            "type": "show_supplier",
                            _token: '{{ csrf_token() }}',
                            'id': id
                        })
                        .then(function(response) {
                            vm.show_supplier = response.data.orderSupplier ;
                            vm.show_detail = response.data.orderSupplierDetail ;
                            $('.toggle-show-model').click();
                        })
                        .catch(function(error) {
                            console.log('ERROR');
                        })
                },
                delBtn(id) {
                    var checkDel = confirm('你確定要刪除嗎？');
                    if (checkDel) {
                        axios.post('/backend/order_supplier/ajax', {
                                "type": "del_order_supplier",
                                _token: '{{ csrf_token() }}',
                                'id': id
                            })
                            .then(function(response) {
                                if (response.data.result) {
                                    alert('刪除成功');
                                    history.go(0);
                                } else {
                                    alert('刪除失敗');
                                }
                            })
                            .catch(function(error) {
                                console.log('ERROR');
                            })
                    }
                },
                supplier_deliver(obj) {
                    $('#supplier_deliver_date').val(obj.supplier_deliver_date);
                    $('#trade_date').empty().text(obj.trade_date);
                    $('#expect_deliver_date').val(obj.expect_deliver_date);
                    $('#get_order_supplier_id').val(obj.id)
                    $('.show_number').html(obj.number);

                    flatpickr("#supplier_deliver_date_flatpickr", {
                        dateFormat: "Y-m-d",
                    });

                    flatpickr("#expect_deliver_date_flatpickr", {
                        dateFormat: "Y-m-d",
                    });
                },
            },
        })

        new showRequisitions().$mount('#page-wrapper');
        $(document).ready(function() {
            $('#supplier').select2();
            $('#status').select2();

            let select_start_date_flatpickr = flatpickr("#select_start_date_flatpickr", {
                dateFormat: "Y-m-d",
                maxDate: $("#select_end_date").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    select_end_date_flatpickr.set('minDate', dateStr);
                },
            });

            let select_end_date_flatpickr = flatpickr("#select_end_date_flatpickr", {
                dateFormat: "Y-m-d",
                minDate: $("#select_start_date").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    select_start_date_flatpickr.set('maxDate', dateStr);
                },
            });

            $("#supplier_deliver_form").validate({
                // debug: true,
                submitHandler: function(form) {
                    var supplier_deliver_date = $('#supplier_deliver_date').val();
                    var expect_deliver_date = $('#expect_deliver_date').val();
                    var get_order_supplier_id = $('#get_order_supplier_id').val();
                    axios.post('/backend/order_supplier/ajax', {
                            "type": "supplier_deliver_time",
                            _token: '{{ csrf_token() }}',
                            'id': get_order_supplier_id,
                            'supplier_deliver_date': supplier_deliver_date,
                            'expect_deliver_date': expect_deliver_date,
                        })
                        .then(function(response) {
                            if (response.data.result) {
                                alert('修改成功');
                                history.go(0);
                            } else {
                                alert('修改失敗');
                            }
                        })
                        .catch(function(error) {
                            console.log('ERROR');
                        })
                },
                rules: {
                    supplier_deliver_date: {
                        dateGreaterEqualThan: function() {
                                let obj = {
                                    date: $('#trade_date').text().trim(),
                                    depends: true,
                                }
                                if ($('#supplier_deliver_date').val() !== '') {
                                    obj.depends = true;
                                } else {
                                    obj.depends = false;
                                }
                                return obj;
                            },
                    },
                    expect_deliver_date: {
                        dateGreaterEqualThan: function() {
                                let obj = {
                                    date: $('#supplier_deliver_date').val(),
                                    depends: true,
                                }
                                if ($('#expect_deliver_date').val() !== '') {
                                    obj.depends = true;
                                } else {
                                    obj.depends = false;
                                }
                                return obj;
                            },
                    }
                },
                messages: {
                    end_launched_at: {
                        greaterThan: "結束時間必須大於開始時間",
                    },
                    supplier_deliver_date: {
                        dateGreaterEqualThan: "不可小於採購日期"
                    },
                    expect_deliver_date: {
                        dateGreaterEqualThan: "不可小於廠商交貨日"
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
                success: function(label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });
        });

        function del(id, doc_number) {
            if (confirm("確定要刪除採購單" + doc_number + "?")) {
                // console.log("del-" + id)
                document.getElementById("del-" + id).submit();
            }
            return false;
        };
    </script>
@endsection
@endsection
