@extends('backend.layouts.master')
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
                    action="" enctype="multipart/form-data"
                    novalidaten="ovalidate">
                    <div class="form-horizontal">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-3">
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
                                    <div class="col-sm-3">
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
                                    <div class="col-sm-3">
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
                                    <div class="col-sm-3">
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
                                    <div class="col-sm-3">
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
                                    <div class="col-sm-3">
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
                                    <div class="col-sm-3">
                                        <label class="control-label">成本</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="supplier_name"
                                            value="{{ $products->item_cost }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label">毛利(%)</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_name"
                                            value="{{ $products->gross_margin }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label">上架時間起<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="input-group" id="start_launched_at_flatpickr">
                                            <input type="text" class="form-control" name="start_launched_at" id="start_launched_at" value="{{ $products->start_launched_at }}" readonly autocomplete="off" data-input />
                                            <span class="input-group-btn" data-toggle>
                                                <button class="btn btn-default" type="button">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label">上架時間訖<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="input-group" id="end_launched_at_flatpickr">
                                            <input type="text" class="form-control" name="end_launched_at" id="end_launched_at" value="{{ $products->end_launched_at }}" readonly autocomplete="off" data-input />
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
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label">開賣時間<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="input-group" id="start_selling_at_flatpickr">
                                            <input type="text" class="form-control" name="start_selling_at"
                                                id="start_selling_at" value="{{$products->start_selling_at}}" autocomplete="off" data-input readonly/>
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
                                            <a href="{{ route('products_mall.show', $products->id) }}"
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
                                            <th>開賣時間</th>
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
                                                <td>{{ $val->start_selling_at }}</td>
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
                        <a class="btn btn-success" href="{{ URL::previous() }}"><i class="fa-solid fa-arrow-rotate-right"></i>
                            返回</a>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
@section('js')
@endsection
