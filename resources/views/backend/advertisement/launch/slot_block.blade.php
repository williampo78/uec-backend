<div id="slot-block" class="form-horizontal">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label">
                        版位 <span class="text-red">*</span>
                    </label>
                </div>
                <div class="col-sm-9">
                    <select class="form-control js-select2-slot-id" name="slot_id" id="slot_id">
                        <option></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label">
                        上架時間 <span class="text-red">*</span>
                    </label>
                </div>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-sm-5">
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
                        <div class="col-sm-2 text-center">
                            <label class="control-label">～</label>
                        </div>
                        <div class="col-sm-5">
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
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label">
                        狀態 <span class="text-red">*</span>
                    </label>
                </div>
                <div class="col-sm-9">
                    <label class="radio-inline">
                        <input type="radio" name="active" id="active_enabled" checked value="1" />啟用
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="active" id="active_disabled" value="0" />關閉
                    </label>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label">備註</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="remark" name="remark" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label label-container">
                        <span>版位標題色</span>
                        <span>(例：#00BB00)</span>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control colorpicker" id="slot_title_color" name="slot_title_color" disabled autocomplete="off" />
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label label-container">
                        <span>版位背景色</span>
                        <span>(例：#DAF7CD)</span>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control colorpicker" type="text" id="slot_color_code" name="slot_color_code" disabled autocomplete="off" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label">版位標題</label>
                </div>
                <div class="col-sm-9">
                    <input class="form-control" type="text" id="slot_title" name="slot_title" value="" disabled />
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-3">
                    <label class="control-label">版位icon</label>
                </div>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-sm-5">
                            <input type="file" id="slot_icon_name" name="slot_icon_name" disabled />
                        </div>
                        <div class="col-sm-3">
                            <img src="" id="img_slot_icon_name" class="img-responsive" width="100" height="100" alt="版位icon" />
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-danger" id="btn-delete-slot-icon-name" title="刪除">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr style="border-top: 1px solid gray;" />

    <div class="row">
        <div class="col-sm-2">
            <label>〔看更多〕</label>
        </div>
        <div class="col-sm-10">
            <div class="row">
                <div class="col-sm-12">
                    <div class="radio">
                        <label>
                            <input type="radio" name="see_more_action" value="X" v-model="seeMore.action" />
                            無連結
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <div class="radio">
                        <label>
                            <input type="radio" name="see_more_action" value="U" v-model="seeMore.action" />
                            URL
                        </label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="see_more_url" name="see_more_url" value="" />
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="see_more_target_blank" name="see_more_target_blank" class="checkbox-normal" /> 另開視窗
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <div class="radio">
                        <label>
                            <input type="radio" name="see_more_action" value="C" v-model="seeMore.action" />
                            商品分類頁
                        </label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <treeselect
                            :class="{'treeselect-invalid': !seeMore.categoryValid}"
                            :required="seeMore.categoryRequired"
                            v-model="seeMore.categoryId"
                            :options="categoryTree"
                            :normalizer="normalizer"
                            name="see_more_cate_hierarchy_id"
                            :flat="true"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr style="border-top: 1px solid gray;" />
</div>


