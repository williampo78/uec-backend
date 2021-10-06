@extends('Backend.master')

@section('title', '功能名稱')

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-plus"></i> 新增物品</h1>
            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        <form role="form" id="new-form" method="post" action="" enctype="multipart/form-data">

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
                                                    <legend><i class="fa fa-photo"></i> 主要產品照片</legend>
                                                    <div><img id="itempic-1"
                                                            src="{{ asset('asset/img/default_item.png') }}"
                                                            style="max-width:100%;"></div>
                                                    <input type="hidden" data-input="false" name="photo-1" id="photo-1"
                                                        value="">
                                                </fieldset>
                                            </div>
                                            <div class="form-group" id="divforclear-1" style="display:none;">
                                                <input type="button" class="btn btn-warning" id="clearfile-1" value="刪除圖片">
                                            </div>
                                            <div class="form-group" id="divforfile-1">
                                                <input type="file" class="filestyle" data-input="false" name="file-1"
                                                    id="file-1" value="" onchange="onchangeimg(this.id,1)">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <fieldset style="width:100%; text-align:center;">
                                                    <legend><i class="fa fa-photo"></i> 產品照片2</legend>
                                                    <div><img id="itempic-2"
                                                            src="{{ asset('asset/img/default_item.png') }}"
                                                            style="max-width:100%;"></div>
                                                    <input type="hidden" data-input="false" name="photo-2" id="photo-2"
                                                        value="">
                                                </fieldset>
                                            </div>
                                            <div class="form-group" id="divforclear-2" style="display:none;">
                                                <input type="button" class="btn btn-warning" id="clearfile-2" value="刪除圖片">
                                            </div>
                                            <div class="form-group" id="divforfile-2">
                                                <input type="file" class="filestyle" data-input="false" name="file-2"
                                                    id="file-2" onchange="onchangeimg(this.id,2)">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <fieldset style="width:100%; text-align:center;">
                                                    <legend><i class="fa fa-photo"></i> 產品照片3</legend>
                                                    <div><img id="itempic-3"
                                                            src="{{ asset('asset/img/default_item.png') }}"
                                                            style="max-width:100%;"></div>
                                                    <input type="hidden" data-input="false" name="photo-3" id="photo-3"
                                                        value="">
                                                </fieldset>
                                            </div>
                                            <div class="form-group" id="divforclear-3" style="display:none;">
                                                <input type="button" class="btn btn-warning" id="clearfile-3" value="刪除圖片">
                                            </div>
                                            <div class="form-group" id="divforfile-3">
                                                <input type="file" class="filestyle" data-input="false" name="file-3"
                                                    id="file-3" onchange="onchangeimg(this.id,3)">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <fieldset style="width:100%; text-align:center;">
                                                    <legend><i class="fa fa-photo"></i> 產品照片4</legend>
                                                    <div><img id="itempic-4"
                                                            src="{{ asset('asset/img/default_item.png') }}"
                                                            style="max-width:100%;"></div>
                                                    <input type="hidden" data-input="false" name="photo-4" id="photo-4"
                                                        value="">
                                                </fieldset>
                                            </div>
                                            <div class="form-group" id="divforclear-4" style="display:none;">
                                                <input type="button" class="btn btn-warning" id="clearfile-4" value="刪除圖片">
                                            </div>
                                            <div class="form-group" id="divforfile-4">
                                                <input type="file" class="filestyle" data-input="false" name="file-4"
                                                    id="file-4" onchange="onchangeimg(this.id,4)">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <fieldset style="width:100%; text-align:center;">
                                                    <legend><i class="fa fa-photo"></i> 產品照片5</legend>
                                                    <div><img id="itempic-5"
                                                            src="{{ asset('asset/img/default_item.png') }}"
                                                            style="max-width:100%;"></div>
                                                    <input type="hidden" data-input="false" name="photo-5" id="photo-5"
                                                        value="">
                                                </fieldset>
                                            </div>
                                            <div class="form-group" id="divforclear-5" style="display:none;">
                                                <input type="button" class="btn btn-warning" id="clearfile-5" value="刪除圖片">
                                            </div>
                                            <div class="form-group" id="divforfile-5">
                                                <input type="file" class="filestyle" data-input="false" name="file-5"
                                                    id="file-5" onchange="onchangeimg(this.id,5)">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <fieldset style="width:100%; text-align:center;">
                                                    <legend><i class="fa fa-photo"></i> 產品照片6</legend>
                                                    <div><img id="itempic-6"
                                                            src="{{ asset('asset/img/default_item.png') }}"
                                                            style="max-width:100%;"></div>
                                                    <input type="hidden" data-input="false" name="photo-6" id="photo-6"
                                                        value="">
                                                </fieldset>
                                            </div>
                                            <div class="form-group" id="divforclear-6" style="display:none;">
                                                <input type="button" class="btn btn-warning" id="clearfile-6" value="刪除圖片">
                                            </div>
                                            <div class="form-group" id="divforfile-6">
                                                <input type="file" class="filestyle" data-input="false" name="file-6"
                                                    id="file-6" onchange="onchangeimg(this.id,6)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="menu_main" class="tab-pane fade in active">

                                    <!-- 產品欄位 -->
                                    <div class="col-sm-12">

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_category">
                                                    <label for="category">分類</label>
                                                    <select class="form-control js-select2" name="category" id="category">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>供應商</label>
                                                    <select class="form-control js-select2-supplier" name="item_supplier"
                                                        id="item_supplier">
                                                        <option value=''></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_item_number">
                                                    <label for="item_number">編號(主分類編號+子分類編號+品項數)</label>
                                                    <input class="form-control" name="item_number" id="item_number">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_brand">
                                                    <label for="brand">品牌</label>
                                                    <input class="form-control" name="brand" id="brand">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_name">
                                                    <label for="name">名稱</label>
                                                    <input class="form-control" name="name" id="name">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_name_en">
                                                    <label for="name_en">名稱(英文)</label>
                                                    <input class="form-control" name="name_en" id="name_en">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_buy_price">
                                                    <label for="buy_price">進價</label>
                                                    <input class="form-control" name="buy_price" id="buy_price"
                                                        type='number'>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_sell_price1">
                                                    <label for="sell_price1">售價</label>
                                                    <input class="form-control" name="sell_price1" id="sell_price1"
                                                        type='number'>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_large_unit">
                                                    <label for="large_unit">進貨單位</label>
                                                    <input class="form-control" name="large_unit" id="large_unit">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_small_unit">
                                                    <label for="small_unit">出貨單位</label>
                                                    <input class="form-control" name="small_unit" id="small_unit">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_stock_qty">
                                                    <label for="stock_qty">當前庫存(以出貨單位計算)</label>
                                                    <input class="form-control" name="stock_qty" id="stock_qty"
                                                        type='number' value='0' readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_spec">
                                                    <label for="spec">規格</label>
                                                    <input class="form-control" name="spec" id="spec">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_public_number">
                                                    <label for="public_number">衛署字號</label>
                                                    <input class="form-control" name="public_number" id="public_number">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_old_number">
                                                    <label for="old_number">舊系統編號</label>
                                                    <input class="form-control" name="old_number" id="old_number">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_active">
                                                    <label for="active">狀態</label>
                                                    <select class="form-control" name="active" id="active">
                                                        <option value="1">顯示</option>
                                                        <option value="0">隱藏</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_safe_stock">
                                                    <label for="safe_stock">安全庫存量</label>
                                                    <input class="form-control" name="safe_stock" id="safe_stock"
                                                        value="0" type="number">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_live_times">
                                                    <label for="live_times">有效期限</label>
                                                    <div class="form-inline">
                                                        <div class="input-group col-sm-12">
                                                            <input type="number" class="form-control text-center"
                                                                name="live_times" id="live_times" value="0">
                                                            <div class="input-group-addon">
                                                                <span class="input-group-text">年</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_minimum_sales_qty">
                                                    <label for="minimum_sales_qty">最低出貨量</label>
                                                    <input class="form-control" name="minimum_sales_qty"
                                                        id="minimum_sales_qty" type="number" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_fda_class">
                                                    <label for="fda_class">醫療器材分類分級(Class)</label>
                                                    <select class="form-control" name="fda_class" id="fda_class">
                                                        <option value="0">無</option>
                                                        <option value="1">第一級</option>
                                                        <option value="2">第二級</option>
                                                        <option value="3">第三級</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group" id="div_open_sales">
                                                    <label for="open_sales">是否可以公開販售(個人)</label>
                                                    <select class="form-control" name="open_sales" id="open_sales">
                                                        <option value="0">否</option>
                                                        <option value="1">是</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group" id="div_bluesign">
                                                    <label for="bluesign">是否 bluesign® 產品</label>
                                                    <select class="form-control" name="bluesign" id="bluesign">
                                                        <option value="0">否</option>
                                                        <option value="1">是</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group" id="div_is_fee_item">
                                                    <label for="is_fee_item">是否為費用性品項</label>
                                                    <select class="form-control" name="is_fee_item" id="is_fee_item">
                                                        <option value="0">否</option>
                                                        <option value="1">是</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group" id="div_remark">
                                                    <label for="remark">備註</label>
                                                    <textarea class="form-control" rows="3" name="remark"
                                                        id="remark"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <h4><i class="fa fa-th-large"></i> 商品簡介(描述)</h4>
                                    <textarea class="form-control" rows="5" name="description"
                                        placeholder="請簡單描述商品介紹"></textarea>
                                </div>
                                <div class="col-sm-6">
                                    <h4><i class="fa fa-th-large"></i> 商品規格</h4>
                                    <textarea class="form-control" rows="5" name="specification" placeholder="例如:
                                    產地：台灣
                                    適用族群：老年人、嬰幼兒、兒童、成人、通用
                                    核准字號：衛署醫器製壹字第002376號　
                                    "></textarea>
                                </div>
                            </div>

                            <h4><i class="fa fa-th-large"></i> 商品特色(圖文)</h4>
                            <textarea name="features"></textarea>
                            <hr>


                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button class="btn btn-success" id="btn-save"><i class="fa fa-save"></i>
                                            儲存</button>
                                        <button class="btn btn-danger" id="btn-cancel"><i class="fa fa-ban"></i>
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
        });
        //判斷fiile change 

        function onchangeimg(id, num) {
            var mfile = $('#' + id)[0].files[0];
            var itempic = "#itempic-" + num;
            console.log(itempic);
            var reader = new FileReader();
            reader.onload = function() {
                dataURL = reader.result;
                $(itempic).attr("src", dataURL);
            };
            reader.readAsDataURL(mfile);

        }
    </script>
@endsection
