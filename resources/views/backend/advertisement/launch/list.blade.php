@extends('backend.layouts.master')

@section('title', '廣告上架管理')

@section('css')
    <style>
        td .fa-solid.fa-check,
        .form-group .fa-solid.fa-check {
            color: green;
        }

        td .fa-solid.fa-xmark,
        .form-group .fa-solid.fa-xmark {
            color: red;
        }

        #start_at_start,
        #start_at_end {
            min-width: 100px;
        }

    </style>
@endsection

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-list"></i> 廣告上架管理</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <form id="search-form" class="form-horizontal" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div style="padding-right:0" class="col-sm-3">
                                            <label class="control-label">版位</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-slot-id" name="slot_id" id="slot_id">
                                                <option></option>
                                                @isset($ad_slots)
                                                    @foreach ($ad_slots as $ad_slot)
                                                        <option value='{{ $ad_slot->id }}'
                                                            {{ $ad_slot->id == request()->input('slot_id') ? 'selected' : '' }}>
                                                            【{{ $ad_slot->slot_code }}】{{ $ad_slot->slot_desc }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div style="padding-right: 0" class="col-sm-4">
                                            <label class="control-label">上下架狀態</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select class="form-control js-select2-launch-status" name="launch_status"
                                                id="launch_status">
                                                <option value=''></option>
                                                <option value='prepare_to_launch'
                                                    {{ 'prepare_to_launch' == request()->input('launch_status') ? 'selected' : '' }}>
                                                    待上架
                                                </option>
                                                <option value='launched'
                                                    {{ 'launched' == request()->input('launch_status') ? 'selected' : '' }}>
                                                    已上架
                                                </option>
                                                <option value='not_launch'
                                                    {{ 'not_launch' == request()->input('launch_status') ? 'selected' : '' }}>
                                                    下架
                                                </option>
                                                <option value='disabled'
                                                    {{ 'disabled' == request()->input('launch_status') ? 'selected' : '' }}>
                                                    關閉
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-5">
                                    <div style="padding-right: 0" class="col-sm-3">
                                        <label class="control-label">上架起日</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group" id="start_at_start_flatpickr">
                                                <input type="text" class="form-control" name="start_at_start" id="start_at_start" value="{{ request()->input('start_at_start') }}" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 text-center">
                                        <label class="control-label">～</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="input-group" id="start_at_end_flatpickr">
                                                <input type="text" class="form-control" name="start_at_end" id="start_at_end" value="{{ request()->input('start_at_end') }}" autocomplete="off" data-input />
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
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div style="padding-right: 0" class="col-sm-3">
                                            <label class="control-label">版位標題</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="slot_title" id="slot_title"
                                                value="{{ request()->input('slot_title') }}" placeholder="模糊查詢" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-9 text-right">
                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-warning"><i class="fa-solid fa-magnifying-glass"></i> 查詢</button>
                                            @endif
                                        </div>
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
                                    <a href="{{ route('advertisemsement_launch.create') }}"
                                        class="btn btn-block btn-warning btn-sm" id="btn-create">
                                        <i class="fa-solid fa-plus"></i> 新增
                                    </a>
                                </div>
                            @endif
                        </div>
                        <hr />
                        <div class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr role="row">
                                        <th class="text-nowrap">功能</th>
                                        <th class="text-nowrap">適用頁面</th>
                                        <th class="text-nowrap">版位代碼</th>
                                        <th class="text-nowrap">版位名稱</th>
                                        <th class="text-nowrap">Mobile適用</th>
                                        <th class="text-nowrap">Desktop適用</th>
                                        <th class="text-nowrap">上下架狀態</th>
                                        <th class="text-nowrap">上架時間</th>
                                        <th class="text-nowrap">版位標題</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($ad_slot_contents)
                                        @foreach ($ad_slot_contents as $ad_slot_content)
                                            <tr>
                                                <td class="text-nowrap">
                                                    @if ($share_role_auth['auth_query'])
                                                        <button type="button" class="btn btn-info btn-sm slot_content_detail"
                                                            data-slot-content-id="{{ $ad_slot_content->slot_content_id }}"
                                                            title="檢視">
                                                            <i class="fa-solid fa-magnifying-glass"></i>
                                                        </button>
                                                    @endif

                                                    @if ($share_role_auth['auth_update'])
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('advertisemsement_launch.edit', $ad_slot_content->slot_content_id) }}">
                                                            編輯
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $ad_slot_content->description ?? '' }}</td>
                                                <td>{{ $ad_slot_content->slot_code ?? '' }}</td>
                                                <td>{{ $ad_slot_content->slot_desc ?? '' }}</td>
                                                <td>
                                                    @if ($ad_slot_content->is_mobile_applicable == 1)
                                                        <i class="fa-solid fa-check fa-lg"></i>
                                                    @else
                                                        <i class="fa-solid fa-xmark fa-lg"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($ad_slot_content->is_desktop_applicable == 1)
                                                        <i class="fa-solid fa-check fa-lg"></i>
                                                    @else
                                                        <i class="fa-solid fa-xmark fa-lg"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $ad_slot_content->launch_status }}
                                                </td>
                                                <td>
                                                    @isset($ad_slot_content->start_at, $ad_slot_content->end_at)
                                                        {{ $ad_slot_content->start_at }} ~ {{ $ad_slot_content->end_at }}
                                                    @endisset
                                                </td>
                                                <td>{{ $ad_slot_content->slot_title ?? '' }}</td>
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
        @include('backend.advertisement.launch.detail')
        <!-- /.modal -->

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('.js-select2-slot-id').select2();
            $('.js-select2-launch-status').select2();

            let start_at_start_flatpickr = flatpickr("#start_at_start_flatpickr", {
                dateFormat: "Y-m-d",
                maxDate: $("#start_at_end").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    start_at_end_flatpickr.set('minDate', dateStr);
                },
            });

            let start_at_end_flatpickr = flatpickr("#start_at_end_flatpickr", {
                dateFormat: "Y-m-d",
                minDate: $("#start_at_start").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    start_at_start_flatpickr.set('maxDate', dateStr);
                },
            });

            $(document).on('click', '.slot_content_detail', function() {
                let slot_content_id = $(this).attr("data-slot-content-id");

                axios.get(`/backend/advertisemsement_launch/${slot_content_id}`)
                    .then(function(response) {
                        let content = response.data.content;
                        let details = response.data.details;

                        $('#modal-slot').empty().append(`【${content.slot_code}】${content.slot_desc}`);
                        $('#modal-start-at-end-at').empty().append(
                            `${content.start_at} ~ ${content.end_at}`);

                        if (content.slot_content_active == 1) {
                            $('#modal-active').empty().append(`啟用`);
                        } else {
                            $('#modal-active').empty().append(`關閉`);
                        }

                        if (content.slot_color_code) {
                            $('#modal-slot-color-code').empty().append(`${content.slot_color_code}`);
                        } else {
                            $('#modal-slot-color-code').empty().append(
                                `<i class="fa-solid fa-xmark fa-lg"></i>`);
                        }

                        if (content.slot_icon_name_url) {
                            $('#modal-slot-icon-name').empty().append(
                                `<img src="${content.slot_icon_name_url}" class="img-responsive" width="50" height="50" />`
                            );
                        } else {
                            $('#modal-slot-icon-name').empty().append(
                                `<i class="fa-solid fa-xmark fa-lg"></i>`);
                        }

                        if (content.slot_title) {
                            $('#modal-slot-title').empty().append(`${content.slot_title}`);
                        } else {
                            $('#modal-slot-title').empty().append(`<i class="fa-solid fa-xmark fa-lg"></i>`);
                        }

                        // 選擇顯示的區塊
                        switch (content.slot_type) {
                            // 圖檔
                            case 'I':
                                $('#image-block').show();
                                $('#text-block').hide();
                                $('#product-block').hide();

                                $('#image-block table > tbody').empty();
                                break;
                                // 文字
                            case 'T':
                                $('#image-block').hide();
                                $('#text-block').show();
                                $('#product-block').hide();

                                $('#text-block table > tbody').empty();
                                break;
                                // 商品
                            case 'S':
                                $('#image-block').hide();
                                $('#text-block').hide();
                                $('#product-block').show();

                                $('#product-block table > tbody').empty();
                                break;
                                // 圖檔+商品
                            case 'IS':
                                $('#image-block').show();
                                $('#text-block').hide();
                                $('#product-block').show();

                                $('#image-block table > tbody').empty();
                                $('#product-block table > tbody').empty();
                                break;
                            default:
                                $('#image-block').hide();
                                $('#text-block').hide();
                                $('#product-block').hide();
                                break;
                        }

                        switch (content.product_assigned_type) {
                            case 'P':
                                $('#product-block-tab a[href="#tab-product"]').tab('show').parent()
                                    .show().siblings().hide();
                                break;
                            case 'C':
                                $('#product-block-tab a[href="#tab-category"]').tab('show').parent()
                                    .show().siblings().hide();
                                break;
                        }

                        $.each(details, function(key, value) {
                            let sort = value.sort !== null ? value.sort :
                                '<i class="fa-solid fa-xmark fa-lg"></i>';
                            let image_name_url = value.image_name_url ?
                                `<img src="${value.image_name_url}" class="img-responsive" width="180" height="180" />` :
                                '<i class="fa-solid fa-xmark fa-lg"></i>';
                            let image_alt = value.image_alt ? value.image_alt :
                                '<i class="fa-solid fa-xmark fa-lg"></i>';
                            let image_title = value.image_title ? value.image_title :
                                '<i class="fa-solid fa-xmark fa-lg"></i>';
                            let image_abstract = value.image_abstract ? value.image_abstract :
                                '<i class="fa-solid fa-xmark fa-lg"></i>';
                            let link_content = '<i class="fa-solid fa-xmark fa-lg"></i>';

                            switch (value.image_action) {
                                // URL
                                case 'U':
                                    if (value.link_content) {
                                        link_content =
                                            `URL: <a href="${value.link_content}" target="_blank">${value.link_content}</a>`;
                                    }
                                    break;
                                    // 商品分類
                                case 'C':
                                    if (value.link_content) {
                                        link_content = `商品分類: ${value.link_content}`;
                                    }
                                    break;
                            }

                            let is_target_blank = value.is_target_blank == 1 ?
                                '<i class="fa-solid fa-check fa-lg"></i>' :
                                '<i class="fa-solid fa-xmark fa-lg"></i>';
                            let texts = value.texts ? value.texts :
                                '<i class="fa-solid fa-xmark fa-lg"></i>';

                            switch (value.data_type) {
                                case 'IMG':
                                    $('#image-block table > tbody').append(`
                                        <tr>
                                            <td>
                                                ${sort}
                                            </td>
                                            <td style="width:220px">
                                                ${image_name_url}
                                            </td>
                                            <td>
                                                ${image_alt}
                                            </td>
                                            <td>
                                                ${image_title}
                                            </td>
                                            <td>
                                                ${image_abstract}
                                            </td>
                                            <td>
                                                ${link_content}
                                            </td>
                                            <td>
                                                ${is_target_blank}
                                            </td>
                                        </tr>
                                    `);
                                    break;
                                case 'TXT':
                                    $('#text-block table > tbody').append(`
                                        <tr>
                                            <td>
                                                ${sort}
                                            </td>
                                            <td>
                                                ${texts}
                                            </td>
                                            <td>
                                                ${link_content}
                                            </td>
                                            <td>
                                                ${is_target_blank}
                                            </td>
                                        </tr>
                                    `);
                                    break;
                                case 'PRD':
                                    switch (content.product_assigned_type) {
                                        case 'P':
                                            if (value.product) {
                                                $('#tab-product table > tbody').append(`
                                                    <tr>
                                                        <td>
                                                            ${sort}
                                                        </td>
                                                        <td>
                                                            ${value.product}
                                                        </td>
                                                    </tr>
                                                `);
                                            }
                                            break;

                                        case 'C':
                                            if (value.product_category) {
                                                $('#tab-category table > tbody').append(`
                                                    <tr>
                                                        <td>
                                                            ${sort}
                                                        </td>
                                                        <td>
                                                            ${value.product_category}
                                                        </td>
                                                    </tr>
                                                `);
                                            }
                                            break;
                                    }
                                    break;
                            }
                        });

                        $('#slot_content_detail').modal('show');
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        });
    </script>
@endsection
