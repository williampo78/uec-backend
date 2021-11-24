@extends('Backend.master')

@section('title', '新增廣告上架')

@section('style')
    <style>
        .colorpicker-2x .colorpicker-saturation {
            width: 200px;
            height: 200px;
        }

        .colorpicker-2x .colorpicker-hue,
        .colorpicker-2x .colorpicker-alpha {
            width: 30px;
            height: 200px;
        }

        .colorpicker-2x .colorpicker-color,
        .colorpicker-2x .colorpicker-color div {
            height: 30px;
        }

    </style>
@endsection

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        <form role="form" id="create-form" method="post"
                            action="{{ route('advertisemsement_launch.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- 欄位 -->
                                <div class="col-sm-12">

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="slot">版位 <span style="color:red;">*</span></label>
                                                <select class="form-control js-select2-slot validate[required]" name="slot"
                                                    id="slot">
                                                    <option></option>
                                                    @isset($ad_slots)
                                                        @foreach ($ad_slots as $obj)
                                                            <option value='{{ $obj->id }}'
                                                                data-is-user-defined="{{ $obj->is_user_defined }}"
                                                                data-slot-type="{{ $obj->slot_type }}">
                                                                【{{ $obj->slot_code }}】{{ $obj->slot_desc }}</option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>上架時間 <span style="color:red;">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-5 text-center">
                                                        <div class='input-group date' id='datetimepicker_start_at'>
                                                            <input type='text'
                                                                class="form-control datetimepicker-input validate[required]"
                                                                data-target="#datetimepicker_start_at" name="start_at"
                                                                id="start_at" value="" />
                                                            <span class="input-group-addon"
                                                                data-target="#datetimepicker_start_at"
                                                                data-toggle="datetimepicker">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1 text-center">
                                                        <h5>～</h5>
                                                    </div>
                                                    <div class="col-sm-5 text-center">
                                                        <div class='input-group date' id='datetimepicker_end_at'>
                                                            <input type='text'
                                                                class="form-control datetimepicker-input validate[required]"
                                                                data-target="#datetimepicker_end_at" name="end_at"
                                                                id="end_at" value="" />
                                                            <span class="input-group-addon"
                                                                data-target="#datetimepicker_end_at"
                                                                data-toggle="datetimepicker">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>狀態 <span style="color:red;">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <input class="validate[required]" type="radio" name="active"
                                                            id="active_enabled" checked value="1" />
                                                        <label for="active_enabled">啟用</label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input class="validate[required]" type="radio" name="active"
                                                            id="active_disabled" value="0" />
                                                        <label for="active_disabled">關閉</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="slot_color_code">版位主色 (例：#00BB00)</label>
                                                <input class="form-control colorpicker validate[required]" type="text"
                                                    id="slot_color_code" name="slot_color_code" value="" disabled />
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="slot_icon_name">版位icon</label>
                                                <input class="form-control validate[required]" type="file"
                                                    id="slot_icon_name" name="slot_icon_name" disabled />
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="slot_title">版位標題</label>
                                                <input class="form-control validate[required]" type="text" id="slot_title"
                                                    name="slot_title" value="" disabled />
                                            </div>
                                        </div>
                                    </div>

                                    <hr />

                                    <div id="image-block" style="display: none;">
                                        <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                            <thead>
                                                <tr>
                                                    <th>排序</th>
                                                    <th>圖片</th>
                                                    <th>alt</th>
                                                    <th>標題</th>
                                                    <th>摘要</th>
                                                    <th>連結內容</th>
                                                    <th>另開視窗</th>
                                                    <th>功能</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <input type="hidden" id="image-block-row-no" value="0" />
                                            </tbody>
                                        </table>

                                        <button type="button" class="btn btn-warning" id="btn-new-image"><i
                                                class="fa fa-plus"></i> 新增圖檔</button>
                                        <hr />
                                    </div>

                                    <div id="text-block" style="display: none;">
                                        <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                            <thead>
                                                <tr>
                                                    <th>排序</th>
                                                    <th>文字</th>
                                                    <th>連結內容</th>
                                                    <th>另開視窗</th>
                                                    <th>功能</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <input type="hidden" id="text-block-row-no" value="0" />
                                            </tbody>
                                        </table>

                                        <button type="button" class="btn btn-warning" id="btn-new-text"><i
                                                class="fa fa-plus"></i> 新增文字</button>
                                        <hr />
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {{-- @if ($share_role_auth['auth_create']) --}}
                                                <button class="btn btn-success" type="button" id="btn-save"><i
                                                        class="fa fa-save"></i> 儲存</button>
                                                {{-- @endif --}}

                                                <button class="btn btn-danger" type="button" id="btn-cancel"><i
                                                        class="fa fa-ban"></i> 取消</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('js')
    <script>
        $(function() {
            $("#create-form").validationEngine();

            $('.js-select2-slot').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('#datetimepicker_start_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm',
                showClear: true,
            });

            $('#datetimepicker_end_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm',
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

            $('.colorpicker').colorpicker({
                format: "hex",
                align: "left",
                customClass: 'colorpicker-2x',
                sliders: {
                    saturation: {
                        maxLeft: 200,
                        maxTop: 200
                    },
                    hue: {
                        maxTop: 200
                    },
                    alpha: {
                        maxTop: 200
                    }
                }
            });

            $('#slot').on('change', function() {
                let element = $(this).find('option:selected');
                let is_user_defined = element.attr('data-is-user-defined');
                let slot_type = element.attr('data-slot-type');

                // 關閉編輯 版位主色、版位icon、版位標題
                if (is_user_defined == 0) {
                    $('#slot_color_code').prop('disabled', true);
                    $('#slot_icon_name').prop('disabled', true);
                    $('#slot_title').prop('disabled', true);
                }
                // 開放編輯 版位主色、版位icon、版位標題，皆為必填
                else {
                    $('#slot_color_code').prop('disabled', false);
                    $('#slot_icon_name').prop('disabled', false);
                    $('#slot_title').prop('disabled', false);
                }

                switch (slot_type) {
                    // 圖檔
                    case 'I':
                        $('#image-block').show();
                        $('#text-block').hide();

                        if ($('#image-block > table > tbody > tr').length <= 0) {
                            $('#btn-new-image').click();
                        }
                        break;
                        // 文字
                    case 'T':
                        $('#image-block').hide();
                        $('#text-block').show();

                        if ($('#text-block > table > tbody > tr').length <= 0) {
                            $('#btn-new-text').click();
                        }
                        break;
                        // 商品
                    case 'S':
                        $('#image-block').hide();
                        $('#text-block').hide();
                        break;
                        // 圖檔+商品
                    case 'IS':
                        $('#image-block').show();
                        $('#text-block').hide();

                        if ($('#image-block > table > tbody > tr').length <= 0) {
                            $('#btn-new-image').click();
                        }
                        break;
                    default:
                        $('#image-block').hide();
                        $('#text-block').hide();
                        break;
                }
            });

            let product_category = @json($product_category);
            let product_category_select_option = '';

            $.each(product_category, function(key, value) {
                product_category_select_option += `
                    <option value='${value['id']}'>${value['name']}</option>
                `;
            });

            // 新增圖檔
            $('#btn-new-image').on('click', function() {
                let image_block_row_no = $('#image-block-row-no').val();

                $('#image-block > table > tbody').append(`
                    <tr>
                        <input type="hidden" name="image_block_id[${image_block_row_no}]" value="new">
                        <td>
                            <input type="text" name="image_block_sort[${image_block_row_no}]" value="" />
                        </td>
                        <td>
                            <input type="file" name="image_block_image_name[${image_block_row_no}]" value="" />
                        </td>
                        <td>
                            <input type="text" name="image_block_image_alt[${image_block_row_no}]" value="" />
                        </td>
                        <td>
                            <input type="text" name="image_block_image_title[${image_block_row_no}]" value="" />
                        </td>
                        <td>
                            <textarea rows="3" name="image_block_image_abstract[${image_block_row_no}]"></textarea>
                        </td>
                        <td>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="image_block_image_action[${image_block_row_no}]" value="X" checked />
                                    無連結
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="image_block_image_action[${image_block_row_no}]" value="U" />
                                    URL
                                </label>
                                <input type="text" name="image_block_target_url[${image_block_row_no}]" value="" />
                            </div>
                            <div class="radio">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label>
                                            <input type="radio" name="image_block_image_action[${image_block_row_no}]" value="C" />
                                            商品分類頁
                                        </label>
                                    </div>
                                    <div class="col-sm-7">
                                        <select class="js-select2-product-category" name="image_block_target_cate_hierarchy_id[${image_block_row_no}]">
                                            <option></option>
                                            ${product_category_select_option}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="checkbox" name="image_block_is_target_blank[${image_block_row_no}]" value="enabled" />
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-delete-image"><i class='fa fa-ban'></i> 刪除</button>
                        </td>
                    </tr>
                `);

                $('.js-select2-product-category').select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: '',
                });

                $('#image-block-row-no').val(parseInt(image_block_row_no) + 1);
            });

            // 刪除圖檔
            $(document).on('click', '.btn-delete-image', function() {
                $(this).parents('tr').remove();
            });

            // 新增文字
            $('#btn-new-text').on('click', function() {
                let text_block_row_no = $('#text-block-row-no').val();

                $('#text-block > table > tbody').append(`
                    <tr>
                        <input type="hidden" name="text_block_id[${text_block_row_no}]" value="new">
                        <td>
                            <input type="text" name="text_block_sort[${text_block_row_no}]" value="" />
                        </td>
                        <td>
                            <input type="text" name="text_block_texts[${text_block_row_no}]" value="" />
                        </td>
                        <td>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="text_block_image_action[${text_block_row_no}]" value="X" checked />
                                    無連結
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="text_block_image_action[${text_block_row_no}]" value="U" />
                                    URL
                                </label>
                                <input type="text" name="text_block_target_url[${text_block_row_no}]" value="" />
                            </div>
                            <div class="radio">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label>
                                            <input type="radio" name="text_block_image_action[${text_block_row_no}]" value="C" />
                                            商品分類頁
                                        </label>
                                    </div>
                                    <div class="col-sm-7">
                                        <select class="js-select2-product-category" name="text_block_target_cate_hierarchy_id[${text_block_row_no}]">
                                            <option></option>
                                            ${product_category_select_option}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="checkbox" name="text_block_is_target_blank[${text_block_row_no}]" value="enabled" />
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-delete-text"><i class='fa fa-ban'></i> 刪除</button>
                        </td>
                    </tr>
                `);

                $('.js-select2-product-category').select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: '',
                });

                $('#text-block-row-no').val(parseInt(text_block_row_no) + 1);
            });

            // 刪除文字
            $(document).on('click', '.btn-delete-text', function() {
                $(this).parents('tr').remove();
            });

            $('#btn-save').on('click', function() {
                $("#create-form").submit();
            });

            $('#btn-cancel').on('click', function() {
                location.href = "{{ route('advertisemsement_launch') }}";
            });
        });
    </script>
@endsection

@include('Backend.Quotation.addItem')
@endsection
