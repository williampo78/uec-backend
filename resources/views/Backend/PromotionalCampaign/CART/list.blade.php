@extends('Backend.master')

@section('title', '滿額活動管理')

@section('style')
    <style>
        .modal-body label,
        .modal-body th {
            color: blue;
        }

    </style>
@endsection

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>滿額活動管理</h1>
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
                                    <div class="row">
                                        <div class="col-sm-3 text-right">
                                            <h5>活動名稱</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control" name="campaign_name" id="campaign_name" value="{{ $query_data['campaign_name'] ?? '' }}"
                                                placeholder="模糊查詢" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-3 text-right">
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
                                </div>

                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-3 text-right">
                                            <h5>活動類型</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-campaign-type" name="campaign_type" id="campaign_type">
                                                <option></option>
                                                @isset($campaign_types)
                                                    @foreach ($campaign_types as $obj)
                                                        <option value='{{ $obj->code }}'
                                                            {{ isset($query_data['campaign_type']) && $obj->code == $query_data['campaign_type'] ? 'selected' : '' }}>
                                                            {{ $obj->description }}</option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-3 text-right">
                                            <h5>上架時間起</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class='input-group date' id='datetimepicker_start_at'>
                                                <input type='text' class="form-control datetimepicker-input"
                                                    data-target="#datetimepicker_start_at" name="start_at" id="start_at"
                                                    value="{{ $query_data['start_at'] ?? '' }}" autocomplete="off" />
                                                <span class="input-group-addon" data-target="#datetimepicker_start_at"
                                                    data-toggle="datetimepicker">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <h5>～</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class='input-group date' id='datetimepicker_end_at'>
                                                <input type='text' class="form-control datetimepicker-input"
                                                    data-target="#datetimepicker_end_at" name="end_at" id="end_at"
                                                    value="{{ $query_data['end_at'] ?? '' }}" autocomplete="off" />
                                                <span class="input-group-addon" data-target="#datetimepicker_end_at"
                                                    data-toggle="datetimepicker">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-3 text-right">
                                            <h5>贈品序號</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control" name="product_no" id="product_no" value="{{ $query_data['product_no'] ?? '' }}" />
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
                                    <a href="{{ route('promotional_campaign_cart.create') }}"
                                        class="btn btn-block btn-warning btn-sm" id="btn-create"><i
                                            class="fa fa-plus"></i> 新增</a>
                                </div>
                            {{-- @endif --}}
                        </div>
                        <hr />
                        <div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr role="row">
                                        <th class="col-sm-1">功能</th>
                                        <th class="col-sm-1">項次</th>
                                        <th class="col-sm-1">活動名稱</th>
                                        <th class="col-sm-1">活動類型</th>
                                        <th class="col-sm-1">狀態</th>
                                        <th class="col-sm-1">上架時間起</th>
                                        <th class="col-sm-1">上架時間訖</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 1;
                                    @endphp

                                    @isset($promotional_campaigns)
                                        @foreach ($promotional_campaigns as $obj)
                                            <tr>
                                                <td>
                                                    {{-- @if ($share_role_auth['auth_query']) --}}
                                                        <button type="button" class="btn btn-info btn-sm promotional_campaign_detail"
                                                            data-promotional-campaign-id="{{ $obj->promotional_campaigns_id }}" title="檢視">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    {{-- @endif --}}

                                                    {{-- @if ($share_role_auth['auth_update']) --}}
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('promotional_campaign_cart.edit', $obj->promotional_campaigns_id) }}">
                                                            編輯
                                                        </a>
                                                    {{-- @endif --}}
                                                </td>
                                                <td>{{ $count }}</td>
                                                <td>{{ $obj->campaign_name ?? '' }}</td>
                                                <td>{{ $obj->lookup_values_v_description ?? '' }}</td>
                                                <td>
                                                    @if ($obj->active == 1)
                                                        生效
                                                    @else
                                                        失效
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $obj->start_at ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $obj->end_at ?? '' }}
                                                </td>
                                            </tr>

                                            @php
                                                $count++;
                                            @endphp
                                        @endforeach
                                    @endisset
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Backend.PromotionalCampaign.CART.detail')
        <!-- /.modal -->

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('.js-select2-active').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('.js-select2-campaign-type').select2({
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

            $('#table_list tbody').on('click', '.slot_content_detail', function() {
                let slot_content_id = $(this).attr("data-slot-content-id");

                axios.post('/backend/advertisemsement_launch/ajax/detail', {
                        slot_content_id: slot_content_id
                    })
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
                                `<i class="fa fa-times fa-lg"></i>`);
                        }

                        if (content.slot_icon_name_url) {
                            $('#modal-slot-icon-name').empty().append(
                                `<img src="${content.slot_icon_name_url}" class="img-responsive" width="400" height="400" />`
                            );
                        } else {
                            $('#modal-slot-icon-name').empty().append(
                                `<i class="fa fa-times fa-lg"></i>`);
                        }

                        if (content.slot_title) {
                            $('#modal-slot-title').empty().append(`${content.slot_title}`);
                        } else {
                            $('#modal-slot-title').empty().append(`<i class="fa fa-times fa-lg"></i>`);
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
                            let sort = value.sort ? value.sort :
                                '<i class="fa fa-times fa-lg"></i>';
                            let image_name_url = value.image_name_url ?
                                `<img src="${value.image_name_url}" class="img-responsive" width="400" height="400" />` :
                                '<i class="fa fa-times fa-lg"></i>';
                            let image_alt = value.image_alt ? value.image_alt :
                                '<i class="fa fa-times fa-lg"></i>';
                            let image_title = value.image_title ? value.image_title :
                                '<i class="fa fa-times fa-lg"></i>';
                            let image_abstract = value.image_abstract ? value.image_abstract :
                                '<i class="fa fa-times fa-lg"></i>';
                            let link_content = '<i class="fa fa-times fa-lg"></i>';

                            switch (value.image_action) {
                                // URL
                                case 'U':
                                    link_content =
                                        `URL: <a href="${value.link_content}" target="_blank">${value.link_content}</a>`;
                                    break;
                                    // 商品分類
                                case 'C':
                                    link_content = `商品分類: ${value.link_content}`;
                                    break;
                            }

                            let is_target_blank = value.is_target_blank == 1 ?
                                '<i class="fa fa-check fa-lg"></i>' :
                                '<i class="fa fa-times fa-lg"></i>';
                            let texts = value.texts ? value.texts :
                                '<i class="fa fa-times fa-lg"></i>';

                            switch (value.data_type) {
                                case 'IMG':
                                    $('#image-block table > tbody').append(`
                                        <tr>
                                            <td>
                                                ${sort}
                                            </td>
                                            <td>
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
