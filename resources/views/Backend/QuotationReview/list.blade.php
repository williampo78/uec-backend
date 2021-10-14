@extends('Backend.master')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-sign-in"></i> 報價單簽核</h1>
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
                                            <button class="btn btn-warning"><i class="fa fa-search  "></i> 查詢</button>
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
                                <th>報價日期</th>
                                <th>報價單號</th>
                                <th>供應商</th>
                                <th>狀態</th>
                                <th>送審時間</th>
                                <th>結案時間</th>
                            </tr>
                            </thead>
{{--                            @foreach($data as $k => $v)--}}
{{--                                <form id="del-{{ $v['id'] }}" action="/backend/quotation/{{ $v['id'] }}" method="post">--}}
{{--                                    @method('DELETE')--}}
{{--                                    @csrf--}}
{{--                                </form>--}}
{{--                                <tbody>--}}
{{--                                <td>--}}
{{--                                    @if($share_role_auth['auth_query'])--}}
{{--                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#row_detail" data-id="{{ $v['id'] }}" onclick="row_detail({{ $v['id'] }});"><i class="fa fa-search"></i></button>--}}
{{--                                    @endif--}}

{{--                                    @if($share_role_auth['auth_update'] && $v['status_code']=='DRAFTED' && $v['created_by']==$data['user_id'])--}}
{{--                                        <a class="btn btn-info btn-sm" href="{{ route('quotation.edit' , $v['id']) }}">修改</a>--}}
{{--                                    @endif--}}

{{--                                    @if($share_role_auth['auth_delete'] && $v['status_code']=='DRAFTED'&& $v['created_by']==$data['user_id'])--}}
{{--                                        <button class="btn btn-danger btn-sm" onclick="del({{ $v['id'] }} , '{{ $v['doc_number'] }}' );">刪除</button>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                                <td>{{ $v['created_at'] }}</td>--}}
{{--                                <td>{{ $v['doc_number'] }}</td>--}}
{{--                                <td>{{ $data['supplier'][$v['supplier_id']]['name'] }}</td>--}}
{{--                                <td>{{ $data['status_code'][$v['status_code']]?? '' }}</td>--}}
{{--                                <td>{{ $v['submitted_at'] }}</td>--}}
{{--                                <td>{{ $v['closed_at'] }}</td>--}}
{{--                                </tbody>--}}
{{--                            @endforeach--}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('js')

    @endsection
@endsection
