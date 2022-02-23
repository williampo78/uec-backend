@extends('backend.master')
@section('title', '請購單簽核')
@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-arrow-right-to-bracket"></i> 請購單簽核</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">

                        <form role="form" id="select-form" method="GET" action="" enctype="multipart/form-data">
                            <div class="row">

                                <div class="col-sm-5">
                                    <div class="col-sm-2"><h5>簽核者</h5></div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="reviewer" id="reviewer" value="{{ $data['user_name'] }}" readonly>
                                    </div>
                                </div>

                                <div class="col-sm-7 text-right">
                                    <div class="col-sm-12">
                                        @if ($share_role_auth['auth_query'])
                                            <button class="btn btn-warning"><i class="fa-solid fa-magnifying-glass"></i></i> 查詢</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">

                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                            <tr>
                                <th>功能</th>
                                <th>請購日期</th>
                                <th>請購單號</th>
                                <th>供應商名稱</th>
                                <th>狀態</th>
                                <th>總金額</th>
                                <th>送審時間</th>
                            </tr>
                            </thead>

                            @foreach($data['requisition_purchase'] as $k => $v)

                                <tbody>
                                <td>
                                    @if($share_role_auth['auth_update'])
                                        <a class="btn btn-info btn-sm" href="{{ route('requisitions_purchase_review.edit' ,$v['id']) }}">簽核</a>
                                    @endif
                                </td>
                                <td>{{ $v['trade_date'] }}</td>
                                <td>{{ $v['number'] }}</td>
                                <td>{{ $data['supplier'][$v['supplier_id']]['name']?? '' }}</td>
                                <td>{{ $data['status_code'][$v['status']]?? '' }}</td>
                                <td>{{ $v['total_price'] }}</td>
                                <td>{{ $v['created_at'] }}</td>
                                </tbody>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('js')

@endsection
@endsection
