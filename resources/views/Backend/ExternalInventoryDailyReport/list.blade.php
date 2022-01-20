@extends('Backend.master')

@section('title', '外倉(秋雨)庫存日報表')

@section('style')

@endsection

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i> 外倉(秋雨)庫存日報表</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕 -->
                    <div class="panel-heading">
                        <form id="search-form" class="form-horizontal" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <h5>庫存日期<span class="text-danger">*</span></h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="form-group">
                                            <div class='input-group date' id='datetimepicker_counting_date'>
                                                <input type='text' class="form-control" name="counting_date"
                                                       id="counting_date"
                                                       value="{{ empty(request()->input('counting_date')) ? date('Y-m-d', strtotime('-1 day')) : request()->input('counting_date') }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">倉庫</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-order-status-code" id="warehouse"
                                                    name="warehouse">
                                                <option value=""></option>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value='{{ $warehouse['id'] }}'
                                                        {{ request()->input('warehouse') && $warehouse['id'] == request()->input('warehouse') ? 'selected' : '' }}>
                                                        {{ $warehouse['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">庫存類型</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-order-status-code" id="stock_type"
                                                    name="stock_type">
                                                <option value=""></option>
                                                <option value="A"
                                                    {{ request()->input('stock_type') == 'A' ? 'selected' : '' }}>買斷[A]
                                                </option>
                                                <option value="B"
                                                    {{ request()->input('stock_type') == 'B' ? 'selected' : '' }}>寄售[B]</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label class="control-label">Item編號</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class='input-group'>
                                                    <input type='text'
                                                           class="form-control"
                                                           name="item_no_start" id="item_no_start"
                                                           value="{{ request()->input('item_no_start') }}"
                                                           autocomplete="off" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 text-center">
                                            <label class="control-label">～</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class='input-group'>
                                                    <input type='text'
                                                           class="form-control"
                                                           name="item_no_end" id="item_no_end"
                                                           value="{{ request()->input('item_no_end') }}"
                                                           autocomplete="off" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">商品名稱
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control search-limit-group" name="product_name" id="product_name"
                                                   value="{{ request()->input('product_name') }}" placeholder="模糊查詢"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">庫存狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-order-status-code" id="is_dangerous"
                                                    name="is_dangerous">
                                                <option></option>
                                                <option value='0' {{ request()->input('is_dangerous') == '0' ? 'selected' : '' }}>正常</option>
                                                <option value='1' {{ request()->input('is_dangerous') == '1' ? 'selected' : '' }}>低於正常庫存量</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">供應商</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-status-code"
                                                    id="supplier_id" name="supplier_id">
                                                <option value=""></option>
                                                @foreach ($supplier as $v)
                                                    <option value='{{ $v['id'] }}'
                                                        {{ request()->input('supplier_id') && $v['id'] == request()->input('supplier_id') ? 'selected' : '' }}>
                                                        {{ $v['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">庫存總量：{{ number_format($dailyReports->sum('original_stock_qty')) }}</label>
                                    </div>
                                    <div class="col-sm-9"></div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9"></div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-9 text-right">
                                            @if ($share_role_auth['auth_export'])
                                                <button data-url="{{ route('external_inventory_daily_report.export_excel') }}" class="btn btn-primary" id="btn-export-excel" type="button"><i
                                                        class="fa fa-file-excel-o"></i>
                                                    匯出EXCEL</button>
                                            @endif

                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-warning" id="btn-search"><i
                                                        class="fa fa-search"></i>
                                                    查詢</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                   id="inventory_table">
                                <thead>
                                    <tr>
                                        <th>庫存日期</th>
                                        <th>倉庫</th>
                                        <th>Item編號</th>
                                        <th>商品名稱</th>
                                        <th>規格一</th>
                                        <th>規格二</th>
                                        <th>POS品號</th>
                                        <th>庫存類型</th>
                                        <th>供應商</th>
                                        <th>到期日</th>
                                        <th>是否追加</th>
                                        <th>安全庫存量</th>
                                        <th>庫存量</th>
                                        <th>售價(未稅)</th>
                                        <th>平均成本(未稅)</th>
                                        <th>毛利率(未稅)</th>
                                        <th>庫存成本(未稅)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dailyReports as $dailyReport)
                                        <tr>
                                            <td>{{ $dailyReport->counting_date }}</td>
                                            <td>{{ $dailyReport->warehouse_name }}</td>
                                            <td>{{ $dailyReport->item_no }}</td>
                                            <td>{{ $dailyReport->product_name }}</td>
                                            <td>{{ $dailyReport->spec_1_value }}</td>
                                            <td>{{ $dailyReport->spec_2_value }}</td>
                                            <td>{{ $dailyReport->pos_item_no }}</td>
                                            <td>{{ $dailyReport->stock_type }}</td>
                                            <td>{{ $dailyReport->supplier_name }}</td>
                                            <td>{{ $dailyReport->expiry_date }}</td>
                                            <td>{{ $dailyReport->is_additional_purchase }}</td>
                                            <td>{{ $dailyReport->safty_qty }}</td>
                                            <td>
                                                @if($dailyReport->is_dangerous == 1)
                                                <span class="label-danger text-white" style="color: #fff;">
                                                    <i class="fa fa-exclamation-triangle"></i>
                                                    {{ $dailyReport->stock_qty }}
                                                </span>
                                                @else
                                                    {{ $dailyReport->stock_qty }}
                                                @endif
                                            </td>
                                            <td>{{ $dailyReport->selling_price }}</td>
                                            <td>{{ $dailyReport->item_cost }}</td>
                                            <td>{{ $dailyReport->gross_margin }}</td>
                                            <td>{{ $dailyReport->stock_amount }}</td>
                                        </tr>
                                    @endforeach
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
    <script src="{{ asset('asset/js/FileSaver.min.js') }}"></script>
    <script>
        $(function() {

            $('#datetimepicker_counting_date').datetimepicker({
                format: 'YYYY-MM-DD',
                showClear: true,
            });

            $('#inventory_table').dataTable({
                "aaSorting": []
            });

            // 驗證表單
            $("#search-form").validate({
                submitHandler: function(form) {
                    $('#btn-search').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    counting_date: {
                        required: true,
                    },
                },
                messages: {
                    counting_date: {
                        required: '必填',
                    },
                },
                errorClass: "help-block",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.parent('.input-group').length) {
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

            // 匯出excel
            $('#btn-export-excel').on('click', function() {

                let url = $(this).data('url');

                axios.get(url, {
                    params: {
                        counting_date:$('#counting_date').val(),
                        warehouse:$('#warehouse').val(),
                        stock_type:$('#stock_type').val(),
                        item_no_start:$('#item_no_start').val(),
                        item_no_end:$('#item_no_end').val(),
                        product_name:$('#product_name').val(),
                        is_dangerous:$('#is_dangerous').val(),
                        supplier_id:$('#supplier_id').val(),
                    },
                    responseType: 'blob',
                })
                .then(function(response) {
                    saveAs(response.data, "external_inventory_daily_reports.xlsx");
                })
                .catch(function(error) {
                    console.log(error);
                });
            });
        });
    </script>
@endsection