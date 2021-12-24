@extends('Backend.master')
@section('title', '請購單簽核')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-primary">
                <div class="panel-heading">請購單簽核</div>
                <div class="panel-body">
                    <form role="form" id="new-form" method="post" action="{{ route('requisitions_purchase_review.update' ,$data['id']) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row" style="padding: 10px;">
                            <div class="col-sm-12">
                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 請購單號</label></div>
                                    <div class="col-sm-5">{{ $data['requisitions_purchase']['number'] }}</div>
                                    <div class="col-sm-1"><label> 供應商</label></div>
                                    <div class="col-sm-5">{{ $data['supplier'][$data['requisitions_purchase']['supplier_id']]['name']?? '' }}</div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 幣別</label></div>
                                    <div class="col-sm-5" >新台幣 (匯率：1)</div>
                                    <div class="col-sm-1"><label> 狀態</label></div>
                                    <div class="col-sm-5">{{ $data['status_code'][$data['requisitions_purchase']['status']]?? '' }}</div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 原幣稅額</label></div>
                                    <div class="col-sm-5">{{ $data['requisitions_purchase']['original_total_tax_price'] }}</div>
                                    <div class="col-sm-1"><label> 原幣金額</label></div>
                                    <div class="col-sm-5">{{ $data['requisitions_purchase']['original_total_price'] }}</div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 稅額</label></div>
                                    <div class="col-sm-5">{{ $data['requisitions_purchase']['total_tax_price'] }}</div>
                                    <div class="col-sm-1"><label> 總金額</label></div>
                                    <div class="col-sm-5">{{ $data['requisitions_purchase']['total_price'] }}</div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 備註</label></div>
                                    <div class="col-sm-10">{{ $data['requisitions_purchase']['remark'] }}</div>
                                </div>
                            </div>

                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                <tr>
                                    <th>商品編號</th>
                                    <th>商品名稱</th>
                                    <th>單價</th>
                                    <th>請購量</th>
                                    <th>單位</th>
                                    <th>小計</th>
                                    <th>贈品</th>
                                    <th>最小採購量</th>
                                </tr>
                                </thead>
                                @foreach($data['requisitions_purchase_detail'] as $v)
                                    <tbody>
                                    <tr>
                                        <td>{{ $v['item_number'] }}</td>
                                        <td>{{ $v['combination_name'] }}</td>
                                        <td>{{ $v['item_price'] }}</td>
                                        <td>{{ $v['item_qty'] }}</td>
                                        <td>{{ $v['uom'] }}</td>
                                        <td>{{ $v['subtotal_price'] }}</td>
                                        <td>{{ $v['is_gift']== 1 ? '是':'否' }}</td>
                                        <td>{{$v['min_purchase_qty']}}</td>
                                    </tr>
                                    </tbody>
                                @endforeach
                            </table>

                            <div class="col-sm-12"><label> 簽核紀錄</label></div>
                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                <tr>
                                    <th>次序</th>
                                    <th>簽核人員</th>
                                    <th>簽核時間</th>
                                    <th>簽核結果</th>
                                    <th>備註</th>
                                </tr>
                                </thead>
                                @foreach($data['requisition_purchase_review_log'] as $k => $logVal)
                                    <tbody>
                                    <tr>
                                        <td>{{ $k+1 }}</td>
                                        <td>{{ $logVal['user_name'] }}</td>
                                        <td>{{ $logVal['review_at'] }}</td>
                                        <td>{{ $logVal['review_result'] }}</td>
                                        <td>{{ $logVal['review_remark'] }}</td>
                                    </tr>
                                    </tbody>
                                @endforeach
                            </table>

                            <hr>
                            <div class="col-sm-12">
                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 簽核結果</label></div>
                                    <label class="btn btn-default form-check-label">
                                        <input class="form-check-input" name="review_result" type="radio" value="1">簽核
                                    </label>
                                    <label class="btn btn-default form-check-label">
                                        <input class="form-check-input" name="review_result" type="radio" value="0">取消
                                    </label>
                                    <label for="">
                                        <div class="review_result_error"></div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 結果備註</label></div>
                                    <textarea class="form-control col-sm-3" name="review_remark"></textarea>
                                </div>
                            </div>
                            <hr>
                            <div class="col-sm-12">
                                <div class="row form-group">
                                    <button class="btn btn-success btn"><i class="fa fa-save"></i>儲存</button>
                                    <a class="btn btn-danger btn" href="{{ route('requisitions_purchase_review') }}"><i class="fa fa-ban"></i>取消</a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')

    <script>
        $(document).ready(function() {
            $('#new-form').validate({
                // debug: true,
                submitHandler: function(form) {
                    form.submit();
                },
                rules: {
                    review_result: {
                        required: true
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    // console.log(error, element);
                    if (element.parent('.input-group').length || element.is(':radio')) {
                        error.appendTo($('.review_result_error'));
                        return;
                    }

                    if (element.is('select')) {
                        console.log('B');
                        element.parent().append(error);
                        return;
                    }

                    // error.insertAfter(element);
                },
                messages: {
                    review_result: "請選取簽核結果",
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection
