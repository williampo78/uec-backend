@extends('backend.layouts.master')

@section('title', '商品新增 - 批次上傳')

@section('content')

    <!--列表-->
    <div id="page-wrapper">
        <div id="import-app">
            <!-- 表頭名稱 -->
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-header"><i class="fa-solid fa-cube"></i>商品新增 - 批次上傳</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link " id="import-tab" data-toggle="tab" href="#import" role="tab"
                                aria-controls="import" aria-selected="true">檔案匯入</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="import-log-tab" data-toggle="tab" href="#import-log" role="tab"
                                aria-controls="import-log" aria-selected="false">匯入紀錄</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        {{-- tab show 1 --}}
                        <div class="tab-pane fade" id="import" role="tabpanel" aria-labelledby="import-tab">
                            <div>
                                <form id="form_import" enctype="multipart/form-data" method="POST"
                                    action="/backend/product-batch-upload">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-2">
                                                    <label class="control-label">商品Excel<span
                                                            class="text-red">*</span></label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="file" id="excel" name="excel"
                                                        accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                                        data-msg-accept="只支援Excel檔案格式">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-2">
                                                    <label class="control-label">圖片ZIP<span
                                                            class="text-red">*</span></label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="file" id="image_zip" name="image_zip"
                                                        accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed"
                                                        data-msg-accept="只支援.zip檔案格式">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-12 text-right">
                                                    <span>
                                                        <button type="button" class="btn btn-warning" @click="importbtn()">
                                                            <i class="fa-solid fa-upload"></i> 匯入
                                                        </button>

                                                        <button type="button" class="btn btn-danger" @click="clearFile()">
                                                            <i class="fa-solid fa-eraser"></i> 清除
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding: 15px; background: aliceblue; margin-top: 15px;">
                                        <h5 class="fw-bold" style="font-size: 15px;">注意事項</h5>
                                        <div class="d-md-flex gap-4">
                                            <ol class="ps-4" style="line-height: 1.5;">
                                                <li>商品Excel (<a href="/backend/download-sample/product-batch-upload-sample.xlsx?rename=商品主檔批次上傳-範例&type=xlsx">下載範例檔</a>)
                                                    <ul class="ps-5">
                                                        <li>包含「items」、「photos」2個頁籤
                                                            <ul class="ps-5">
                                                                <li>第1列為欄位名稱、第2列為欄位說明，須保留</li>
                                                                <li>第3列開始才是真正的資料內容</li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>圖片ZIP
                                                    <ul class="ps-5">
                                                        <li>附檔名須為zip</li>
                                                        <li>各商品請獨立一個資料夾存放圖片</li>
                                                        <li>單張圖片的寬與高的比例須為1:1、至少須為480*480</li>
                                                    </ul>
                                                </li>
                                                <li>上傳檔案「商品Excel」、「圖片ZIP」加總大小上限為400MB</li>
                                                <li>執行匯入後，約須10~20分鐘的等待時間，執行結果可於此功能的「匯入記錄」頁籤查看。</li>
                                            </ol>
                                            <picture style="max-width: 431px;">
                                                <img src="{{ asset('asset/img/req-batch-image-sample.jpg') }}" alt="圖片ZIP示意圖" width="431" height="220" class="w-100 h-auto">
                                            </picture>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                        {{-- tab show 2 --}}
                        <div class="tab-pane fade" id="import-log" role="tabpanel" aria-labelledby="import-log-tab">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr>
                                        <th style="white-space: nowrap">匯入時間</th>
                                        <th style="white-space: nowrap">匯入檔名</th>
                                        <th style="white-space: nowrap">執行結果</th>
                                        <th style="white-space: nowrap">執行完畢時間</th>
                                        <th style="min-width: 150px;">執行結果說明</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in logList">
                                        <td>@{{ item.created_at }}</td>
                                        <td>@{{ item.excel_name }}</td>
                                        <td>@{{ item.status_name }}</td>
                                        <td>@{{ item.job_completed_at }}</td>
                                        <td>@{{ item.job_completed_log }}
                                            <a v-show="item.job_log_file !== null" v-show
                                                :href="'/backend/product-batch-upload/download/' + item.id">下載失敗原因說明</a>
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

@section('js')
    <script>
        var importApp = Vue.extend({
            data: function() {
                return {
                    logList: @json($log_list),
                }
            },
            methods: {
                importbtn() {
                    $("#edit-form").validate({
                        // debug: true,
                        submitHandler: function(form) {
                            return true;
                        },
                        errorClass: "help-block",
                        errorElement: "span",
                        errorPlacement: function(error, element) {
                            if (element.closest(".input-group").length) {
                                element.closest(".input-group").parent().append(error);
                                return;
                            }

                            if (element.closest(".radio-inline").length) {
                                element.closest(".radio-inline").parent().append(error);
                                return;
                            }

                            if (element.is('select')) {
                                element.parent().append(error);
                                return;
                            }

                            error.insertAfter(element);
                        },
                        highlight: function(element, errorClass, validClass) {
                            if ($(element).closest('.input-group').length) {
                                $(element).closest(".input-group").parent().addClass("has-error");
                                return;
                            }

                            $(element).closest(".form-group").addClass("has-error");
                        },
                        success: function(label, element) {
                            if ($(element).closest('.input-group').length) {
                                $(element).closest(".input-group").parent().removeClass("has-error");
                                return;
                            }

                            $(element).closest(".form-group").removeClass("has-error");
                        },
                    });
                    $("#form_import").submit();

                    return true;
                },
                clearFile() {
                    document.getElementById('excel').value = null;
                    document.getElementById('image_zip').value = null;
                }
            },
            mounted() {
                // 觸發預設顯示active
                var importTab = document.getElementById("import-tab");
                importTab.click();
                // select 2 套件宣告
                $("#supplier_id").select2();
                // jquery 驗證
                $("#form_import").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        var yes = confirm('確定要匯入資料？');
                        if (yes) {
                            return true;
                        } else{
                            return false;
                        }
                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    rules: {
                        supplier_id: {
                            required: true,
                        },
                        excel: {
                            required: true,
                            // extension: "xlsx|xls|xlsm|csv"
                        },
                        image_zip: {
                            required: true,
                            // extension:"zip"
                        }
                    },
                    errorPlacement: function(error, element) {
                        if (element.closest(".input-group").length) {
                            element.closest(".input-group").parent().append(error);
                            return;
                        }

                        if (element.closest(".radio-inline").length) {
                            element.closest(".radio-inline").parent().append(error);
                            return;
                        }

                        if (element.is('select')) {
                            element.parent().append(error);
                            return;
                        }

                        error.insertAfter(element);
                    },
                    highlight: function(element, errorClass, validClass) {
                        if ($(element).closest('.input-group').length) {
                            $(element).closest(".input-group").parent().addClass("has-error");
                            return;
                        }

                        $(element).closest(".form-group").addClass("has-error");
                    },
                    success: function(label, element) {
                        if ($(element).closest('.input-group').length) {
                            $(element).closest(".input-group").parent().removeClass("has-error");
                            return;
                        }

                        $(element).closest(".form-group").removeClass("has-error");
                    },
                });
            },
            created() {
                console.log('Vue created')
            },

            computed: {},
            watch: {},
        })

        new importApp().$mount('#import-app');
    </script>
@endsection
