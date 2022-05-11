<div id="slot-block">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="slot">版位 <span style="color:red;">*</span></label>
                <select class="form-control js-select2-slot-id" name="slot_id" id="slot_id">
                    <option></option>
                </select>
            </div>
        </div>

        <div class="col-sm-6">
            <label>上架時間 <span style="color:red;">*</span></label>
            <div class="row">
                <div class="col-sm-5">
                    <div class="form-group">
                        <div class="input-group" id="start_at_flatpickr">
                            <input type="text" class="form-control" name="start_at" id="start_at" value=""
                                autocomplete="off" data-input />
                            <span class="input-group-btn" data-toggle>
                                <button class="btn btn-default" type="button">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1">
                    <h5>～</h5>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <div class="input-group" id="end_at_flatpickr">
                            <input type="text" class="form-control" name="end_at" id="end_at" value=""
                                autocomplete="off" data-input />
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


    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>狀態 <span style="color:red;">*</span></label>
                <div class="row">
                    <div class="col-sm-6">
                        <label class="radio-inline">
                            <input type="radio" name="active" id="active_enabled" checked value="1" />啟用
                        </label>
                    </div>
                    <div class="col-sm-6">
                        <label class="radio-inline">
                            <input type="radio" name="active" id="active_disabled" value="0" />關閉
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="slot_title">備註</label>
                <input class="form-control" type="text" id="remark" name="remark" value=""/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="slot_title_color">版位標題色(例：#00BB00)</label>
                <input class="form-control colorpicker" type="text" id="slot_title_color" name="slot_title_color"
                    value="" disabled autocomplete="off" />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="slot_color_code">版位背景色(例：#DAF7CD)</label>
                <input class="form-control colorpicker" type="text" id="slot_color_code" name="slot_color_code" value=""
                    disabled autocomplete="off" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="slot_title">版位標題</label>
                <input class="form-control" type="text" id="slot_title" name="slot_title" value="" disabled />
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="slot_icon_name">版位icon</label>
                <input type="file" id="slot_icon_name" name="slot_icon_name" disabled /><br />
                <img src="" id="img_slot_icon_name" class="img-responsive" width="70" height="70" /><br />
                <button type="button" class="btn btn-danger" id="btn-delete-slot-icon-name" title="刪除"><i
                        class="fa-solid fa-trash-can"></i></button>
            </div>
        </div>
    </div>
</div>
<hr style="border-top: 1px solid gray;" />
<div>
    <div class="form-group">
        <div class="radio row">
            <div class="col-sm-2"><label>〔看更多〕</label></div>
            <div class="col-sm-2">
                <label>
                    <input type="radio" name="see_more_action" value="X" checked />
                    無連結
                </label>
            </div>
            <div class="col-sm-4"></div>

        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-2">
                <div class="radio">
                    <label>
                        <input type="radio" name="see_more_action" value="U" />
                        URL
                    </label>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <input type="text" class="form-control" id="see_more_url" name="see_more_url" value="" />
                </div>
            </div>
            <div class="col-sm-2">
                <div class="radio">
                    <label class="checkbox">
                        <input type="checkbox" id="see_more_target_blank" name="see_more_target_blank" />另開視窗
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-2">
                <div class="radio">
                    <label>
                        <input type="radio" name="see_more_action" value="C" />
                        商品分類頁
                    </label>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <select class="form-control js-select2-text-block-product-category" name="see_more_cate_hierarchy_id" id="see_more_cate_hierarchy_id" >
                        <option value=""></option>
                        @foreach ($product_category as $val)
                        <option value="{{$val->id}}">{{$val->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<hr style="border-top: 1px solid gray;" />
