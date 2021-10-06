@extends('Backend.master')

@section('title', '功能名稱')

@section('content')
    <!--新增-->

    <div id="page-wrapper" style="min-height: 508px;">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-plus"></i>{{ isset($ShowData) ? '編輯' : '新增' }}供應商類別</h1>
            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        @if (isset($ShowData))
                            <form role="form" id="new-form" method="POST"
                                action="{{ route('supplier_type.update', $ShowData['id']) }}"
                                enctype="multipart/form-data" novalidate="novalidate">
                                {{ method_field('PUT') }}
                                {{ csrf_field() }}
                            @else
                                <form role="form" id="new-form" method="POST" action="{{ route('supplier_type') }}"
                                    enctype="multipart/form-data" novalidate="novalidate">
                        @endif
                        <input type="hidden" name="id" value="{{ isset($ShowData['id']) ? $ShowData['id'] : '' }}">
                        <div class="row">
                            @csrf
                            <!-- 欄位 -->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_supplier_type_code">
                                            <label for="supplier_type_code">編號</label>
                                            <input class="form-control" name="code"
                                                value="{{ isset($ShowData['code']) ? $ShowData['code'] : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_supplier_type_name">
                                            <label for="supplier_type_name">供應商類別名稱</label>
                                            <input class="form-control" name="name"
                                                value="{{ isset($ShowData['name']) ? $ShowData['name'] : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success" id="btn-save"><i class="fa fa-save"></i>
                                                儲存</button>
                                            <a href="{{route('supplier_type')}}" class="btn btn-danger" id="btn-cancel"><i class="fa fa-ban"></i>
                                                取消</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
