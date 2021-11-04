@extends('Backend.master')
@section('title', '分類階層管理')
@section('content')
    <style>
        h4{
            font-weight:bold;
        }
        h4 span{
            color: blue  ;
        }
    </style>
    <!--列表-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-bank"></i>分類階層管理</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body row">
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 style="font-weight:bold;">大分類</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <a class="btn btn-block btn-warning btn-sm">新增大類</a>
                                </div>
                                <div class="col-sm-4">
                                    <a class="btn btn-block btn-success btn-sm">儲存</a>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-sm-4">名稱</th>
                                        <th class="col-sm-8">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="vertical-align:middle">
                                            <i class="fa fa-list"></i>
                                            大分類A
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <button type="button" class="btn btn-primary">展中類</button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-warning">編輯</button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-danger">刪除</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4> 【<span>大分類A</span>】的中分類</h4>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <a class="btn btn-block btn-warning btn-sm">新增中類</a>
                                </div>
                                <div class="col-sm-4">
                                    <a class="btn btn-block btn-success btn-sm">儲存</a>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-sm-4">名稱</th>
                                        <th class="col-sm-8">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="vertical-align:middle">
                                            <i class="fa fa-list"></i>
                                            中分類A
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <button type="button" class="btn btn-primary">展中類</button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-warning">編輯</button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-danger">刪除</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4> 【<span>居家生活 > 衛生紙衛生棉</span>】的小分類</h4>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <a class="btn btn-block btn-warning btn-sm">新增小類</a>
                                </div>
                                <div class="col-sm-4">
                                    <a class="btn btn-block btn-success btn-sm">儲存</a>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-sm-4">名稱</th>
                                        <th class="col-sm-8">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="vertical-align:middle">
                                            <i class="fa fa-list"></i>
                                            小分類A
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <button type="button" class="btn btn-primary">展中類</button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-warning">編輯</button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-danger">刪除</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
