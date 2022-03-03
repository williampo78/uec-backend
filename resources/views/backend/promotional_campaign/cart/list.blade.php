@extends('backend.master')

@section('title', '滿額活動管理')

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-list"></i> 滿額活動管理</h1>
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
                                            <label class="control-label">活動名稱</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="campaign_name"
                                                id="campaign_name" value="{{ request()->input('campaign_name') }}"
                                                placeholder="模糊查詢" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-active" name="active" id="active">
                                                <option value=''></option>
                                                <option value='enabled'
                                                    {{ 'enabled' == request()->input('active') ? 'selected' : '' }}>
                                                    生效
                                                </option>
                                                <option value='disabled'
                                                    {{ 'disabled' == request()->input('active') ? 'selected' : '' }}>
                                                    失效
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">活動類型</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control js-select2-campaign-type" name="campaign_type"
                                                id="campaign_type">
                                                <option></option>
                                                @isset($campaign_types)
                                                    @foreach ($campaign_types as $obj)
                                                        <option value='{{ $obj->code }}'
                                                            {{ $obj->code == request()->input('campaign_type') ? 'selected' : '' }}>
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
                                        <div class="col-sm-3">
                                            <label class="control-label">上架時間起</label>
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

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">商品序號</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="product_no" id="product_no"
                                                value="{{ request()->input('product_no') }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-9 text-right">
                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-warning">
                                                    <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                                </button>

                                                <button type="button" class="btn btn-danger" id="btn-reset">
                                                    <i class="fa-solid fa-eraser"></i> 清除
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
                        <div class="row">
                            @if ($share_role_auth['auth_create'])
                                <div class="col-sm-2">
                                    <a href="{{ route('promotional_campaign_cart.create') }}"
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
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">活動名稱</th>
                                        <th class="text-nowrap">活動類型</th>
                                        <th class="text-nowrap">狀態</th>
                                        <th class="text-nowrap">上架時間起</th>
                                        <th class="text-nowrap">上架時間訖</th>
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
                                                    @if ($share_role_auth['auth_query'])
                                                        <button type="button"
                                                            class="btn btn-info btn-sm promotional_campaign_detail"
                                                            data-promotional-campaign-id="{{ $obj->id }}" title="檢視">
                                                            <i class="fa-solid fa-magnifying-glass"></i>
                                                        </button>
                                                    @endif

                                                    @if ($share_role_auth['auth_update'])
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('promotional_campaign_cart.edit', $obj->id) }}">
                                                            編輯
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $count++ }}</td>
                                                <td>{{ $obj->campaign_name ?? '' }}</td>
                                                <td>{{ $obj->description ?? '' }}</td>
                                                <td>
                                                    @isset(config('uec.active2_options')[$obj->active])
                                                        {{ config('uec.active2_options')[$obj->active] }}
                                                    @endisset
                                                </td>
                                                <td>
                                                    {{ $obj->start_at ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $obj->end_at ?? '' }}
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
        @include('backend.promotional_campaign.cart.detail')
        <!-- /.modal -->

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('.js-select2-active').select2();
            $('.js-select2-campaign-type').select2();

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

            // 重置搜尋表單
            $('#btn-reset').on('click', function() {
                $('#search-form').find(':text, select').val('');
                $('#active, #campaign_type').trigger('change');
            });

            $(document).on('click', '.promotional_campaign_detail', function() {
                let promotional_campaign_id = $(this).attr("data-promotional-campaign-id");

                axios.post('/backend/promotional_campaign/ajax/detail', {
                        promotional_campaign_id: promotional_campaign_id,
                        level_code: 'CART',
                    })
                    .then(function(response) {
                        let promotional_campaign = response.data;

                        $('#modal-campaign-name').empty().append(
                            `${promotional_campaign.campaign_name}`);

                        if (promotional_campaign.active == 1) {
                            $('#modal-active').empty().append(`生效`);
                        } else {
                            $('#modal-active').empty().append(`失效`);
                        }

                        $('#modal-campaign-type').empty().append(`${promotional_campaign.description}`);
                        $('#modal-n-value').empty().append(`${promotional_campaign.n_value}`);

                        if (promotional_campaign.x_value) {
                            $('#modal-x-value').empty().append(`${promotional_campaign.x_value}`)
                                .closest('.form-group').show();
                        } else {
                            $('#modal-x-value').closest('.form-group').hide();
                        }

                        $('#modal-start-at').empty().append(`${promotional_campaign.start_at}`);
                        $('#modal-end-at').empty().append(`${promotional_campaign.end_at}`);
                        $('#modal-target-groups').empty().append(`所有會員`);

                        // 活動類型
                        switch (promotional_campaign.campaign_type) {
                            // ﹝滿額﹞購物車滿N元，打X折
                            case 'CART01':
                                $('#prd-block').hide();
                                $('#gift-block').hide();
                                break;
                                // ﹝滿額﹞購物車滿N元，折X元
                            case 'CART02':
                                $('#prd-block').hide();
                                $('#gift-block').hide();
                                break;
                                // ﹝滿額﹞購物車滿N元，送贈品
                            case 'CART03':
                                $('#prd-block').hide();
                                $('#gift-block').show();

                                $("#gift-product-table > tbody").empty();
                                break;
                                // ﹝滿額﹞指定商品滿N件，送贈品
                            case 'CART04':
                                $('#prd-block').show();
                                $('#gift-block').show();

                                $("#prd-product-table > tbody").empty();
                                $("#gift-product-table > tbody").empty();
                                break;
                            default:
                                $('#prd-block').hide();
                                $('#gift-block').hide();
                                break;
                        }

                        if (promotional_campaign.products) {
                            let count = 1;

                            $.each(promotional_campaign.products, function(id, product) {
                                $("#prd-product-table > tbody").append(`
                                    <tr>
                                        <td>${count++}</td>
                                        <td>${product.product_no}</td>
                                        <td>${product.product_name}</td>
                                        <td>${product.selling_price}</td>
                                        <td>${product.launched_at}</td>
                                        <td>${product.launched_status}</td>
                                        <td>${product.gross_margin ? product.gross_margin : ''}</td>
                                    </tr>
                                `);
                            });
                        }

                        if (promotional_campaign.giveaways) {
                            let count = 1;

                            $.each(promotional_campaign.giveaways, function(id, product) {
                                $("#gift-product-table > tbody").append(`
                                    <tr>
                                        <td>${count++}</td>
                                        <td>${product.product_no}</td>
                                        <td>${product.product_name}</td>
                                        <td>${product.assigned_qty}</td>
                                    </tr>
                                `);
                            });
                        }

                        $('#promotional_campaign_detail').modal('show');
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        });
    </script>
@endsection
