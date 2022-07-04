@extends('backend.layouts.master')

@section('title', '常見問題Q&A')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-users"></i> 常見問題Q&A</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">

                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form id="search-form" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-1 text-right">
                                    <h5>類別</h5>
                                </div>
                                <div class="col-sm-3">
                                    <select name="code" id="code" class="js-select2">
                                        <option value="">請選擇</option>
                                        @foreach ($data['category'] as $cate)
                                            <option value="{{ $cate['code'] }}"
                                                {{ $cate['code'] == request()->input('code') ? 'selected' : '' }}>
                                                {{ $cate['description'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-1 text-right">
                                    <h5>問題描述</h5>
                                </div>
                                <div class="col-sm-3"><input class="form-control" name="content_name"
                                        id="content_name" placeholder="模糊查詢"
                                        value="{{ request()->input('content_name') }}"></div>
                                <div class="col-sm-1 text-right">
                                    <h5>狀態</h5>
                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control js-select2-active" name="active" id="active">
                                        <option value=''></option>
                                        <option value="1" {{ '1' == request()->input('active') ? 'selected' : '' }}>
                                            啟用
                                        </option>
                                        <option value="0" {{ '0' == request()->input('active') ? 'selected' : '' }}>
                                            關閉
                                        </option>
                                    </select>
                                </div>
                                @if ($share_role_auth['auth_query'])
                                    <div class="col-sm-1 text-right">
                                        <button class="btn btn-warning"><i class="fa-solid fa-magnifying-glass"></i> 查詢</button>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            @if ($share_role_auth['auth_create'])
                                <div class="col-sm-2">
                                    <a class="btn btn-block btn-warning btn-sm" id="btn-new"
                                        href="{{ route('qa.create') }}"><i class="fa-solid fa-plus"></i> 新增</a>
                                </div>
                            @endif
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">功能</th>
                                    <th class="text-nowrap">類別</th>
                                    <th class="text-nowrap">排序</th>
                                    <th class="text-nowrap">問題描述</th>
                                    <th class="text-nowrap">狀態</th>
                                    <th class="text-nowrap">最後異動時間</th>
                                    <th class="text-nowrap">最後異動者</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['footer'] as $item)
                                    <tr>
                                        <td>
                                            @if ($share_role_auth['auth_query'])
                                                <button type="button" class="btn btn-info btn-sm qa_detail"
                                                    data-web-content-id="{{ $item->id }}" title="檢視">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            @endif

                                            @if ($share_role_auth['auth_update'])
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('qa.edit', $item->id) }}">編輯</a>
                                            @endif
                                        </td>
                                        <td>{{ $data['code'][$item->parent_code] }}</td>
                                        <td>{{ $item->sort }}</td>
                                        <td>{{ $item->content_name }}</td>
                                        <td>{{ $item->active == 1 ? '啟用' : '關閉' }}</td>
                                        <td>{{ $item->updated_at }}</td>
                                        <td>{{ $item->updated_by > 0 ? $data['user'][$item->updated_by]['user_name'] : $data['user'][$item->created_by]['user_name'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.qa.detail')
@endsection

@section('js')
    <script>
        $('.js-select2').select2();
        $('.js-select2-active').select2();

        $(document).on('click', '.qa_detail', function() {
            let web_content_id = $(this).attr("data-web-content-id");

            axios.get(`/backend/qa/${web_content_id}`)
                .then(function(response) {
                    let web_content = response.data;

                    $('#modal-description').empty().append(`${web_content.description}`);
                    $('#modal-sort').empty().append(`${web_content.sort}`);
                    $('#modal-active').empty().append(`${web_content.active}`);
                    $('#modal-content-name').empty().append(`${web_content.content_name}`);
                    $('#modal-content-text').empty().append(`${web_content.content_text}`);

                    $('#qa_detail').modal('show');
                })
                .catch(function(error) {
                    console.log(error);
                });
        });
    </script>
@endsection
