@extends('Backend.master')
@section('title', '報價單簽核')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-primary">
                <div class="panel-heading">報價單簽核</div>
                <div class="panel-body">
                    <form role="form" id="new-form" method="post" action="{{ route('quotation_review.update' , $data['id']) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row" style="padding: 10px;">
                            <div class="col-sm-12">
                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 單號</label></div>
                                    <div class="col-sm-3">{{ $data['quotation']['doc_number'] }}</div>
                                    <div class="col-sm-1"><label> 供應商</label></div>
                                    <div class="col-sm-3">{{ $data['supplier'][ $data['quotation']['supplier_id']]['name']?? '' }}</div>
                                    <div class="col-sm-1"><label> 狀態</label></div>
                                    <div class="col-sm-3">{{ $data['status_code'][$data['quotation']['status_code']]?? '' }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 幣別</label></div>
                                    <div class="col-sm-3" >{{ $data['quotation']['currency_code'] }}</div>
                                    <div class="col-sm-1"><label> 匯率</label></div>
                                    <div class="col-sm-3">{{ $data['quotation']['exchange_rate'] }}</div>
                                    <div class="col-sm-1"><label> 稅別</label></div>
                                    <div class="col-sm-3">{{ $data['taxList'][$data['quotation']['tax']]?? '' }}</div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-1"><label> 備註</label></div>
                                    <div class="col-sm-10">{{ $data['quotation']['remark'] }}</div>
                                </div>
                            </div>

                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                    <tr>
                                        <th>商品編號</th>
                                        <th>商品名稱</th>
                                        <th>國際條碼</th>
                                        <th>單價</th>
                                        <th>最小採購量</th>
                                    </tr>
                                </thead>
                                @foreach($data['quotation_detail'] as $v)
                                     <tbody>
                                        <tr>
                                            <td>{{ $v['product_items_no'] }}</td>
                                            <td>{{ $v['product_name'] }}</td>
                                            <td>{{$v['pos_item_no']}}</td>
                                            <td>{{ $v['original_unit_price'] }}</td>
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
                                @foreach($data['quotation_detail_log'] as $k => $logVal)
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
                                    <a class="btn btn-danger btn" href="{{ route('quotation_review') }}"><i class="fa fa-ban"></i>取消</a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
