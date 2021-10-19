@extends('Backend.master')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-sign-in"></i> 採購單</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">

                        <form role="form" id="select-form" method="GET" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2"><h5>供應商</h5></div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-department" name="supplier" id="supplier">
                                            @foreach($data['supplier'] as $v)
                                                <option value='{{ $v['id'] }}' {{ (isset($data['getData']['supplier']) && $v['id']==$data['getData']['supplier'])? 'selected':'' }}>{{ $v['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3"><h5>供應商統編</h5></div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="company_number" id="company_number" value="{{ $data['getData']['company_number']?? '' }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3"><h5>狀態</h5></div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="status" id="status">
                                            <option value=''></option>
                                            <option value='drafted' {{ (isset($data['getData']['status']) && $data['getData']['status'] == 'drafted')? 'selected':''  }}>草稿</option>
                                            <option value='reviewing' {{ (isset($data['getData']['status']) && $data['getData']['status'] == 'reviewing')? 'selected':''  }}>簽核</option>
                                            <option value='approved' {{ (isset($data['getData']['status']) && $data['getData']['status'] == 'approved')? 'selected':''  }}>已核准</option>
                                            <option value='rejected' {{ (isset($data['getData']['status']) && $data['getData']['status'] == 'rejected')? 'selected':''  }}>已駁回</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2"><h5>採購日期：</h5></div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_start_date">
                                            <div class='input-group date' id='datetimepicker'>
                                                <input type='text' class="form-control" name="select_start_date" id="select_start_date" value="{{ $data['getData']['select_start_date']?? '' }}"/>
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1"><h5>～</h5></div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_end_date">
                                            <div class='input-group date' id='datetimepicker2'>
                                                <input type='text' class="form-control" name="select_end_date" id="select_end_date" value="{{ $data['getData']['select_end_date']?? '' }}"/>
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3"><h5>採購單號</h5></div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="order_number" id="order_number" value="{{ $data['getData']['order_number']?? '' }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3"><h5>請購單號</h5></div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="requisitions_purchase_number" id="order_number" value="{{ $data['getData']['requisitions_purchase_number']?? '' }}">
                                    </div>
                                </div>

                                <div class="col-sm-9">
                                </div>

                                <div class="col-sm-3 text-right">
                                    <div class="col-sm-12">
                                        {{--                                        @if ($share_role_auth['auth_query'])--}}
                                        <button class="btn btn-warning"><i class="fa fa-search  "></i> 查詢</button>
                                        {{--                                        @endif--}}
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                @if($share_role_auth['auth_create'])
                                    <a class="btn btn-block btn-warning btn-sm" href="{{route('order_supplier.create')}}"><i class="fa fa-plus"></i> 由請購單帶入</a>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                            <tr>
                                <th>功能</th>
                                <th>採購日期</th>
                                <th>採購單號</th>
                                <th>供應商</th>
                                <th>總金額</th>
                                <th>狀態</th>
                                <th>請購單號</th>
                                <th>預計進貨日</th>
                                <th>預進表拋出時間</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data['order_supplier'] as $k => $v)
                                <form id="del-{{ $v['id'] }}" action="/backend/quotation/{{ $v['id'] }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                </form>
                                <tr>
                                    <td>
{{--                                        @if($share_role_auth['auth_query'])--}}
                                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#row_detail" data-id="{{ $v['id'] }}" onclick="row_detail({{ $v['id'] }});"><i class="fa fa-search"></i></button>
{{--                                        @endif--}}

{{--                                        @if($share_role_auth['auth_update'] && $v['status_code']=='DRAFTED' && $v['created_by']==$data['user_id'])--}}
                                            <a class="btn btn-info btn-sm" href="{{ route('order_supplier.edit' , $v['id']) }}">修改</a>
{{--                                        @endif--}}

                                        @if($share_role_auth['auth_delete'] && $v['status_code']=='DRAFTED'&& $v['created_by']==$data['user_id'])
                                            <button class="btn btn-danger btn-sm" onclick="del({{ $v['id'] }} , '{{ $v['number'] }}' );">刪除</button>
                                        @endif
                                    </td>
                                    <td>{{ $v['trade_date'] }}</td>
                                    <td>{{ $v['number'] }}</td>
                                    <td>{{ $data['supplier'][$v['supplier_id']]['name']?? '' }}</td>
                                    <td>{{ $v['total_price'] }}</td>
                                    <td>{{ $data['status_code'][$v['status_code']]?? '' }}</td>
                                    <td>{{ $v['requisitions_purchase_number'] }}</td>
                                    <td>預計進貨日</td>
                                    <td>預進表拋出時間</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('Backend.OrderSupplier.detail')
    @section('js')
        <script>
            $('#supplier').select2();
            $('#status').select2();
            $(document).ready(function () {
            });

            $(function () {
                $('#datetimepicker').datetimepicker({
                    format:'YYYY-MM-DD',
                });
                $('#datetimepicker2').datetimepicker({
                    format:'YYYY-MM-DD',
                });
            });

            function del(id, doc_number)
            {
                if(confirm("確定要刪除採購單"+doc_number+"?")){
                    document.getElementById('del-'+id).submit();
                }
                return false;
            };


        </script>
    @endsection
@endsection
