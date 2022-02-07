@extends('Backend.master')

@section('title', '庫存彙總表')

@section('style')

@endsection

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i> 庫存彙總表</h1>
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

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">庫存狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-order-status-code" id="stock_status"
                                                    name="stock_status">
                                                <option></option>
                                                <option value='0' {{ request()->input('stock_status') == '0' ? 'selected' : '' }}>正常</option>
                                                <option value='1' {{ request()->input('stock_status') == '1' ? 'selected' : '' }}>低於正常庫存量</option>
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
                                            <label class="control-label">供應商</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-status-code"
                                                    id="supplier" name="supplier">
                                                <option value=""></option>
                                                @foreach ($supplier as $v)
                                                    <option value='{{ $v['id'] }}'
                                                        {{ request()->input('supplier') && $v['id'] == request()->input('supplier') ? 'selected' : '' }}>
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
                                        <label class="control-label">庫存總量：{{ number_format($inventories->sum('original_stock_qty')) }}</label>
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
                                                <button data-url="{{ route('inventory.export_excel') }}" class="btn btn-primary" id="btn-export-excel" type="button"><i
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
                                   id="table_list">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">倉庫</th>
                                        <th class="text-nowrap">Item編號</th>
                                        <th class="text-nowrap">商品名稱</th>
                                        <th class="text-nowrap">規格一</th>
                                        <th class="text-nowrap">規格二</th>
                                        <th class="text-nowrap">POS品號</th>
                                        <th class="text-nowrap">庫存類型</th>
                                        <th class="text-nowrap">安全庫存量</th>
                                        <th class="text-nowrap">庫存量</th>
                                        <th class="text-nowrap">售價(含稅)</th>
                                        <th class="text-nowrap">平均成本(含稅)</th>
                                        <th class="text-nowrap">毛利率</th>
                                        <th class="text-nowrap">庫存成本(含稅)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventories as $inventory)
                                        <tr>
                                            <td>{{ $inventory->name }}</td>
                                            <td>{{ $inventory->item_no }}</td>
                                            <td>{{ $inventory->product_name }}</td>
                                            <td>{{ $inventory->spec_1_value }}</td>
                                            <td>{{ $inventory->spec_2_value }}</td>
                                            <td>{{ $inventory->pos_item_no }}</td>
                                            <td>{{ $inventory->stock_type }}</td>
                                            <td class="text-right">{{ $inventory->safty_qty }}</td>
                                            <td class="text-right">
                                                @if($inventory->is_dangerous == 1)
                                                    <span class="label-danger text-white" style="color: #fff;">
                                                    <i class="fa fa-exclamation-triangle"></i>
                                                    {{ $inventory->stock_qty }}
                                                </span>
                                                @else
                                                    {{ $inventory->stock_qty }}
                                                @endif
                                            </td>
                                            <td class="text-right">{{ $inventory->selling_price }}</td>
                                            <td class="text-right">{{ $inventory->item_cost }}</td>
                                            <td class="text-right">{{ $inventory->gross_margin }}</td>
                                            <td class="text-right">{{ $inventory->stock_amount }}</td>
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
            // 匯出excel
            $('#btn-export-excel').on('click', function() {
                let url = $(this).data('url');

                axios.get(url, {
                    params: {
                        warehouse:$('#warehouse').val(),
                        stock_type:$('#stock_type').val(),
                        stock_status:$('#stock_status').val(),
                        item_no_start:$('#item_no_start').val(),
                        ordered_date_end:$('#ordered_date_end').val(),
                        product_name:$('#product_name').val(),
                        product_name:$('#product_name').val(),
                    },
                    responseType: 'blob',
                })
                .then(function(response) {
                    saveAs(response.data, "inventories.xlsx");
                })
                .catch(function(error) {
                    console.log(error);
                });
            });
        });
    </script>
@endsection
