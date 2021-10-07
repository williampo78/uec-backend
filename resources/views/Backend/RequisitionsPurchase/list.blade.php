@extends('Backend.master')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-sign-in"></i> 請購單</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">

                        <form role="form" id="select-form" method="post" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2"><h5>日期：</h5></div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_start_date">
                                            <div class='input-group date' id='datetimepicker'>
                                                <input type='text' class="form-control" name="select_start_date" id="select_start_date" value="'$select_start_date'"/>
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
                                                <input type='text' class="form-control" name="select_end_date" id="select_end_date" value="'$select_end_date'"/>
                                                <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-2"><h5>請購部門：</h5></div>
                                    <div class="col-sm-10">
                                        <select class="form-control js-select2-department" name="department" id="department">
                                            <option value='1'>number-name</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2"><h5>類別：</h5></div>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="active" id="active">
                                            <option value = "%">所有類別</option>
                                            <option value = "1">正常</option>
                                            <option value = "0">作廢</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <div class="col-sm-12">
                                        <button class="btn btn-warning" id="btn-select"><i class="fa fa-search  "></i> 查詢</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <button class="btn btn-block btn-warning btn-sm" id="btn-new"><i class="fa fa-plus"></i> 新增</button>
                            </div>
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                            <tr>
                                <th>功能</th>
                                <th>日期</th>
                                <th>單號</th>
                                <th>請購部門</th>
                                <th>供應商</th>
                                <th>總金額</th>
                                <th>倉庫</th>
                                <th>狀態</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

//
//                                if($data['status'] == "1")
//                                    $ShowStatus = "<i class='btn btn-circle btn-success fa fa-check'></i>";
//                                else
//                                    $ShowStatus = "<i class='btn btn-circle btn-danger fa fa-close'></i>";
//
//                                $ShowActiveColor = "";
//                                if($data['is_transfer'] == "1")
//                                {
//                                    $ShowActive = "<i class='btn btn-circle btn-success fa fa-check' title='已完成'> </i>";
//                                }
//                                elseif($data['active'] == "1")
//                                {
//                                    $ShowActive = "<i class='btn btn-circle btn-info fa fa-hourglass-half' title='待處理'> </i>";
//                                }
//                                else
//                                {
//                                    $ShowActiveColor = " class='tr-deactive'";
//                                    $ShowActive = "<i class='btn btn-circle btn-danger fa fa-close' title='作廢'></i>";
//                                }
//
//                                echo '<tr '.$ShowActiveColor.'>
?>
                            <td>
                              <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#row_detail" data-id="'.$data['id'].'" onclick="row_detail('.$data['id'].');"><i class="fa fa-search"></i></button>
                            </td>
                            <td>data['trade_date']</td>
                            <td>'.$data['number'].'</td>
                            <td>'.$data['department_name'].'</td>
                            <td>'.$data['supplier_name'].'</td>
                            <td>'.$data['total_price'].'</td>
                            <td>'.$data['warehouse_name'].'</td>
                            <td>'.$ShowActive.'</td>
                          </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
