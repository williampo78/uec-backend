@extends('Backend.master')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-sign-in"></i> 報價單</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">

                        <form role="form" id="select-form" method="GET" action="" enctype="multipart/form-data">
{{--                            @csrf--}}
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
                                        <input class="form-control" name="company_number" id="company_number" value="{{ isset($data['getData']['supplier'])? }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3"><h5>狀態</h5></div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-department" name="status" id="status">
                                            <option value=''></option>
                                            <option value='drafted'>草稿</option>
                                            <option value='reviewing'>簽核</option>
                                            <option value='approved'>已核准</option>
                                            <option value='rejected'>已駁回</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2"><h5>日期：</h5></div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_start_date">
                                            <div class='input-group date' id='datetimepicker'>
                                                <input type='text' class="form-control" name="select_start_date" id="select_start_date" value=""/>
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
                                                <input type='text' class="form-control" name="select_end_date" id="select_end_date" value=""/>
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3"><h5>報價單號</h5></div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="number" id="number">
                                    </div>
                                </div>

                                <div class="col-sm-3 text-right">
                                    <div class="col-sm-12">
                                        <button class="btn btn-warning"><i class="fa fa-search  "></i> 查詢</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <a class="btn btn-block btn-warning btn-sm" href="{{route('quotation.create')}}"><i class="fa fa-plus"></i> 新增</a>
                            </div>
                        </div>
                        <hr>
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
                            <tbody>

                            @foreach($data['quotation'] as $k => $v)
                                <td>
                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#row_detail" data-id="{{ $v['id'] }}" onclick="row_detail({{ $v['id'] }});"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-info btn-sm" href="{{ route('quotation.edit' , $v['id']) }}">修改</button>
                                    <button class="btn btn-info btn-sm" href="{{ route('quotation.destroy' , $v['id']) }}">刪除</button>
                                </td>
                                <td>{{ $v['created_at'] }}</td>
                                <td>{{ $v['doc_number'] }}</td>
                                <td>{{ $data['supplier'][$v['supplier_id']]['name'] }}</td>
                                <td>{{ $data['status_code'][$v['status_code']] }}</td>
                                <td>{{ $v['submitted_at'] }}</td>
                                <td>{{ $v['closed_at'] }}</td>

                            @endforeach

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('js')
        <script>

            $(function () {
                $('#datetimepicker').datetimepicker();
                $('#datetimepicker2').datetimepicker();
            });
        </script>
    @endsection
@endsection
