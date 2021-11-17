@extends('Backend.master')

@section('title', '廣告版位')

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-truck"></i>廣告版位</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕 -->
                    <div class="panel-heading">
                        <form role="form" id="select-form" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <h5>適用頁面</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-applicable-page" name="applicable_page" id="applicable_page">
                                            <option></option>
                                            @foreach ($applicable_page as $obj)
                                                <option value='{{ $obj->code }}' {{ isset($query_data['applicable_page']) && $obj->code == $query_data['applicable_page'] ? 'selected' : '' }}>{{ $obj->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>適用設備</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-device" name="device" id="device">
                                            <option value=''></option>
                                            <option value='desktop' {{ isset($query_data['device']) && $query_data['device'] == 'desktop' ? 'selected' : '' }}>Desktop</option>
                                            <option value='mobile' {{ isset($query_data['device']) && $query_data['device'] == 'mobile' ? 'selected' : '' }}>Mobile</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>狀態</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-status" name="status" id="status">
                                            <option value=''></option>
                                            <option value='enabled' {{ isset($query_data['status']) && $query_data['status'] == 'enabled' ? 'selected' : '' }}>啟用</option>
                                            <option value='disabled' {{ isset($query_data['status']) && $query_data['status'] == 'disabled' ? 'selected' : '' }}>關閉</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2 text-right">
                                    <div class="col-sm-12">
                                        {{-- @if ($share_role_auth['auth_query']) --}}
                                            <button class="btn btn-warning"><i class="fa fa-search"></i> 查詢</button>
                                        {{-- @endif --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div id="table_list_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr role="row">
                                        <th class="col-sm-1 ">功能</th>
                                        <th class="col-sm-1 ">適用頁面</th>
                                        <th class="col-sm-1 ">代碼</th>
                                        <th class="col-sm-1 ">描述</th>
                                        <th class="col-sm-1 ">Mobile適用</th>
                                        <th class="col-sm-1 ">Desktop適用</th>
                                        <th class="col-sm-1 ">上架類型</th>
                                        <th class="col-sm-1 ">狀態</th>
                                        <th class="col-sm-1 ">備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ad_slots as $obj)
                                        <tr>
                                            <td>
                                                {{-- @if ($share_role_auth['auth_query']) --}}
                                                    <button type="button" class="btn btn-info btn-sm slot_detail" data-toggle="modal" data-target="#slot_detail"  data-slot="{{ $obj->id }}">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                {{-- @endif --}}

                                                {{-- @if ($share_role_auth['auth_update']) --}}
                                                    <a class="btn btn-info btn-sm" href="{{ route('supplier') }}/{{ $obj->id }}/edit" value="1">
                                                        編輯
                                                    </a>
                                                {{-- @endif --}}
                                            </td>
                                            <td>{{ $obj->description }}</td>
                                            <td>{{ $obj->slot_code }}</td>
                                            <td>{{ $obj->slot_desc }}</td>
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
                                                @isset($slot_type_option[$obj->slot_type])
                                                    {{ $slot_type_option[$obj->slot_type] }}
                                                @endisset
                                            </td>
                                            <td>
                                                @isset($active_option[$obj->active])
                                                    {{ $active_option[$obj->active] }}
                                                @endisset
                                            </td>
                                            <td>{{ $obj->remark }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Backend.Advertisement.detail')
        <!-- /.modal -->

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('.js-select2-applicable-page').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('.js-select2-device').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('.js-select2-status').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            let ad_slots_json = @json($ad_slots);
            let slot_type_option_json = @json($slot_type_option);

            $('.slot_detail').on('click', function() {
                    let slot_id = $(this).attr("data-slot");

                    $.each(ad_slots_json, function(index, val) {
                        if (slot_id == val.id) {
                            $('#show_applicable_page').val(val.description);
                            $('#show_slot_code').val(val.slot_code);
                            $('#show_slot_desc').val(val.slot_desc);

                            if (val.is_mobile_applicable) {
                                $('#is_mobile_applicable_enabled').prop('checked', true);
                            } else {
                                $('#is_mobile_applicable_disabled').prop('checked', true);
                            }

                            if (val.is_desktop_applicable) {
                                $('#is_desktop_applicable_enabled').prop('checked', true);
                            } else {
                                $('#is_desktop_applicable_disabled').prop('checked', true);
                            }

                            $('#show_slot_type').val(slot_type_option_json[val.slot_type]);

                            if (val.active) {
                                $('#active_enabled').prop('checked', true);
                            } else {
                                $('#active_disabled').prop('checked', true);
                            }

                            $('#remark').val(val.remark);

                            return;
                        }
                    });
            });
        });
    </script>
@endsection
