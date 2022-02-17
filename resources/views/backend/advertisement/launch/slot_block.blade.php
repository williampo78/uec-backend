<div id="slot-block">
    <div class="row">
        <div class="col-sm-3">
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
                        <div class='input-group date' id='datetimepicker_start_at'>
                            <input type='text' class="form-control datetimepicker-input"
                                data-target="#datetimepicker_start_at" name="start_at" id="start_at" value=""
                                autocomplete="off" />
                            <span class="input-group-addon" data-target="#datetimepicker_start_at"
                                data-toggle="datetimepicker">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1">
                    <h5>～</h5>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <div class='input-group date' id='datetimepicker_end_at'>
                            <input type='text' class="form-control datetimepicker-input"
                                data-target="#datetimepicker_end_at" name="end_at" id="end_at" value=""
                                autocomplete="off" />
                            <span class="input-group-addon" data-target="#datetimepicker_end_at"
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
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="slot_color_code">版位主色 (例：#00BB00)</label>
                <input class="form-control colorpicker" type="text" id="slot_color_code" name="slot_color_code" value=""
                    disabled autocomplete="off" />
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="slot_icon_name">版位icon</label>
                <input type="file" id="slot_icon_name" name="slot_icon_name" disabled />
                {{-- <div id="slot_icon_name"></div> --}}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="slot_title">版位標題</label>
                <input class="form-control" type="text" id="slot_title" name="slot_title" value="" disabled />
            </div>
        </div>
    </div>

    <hr style="border-top: 1px solid gray;" />
</div>
