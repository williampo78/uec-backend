@extends('backend.master')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-pencil"></i> 編輯分類</h1>
            </div>
            <!-- /.col-sm-12 -->
        </div>
        <!-- /.row -->
        <form method="POST" action="{{ route('category.update' , $data['id']) }}">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group" id="div_category">
                                        <label for="category">主分類</label>
                                        <select class="form-control js-select2" name="primary_category_id" id="category">
                                            @foreach($primary_category_list as $id => $v)
                                                <option value='{{ $id }}' {{$data['primary_category_id']==$id? 'selected' : ''}}>{{ $v['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_number">
                                                <label for="number">編號</label>
                                                <input class="form-control" name="number" id="number" value="{{$data['number']}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_name">
                                                <label for="name">名稱</label>
                                                <input class="form-control" name="name" id="name" value="{{$data['name']}}">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <button class="btn btn-success" type="submit"><i class="fa fa-check"></i> 完成</button>
                                        <a class="btn btn-danger" href="{{route('category')}}"><i class="fa fa-ban"></i> 取消</a>
                                    </div>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <div class="form-group">
                                        {{--                                        <a class="btn btn-danger" s"><i class="fa fa-trash"></i> 刪除</a>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
