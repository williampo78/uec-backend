@extends('Backend.master')

@section('title', '金流對帳單')

@section('style')

@endsection

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i> 金流對帳單</h1>
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
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label class="control-label">日期</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker_date_start'>
                                                    <input type='text'
                                                           class="form-control datetimepicker-input search-limit-group"
                                                           data-target="#datetimepicker_date_start"
                                                           name="date_start" id="date_start"
                                                           value="{{ request()->input('date_start') }}"
                                                           autocomplete="off" />
                                                    <span class="input-group-addon"
                                                          data-target="#datetimepicker_date_start"
                                                          data-toggle="datetimepicker">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 text-center">
                                            <label class="control-label">～</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker_date_end'>
                                                    <input type='text'
                                                           class="form-control datetimepicker-input search-limit-group"
                                                           data-target="#datetimepicker_date_end"
                                                           name="date_end" id="date_end"
                                                           value="{{ request()->input('date_end') }}"
                                                           autocomplete="off" />
                                                    <span class="input-group-addon"
                                                          data-target="#datetimepicker_date_end"
                                                          data-toggle="datetimepicker">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">金流方式</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-status-code"
                                                    id="payment_method" name="payment_method">
                                                <option></option>
                                                @if (config()->has('uec.payment_method_options'))
                                                    @foreach (config('uec.payment_method_options') as $key => $value)
                                                        <option value='{{ $key }}'
                                                            {{ $key == request()->input('payment_method') ? 'selected' : '' }}>
                                                            {{ $value }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-status-code"
                                                    id="payment_status" name="payment_status">
                                                <option></option>
                                                @if (config()->has('uec.payment_status_options'))
                                                    @foreach (config('uec.payment_status_options') as $key => $value)
                                                        <option value='{{ $key }}'
                                                            {{ $key == request()->input('payment_status') ? 'selected' : '' }}>
                                                            {{ $value }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <br />
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3"></div>
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
                                                <button data-url="{{ route('order_payments_report.export_excel') }}" class="btn btn-primary" id="btn-export-excel" type="button">
                                                    <i class="fa fa-file-excel-o"></i>
                                                    匯出EXCEL
                                                </button>
                                            @endif

                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-warning" id="btn-search">
                                                    <i class="fa fa-search"></i>
                                                    查詢
                                                </button>
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
                                   id="order_refund_table">
                                <thead>
                                    <tr>
                                        <th>項次</th>
                                        <th>日期</th>
                                        <th>訂單編號</th>
                                        <th>類型</th>
                                        <th>金流方式</th>
                                        <th>分期期數</th>
                                        <th>狀態</th>
                                        <th>金額</th>
                                        <th>發票號碼</th>
                                        <th>發票日期</th>
                                        <th>備註</th>
                                        <th>收款行</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderPaymentsReports as $orderPaymentsReport)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $orderPaymentsReport->created_at }}</td>
                                        <td>{{ $orderPaymentsReport->order_no }}</td>
                                        <td>{{ $orderPaymentsReport->payment_type }}</td>
                                        <td>{{ $orderPaymentsReport->payment_method }}</td>
                                        <td></td>
                                        <td>{{ $orderPaymentsReport->status_desc }}</td>
                                        <td>{{ $orderPaymentsReport->amount }}</td>
                                        <td>{{ $orderPaymentsReport->invoice_no }}</td>
                                        <td>{{ $orderPaymentsReport->invoice_date }}</td>
                                        <td>{{ $orderPaymentsReport->record_created_reason }}</td>
                                        <td>{{ $orderPaymentsReport->bank_name }}</td>
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
    <script>
        $(function() {
            $('#datetimepicker_date_start').datetimepicker({
                format: 'YYYY-MM-DD',
                showClear: true,
            });

            $('#datetimepicker_date_end').datetimepicker({
                format: 'YYYY-MM-DD',
                showClear: true,
            });

            $("#datetimepicker_date_start").on("dp.change", function(e) {
                if ($('#date_end').val()) {
                    $('#datetimepicker_date_end').datetimepicker('minDate', e.date);
                }
            });

            $("#datetimepicker_date_end").on("dp.change", function(e) {
                if ($('#date_start').val()) {
                    $('#datetimepicker_date_start').datetimepicker('maxDate', e.date);
                }
            });

            $('#order_refund_table').dataTable({
                "aaSorting": []
            });

            $.validator.addMethod("date_grid", function(value, element) {

                let start_date = $('#date_start').val();
                let end_date = $('#date_end').val();
                if(start_date == '' || end_date == ''){
                    return true;
                }

                let start_day = new Date(start_date);
                let end_day = new Date(end_date);
                let diffTime = Math.abs(end_day - start_day);
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if(diffDays > 90){
                    return false;
                }
                return true;

            }, "開始和結束不得超過90天");

            // 驗證表單
            $("#search-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-search').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    date_start: {
                        required:true,
                        date_grid:true
                    },
                    date_end: {
                        required:true,
                        date_grid:true
                    }
                },
                messages: {
                    date_start: {
                        required:'必填',
                    },
                    date_end: {
                        required:'必填',
                    }
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
                        date_start:$('#date_start').val(),
                        date_end:$('#date_end').val(),
                        payment_method:$('#payment_method').val(),
                        payment_status:$('#payment_status').val(),
                    },
                    responseType: 'blob',
                })
                .then(function(response) {
                    saveAs(response.data, "order_payments_reports.xlsx");
                })
                .catch(function(error) {
                    console.log(error);
                });
            });
        });
    </script>
@endsection
