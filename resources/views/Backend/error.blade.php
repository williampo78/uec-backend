@extends('Backend.master')

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
                                <div class="alert alert-danger"><i class="fa fa-ban"></i>
                                  @if(isset($message))
                                    {{$message}}
                                  @else
                                  @endif
                                </div>
                                <a class="btn btn-block btn-warning" href="{{route($route_name)}}"><i class="fa fa-fw fa-book"></i> 返回列表</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
