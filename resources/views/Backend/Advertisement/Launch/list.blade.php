@extends('Backend.master')

@section('title', '廣告上架管理')

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>廣告上架管理</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕 -->
                    <div class="panel-heading">
                        <form role="form" id="select-form" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="col-sm-3 text-right">
                                        <h5>版位</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-block" name="block" id="block">
                                            <option></option>
                                            @isset($blocks)
                                                @foreach ($blocks as $key => $value)
                                                    <option value='{{ $key }}'
                                                        {{ isset($query_data['block']) && $key == $query_data['block'] ? 'selected' : '' }}>
                                                        {{ $value }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-4 text-right">
                                        <h5>上下架狀態</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <select class="form-control js-select2-launch-status" name="launch_status"
                                            id="launch_status">
                                            <option value=''></option>
                                            <option value='enabled'
                                                {{ isset($query_data['launch_status']) && $query_data['launch_status'] == 'enabled' ? 'selected' : '' }}>
                                                上架</option>
                                            <option value='disabled'
                                                {{ isset($query_data['launch_status']) && $query_data['launch_status'] == 'disabled' ? 'selected' : '' }}>
                                                下架</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="col-sm-3 text-right">
                                        <h5>上架起日：</h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class='input-group date' id='datetimepicker_start_at'>
                                                <input type='text'
                                                    class="form-control datetimepicker-input"
                                                    data-target="#datetimepicker_start_at" name="start_at" id="start_at"
                                                    value="{{ $query_data['start_at'] ?? '' }}" />
                                                <span class="input-group-addon" data-target="#datetimepicker_start_at"
                                                    data-toggle="datetimepicker">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <h5>～</h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class='input-group date' id='datetimepicker_end_at'>
                                                <input type='text' class="form-control datetimepicker-input"
                                                    data-target="#datetimepicker_end_at" name="end_at" id="end_at"
                                                    value="{{ $query_data['end_at'] ?? '' }}" />
                                                <span class="input-group-addon" data-target="#datetimepicker_end_at"
                                                    data-toggle="datetimepicker">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    {{-- @if ($share_role_auth['auth_query']) --}}
                                    <button class="btn btn-warning"><i class="fa fa-search"></i> 查詢</button>
                                    {{-- @endif --}}
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            {{-- @if ($share_role_auth['auth_create']) --}}
                            <div class="col-sm-2">
                                <a href="{{ route('advertisemsement_launch.create') }}" class="btn btn-block btn-warning btn-sm"
                                    id="btn-create"><i class="fa fa-plus"></i> 新增</a>
                            </div>
                            {{-- @endif --}}
                        </div>
                        <hr/>
                        <div id="table_list_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr role="row">
                                        <th class="col-sm-1 ">功能</th>
                                        <th class="col-sm-1 ">適用頁面</th>
                                        <th class="col-sm-1 ">版位代碼</th>
                                        <th class="col-sm-1 ">版位名稱</th>
                                        <th class="col-sm-1 ">Mobile適用</th>
                                        <th class="col-sm-1 ">Desktop適用</th>
                                        <th class="col-sm-1 ">上下架狀態</th>
                                        <th class="col-sm-1 ">上架時間</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($ad_slot_contents)
                                        @foreach ($ad_slot_contents as $obj)
                                            <tr>
                                                <td>
                                                    {{-- @if ($share_role_auth['auth_query']) --}}
                                                    <button type="button" class="btn btn-info btn-sm slot_content_detail"
                                                        data-slot-content="{{ $obj->slot_content_id }}">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                    {{-- @endif --}}

                                                    {{-- @if ($share_role_auth['auth_update']) --}}
                                                    <a class="btn btn-info btn-sm"
                                                        href="{{ route('advertisemsement_launch.edit', $obj->slot_content_id) }}">
                                                        編輯
                                                    </a>
                                                    {{-- @endif --}}
                                                </td>
                                                <td>{{ $obj->description ?? '' }}</td>
                                                <td>{{ $obj->slot_code ?? '' }}</td>
                                                <td>{{ $obj->slot_desc ?? '' }}</td>
                                                <td>
                                                    @if ($obj->is_mobile_applicable)
                                                        <i class="fa fa-check"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($obj->is_desktop_applicable)
                                                        <i class="fa fa-check"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $obj->launch_status }}
                                                </td>
                                                <td>
                                                    @isset($obj->start_at, $obj->end_at)
                                                        {{ $obj->start_at }} ~ {{ $obj->end_at }}
                                                    @endisset
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endisset
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Backend.Advertisement.Block.detail')
        <!-- /.modal -->

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('.js-select2-block').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('.js-select2-launch-status').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('#datetimepicker_start_at').datetimepicker({
                format: 'YYYY-MM-DD',
                showClear: true,
            });

            $('#datetimepicker_end_at').datetimepicker({
                format: 'YYYY-MM-DD',
                showClear: true,
            });

            $("#datetimepicker_start_at").on("dp.change", function(e) {
                if ($('#end_at').val()) {
                    $('#datetimepicker_end_at').datetimepicker('minDate', e.date);
                }
            });

            $("#datetimepicker_end_at").on("dp.change", function(e) {
                if ($('#start_at').val()) {
                    $('#datetimepicker_start_at').datetimepicker('maxDate', e.date);
                }
            });

            let slot_type_option_json = @json(config('uec.ad_slot_type_option'));

            // $('.slot_detail').on('click', function() {
            //     let slot_id = $(this).attr("data-slot");

            //     axios.post('/backend/advertisemsement_block/ajax', {
            //         slot_id: slot_id
            //     })
            //     .then(function (response) {
            //         $('#modal_applicable_page').val(response.data.ad_slot.description);
            //         $('#modal_slot_code').val(response.data.ad_slot.slot_code);
            //         $('#modal_slot_desc').val(response.data.ad_slot.slot_desc);

            //         if (response.data.ad_slot.is_mobile_applicable) {
            //             $('#modal_is_mobile_applicable_enabled').prop('checked', true);
            //         } else {
            //             $('#modal_is_mobile_applicable_disabled').prop('checked', true);
            //         }

            //         if (response.data.ad_slot.is_desktop_applicable) {
            //             $('#modal_is_desktop_applicable_enabled').prop('checked', true);
            //         } else {
            //             $('#modal_is_desktop_applicable_disabled').prop('checked', true);
            //         }

            //         $('#modal_slot_type').val(slot_type_option_json[response.data.ad_slot.slot_type]);

            //         if (response.data.ad_slot.active) {
            //             $('#modal_active_enabled').prop('checked', true);
            //         } else {
            //             $('#modal_active_disabled').prop('checked', true);
            //         }

            //         $('#modal_remark').val(response.data.ad_slot.remark);

            //         $('#slot_detail').modal('show');
            //     })
            //     .catch(function (error) {
            //         console.log(error);
            //     });
            // });
        });
    </script>
@endsection
