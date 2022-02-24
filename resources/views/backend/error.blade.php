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
                                <div class="alert alert-danger"><i class="fa-solid fa-ban"></i>
                                  @if(isset($message))
                                    {{$message}}
                                  @endif
                                  @if(isset($error_code))
                                  <br>
                                  {{$error_code}}
                                  @endif
                                </div>
                                <a class="btn btn-block btn-warning" href="{{isset($route_name) ? route($route_name) : '/'}}"><i class="fa-solid fa-book"></i> {{isset($route_name) ? '返回列表' : '返回首頁'}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
