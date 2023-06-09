@extends('backend.layouts.master')

@section('title', '商品主檔 - 上下架申請')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><span class="fa-solid fa-cube"></span> 商品主檔 - 上下架申請</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading p-4">
                        <form role="form" id="select-form" method="GET" action="{{ route('product_review_register') }}"
                            enctype="multipart/form-data">
                            <div class="d-block d-md-grid custom-outer">
                                <div class="mb-4 custom-title">
                                    <label class=>庫存類型</label>
                                    <select class="form-control js-select2" name="stock_type" id="stock_type">
                                        <option value="">全部</option>
                                        <option value="A"
                                            {{ request()->input('stock_type') == 'A' ? 'selected' : '' }}>買斷
                                        </option>
                                        <option value="B"
                                            {{ request()->input('stock_type') == 'B' ? 'selected' : '' }}>寄售</option>
                                        <option value="T"
                                            {{ request()->input('stock_type') == 'T' ? 'selected' : '' }}>轉單
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-4 custom-title">
                                    <label>商品序號</label>
                                    <input type="text" class="form-control" name="product_no" id="product_no"
                                        value="{{ request()->input('product_no') }}">
                                </div>

                                <div class="mb-4 custom-title">
                                    <label>供應商</label>
                                    <select class="form-control js-select2" name="supplier_id" id="supplier_id">
                                        <option value="">全部</option>
                                        @foreach ($supplier as $val)
                                            <option value="{{ $val->id }}"
                                                {{ request()->input('supplier_id') == $val->id ? 'selected' : '' }}>
                                                {{ $val->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-block d-md-grid custom-outer">
                                <div class="mb-4 custom-title">
                                    <label>商品通路</label>
                                    <select class="form-control js-select2" name="selling_channel" id="selling_channel">
                                        <option value="">全部</option>
                                        <option value="EC"
                                            {{ request()->input('selling_channel') == 'EC' ? 'selected' : '' }}>網路獨賣
                                        </option>
                                        <option value="STORE"
                                            {{ request()->input('selling_channel') == 'STORE' ? 'selected' : '' }}>
                                            門市限定
                                        </option>
                                        <option value="WHOLE"
                                            {{ request()->input('selling_channel') == 'WHOLE' ? 'selected' : '' }}>
                                            全通路
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-4 custom-title">
                                    <label>商品名稱</label>
                                    <input type="text" class="form-control" name="product_name" id="product_name"
                                        value="{{ request()->input('product_name') }}">
                                </div>

                                <div class="mb-4 custom-title">
                                    <label>前台分類</label>
                                    <select class="form-control js-select2" name="web_category_hierarchy_id"
                                        id="web_category_hierarchy_id">
                                        <option value="">全部</option>
                                        @foreach ($pos as $val)
                                            <option value="{{ $val->id }}"
                                                {{ request()->input('web_category_hierarchy_id') == $val->id ? 'selected' : '' }}>
                                                {{ $val->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-block d-md-grid custom-outer">
                                <div class="mb-4 custom-title">
                                    <label>配送方式</label>
                                    <select class="form-control js-select2" name="lgst_method" id="lgst_method">
                                        <option value="">全部</option>
                                        <option value="HOME"
                                            {{ request()->input('lgst_method') == 'HOME' ? 'selected' : '' }}>宅配
                                        </option>
                                        <option value="FAMILY"
                                            {{ request()->input('lgst_method') == 'FAMILY' ? 'selected' : '' }}>全家取貨
                                        </option>
                                        <option value="STORE"
                                            {{ request()->input('lgst_method') == 'STORE' ? 'selected' : '' }}>門市取貨
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-4 custom-title">
                                    <label>商品類型</label>
                                    <select class="form-control js-select2" name="product_type" id="product_type">
                                        <option value="">全部</option>
                                        <option value="N"
                                            {{ request()->input('product_type') == 'N' ? 'selected' : '' }}>一般品
                                        </option>
                                        <option value="G"
                                            {{ request()->input('product_type') == 'G' ? 'selected' : '' }}>贈品
                                        </option>
                                        <option value="A"
                                            {{ request()->input('product_type') == 'A' ? 'selected' : '' }}>加購品
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-4 custom-title">
                                    <label>上架狀態</label>
                                    <select class="form-control js-select2" name="approval_status" id="approval_status">
                                        <option value="">全部</option>
                                        <option value="NA"
                                            {{ request()->input('approval_status') == 'NA' ? 'selected' : '' }}>
                                            未設定
                                        </option>
                                        <option value="REVIEWING"
                                            {{ request()->input('approval_status') == 'REVIEWING' ? 'selected' : '' }}>
                                            上架申請
                                        </option>
                                        <option value="APPROVED_STATUS_ON"
                                            {{ request()->input('approval_status') == 'APPROVED_STATUS_ON' ? 'selected' : '' }}>
                                            商品上架
                                        </option>
                                        <option value="REJECTED"
                                            {{ request()->input('approval_status') == 'REJECTED' ? 'selected' : '' }}>
                                            上架駁回
                                        </option>
                                        <option value="APPROVED_STATUS_OFF"
                                            {{ request()->input('approval_status') == 'APPROVED_STATUS_OFF' ? 'selected' : '' }}>
                                            商品下架
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-block d-md-grid custom-outer">
                                <div class="mb-4 custom-title">
                                    <label>上架時間</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-group mb-0">
                                            <div class="input-group" id="start_launched_at_start_flatpickr">
                                                <input type="text" class="form-control" name="start_launched_at_start" id="start_launched_at_start" value="{{ request()->input('start_launched_at_start') }}" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <span class="fa-solid fa-calendar-days"></span>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <label>～</label>
                                        <div class="form-group mb-0">
                                            <div class="input-group" id="start_launched_at_end_flatpickr">
                                                <input type="text" class="form-control" name="start_launched_at_end" id="start_launched_at_end" value="{{ request()->input('start_launched_at_end') }}" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <span class="fa-solid fa-calendar-days"></span>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4 custom-title">
                                        <label>查詢筆數上限</label>
                                        <input type="number" class="form-control" name="limit" id="limit" readonly
                                            value="500">
                                </div>
                                <div class="mb-4 custom-title">
                                    <label>建檔時間</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-group mb-0">
                                            <div class="input-group" id="create_at_start_flatpickr">
                                                <input type="text" class="form-control" name="create_at_start" id="create_at_start" value="{{ request()->input('create_at_start') }}" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <span class="fa-solid fa-calendar-days"></span>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <label class="control-label">～</label>
                                        <div class="form-group mb-0">
                                            <div class="input-group" id="create_at_start_end_flatpickr">
                                                <input type="text" class="form-control" name="create_at_start_end" id="create_at_start_end" value="{{ request()->input('create_at_start_end') }}" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <span class="fa-solid fa-calendar-days"></span>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right" style="padding: 0 5px;">
                                <button class="btn btn-warning"><span class="fa-solid fa-magnifying-glass"></span> 查詢</button>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <hr>
                        <div class="overflow-auto">
                            <table class="table table-striped table-bordered table-hover" id="table_data">
                                <thead>
                                    <tr>
                                        <th style="width: auto">功能</th>
                                        <th style="width: auto">項次</th>
                                        <th style="width: auto; max-width: 50px">供應商</th>
                                        <th style="width: auto">商品序號</th>
                                        <th style="width: auto; max-width: 160px">商品名稱</th>
                                        <th style="width: auto">售價(含稅)</th>
                                        <th style="width: auto">成本(含稅)</th>
                                        <th style="width: auto">毛利(%)</th>
                                        <th style="width: auto">商品類型</th>
                                        <th style="width: auto">建檔日期</th>
                                        <th style="width: auto">上架狀態</th>
                                        <th style="width: auto">上架時間</th>
                                        <th style="width: auto">下架時間</th>
                                        <th style="width: auto">開賣時間</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $key => $val)
                                        <tr>
                                            <td>
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('product_review_register.show', $val->id) }}">
                                                    <span class="fa-solid fa-magnifying-glass"></span></a>
                                                @if ($share_role_auth['auth_update'])
                                                    @if ($val->launched_status == '未設定' || $val->launched_status == '上架駁回' || $val->launched_status == '商品下架')
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('product_review_register.edit', $val->id) }}">提案
                                                        </a>
                                                    @endif
                                                @endif
                                                @if ($share_role_auth['auth_update'])
                                                    @if ($val->launched_status == '商品上架')
                                                        <button class="btn btn-danger btn-sm offProduct" type="button"
                                                            data-json="{{ $val }}"> 下架</button>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $key += 1 }}</td>
                                            <td>{{ $val->supplier_name }}</td>
                                            <td>{{ $val->product_no }}</td>
                                            <td>{{ $val->product_name }}</td>
                                            <td>{{ $val->selling_price }}</td>
                                            <td>{{ $val->item_cost }}</td>
                                            <td>{{ $val->gross_margin }}</td>
                                            <td>
                                                @switch($val->product_type)
                                                    @case('N')
                                                        一般品
                                                    @break
                                                    @case('G')
                                                        贈品
                                                    @break
                                                    @case('A')
                                                        加購品
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                {{ $val->created_at }}
                                            </td>
                                            <td>
                                                {{ $val->launched_status }}
                                            </td>
                                            <td>{{ $val->start_launched_at }}</td>
                                            <td>{{ $val->end_launched_at }}</td>
                                            <td>
                                                {{$val->start_selling_at}}
                                            </td>
                                        </tr>
                                    @endforeach
                                    {{-- {{$category_products_list}} --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#table_data').DataTable({
                "order": [
                    [1, "asc"]
                ]
            });

            $("#stock_type").select2();
            $("#selling_channel").select2();
            $('#web_category_hierarchy_id').select2()
            $("#lgst_method").select2();
            $("#product_type").select2();
            $("#supplier_id").select2();
            $("#category_id").select2();
            $('#approval_status').select2();

            let start_launched_at_start_flatpickr = flatpickr("#start_launched_at_start_flatpickr", {
                dateFormat: "Y-m-d",
                maxDate: $("#start_launched_at_end").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    start_launched_at_end_flatpickr.set('minDate', dateStr);
                },
            });

            let start_launched_at_end_flatpickr = flatpickr("#start_launched_at_end_flatpickr", {
                dateFormat: "Y-m-d",
                minDate: $("#start_launched_at_start").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    start_launched_at_start_flatpickr.set('maxDate', dateStr);
                },
            });

            let create_at_start_flatpickr = flatpickr("#create_at_start_flatpickr", {
                dateFormat: "Y-m-d",
                maxDate: $("#create_at_start_end").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    create_at_start_end_flatpickr.set('minDate', dateStr);
                },
            });

            let create_at_start_end_flatpickr = flatpickr("#create_at_start_end_flatpickr", {
                dateFormat: "Y-m-d",
                minDate: $("#create_at_start").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    create_at_start_flatpickr.set('maxDate', dateStr);
                },
            });

            $(document).on("click", ".offProduct", function() {
                let product = $(this).data('json');
                let msg = '您確定要將《' + product.product_no + '-' + product.product_name+' 》下架嗎？';
                var check = confirm(msg);
                if (check) {
                    var req = async () => {
                        const response = await axios.post('/backend/product_review_register/ajax', {
                            type: 'checkProductInCampaignIsset',
                            product_id: product.id,
                        });
                        if (!response.data.status) {
                            if (confirm('商品存在上架中的行銷活動，您確認要下架商品嗎？')) {
                                offProduct(product.id);
                            }
                        } else {
                            offProduct(product.id);
                        }
                    }
                    req();
                }
            })

            function offProduct(product_id) {
                axios.post('/backend/product_review_register/ajax', {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        type: 'offProduct',
                        product_id: product_id,
                    })
                    .then(function(response) {
                        if (response.data.status) {
                            alert('下架成功');
                            history.go(0);
                        } else {
                            alert('下架失敗');
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            }
        });
    </script>
@endsection
