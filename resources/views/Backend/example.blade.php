@extends('Backend.master')

@section('title', '功能名稱')

@section('content')
       <!--新增-->
       <div id="page-wrapper">
    
        <!-- 表頭名稱 -->
        <div class="row">
          <div class="col-sm-12">
            <h1 class="page-header"><i class="fa fa-plus"></i> 我是首頁</h1>
          </div>
        </div>
      </div>
      <script>
        $('#btn-cancel').click(function () { window.location.href='index.php?func=warehouse'; return false; });
        $('#btn-save').click(function () { $('#new-form').submit(); });
      </script>
@endsection