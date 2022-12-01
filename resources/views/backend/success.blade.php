@extends('backend.layouts.master')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header">系統訊息</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-success"><i class="fa-solid fa-check"></i>
                                    @switch($act)
                                        @case('add')
                                            已經成功新增資料！
                                            @break

                                        @case('upd')
                                            已經成功儲存修改！
                                            @break

                                        @case('del')
                                            資料已刪除！
                                            @break

                                        @case('DRAFTED')
                                            儲存草稿成功！
                                            @break

                                        @case('REVIEWING')
                                            儲存成功，報價單送審中！
                                            @break

                                        @case('review')
                                            儲存成功！
                                            @break

                                        @case('product_reviewing')
                                            儲存成功，商品送審中！
                                            @break

                                        @case('review_success')
                                            審核成功！
                                            @break
                                        @case('upload_success')
                                            已成功上傳檔案！
                                        @break
                                        @case('batch_upload_success')
                                        檔案已上傳主機，處理時間約需10~20分鐘(視資料量多寡而定)，執行結果可於此功能的「匯入記錄」頁籤查看、避免重複上傳檔案，謝謝！
                                        @break
                                        @default
                                            @isset($message)
                                                {{ $message }}
                                            @endisset
                                    @endswitch
                                </div>
                                <a class="btn btn-block btn-success" href="{{ route($route_name) }}">
                                    <i class="fa-solid fa-book"></i> 返回列表
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @isset($alertMessage)
    <script>
        let alertMessage = @json($alertMessage) ;
        alertMessage.forEach(function(item){
            alert(item);
        });
    </script>
    @endisset
@endsection
