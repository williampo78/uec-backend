@extends('Backend.master')

@section('title', '功能名稱')

@section('content')
    <!--新增-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-cubes"></i>供應商類別處理</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">

                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="{{ route('supplier_type') }}/create" class="btn btn-block btn-warning btn-sm"
                                    id="btn-new"><i class="fa fa-plus"></i>
                                    新增</a>
                            </div>
                        </div>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div id="table_list_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <div class="dt-buttons btn-group"> <button
                                    class="btn btn-default buttons-collection buttons-page-length" tabindex="0"
                                    aria-controls="table_list" type="button" aria-haspopup="true">
                                    <span>顯示筆數</span>
                                </button>
                                <button class="btn btn-default buttons-excel buttons-html5" tabindex="0"
                                    aria-controls="table_list" type="button"><span><i class="fa fa-fw fa-file-excel-o"></i>
                                        匯出Excel</span></button> <button class="btn btn-default buttons-print" tabindex="0"
                                    aria-controls="table_list" type="button"><span><i class="fa fa-fw fa-print"></i>
                                        列印</span></button>
                            </div>
                            <div id="table_list_filter" class="dataTables_filter"><label>搜尋:<input type="search"
                                        class="form-control input-sm" placeholder="" aria-controls="table_list"></label>
                            </div>
                            <table class="table table-striped table-bordered table-hover dataTable no-footer"
                                style="width: 100%;" id="table_list" role="grid" aria-describedby="table_list_info">
                                <thead>
                                    <tr role="row">
                                        <th class="col-sm-1 sorting" tabindex="0" aria-controls="table_list" rowspan="1"
                                            colspan="1" aria-label="功能: 由小至大排序" style="width: 110px;">功能</th>
                                        <th class="col-sm-1 sorting" tabindex="0" aria-controls="table_list" rowspan="1"
                                            colspan="1" aria-label="編號: 由小至大排序" style="width: 153px;">編號</th>
                                        <th class="col-sm-10 sorting" tabindex="0" aria-controls="table_list" rowspan="1"
                                            colspan="1" aria-label="名稱: 由小至大排序" style="width: 99px;">名稱</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($SupplierTypeService as $val)
                                        <tr role="row">

                                            <td>
                                                <a href="{{ route('supplier_type') }}/{{ $val['id'] }}/edit"
                                                    class="btn btn-block btn-info btn-sm"><i class="fa fa-pencil"></i>
                                                    編輯</a>
                                            </td>
                                            <td>{{ $val['code'] }}</td>
                                            <td>{{ $val['name'] }}</td>
                                        </tr>

                                    @endforeach

                                </tbody>
                            </table>
                            <div class="dataTables_info" id="table_list_info" role="status" aria-live="polite">顯示 1 筆紀錄中 1 至
                                1 紀錄</div>
                            <div class="dataTables_paginate paging_simple_numbers" id="table_list_paginate">
                                <ul class="pagination">
                                    <li class="paginate_button previous disabled" id="table_list_previous"><a href="#"
                                            aria-controls="table_list" data-dt-idx="0" tabindex="0">前一頁</a></li>
                                    <li class="paginate_button active"><a href="#" aria-controls="table_list"
                                            data-dt-idx="1" tabindex="0">1</a></li>
                                    <li class="paginate_button next disabled" id="table_list_next"><a href="#"
                                            aria-controls="table_list" data-dt-idx="2" tabindex="0">下一頁</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
