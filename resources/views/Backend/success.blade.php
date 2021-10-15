@extends('backend.master')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header">系統訊息</h1>
            </div>
            <!-- /.col-sm-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-success"><i class="fa fa-check"></i>
                                    @if($act==='add')
                                        已經成功新增資料！
                                    @elseif($act==='upd')
                                        已經成功儲存修改！
                                    @elseif($act==='del')
                                        資料已刪除！
                                    @elseif($act==='DRAFTED')
                                        儲存草稿成功！
                                    @elseif($act==='REVIEWING')
                                        儲存成功，報價單送審中！
                                    @elseif($act==='review')
                                        儲存成功！
                                    @endif
                                </div>
                                <a class="btn btn-block btn-success" href="{{route($route_name)}}"><i class="fa fa-fw fa-book"></i> 返回列表</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
