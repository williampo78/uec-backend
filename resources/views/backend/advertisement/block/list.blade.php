@extends('backend.master')

@section('title', '廣告版位管理')

@section('style')
    <style>
        td .fa-solid.fa-check,
        .form-group .fa-solid.fa-check {
            color: green;
        }

        td .fa-solid.fa-xmark,
        .form-group .fa-solid.fa-xmark {
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
                <h1 class="page-header"><i class="fa-solid fa-list"></i> 廣告版位管理</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕 -->
                    <div class="panel-heading">
                        <form id="search-form" class="form-horizontal" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">適用頁面</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-applicable-page" name="applicable_page"
                                                id="applicable_page">
                                                <option></option>
                                                @foreach ($applicable_pages as $obj)
                                                    <option value='{{ $obj->code }}'
                                                        {{ $obj->code == request()->input('applicable_page') ? 'selected' : '' }}>
                                                        {{ $obj->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">適用設備</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-device" name="device" id="device">
                                                <option value=''></option>
                                                <option value='desktop'
                                                    {{ 'desktop' == request()->input('device') ? 'selected' : '' }}>
                                                    Desktop</option>
                                                <option value='mobile'
                                                    {{ 'mobile' == request()->input('device') ? 'selected' : '' }}>
                                                    Mobile</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-active" name="active" id="active">
                                                <option value=''></option>
                                                <option value='enabled'
                                                    {{ 'enabled' == request()->input('active') ? 'selected' : '' }}>
                                                    啟用</option>
                                                <option value='disabled'
                                                    {{ 'disabled' == request()->input('active') ? 'selected' : '' }}>
                                                    關閉</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-9 text-right">
                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-warning"><i class="fa-solid fa-magnifying-glass"></i>
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
                                    <tr role="row">
                                        <th class="text-nowrap">功能</th>
                                        <th class="text-nowrap">適用頁面</th>
                                        <th class="text-nowrap">代碼</th>
                                        <th class="text-nowrap">描述</th>
                                        <th class="text-nowrap">Mobile適用</th>
                                        <th class="text-nowrap">Desktop適用</th>
                                        <th class="text-nowrap">上架類型</th>
                                        <th class="text-nowrap">圖檔寬度(px)</th>
                                        <th class="text-nowrap">圖檔高度(px))</th>
                                        <th class="text-nowrap">狀態</th>
                                        <th class="text-nowrap">備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ad_slots as $obj)
                                        <tr>
                                            <td>
                                                @if ($share_role_auth['auth_query'])
                                                    <button type="button" class="btn btn-info btn-sm slot_detail"
                                                        data-slot="{{ $obj->id }}" title="檢視">
                                                        <i class="fa-solid fa-magnifying-glass"></i>
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
                                                    <i class="fa-solid fa-check fa-lg"></i>
                                                @else
                                                    <i class="fa-solid fa-xmark fa-lg"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($obj->is_desktop_applicable == 1)
                                                    <i class="fa-solid fa-check fa-lg"></i>
                                                @else
                                                    <i class="fa-solid fa-xmark fa-lg"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @isset(config('uec.ad_slot_type_option')[$obj->slot_type])
                                                    {{ config('uec.ad_slot_type_option')[$obj->slot_type] }}
                                                @endisset
                                            </td>
                                            <td>
                                                @if ($obj->photo_width !== '' && $obj->photo_width > 0)
                                                    {{ $obj->photo_width }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($obj->photo_height !== '' && $obj->photo_height > 0)
                                                    {{ $obj->photo_height }}
                                                @endif
                                            </td>
                                            <td>
                                                @isset(config('uec.active_options')[$obj->active])
                                                    {{ config('uec.active_options')[$obj->active] }}
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
        @include('backend.advertisement.block.detail')
        <!-- /.modal -->

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('.js-select2-applicable-page').select2();
            $('.js-select2-device').select2();
            $('.js-select2-active').select2();

            let slot_type_option_json = @json(config('uec.ad_slot_type_option'));

            $(document).on('click', '.slot_detail', function() {
                let slot_id = $(this).attr("data-slot");

                axios.get(`/backend/advertisemsement_block/${slot_id}`)
                    .then(function(response) {
                        let ad_slot = response.data.ad_slot;

                        $('#modal_applicable_page').empty().append(ad_slot.description);
                        $('#modal_slot_code').empty().append(ad_slot.slot_code);
                        $('#modal_slot_desc').empty().append(ad_slot.slot_desc);

                        if (ad_slot.is_mobile_applicable == 1) {
                            $('#modal_is_mobile_applicable').empty().append(
                                '<i class="fa-solid fa-check fa-lg"></i>');
                        } else {
                            $('#modal_is_mobile_applicable').empty().append(
                                '<i class="fa-solid fa-xmark fa-lg"></i>');
                        }

                        if (ad_slot.is_desktop_applicable == 1) {
                            $('#modal_is_desktop_applicable').empty().append(
                                '<i class="fa-solid fa-check fa-lg"></i>');
                        } else {
                            $('#modal_is_desktop_applicable').empty().append(
                                '<i class="fa-solid fa-xmark fa-lg"></i>');
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
