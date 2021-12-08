<div id="campaign-block" class="form-horizontal">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-2">
                    <label for="campaign_name" class="control-label">活動名稱 <span style="color: red;">*</span></label>
                </div>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="campaign_name" name="campaign_name" value="" />
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-2">
                    <label class="control-label">狀態 <span style="color: red;">*</span></label>
                </div>
                <div class="col-sm-2">
                    <label class="radio-inline">
                        <input type="radio" name="active" id="active_enabled" value="1" />生效
                    </label>
                </div>
                <div class="col-sm-2">
                    <label class="radio-inline">
                        <input type="radio" name="active" id="active_disabled" checked value="0" />失效
                    </label>
                </div>
            </div>
        </div>
    </div>
    <br/>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-2">
                    <label for="campaign_type" class="control-label">活動類型 <span style="color: red;">*</span></label>
                </div>
                <div class="col-sm-10">
                    <select class="form-control" name="campaign_type" id="campaign_type">
                        <option></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-5">
                    <div class="form-group">
                        <div class="col-sm-5">
                            <label for="n_value" class="control-label">N (滿額) = <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-sm-7">
                            <input type="number" class="form-control" id="n_value" name="n_value" value="" min="0" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label for="x_value" class="control-label">X (折扣) = <span style="color: red;">*</span></label>
                        </div>
                        <div class="col-sm-5">
                            <input type="number" class="form-control" id="x_value" name="x_value" value="" />
                        </div>
                        <div class="col-sm-4">
                            <p class="form-control-static">打85折，輸入0.85</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br/>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-2">
                    <label for="start_at" class="control-label">上架時間起 <span style="color: red;">*</span></label>
                </div>
                <div class="col-sm-10">
                    <div class='input-group date' id='datetimepicker_start_at'>
                        <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker_start_at"
                            name="start_at" id="start_at" value="" autocomplete="off" />
                        <span class="input-group-addon" data-target="#datetimepicker_start_at" data-toggle="datetimepicker">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <div class="col-sm-2">
                    <label for="end_at" class="control-label">上架時間訖 <span style="color: red;">*</span></label>
                </div>
                <div class="col-sm-10">
                    <div class='input-group date' id='datetimepicker_end_at'>
                        <input type='text' class="form-control datetimepicker-input" data-target="#datetimepicker_end_at"
                            name="end_at" id="end_at" value="" autocomplete="off" />
                        <span class="input-group-addon" data-target="#datetimepicker_end_at" data-toggle="datetimepicker">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr style="border-top: 1px solid gray;" />
</div>
