@extends('backend.master')
@section('title', '商品主檔 - 商品上下架申請')
@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-list"></i>商品主檔 - 商品上下架申請</h1>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">請輸入下列欄位資料</div>
            <div class="panel-body" id="CategoryHierarchyContentInput">
                <form role="form" id="new-form" method="POST"
                    action="{{ route('product_review_register.update', $products->id) }}" enctype="multipart/form-data"
                    novalidaten="ovalidate">
                    @csrf
                    @method('PUT')
                    <div class="form-horizontal">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">庫存類型</label>
                                    </div>
                                    <input type="hidden" id="products_id" value="{{ $products->id }}">
                                    <input type="hidden" id="product_type" value="{{ $products->product_type }}">
                                    <input type="hidden" id="description" value="{{ $products->description }}">
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="A"
                                                {{ $products->stock_type == 'A' ? 'checked' : 'disabled' }}>
                                            買斷
                                            [A]
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="B"
                                                {{ $products->stock_type == 'B' ? 'checked' : 'disabled' }}>
                                            寄售
                                            [B]
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="T"
                                                {{ $products->stock_type == 'T' ? 'checked' : 'disabled' }}>
                                            轉單[T]
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label ">商品序號</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_no"
                                            value="{{ $products->product_no }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">供應商</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="supplier_name"
                                            value="{{ $products->supplier_name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">商品名稱</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_name"
                                            value="{{ $products->product_name }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">市價(含稅)</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="list_price"
                                            value="{{ $products->list_price }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">售價(含稅)</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="selling_price"
                                            value="{{ $products->selling_price }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">成本</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="item_cost" value="{{ $products->item_cost }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">毛利(%)</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="gross_margin"
                                            value="{{ $products->gross_margin }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label class="control-label">上架時間起<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="input-group" id="start_launched_at_flatpickr">
                                            <input type="text" class="form-control" name="start_launched_at"
                                                id="start_launched_at" value="" autocomplete="off" data-input />
                                            <span class="input-group-btn" data-toggle>
                                                <button class="btn btn-default" type="button">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <input type="hidden" value="0" id="old_date_is_today" readonly>
                                    <input type="hidden" value="" id="old_date" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">上架時間訖<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="input-group" id="end_launched_at_flatpickr">
                                            <input type="text" class="form-control" name="end_launched_at"
                                                id="end_launched_at" value="" autocomplete="off" data-input />
                                            <span class="input-group-btn" data-toggle>
                                                <button class="btn btn-default" type="button">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">
                                            <a href="{{ route('products.show', $products->id) }}"
                                                target="_blank">查看基本資訊</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">
                                            <a href="{{ route('product_small.show', $products->id) }}"
                                                target="_blank">查看商城資訊
                                            </a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-large btn-success" type="button" id="save_data">
                            <i class="fa-solid fa-floppy-disk"></i>
                            送審
                        </button>
                        <a class="btn btn-danger" href="{{ URL::previous() }}"><i class="fa-solid fa-ban"></i>
                            取消</a>
                        <hr>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">
                                            上下架歷程
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>上架時間</th>
                                            <th>售價</th>
                                            <th>上架審核時間</th>
                                            <th>上架審核結果</th>
                                            <th>上架審核備註</th>
                                            <th>下架時間</th>
                                            <th>下架人員</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product_review_log as $val)
                                            <tr>
                                                <td>{{ $val->start_launched_at }} ~ {{ $val->end_launched_at }}</td>
                                                <td>{{ $val->selling_price }}</td>
                                                <td>{{ $val->review_at }}</td>
                                                <td>
                                                    @switch($val->review_result)
                                                        @case('APPROVE')
                                                            核准
                                                        @break

                                                        @case('REJECT')
                                                            駁回
                                                        @break

                                                        @default
                                                            尚未審核
                                                    @endswitch
                                                </td>
                                                <td>{{ $val->review_remark }}</td>
                                                <td>{{ $val->discontinued_at }}</td>
                                                <td>{{ $val->discontinued_user_name }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
@section('js')
    <script>
        $(document).ready(function() {
            function isToday(getDate) {
                let today = new Date();
                let compareDate = getDate;
                return (today.setHours(0, 0, 0, 0) == compareDate.setHours(0, 0, 0, 0))
            }

            function getDateStr(time) {
                month = '' + (time.getMonth() + 1),
                    day = '' + time.getDate(),
                    year = time.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                return [year, month, day].join('-');
            }

            function isSameDay(compareDateA, compareDateB) {
                let DateA = compareDateA;
                let DateB = compareDateB;
                return (DateA.setHours(0, 0, 0, 0) == DateB.setHours(0, 0, 0, 0))
            }
            let start_launched_at_flatpickr = flatpickr("#start_launched_at_flatpickr", {
                dateFormat: "Y-m-d H:i:S",
                maxDate: $("#end_launched_at").val(),
                enableTime: true,
                enableSeconds: true,
                defaultHour: 0,
                defaultMinute: 0,
                defaultSeconds: 0,
                onChange: function(selectedDates, dateStr, instance) {
                    let old_date_is_today = $('#old_date_is_today').val();
                    let old_date = $('#old_date').val() !== '' ? new Date(Date.parse($('#old_date')
                        .val())) : '';
                    let is_today = isToday(selectedDates[0]);
                    if (is_today && old_date_is_today == '0') {
                        start_launched_at_flatpickr.setDate(new Date(new Date().getTime() + 15 * 60 *
                            1000));
                        $('#old_date_is_today').val('1');
                    }
                    if (old_date !== '') {
                        let is_same_day = isSameDay(selectedDates[0], old_date);
                        if (!is_today) {
                            old_date_is_today = '0';
                            $('#old_date_is_today').val('0');
                        }
                        if (!is_today && !is_same_day) {
                            start_launched_at_flatpickr.setDate(selectedDates[0].setHours(0, 0, 0, 0));
                        }
                    }
                    $('#old_date').val(getDateStr(selectedDates[0]));
                },
            });

            let end_launched_at_flatpickr = flatpickr("#end_launched_at_flatpickr", {
                dateFormat: "Y-m-d H:i:S",
                minDate: $("#start_launched_at").val(),
                enableTime: true,
                enableSeconds: true,
                defaultHour: 23,
                defaultMinute: 59,
                defaultSeconds: 59,
            });
            $(document).on("click", "#save_data", function() {
                $("#new-form").submit();
            })

            $("#new-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#save_data').prop('disabled', true);
                    if ($('#product_type').val() == 'N') {
                        if ($('#description').val() == '') {
                            alert('該商品未完成「商城資料」維護，不允許執行上架送審');
                            return false;
                        };
                    }
                    axios.post('/backend/product_review_register/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'checkProductReady',
                            product_id: $('#products_id').val(),
                            product_type: $('#product_type').val(),
                        })
                        .then(function(response) {
                            if (response.data.status) {
                                form.submit();
                            } else {
                                alert('該商品未完成「商城資料」維護，不允許執行上架送審 (至少須有一組前台分類)');
                                return false;
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        }).finally(function(error) {
                            $('#save_data').prop('disabled', false);
                        });
                },
                rules: {
                    start_launched_at: {
                        required: true,
                        dateGreaterThanNow: true,
                    },
                    end_launched_at: {
                        required: true,
                        dateGreaterThanNow: true,
                        greaterThan: function() {
                            return $('#start_launched_at').val();
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
                success: function(label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });
        });
    </script>
@endsection
