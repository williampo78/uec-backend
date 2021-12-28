@extends('Backend.master')
@section('title', '進貨單')
@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-sign-in"></i> 進貨單</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->

                    <div class="panel-heading">
                        <form role="form" class="form-horizontal" id="select-form" method="GET" action=""
                            enctype="multipart/form-data">
                            <br>
                            <div class="row">
                                {{-- row 1 start --}}
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"><label class="control-label"> 供應商</label></div>
                                        <div class="col-sm-9">
                                            <div class='input-group' id='supplier_deliver_date_dp'>
                                                <select class="form-control js-select2-department" name="supplier"
                                                    id="supplier">
                                                    @foreach ($supplier as $v)
                                                        <option value='{{ $v['id'] }}'
                                                            {{ isset($data['getData']['supplier']) && $v['id'] == $data['getData']['supplier'] ? 'selected' : '' }}>
                                                            {{ $v['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4"><label class="control-label">供應商統編</label></div>
                                        <div class="col-sm-8">
                                            <div class='input-group'>
                                                <input class="form-control" name="company_number" id="company_number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4"><label class="control-label">採購單號</label></div>
                                        <div class="col-sm-8">
                                            <div class='input-group'>
                                                <input class="form-control" name="company_number" id="company_number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{-- row 1 end --}}
                            {{-- row 2 start --}}
                            <div class="row">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"><label class="control-label">採購單號</label></div>
                                        <div class="col-sm-4">
                                            <div class="input-group date" id="datetimepicker">
                                                <input type="text" class="form-control" name="select_start_date"
                                                    id="select_start_date" value="">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <h5>～</h5>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group date" id="datetimepicker2">
                                                <input type="text" class="form-control" name="select_end_date"
                                                    id="select_end_date" value="">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4"><label class="control-label">進貨單號</label></div>
                                        <div class="col-sm-8">
                                            <div class='input-group'>
                                                <input class="form-control" name="company_number" id="company_number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <div class="col-sm-12">
                                        <button class="btn btn-warning"><i class="fa fa-search  "></i> 查詢</button>
                                    </div>
                                </div>
                                {{-- row 2 end --}}
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
                            </tr>
                        </thead>
                        <tbody>
                            <td></td>
                            <td>1</td>
                            <td>2</td>
                            <td>3</td>
                            <td>4</td>
                            <td>5</td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

@section('js')
    <script>
        $(document).ready(function() {

            $('#supplier').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $('#datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $('#datetimepicker2').datetimepicker({
                format: 'YYYY-MM-DD',
            });
        });
    </script>
@endsection
@endsection
