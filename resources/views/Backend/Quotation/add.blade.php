@extends('Backend.master')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body">
                    <form role="form" id="new-form" method="post" action="{{ route('quotation.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <!-- 欄位 -->
                            <div class="col-sm-12">

                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group" id="supplier">
                                            <label for="supplier">供應商</label>
                                            <select class="form-control js-select2-department" name="supplier_id" id="supplier_id">
                                                @foreach($data['supplier'] as $v)
                                                    <option value='{{ $v['id'] }}' {{ (isset($data['quotation']['supplier_id']) && $v['id']==$data['quotation']['supplier_id'])? 'selected':'' }}>{{ $v['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group" id="div_trade_date">
                                            <label for="trade_date">報價日期</label>
                                            <div class='input-group date' id='datetimepickera'>
                                                <input type='text' class="form-control" name="submitted_at" id="submitted_at" value="" />
                                                <span class="input-group-addon">
                                                  <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group" id="div_doc_number">
                                            <label for="doc_number">報價單號</label>
                                            <input class="form-control" name="doc_number" id="doc_number" value="{{ $data['quotation']['doc_number']?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group" id="div_currency_code">
                                            <label for="currency_code">幣別</label>
                                            <select class="form-control js-select2" name="currency_code" id="currency_code">
                                                <option value='TWD'>新台幣</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label for="total_tax_price">匯率</label>
                                            <input class="form-control" name="exchange_rate" id="exchange_rate" value="1" readonly >
                                            <input type="hidden" name="exchange_rate" id="exchange_rate" value="1">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group" id="supplier">
                                            <label for="supplier">稅別</label>
                                            <select class="form-control js-select2-item" name="tax" id="tax">
                                                <option value=1 {{ (isset($data['quotation']['tax']) && $data['quotation']['tax'] ==1)? 'selected' : '' }}>應稅</option>
                                                <option value=0 {{ (isset($data['quotation']['tax']) && $data['quotation']['tax'] ==0)? 'selected' : '' }}>未稅</option>
                                                <option value=2 {{ (isset($data['quotation']['tax']) && $data['quotation']['tax'] ==2)? 'selected' : '' }}>內含</option>
                                                <option value=3 {{ (isset($data['quotation']['tax']) && $data['quotation']['tax'] ==3)? 'selected' : '' }}>零稅率</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" id="div_remark">
                                            <label for="remark">備註</label>
                                            <textarea class="form-control" rows="3" name="remark" id="remark">{{ $data['quotation']['remark']?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h4><i class="fa fa-th-large"></i> 品項</h4>
                                <div id="ItemDiv" >
                                    <input type="hidden" name="rowNo" id="rowNo" value="0">
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <a class="btn btn-warning" id="btn-addNewRow"><i class="fa fa-plus"></i> 新增品項</a>
                                    </div>
                                </div>
                                <div class="row"><div class="col-sm-12"><hr></div></div>

                                <input type="hidden" name="status_code" id="status_code" value="">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success" type="button" onclick="saveDraft()"><i class="fa fa-save"></i> 儲存草稿</button>
                                            <button class="btn btn-success" type="button" onclick="saveReview()"><i class="fa fa-save"></i> 儲存並送審</button>
                                            <button class="btn btn-danger" type="button" onclick="cancel()"><i class="fa fa-ban"></i> 取消</button>
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

        @section('js')
            <script>
                $(document).ready(function()
                {
                    // 新增品項
                    $('#btn-addNewRow').click(function () { AddItemRow("process","input"); });

                    $('#supplier_id').select2();
                    $('#tax').select2();
                });

                $(function () {
                    $('#datetimepickera').datetimepicker({
                        format:'YYYY-MM-DD',
                    });

                    var quotation_id = '{{ $data['quotation']['id']?? '' }}';
                    if (quotation_id != ''){
                        ajaxGetItem(quotation_id);
                    }
                });

                function cancel()
                {
                    if(confirm("確認放棄存檔?")){
                        window.location.href = "{{ route('quotation') }}";
                    }
                    return false;
                };

                function saveDraft(){
                    if(confirm("確定要儲存為草稿？")){
                        $('#status_code').val('DRAFTED');
                        $('#new-form').submit();
                    }
                    return false;
                }

                function saveReview(){
                    if(confirm("單據送審後無法再修改，確定要送審？")){
                        $('#status_code').val('REVIEWING');
                        $('#new-form').submit();
                    }
                    return false;
                }
            </script>

            <script>
                $(document).ready(function () {
                    $('#new-form').validate({
                        rules: {
                            submitted_at: {
                                required: true
                            },
                            "item[]":{
                                required: true
                            }
                        },
                        messages: {
                            submitted_at: "請輸入報價日期",
                            "item[]": "請選擇品項",
                        },
                        errorElement: 'span',
                        errorPlacement: function (error, element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                            $(element).addClass('is-invalid');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $(element).removeClass('is-invalid');
                        }
                    });
                });
            </script>
        @endsection

    @include('Backend.Quotation.addItem')
@endsection
