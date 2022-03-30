@extends('backend.master')
@section('title', '報價單')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-arrow-right-to-bracket"></i> 報價單</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form role="form" id="select-form" method="GET" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="col-sm-2">
                                        <h5>供應商</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control select2-default" name="supplier" id="supplier">
                                            <option value=""></option>
                                            @foreach ($supplier as $v)
                                                <option value='{{ $v['id'] }}'
                                                    {{ $v['id'] == request()->input('supplier') ? 'selected' : '' }}>
                                                    {{ $v['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <h5>供應商統編</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="company_number" id="company_number"
                                            value="{{ request()->input('company_number') }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>狀態</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control select2-default" name="status" id="status">
                                            <option value=''></option>
                                            <option value='drafted'
                                                {{ request()->input('status') == 'drafted' ? 'selected' : '' }}>草稿
                                            </option>
                                            <option value='reviewing'
                                                {{ request()->input('status') == 'reviewing' ? 'selected' : '' }}>簽核中
                                            </option>
                                            <option value='approved'
                                                {{ request()->input('status') == 'approved' ? 'selected' : '' }}>已核准
                                            </option>
                                            <option value='rejected'
                                                {{ request()->input('status') == 'rejected' ? 'selected' : '' }}>已駁回
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="col-sm-2">
                                        <h5>報價日期：</h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group" id="select_start_date_flatpickr">
                                                <input type="text" class="form-control" name="select_start_date" id="select_start_date" value="{{ request()->input('select_start_date') }}" autocomplete="off" data-input />
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
                                        <div class="form-group">
                                            <div class="input-group" id="select_end_date_flatpickr">
                                                <input type="text" class="form-control" name="select_end_date" id="select_end_date" value="{{ request()->input('select_end_date') }}" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <h5>報價單號</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="doc_number" id="doc_number"
                                            value="{{ request()->input('doc_number') }}">
                                    </div>
                                </div>

                                <div class="col-sm-3 text-right">
                                    <div class="col-sm-12">
                                        @if ($share_role_auth['auth_query'])
                                            <button class="btn btn-warning"><i class="fa-solid fa-magnifying-glass"></i></i> 查詢</button>
                                            <button type="button" class="btn btn-danger" id="btn-reset">
                                                <i class="fa-solid fa-eraser"></i> 清除
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            @if ($share_role_auth['auth_create'])
                                <div class="col-sm-2">
                                    <a class="btn btn-block btn-warning btn-sm" href="{{ route('quotation.create') }}">
                                        <i class="fa-solid fa-plus"></i> 新增
                                    </a>
                                </div>
                            @endif
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>報價日期</th>
                                    <th>報價單號</th>
                                    <th>供應商</th>
                                    <th>狀態</th>
                                    <th>送審時間</th>
                                    <th>結案時間</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quotation as $k => $v)
                                    <form id="del-{{ $v['id'] }}" action="/backend/quotation/{{ $v['id'] }}"
                                        method="post">
                                        @method('DELETE')
                                        @csrf
                                    </form>
                                    <tr>
                                        <td>
                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-info btn-sm" data-toggle="modal"
                                                    data-target="#row_detail" data-id="{{ $v['id'] }}"
                                                    onclick="row_detail({{ $v['id'] }});">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            @endif

                                            @if ($share_role_auth['auth_update'] && $v['status_code'] == 'DRAFTED' && $v['created_by'] == Auth::user()->id)
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('quotation.edit', $v['id']) }}">修改</a>
                                            @endif

                                            @if ($share_role_auth['auth_delete'] && $v['status_code'] == 'DRAFTED' && $v['created_by'] == Auth::user()->id)
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="del({{ $v['id'] }} , '{{ $v['doc_number'] }}' );">刪除</button>
                                            @endif
                                        </td>
                                        <td>{{date('Y-m-d', strtotime($v['trade_date']));}}</td>
                                        <td>{{ $v['doc_number'] }}</td>
                                        <td>{{ $v['supplier_name'] }}</td>
                                        <td>
                                            @switch($v['status_code'])
                                                @case('DRAFTED')
                                                    草稿
                                                @break
                                                @case('REVIEWING')
                                                    簽核中
                                                @break
                                                @case('APPROVED')
                                                    已核准
                                                @break
                                                @case('REJECTED')
                                                    已駁回
                                                @break
                                                @default
                                            @endswitch
                                        </td>
                                        <td>{{ $v['submitted_at'] }}</td>
                                        <td>{{ $v['closed_at'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.quotation.detail')
    @section('js')
        <script>
            $(function() {
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
            });

            function del(id, doc_number) {
                if (confirm("確定要刪除報價單" + doc_number + "?")) {
                    document.getElementById('del-' + id).submit();
                }
                return false;
            };
            $('#btn-reset').on('click', function() {
                $('#select-form').find(':text:not("#limit"), select').val('');
                $('#supplier, #status').trigger('change');
            });
        </script>
    @endsection
@endsection
