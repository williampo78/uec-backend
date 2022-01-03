@extends('Backend.master')

@section('title', '廣告版位管理')

@section('style')
    <style>
        .modal-body label,
        .modal-body th {
            color: blue;
        }

        .fa.fa-check {
            color: green;
        }

        .fa.fa-times {
            color: red;
        }

    </style>
@endsection

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i> 廣告版位管理</h1>
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
                                        <select class="form-control js-select2-applicable-page" name="applicable_page"
                                            id="applicable_page">
                                            <option></option>
                                            @foreach ($applicable_pages as $obj)
                                                <option value='{{ $obj->code }}'
                                                    {{ isset($query_data['applicable_page']) && $obj->code == $query_data['applicable_page'] ? 'selected' : '' }}>
                                                    {{ $obj->description }}</option>
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
                                            <option value='desktop'
                                                {{ isset($query_data['device']) && $query_data['device'] == 'desktop' ? 'selected' : '' }}>
                                                Desktop</option>
                                            <option value='mobile'
                                                {{ isset($query_data['device']) && $query_data['device'] == 'mobile' ? 'selected' : '' }}>
                                                Mobile</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>狀態</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-active" name="active" id="active">
                                            <option value=''></option>
                                            <option value='enabled'
                                                {{ isset($query_data['active']) && $query_data['active'] == 'enabled' ? 'selected' : '' }}>
                                                啟用</option>
                                            <option value='disabled'
                                                {{ isset($query_data['active']) && $query_data['active'] == 'disabled' ? 'selected' : '' }}>
                                                關閉</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2 text-right">
                                    <div class="col-sm-12">
                                        @if ($share_role_auth['auth_query'])
                                            <button class="btn btn-warning"><i class="fa fa-search"></i> 查詢</button>
                                        @endif
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
                                                @if ($share_role_auth['auth_query'])
                                                    <button type="button" class="btn btn-info btn-sm slot_detail"
                                                        data-slot="{{ $obj->id }}" title="檢視">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                @endif

                                                @if ($share_role_auth['auth_update'])
                                                    <a class="btn btn-info btn-sm"
                                                        href="{{ route('advertisemsement_block.edit', $obj->id) }}">
                                                        編輯
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $obj->description }}</td>
                                            <td>{{ $obj->slot_code }}</td>
                                            <td>{{ $obj->slot_desc }}</td>
                                            <td>
                                                @if ($obj->is_mobile_applicable == 1)
                                                    <i class="fa fa-check fa-lg"></i>
                                                @else
                                                    <i class="fa fa-times fa-lg"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($obj->is_desktop_applicable == 1)
                                                    <i class="fa fa-check fa-lg"></i>
                                                @else
                                                    <i class="fa fa-times fa-lg"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @isset(config('uec.ad_slot_type_option')[$obj->slot_type])
                                                    {{ config('uec.ad_slot_type_option')[$obj->slot_type] }}
                                                @endisset
                                            </td>
                                            <td>
                                                @isset(config('uec.active_option')[$obj->active])
                                                    {{ config('uec.active_option')[$obj->active] }}
                                                @endisset
                                            </td>
                                            <td>{!! nl2br(e($obj->remark)) !!}</td>
                                        </tr>
                                    @endforeach

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

            $('.js-select2-active').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            let slot_type_option_json = @json(config('uec.ad_slot_type_option'));

            $('#table_list tbody').on('click', '.slot_detail', function() {
                let slot_id = $(this).attr("data-slot");

                axios.post('/backend/advertisemsement_block/ajax/detail', {
                        slot_id: slot_id
                    })
                    .then(function(response) {
                        let ad_slot = response.data.ad_slot;

                        $('#modal_applicable_page').empty().append(ad_slot.description);
                        $('#modal_slot_code').empty().append(ad_slot.slot_code);
                        $('#modal_slot_desc').empty().append(ad_slot.slot_desc);

                        if (ad_slot.is_mobile_applicable == 1) {
                            $('#modal_is_mobile_applicable').empty().append(
                                '<i class="fa fa-check fa-lg"></i>');
                        } else {
                            $('#modal_is_mobile_applicable').empty().append(
                                '<i class="fa fa-times fa-lg"></i>');
                        }

                        if (ad_slot.is_desktop_applicable == 1) {
                            $('#modal_is_desktop_applicable').empty().append(
                                '<i class="fa fa-check fa-lg"></i>');
                        } else {
                            $('#modal_is_desktop_applicable').empty().append(
                                '<i class="fa fa-times fa-lg"></i>');
                        }

                        $('#modal_slot_type').empty().append(slot_type_option_json[ad_slot.slot_type]);

                        if (ad_slot.active == 1) {
                            $('#modal_active').empty().append('啟用');
                        } else {
                            $('#modal_active').empty().append('關閉');
                        }

                        $('#modal_remark').empty().append(ad_slot.remark);

                        $('#slot_detail').modal('show');
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        });
    </script>
@endsection
