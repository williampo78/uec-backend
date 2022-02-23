@extends('backend.master')

@section('title', isset($item) ? '編輯物品':'新增物品')

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-plus"></i> {{isset($item) ? '編輯物品':'新增物品'}}</h1>
            </div>
        </div>
        <!-- /.row -->
        {{-- {{ dump($errors->messages['name']) }} --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        @if (isset($item))
                            <form role="form" id="new-form" method="POST" action="{{ route('item.update', $item->id) }}"
                                enctype="multipart/form-data" novalidate="novalidate">
                                @method('PUT')
                                @csrf
                            @else
                                <form role="form" id="new-form" method="POST" action="{{ route('item') }}"
                                    enctype="multipart/form-data" novalidate="novalidate">
                        @endif
                        @csrf
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#menu_main">基本資料</a></li>
                            <li><a data-toggle="tab" href="#menu_photo">產品照片</a></li>
                        </ul>
                        <br>
                        <div class="tab-content">
                            <div id="menu_photo" class="tab-pane fade">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <fieldset style="width:100%; text-align:center;">
                                                <legend><i class="fa-solid fa-image"></i> 主要產品照片</legend>
                                                <div>
                                                    @if (isset($item) && $item->photo_name !== '')
                                                        <img id="itempic-1"
                                                            src="{{ asset('/images/item') . $item->photo_name }}"
                                                            style="max-width:100%;" data-fileid='{{ $item->id }}'>
                                                    @else
                                                        <img id="itempic-1"
                                                            src="{{ asset('asset/img/default_item.png') }}"
                                                            style="max-width:100%;" data-fileid=''>
                                                    @endif
                                                </div>
                                                <input type="hidden" data-input="true" name="photo-1" id="photo-1" value="">
                                            </fieldset>
                                        </div>
                                        <div class="form-group" id="divforclear-1" style="display:none">
                                            <input type="button" class="btn btn-warning" id="clearfile-1" value="刪除圖片"
                                                onclick="del_img('1')">
                                        </div>
                                        <div class="form-group" id="divforfile-1">
                                            <input type="file" class="filestyle" data-input="false" name="file-1"
                                                id="file-1" value="" onchange="onchangeimg(this.id,1)">
                                        </div>
                                    </div>
                                    @for ($i = 2; $i <= 6; $i++)
                                        <?php $photo_status = false; ?>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <fieldset style="width:100%; text-align:center;">
                                                    <legend>
                                                        <i class="fa-solid fa-image"></i> 產品照片 {{ $i }}
                                                    </legend>
                                                    @if (isset($itemPhoto))
                                                        @foreach ($itemPhoto as $photo)
                                                            @if ($photo['sort'] == $i && $photo['photo_name'] !== '')
                                                                <div>
                                                                    <img data-fileid="{{ $photo['id'] }}"
                                                                        id="itempic-{{ $i }}"
                                                                        src="{{ asset('/images/item') . $photo['photo_name'] }}"
                                                                        style="max-width:100%;">
                                                                </div>
                                                                <?php $photo_status = true; ?>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    @if (!$photo_status)
                                                        <div>
                                                            <img id="itempic-{{ $i }}"
                                                                src="{{ asset('asset/img/default_item.png') }}"
                                                                style="max-width:100%;" data-fileid="">
                                                        </div>
                                                    @endif
                                                    <input type="hidden" data-input="false"
                                                        name="photo-{{ $i }}" id="photo-{{ $i }}"
                                                        value="">
                                                </fieldset>
                                            </div>
                                            <div class="form-group" id="divforclear-{{ $i }}"
                                                style="display:none">
                                                <input type="button" class="btn btn-warning"
                                                    id="clearfile-{{ $i }}" value="刪除圖片"
                                                    onclick="del_img({{ $i }})">
                                            </div>
                                            <div class="form-group" id="divforfile-{{ $i }}">
                                                <input type="file" class="filestyle" data-input="false"
                                                    name="file-{{ $i }}" id="file-{{ $i }}"
                                                    onchange="onchangeimg(this.id,{{ $i }})">
                                            </div>
                                        </div>
                                    @endfor


                                </div>
                            </div>
                            <div id="menu_main" class="tab-pane fade in active">

                                <!-- 產品欄位 -->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="category">分類</label>
                                                <select class="form-control js-select2" name="category_id" id="category">
                                                    @foreach ($category as $val)
                                                        <option value='{{ $val->id }}'
                                                            {{ (old('bluesign') ?? (isset($item) ? $item->bluesign : '')) == $val->id ? 'selected' : '' }}>
                                                            {{ $val->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>供應商</label>
                                                <select class="form-control js-select2-supplier" name="supplier_id"
                                                    id="item_supplier">
                                                    @foreach ($supplier as $val)
                                                        <option value='{{ $val->id }}'>{{ $val->name }}
                                                            {{ (old('bluesign') ?? (isset($item) ? $item->bluesign : '')) == $val->id ? 'selected' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_item_number">
                                                <label for="item_number">編號(主分類編號+子分類編號+品項數)
                                                    <span class="error">{{ $errors->first('number') }}</span>
                                                </label>
                                                <input class="form-control" name="number" id="number"
                                                    value="{{ old('number') ?? (isset($item) ? $item->number : '') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_brand">
                                                <label for="brand">品牌
                                                    <span class="error">{{ $errors->first('brand') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="brand" id="brand"
                                                    value="{{ old('brand') ?? (isset($item) ? $item->brand : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="name">名稱 <span class="error">
                                                        {{ $errors->first('name') }}
                                                    </span></label>
                                                <input class="form-control" name="name" id="name"
                                                    value="{{ old('name') ?? (isset($item) ? $item->name : '') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="name_en">名稱(英文) <span class="error">
                                                        {{ $errors->first('name_en') }}
                                                    </span></label>
                                                <input class="form-control" name="name_en" id="name_en"
                                                    value="{{ old('name') ?? (isset($item) ? $item->name_en : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_buy_price">
                                                <label for="buy_price">進價
                                                    <span class="error">
                                                        {{ $errors->first('buy_price') }}
                                                    </span></label>
                                                <input class="form-control" name="buy_price" id="buy_price"
                                                    value="{{ old('buy_price') ?? (isset($item) ? $item->buy_price : '') }}"
                                                    type='number'>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_sell_price1">
                                                <label for="sell_price1">售價
                                                    <span class="error">
                                                        {{ $errors->first('sell_price1') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="sell_price1" id="sell_price1"
                                                    value="{{ old('sell_price1') ?? (isset($item) ? $item->sell_price1 : '') }}"
                                                    value="{{ old('sell_price1') }}" type='number'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_large_unit">
                                                <label for="large_unit">進貨單位
                                                    <span class="error">
                                                        {{ $errors->first('large_unit') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="large_unit" id="large_unit"
                                                    value="{{ old('large_unit') ?? (isset($item) ? $item->large_unit : '') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_small_unit">
                                                <label for="small_unit">出貨單位
                                                    <span class="error">
                                                        {{ $errors->first('small_unit') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="small_unit" id="small_unit"
                                                    value="{{ old('small_unit') ?? (isset($item) ? $item->small_unit : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_stock_qty">
                                                <label for="stock_qty">當前庫存(以出貨單位計算)
                                                    <span class="error">
                                                        {{ $errors->first('stock_qty') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="stock_qty" id="stock_qty" type='number'
                                                    value="{{ old('stock_qty') ?? (isset($item) ? $item->stock_qty : '0') }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_spec">
                                                <label for="spec">規格
                                                    <span class="error">
                                                        {{ $errors->first('spec') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="spec"
                                                    value="{{ old('spec') ?? (isset($item) ? $item->spec : '') }}"
                                                    id="spec">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_public_number">
                                                <label for="public_number">衛署字號
                                                    <span class="error">
                                                        {{ $errors->first('public_number') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="public_number" id="public_number"
                                                    value="{{ old('public_number') ?? (isset($item) ? $item->public_number : '') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_old_number">
                                                <label for="old_number">舊系統編號
                                                    <span class="error">
                                                        {{ $errors->first('old_number') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="old_number" id="old_number"
                                                    value="{{ old('old_number') ?? (isset($item) ? $item->old_number : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_active">
                                                <label for="active">狀態</label>
                                                <select class="form-control" name="active" id="active">
                                                    <option value="1"
                                                        {{ (old('fda_class') ?? (isset($item) ? $item->fda_class : '')) == 1 ? 'selected' : '' }}>
                                                        顯示</option>
                                                    <option value="0"
                                                        {{ (old('fda_class') ?? (isset($item) ? $item->fda_class : '')) == 0 ? 'selected' : '' }}>
                                                        隱藏</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_safe_stock">
                                                <label for="safe_stock">安全庫存量
                                                    <span class="error">
                                                        {{ $errors->first('safe_stock') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="safe_stock" id="safe_stock"
                                                    value="{{ old('safe_stock') ?? (isset($item) ? $item->safe_stock : '') }}"
                                                    type="number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_live_times">
                                                <label for="live_times">有效期限
                                                    <span class="error">
                                                        {{ $errors->first('live_times') }}
                                                    </span>
                                                </label>
                                                <div class="form-inline">
                                                    <div class="input-group col-sm-12">
                                                        <input type="number" class="form-control text-center"
                                                            name="live_times" id="live_times"
                                                            value="{{ old('live_times') ?? (isset($item) ? $item->live_times : '') }}">
                                                        <div class="input-group-addon">
                                                            <span class="input-group-text">年</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_minimum_sales_qty">
                                                <label for="minimum_sales_qty">最低出貨量 <span class="error">
                                                        {{ $errors->first('minimum_sales_qty') }}
                                                    </span>
                                                </label>
                                                <input class="form-control" name="minimum_sales_qty"
                                                    id="minimum_sales_qty" type="number"
                                                    value="{{ old('minimum_sales_qty') ?? (isset($item) ? $item->minimum_sales_qty : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_fda_class">
                                                <label for="fda_class">醫療器材分類分級(Class)</label>
                                                <select class="form-control" name="fda_class" id="fda_class">
                                                    <option value="0"
                                                        {{ (old('fda_class') ?? (isset($item) ? $item->fda_class : '')) == 0 ? 'selected' : '' }}>
                                                        無</option>
                                                    <option value="1"
                                                        {{ (old('fda_class') ?? (isset($item) ? $item->fda_class : '')) == 1 ? 'selected' : '' }}>
                                                        第一級</option>
                                                    <option value="2"
                                                        {{ (old('fda_class') ?? (isset($item) ? $item->fda_class : '')) == 2 ? 'selected' : '' }}>
                                                        第二級</option>
                                                    <option value="3"
                                                        {{ (old('fda_class') ?? (isset($item) ? $item->fda_class : '')) == 3 ? 'selected' : '' }}>
                                                        第三級</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group" id="div_open_sales">
                                                <label for="open_sales">是否可以公開販售(個人)</label>
                                                <select class="form-control" name="open_sales" id="open_sales">
                                                    <option value="0"
                                                        {{ (old('open_sales') ?? (isset($item) ? $item->open_sales : '')) == 0 ? 'selected' : '' }}>
                                                        否</option>
                                                    <option value="1"
                                                        {{ (old('open_sales') ?? (isset($item) ? $item->open_sales : '')) == 1 ? 'selected' : '' }}>
                                                        是</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group" id="div_bluesign">
                                                <label for="bluesign">是否 bluesign® 產品</label>
                                                <select class="form-control" name="bluesign" id="bluesign">
                                                    <option value="0"
                                                        {{ (old('bluesign') ?? (isset($item) ? $item->bluesign : '')) == 0 ? 'selected' : '' }}>
                                                        否</option>
                                                    <option value="1"
                                                        {{ (old('bluesign') ?? (isset($item) ? $item->bluesign : '')) == 1 ? 'selected' : '' }}>
                                                        是</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group" id="div_is_fee_item">
                                                <label for="is_fee_item">是否為費用性品項</label>
                                                <select class="form-control" name="is_fee_item" id="is_fee_item"
                                                    {{ (old('is_fee_item') ?? (isset($item) ? $item->is_fee_item : '')) == 0 ? 'selected' : '' }}>
                                                    <option value="0"
                                                        {{ (old('is_fee_item') ?? (isset($item) ? $item->is_fee_item : '')) == 0 ? 'selected' : '' }}>
                                                        否</option>
                                                    <option value="1"
                                                        {{ (old('is_fee_item') ?? (isset($item) ? $item->is_fee_item : '')) == 1 ? 'selected' : '' }}>
                                                        是</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="div_remark">
                                                <label for="remark">備註
                                                    <span class="error">
                                                        {{ $errors->first('minimum_sales_qty') }}
                                                    </span>
                                                </label>
                                                <textarea class="form-control" rows="3" name="remark" id="remark"
                                                    value="{{ old('remark') ?? (isset($item) ? $item->remark : '') }}">{{ old('remark') ?? (isset($item) ? $item->remark : '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <h4><i class="fa-solid fa-table-cells-large"></i> 商品簡介(描述)</h4>
                                <span class="error">
                                    {{ $errors->first('description') }}
                                </span>
                                <textarea class="form-control" rows="5" name="description"
                                    placeholder="請簡單描述商品介紹">{{ old('description') ?? (isset($item) ? $item->specification : '') }}</textarea>
                            </div>
                            <div class="col-sm-6">
                                <h4><i class="fa-solid fa-table-cells-large"></i> 商品規格</h4>
                                <span class="error">
                                    {{ $errors->first('specification') }}
                                </span>
                                <textarea class="form-control" rows="5" name="specification"
                                    placeholder="例如:
                                    產地：台灣
                                    適用族群：老年人、嬰幼兒、兒童、成人、通用
                                    核准字號：衛署醫器製壹字第002376號">{{ old('specification') ?? (isset($item) ? $item->specification : '') }}</textarea>
                            </div>
                        </div>

                        <h4><i class="fa-solid fa-table-cells-large"></i> 商品特色(圖文)</h4>
                        <span class="error">
                            {{ $errors->first('features') }}
                        </span>
                        <textarea
                            name="features">{{ old('features') ?? (isset($item) ? $item->features : '') }}</textarea>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button class="btn btn-success" id="btn-save"><i class="fa-solid fa-floppy-disk"></i>
                                        儲存</button>
                                    <button class="btn btn-danger" type="button" id="btn-cancel"><i class="fa-solid fa-ban"></i>
                                        取消</button>
                                </div>
                            </div>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            //文字編輯器
            var editor = CKEDITOR.replace('features', {
                filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
                filebrowserImageBrowseUrl: 'ckfinder/ckfinder.html?Type=Images',
                //filebrowserUploadUrl : 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files', //可上傳一般檔案
                filebrowserImageUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images' //可上傳圖檔
            });

            $("#btn-cancel").click(function () {
                window.location.href = '{{route("item")}}';
            });
        });
        var read_del_item = [];
        var read_del_item_photos = [];

        //判斷fiile change
        function onchangeimg(id, num) {
            var mfile = $('#' + id)[0].files[0];
            var itempic = "#itempic-" + num;
            var reader = new FileReader();
            reader.onload = function() {
                dataURL = reader.result;
                $(itempic).attr("src", dataURL);
            };
            reader.readAsDataURL(mfile);
            $('#divforclear-' + num).show(); //顯示刪除圖片
        }

        function checkShowImgDelBtn() {
            for (var i = 1; i <= 6; i++) {
                var getPhoto = "#itempic-" + i;
                var check = $(getPhoto).prop('src');
                var default_src = "{{ asset('asset/img/default_item.png') }}";
                if (default_src !== check) {
                    $('#divforclear-' + i).show();
                }
            }
        }
        checkShowImgDelBtn();

        function del_img(num) {
            var getPhoto = "#itempic-" + num; //照片檔案
            var default_src = "{{ asset('asset/img/default_item.png') }}"; //預設值圖片路徑
            var id = $(getPhoto).data('fileid'); //存放id
            if (id !== '') {
                $.ajax({
                    type: "POST",
                    url: '/backend/item/ajaxphoto/del',
                    dataType: "json",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "num": num,
                        "id": id,
                    },
                    success: function(response) {
                        $(getPhoto).prop('src', default_src); //將圖片改回預設值
                        $(getPhoto).data('fileid', '');
                        $('#divforclear-' + num).hide();
                        alert('刪除成功');
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            } else {
                $(getPhoto).prop('src', default_src); //將圖片改回預設值
                $(getPhoto).data('fileid', '');
                $('#divforclear-' + num).hide();
                $('#file-' + num).val('');
            }

        }
    </script>
@endsection
