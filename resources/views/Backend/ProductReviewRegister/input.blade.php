@extends('Backend.master')
@section('title', '商品主檔 - 商品上下架申請')
@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>商品主檔 - 商品上下架申請</h1>
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
                                        <input class="form-control" name="supplier_name"
                                            value="{{ $products->supplier_name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">毛利(%)</label>
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
                                    <div class="col-sm-2">
                                        <label class="control-label">上架時間起<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9" id="div_start_launched_at">
                                        <div class='input-group date' id='datetimepicker'>
                                            <input type='text' class="form-control" name="start_launched_at"
                                                id="start_launched_at" value="{{ $products->start_launched_at }}" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">上架時間訖<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class='input-group date' id='datetimepicker2'>
                                            <input type='text' class="form-control" name="end_launched_at"
                                                id="end_launched_at" value="{{ $products->end_launched_at }}" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
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
                        <button class="btn btn-large btn-success" type="button" id="save_data">
                            <i class="fa fa-save"></i>
                            送審
                        </button>
                        <a class="btn btn-danger" href="{{ url('product_small') }}"><i class="fa fa-ban"></i>
                            取消</a>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD HH:mm',
            });
            $('#datetimepicker2').datetimepicker({
                format: 'YYYY-MM-DD HH:mm',
            });
            $(document).on("click", "#save_data", function() {
                $("#new-form").submit();
            })
            //start_launched_at
            //end_launched_at
            $("#new-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#save_data').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    start_launched_at: {
                        required: true,                        
                    },
                    end_launched_at: {
                        required: true,      
                        dateGreaterThanNow: true,   
                        greaterThan: function() {
                            return $('#start_launched_at').val();
                        },     
                    }
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