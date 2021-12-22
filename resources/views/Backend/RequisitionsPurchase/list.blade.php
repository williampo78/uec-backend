@extends('Backend.master')
@section('title', '請購單')
@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-sign-in"></i> 請購單</h1>
            </div>
        </div>
        <div class="row" id="requisitions_vue_app">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">

                        <form role="form" id="select-form" method="GET" action="{{ route('requisitions_purchase') }}"
                            enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>供應商 </h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="supplier_id" id="supplier_id">
                                            @foreach ($supplier as $obj)
                                                <option value='{{ $obj->id }}'
                                                    {{ request()->input('supplier_id') == $obj->id ? 'selected="selected"' : '' }}>
                                                    {{ $obj->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <h5>供應商統編</h5>
                                        </div>
                                        <div class="col-sm-7">
                                            <input class="form-control" name="company_number" id="company_number"
                                                value="{{ request()->input('company_number') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <h5>狀態</h5>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="status" id="status">
                                                <option value="">無</option>
                                                <option value='drafted'
                                                    {{ request()->input('status') == 'drafted' ? 'selected="selected"' : '' }}>
                                                    草稿</option>
                                                <option value='reviewing'
                                                    {{ request()->input('status') == 'reviewing' ? 'selected="selected"' : '' }}>
                                                    簽核</option>
                                                <option value='approved'
                                                    {{ request()->input('status') == 'approved' ? 'selected="selected"' : '' }}>
                                                    已核准</option>
                                                <option value='rejected'
                                                    {{ request()->input('status') == 'rejected' ? 'selected="selected"' : '' }}>
                                                    已駁回</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">

                                    <div class="col-sm-2">
                                        <h5>日期：</h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_start_date">
                                            <div class='input-group date' id='datetimepicker'>
                                                <input type='text' class="form-control" name="select_start_date"
                                                    id="select_start_date"
                                                    value="{{ request()->input('select_start_date') }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <h5>～</h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_end_date">
                                            <div class='input-group date' id='datetimepicker2'>
                                                <input type='text' class="form-control" name="select_end_date"
                                                    id="select_end_date"
                                                    value="{{ request()->input('select_end_date') }} == '' ??  date('Y-m-d')" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <h5>請購單號</h5>
                                        </div>
                                        <div class="col-sm-7">
                                            <input class="form-control" name="doc_number" id="doc_number"
                                                value="{{ request()->input('doc_number') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3 text-right">
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
                        <div class="row">
                            <div class="col-sm-2">
                                @if ($share_role_auth['auth_create'])
                                    <a class="btn btn-block btn-warning btn-sm"
                                        href="{{ route('requisitions_purchase.create') }}"><i class="fa fa-plus"></i>
                                        新增</a>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>請購日期</th>
                                    <th>請購單號</th>
                                    <th>供應商</th>
                                    <th>狀態</th>
                                    <th>送審時間</th>
                                    <th>結案時間</th>
                                </tr>
                            </thead>
                            <tbody>
                                <button style="display:none" class="btn btn-info btn-sm toggle-show-model"
                                    data-toggle="modal" data-target="#row_detail">SHOW
                                </button>
                                @if (isset($requisitionsPurchase))
                                    @foreach ($requisitionsPurchase as $obj)
                                        <tr>
                                            <td>
                                                {{-- @if ($share_role_auth['auth_query']) --}}
                                                <button class="btn btn-info btn-sm"
                                                    @click="showBtn({{ $obj->id }})"><i
                                                        class="fa fa-search"></i></button>
                                                {{-- @endif --}}
                                                {{-- @if ($share_role_auth['auth_update'] && $v['status_code'] == 'DRAFTED' && $v['created_by'] == $data['user_id']) --}}

                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('requisitions_purchase') }}/{{ $obj->id }}/edit/">修改</a>
                                                {{-- @endif --}}
                                                {{-- @if ($share_role_auth['auth_delete'] && $v['status_code'] == 'DRAFTED' && $v['created_by'] == $data['user_id']) --}}

                                                <button class="btn btn-danger btn-sm"
                                                    @click="delBtn({{ $obj->id }})">刪除</button>
                                                {{-- @endif --}}
                                            </td>
                                            </td>
                                            <td>{{ $obj->trade_date }} </td>
                                            <td>{{ $obj->number }}</td>
                                            <td>{{ $obj->supplier_name }}</td>
                                            <td>
                                                @switch($obj->status)
                                                    @case('DRAFTED')
                                                        草稿
                                                    @break
                                                    @case('REVIEWING')
                                                        簽核中
                                                    @break
                                                    @case('APPROVED')
                                                        已核准
                                                    @break
                                                    @case('REJECTED')
                                                        已駁回
                                                    @break
                                                    @default
                                                @endswitch
                                            </td>
                                            <td>{{ $obj->created_at }}</td>
                                            <td>尚未有該欄位</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @include('Backend.RequisitionsPurchase.detail')
        </div>
    </div>
    @section('js')
        <script>
            var showRequisitions = Vue.extend({
                data: function() {
                    return {
                        requisitionsPurchase: {},
                        requisitionsPurchaseDetail: {},
                        getRequisitionPurchaseReviewLog: {},
                    }
                },
                methods: {
                    showBtn(id) {
                        var req = this;
                        axios.get('/backend/requisitions_purchase/' + id)
                            .then(function(response) {
                                req.requisitionsPurchase = JSON.parse(response.data.requisitionsPurchase);
                                req.requisitionsPurchaseDetail = JSON.parse(response.data
                                    .requisitionsPurchaseDetail);
                                req.getRequisitionPurchaseReviewLog = JSON.parse(response.data
                                    .getRequisitionPurchaseReviewLog);
                                $('.toggle-show-model').click();
                                return req;
                            })
                            .catch(function(error) {
                                console.log('ERROR');
                            })
                    },
                    delBtn(id) {
                        var checkDel = confirm('你確定要刪除嗎？');
                        if (checkDel) {
                            axios({
                                    method: 'delete',
                                    url: '/backend/requisitions_purchase/' + id
                                }).then(function(response) {
                                    if (response.data.status) {
                                        alert('刪除成功');
                                        history.go(0);
                                    }
                                })
                                .catch(function(error) {
                                    console.log('ERROR');
                                })
                            console.log('刪除');
                        } else {
                            console.log('不刪除');
                        }

                        console.log(id);
                    }

                },


            })

            new showRequisitions().$mount('#requisitions_vue_app');

            $(function() {
                $('#datetimepicker').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#datetimepicker2').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $("#supplier_id").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇"
                });
                $("#status").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇"
                });

            });

            function del(id, doc_number) {
                if (confirm("確定要刪除請購單" + doc_number + "?")) {
                    document.getElementById('del-' + id).submit();
                }
                return false;
            };
        </script>
    @endsection
@endsection
