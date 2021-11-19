@extends('Backend.master')

@section('title', '編輯廣告版位' )

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body" id="requisitions_vue_app">
                        <form role="form" id="update-form" method="post" action="{{ route('advertisemsement_block.update', $ad_slot->id) }}">
                            @method('PUT')
                            @csrf

                            <div class="row">
                                <!-- 欄位 -->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>適用頁面</label>
                                                <input class="form-control" value="{{ $ad_slot->description }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>代碼</label>
                                                <input class="form-control"  value="{{ $ad_slot->slot_code }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="slot_desc">名稱</label>
                                                <input class="form-control" value="{{ $ad_slot->slot_desc }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Mobile適用</label>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <input type="radio" name="is_mobile_applicable" id="is_mobile_applicable_enabled" {{ $ad_slot->is_mobile_applicable ? 'checked' : '' }} disabled>
                                                        <label for="is_mobile_applicable_enabled">是</label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="radio"  name="is_mobile_applicable" id="is_mobile_applicable_disabled" {{ $ad_slot->is_mobile_applicable ? '' : 'checked' }} disabled>
                                                        <label for="is_mobile_applicable_disabled">否</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Desktop適用</label>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <input type="radio" name="is_desktop_applicable" id="is_desktop_applicable_enabled" {{ $ad_slot->is_desktop_applicable ? 'checked' : '' }} disabled>
                                                        <label for="is_desktop_applicable_enabled">是</label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="radio"  name="is_desktop_applicable" id="is_desktop_applicable_disabled" {{ $ad_slot->is_desktop_applicable ? '' : 'checked' }} disabled>
                                                        <label for="is_desktop_applicable_disabled">否</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>上架類型</label>
                                                <input class="form-control" value="{{ config('uec.ad_slot_type_option')[$ad_slot->slot_type] ?? '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>狀態 <span style="color:red;">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <input class="validate[required]" type="radio" name="active" id="active_enabled" {{ $ad_slot->active ? 'checked' : '' }} value="1">
                                                        <label for="active_enabled">是</label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input class="validate[required]" type="radio"  name="active" id="active_disabled" {{ $ad_slot->active ? '' : 'checked' }} value="0">
                                                        <label for="active_disabled">否</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="remark">備註</label>
                                                <textarea class="form-control validate[maxSize[255]]" rows="3" name="remark" id="remark">{{ $ad_slot->remark ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr/>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                @if ($share_role_auth['auth_update'])
                                                    <button class="btn btn-success" type="button" id="btn-save"><i class="fa fa-save"></i> 儲存</button>
                                                @endif

                                                <button class="btn btn-danger" type="button" id="btn-cancel"><i class="fa fa-ban"></i> 取消</button>
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
@endsection

@section('js')
    <script>
        $(function () {
            $("#update-form").validationEngine();

            $('#btn-save').on('click', function () {
                $("#update-form").submit();
            });

            $('#btn-cancel').on('click', function () {
                location.href = "{{ route('advertisemsement_block') }}";
            });
        });
    </script>
@endsection

