@extends('Backend.master')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body">
                    <form role="form" id="new-form" method="post" action="{{ ($data['act'] == 'add')? route('order_supplier.store') : route('order_supplier.update' , $data['id']) }}" enctype="multipart/form-data">
                        @csrf
                        @if($data['act'] == 'upd')
                            @method('PUT')
                        @endif

                        <div class="row">

                            <!-- 欄位 -->
                            <div class="col-sm-12">

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="supplier">請購單</label>
                                            <select class="form-control js-select2-department" onchange="getRPInfo(this.value);" name="requisitions_purchase_id" id="requisitions_purchase_id">
                                                <option value="">請選擇</option>
                                                @foreach($data['requisitions_purchase'] as $v)
                                                    <option value='{{ $v['id'] }}' {{ (isset($data['order_supplier']['requisitions_purchase_id']) && $v['id']==$data['order_supplier']['requisitions_purchase_id'])? 'selected':'' }}>{{ $v['number'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_trade_date">
                                            <label for="trade_date">採購日期</label>
                                            <div class='input-group date' id='datetimepicker'>
                                                <input type='text' class="form-control" name="trade_date" id="trade_date" value="{{ $data['order_supplier']['trade_date']?? '' }}" />
                                                <span class="input-group-addon">
                                                  <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="number">採購單號</label>
                                            <input class="form-control" id="number" value="{{ $data['order_supplier']['number']?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="supplier">供應商</label>
                                            <input class="form-control" id="supplier" value="{{ (isset($data['order_supplier']) && $data['supplier'][$data['order_supplier']['supplier_id']]['name'])?? '' }}" readonly>
                                            <input type="hidden" id="supplier_id" name="supplier_id">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="receiver_name">收件人名稱</label>
                                            <input class="form-control" name="receiver_name" id="receiver_name" value="{{ $data['order_supplier']['receiver_name']?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="receiver_address">收件人地址</label>
                                            <input class="form-control" name="receiver_address" id="receiver_address" value="{{ $data['order_supplier']['receiver_address']?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="currency_code">幣別</label>
                                            <select class="form-control js-select2-default" id="currency_code" name="currency_code">
                                                <option value='TWD'>新台幣</option>
                                            </select>
                                        </div>

                                        <input type="hidden" name="currency_id" value="1">
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="currency_price">匯率</label>
                                            <input class="form-control" id="currency_price" name="currency_price" value="1" readonly >
                                            <input type="hidden" name="exchange_rate" id="exchange_rate" value="1">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="original_total_tax_price">原幣稅額</label>
                                            <input class="form-control" id="original_total_tax_price" name="original_total_tax_price" value="{{ $data['order_supplier']['original_total_tax_price']?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="original_total_price">原幣總金額</label>
                                            <input class="form-control" id="original_total_price" name="original_total_price" value="{{ $data['order_supplier']['original_total_price']?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="tax">稅別</label>
                                            <input class="form-control" id="tax" value="{{ isset($data['order_supplier']) && $data['tax'][$data['order_supplier']['tax']]?? '' }}" readonly>
                                            <input type="hidden" name="tax" id="tax_code">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="total_tax_price">稅額</label>
                                            <input class="form-control" id="total_tax_price" name="total_tax_price" value="{{ $data['order_supplier']['total_tax_price']?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="total_price">總金額</label>
                                            <input class="form-control" id="total_price" name="total_price" value="{{ $data['order_supplier']['total_price']?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="invoice_company_number">發票統編</label>
                                            <input class="form-control" name="invoice_company_number" id="invoice_company_number" value="{{ $data['order_supplier']['invoice_company_number']?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="invoice_name">發票抬頭</label>
                                            <input class="form-control" name="invoice_name" id="invoice_name" value="{{ $data['order_supplier']['invoice_name']?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="invoice_address">發票地址</label>
                                            <input class="form-control" name="invoice_address" id="invoice_address" value="{{ $data['order_supplier']['invoice_address']?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="doc_number">庫別</label>
                                            <input class="form-control" id="warehouse" value="庫別" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="trade_date">廠商交貨日</label>
                                            <div class='input-group date' id='datetimepicker2'>
                                                <input type='text' class="form-control" name="supplier_deliver_date" id="supplier_deliver_date" value="{{ $data['order_supplier']['supplier_deliver_date']?? '' }}" />
                                                <span class="input-group-addon">
                                                  <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="trade_date">預計進貨日</label>
                                            <div class='input-group date' id='datetimepicker3'>
                                                <input type='text' class="form-control" name="expect_deliver_date" id="expect_deliver_date" value="{{ $data['order_supplier']['expect_deliver_date']?? '' }}" />
                                                <span class="input-group-addon">
                                                  <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" id="div_remark">
                                            <label for="remark">備註</label>
                                            <textarea class="form-control" rows="3" name="remark" id="remark">{{ $data['order_supplier']['remark']?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h4><i class="fa fa-th-large"></i> 品項</h4>
                                <div id="ItemDiv" >
                                    <input type="hidden" name="rowNo" id="rowNo" value="0">
                                </div>

                                <div class="row"><div class="col-sm-12"><hr></div></div>

                                <input type="hidden" name="status_code" id="status_code" value="">
                                <input type="hidden" name="order_supplier_id" id="order_supplier_id" value="">

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
            $(function () {
                $('#datetimepicker').datetimepicker({
                    format:'YYYY-MM-DD',
                });
                $('#datetimepicker2').datetimepicker({
                    format:'YYYY-MM-DD',
                });
                $('#datetimepicker3').datetimepicker({
                    format:'YYYY-MM-DD',
                });

                // $('#order_supplier_id').val(order_supplier_id);
                var requisitions_purchase_id = '{{ $data['order_supplier']['requisitions_purchase_id']?? '' }}';
                if (requisitions_purchase_id != ''){
                    getRPInfo(requisitions_purchase_id);
                }
            });

            function cancel()
            {
                if(confirm("確認放棄存檔?")){
                    window.location.href = "{{ route('order_supplier') }}";
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

            function getRPInfo(requisitions_purchase_id){
                $.ajax({
                    url: "/backend/order_supplier/ajax",
                    type: "POST",
                    data: {'get_type': "requisitions_purchase" ,'requisitions_purchase_id': requisitions_purchase_id , _token: '{{csrf_token()}}'},
                    enctype: 'multipart/form-data',
                })
                .done(function( data ){

                    var data_array = data.split('@@');
                    if(data_array[0] == "OK") {
                        var obj = jQuery.parseJSON(data_array[1]);

                        $('#supplier').prop('readonly', false).val(obj.supplier_name).prop('readonly', true);
                        $('#original_total_tax_price').prop('readonly', false).val(obj.original_total_tax_price).prop('readonly', true);
                        $('#original_total_price').prop('readonly', false).val(obj.original_total_price).prop('readonly', true);
                        $('#tax').prop('readonly', false).val(obj.tax_name).prop('readonly', true);
                        $('#total_tax_price').prop('readonly', false).val(obj.total_tax_price).prop('readonly', true);
                        $('#total_price').prop('readonly', false).val(obj.total_price).prop('readonly', true);
                        $('#warehouse').prop('readonly', false).val(obj.warehouse_name).prop('readonly', true);
                        $('#supplier_id').val(obj.supplier_id);
                        $('#tax_code').val(obj.tax);

                    }
                });

                ajaxGetItem(requisitions_purchase_id);
            }
        </script>

        <script>

    @endsection

    @include('Backend.OrderSupplier.item')
@endsection
