@extends('backend.layouts.master')

@section('title', '信用卡分期設定')

@section('content')
    <div id="app" v-cloak>
        <div id="page-wrapper">
            <!-- 表頭名稱 -->
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-list"></i> 信用卡分期設定</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <!-- 功能按鈕 -->
                        <div class="panel-heading">
                            <form id="search-form" class="form-horizontal" method="GET">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">發卡銀行</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select2 class="form-control" :options="options.banks"
                                                         v-model="searchParameters.bank_no" name="bank_no">
                                                    <option disabled value=""></option>
                                                </select2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">期數</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select2 class="form-control" :options="options.numberOfInstallments"
                                                         v-model="searchParameters.numberOfInstallments"
                                                         name="number_of_installments">
                                                    <option disabled value=""></option>
                                                </select2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">上下架狀態</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select2 class="form-control" :options="options.statuses"
                                                         v-model="searchParameters.status" name="status">
                                                    <option disabled value=""></option>
                                                </select2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <br/>
                                <div class="row">
                                    <div class="col-sm-4">
                                    </div>
                                    <div class="col-sm-4">
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-9 text-right">
                                                <div v-if="auth.auth_query">
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                                    </button>
                                                    <button type="button" class="btn btn-danger" @click="resetForm">
                                                        <i class="fa-solid fa-eraser"></i> 清除
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Table list -->
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-2" v-if="auth.auth_query">
                                    <button class="btn btn-block btn-warning btn-sm" @click="onCreate">
                                        <i class="fa-solid fa-plus"></i> 新增
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <div class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
                                <table class="table table-striped table-bordered table-hover" style="width:100%"
                                       id="table_list">
                                    <thead>
                                    <tr>
                                        <th class="text-nowrap">功能</th>
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">發卡銀行</th>
                                        <th class="text-nowrap">適用期間</th>
                                        <th class="text-nowrap">期數</th>
                                        <th class="text-nowrap">利率(%)</th>
                                        <th class="text-nowrap">消費門檻</th>
                                        <th class="text-nowrap">上下架狀態</th>
                                        <th class="text-nowrap">說明</th>
                                        <th class="text-nowrap">最後異動時間</th>
                                        <th class="text-nowrap">最後異動者</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(installmentInterestRate, index) in installmentInterestRates"
                                        :key="installmentInterestRate.id">
                                        @verbatim
                                            <td>
                                                <span v-if="auth.auth_update">
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                            @click="onEdit(installmentInterestRate.id)">
                                                        編輯
                                                    </button>
                                                </span>
                                            </td>
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ installmentInterestRate.bank_code_and_name }}</td>
                                            <td>{{ installmentInterestRate.period }}</td>
                                            <td>{{ installmentInterestRate.number_of_installments }}</td>
                                            <td>{{ installmentInterestRate.interest_rate }}</td>
                                            <td>{{ installmentInterestRate.min_consumption }}</td>
                                            <td>{{ installmentInterestRate.active_chinese }}</td>
                                            <td>{{ installmentInterestRate.remark }}</td>
                                            <td>{{ installmentInterestRate.updated_at }}</td>
                                            <td>{{ installmentInterestRate.updated_by }}</td>
                                        @endverbatim
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="form-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-primary panel-primary">
                    <div class="modal-header panel-heading">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">
                            <i class="fa-solid fa-gear"></i> @{{ modal.title }}
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form id="submit-date-form" class="form-horizontal" method="get">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label">發卡銀行</label>
                                        </div>
                                        <div class="col-sm-10">
                                            <select2 class="form-control" :options="options.banks"
                                                     v-model="modal.bankNo" name="bank_no"
                                                     :disabled="modal.bankNoDisabled">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label">期數 <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-sm-10">
                                            <select2 class="form-control" :options="options.numberOfInstallments"
                                                     v-model="modal.numberOfInstallments" name="number_of_installments"
                                                     :disabled="modal.numberOfInstallmentsDisabled">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label">適用期間 <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <vue-flat-pickr name="started_at"
                                                                    :value.sync="modal.startedAt.date"
                                                                    :config="modal.startedAt.configs"
                                                                    @on-change="onStartedAtChange">
                                                    </vue-flat-pickr>
                                                </div>
                                                <div class="col-sm-2 text-center">
                                                    <label class="control-label">～</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <vue-flat-pickr name="ended_at"
                                                                    :value.sync="modal.endedAt.date"
                                                                    :config="modal.endedAt.configs"
                                                                    @on-change="onEndedAtChange">
                                                    </vue-flat-pickr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label">利率(%) <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="interest_rate"
                                                   v-model="modal.interestRate" minlength="1" maxlength="5"
                                                   :disabled="modal.interestRateDisabled">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label">消費門檻 <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="min_consumption"
                                                   v-model="modal.minConsumption"
                                                   :disabled="modal.minConsumptionDisabled">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label">狀態 <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-sm-10">
                                            <label class="radio-inline">
                                                <input type="radio" name="active" value="1" v-model="modal.active">啟用
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="active" value="0" v-model="modal.active">關閉
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label">說明</label>
                                        </div>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" rows="3" name="remark"
                                                      placeholder="最多輸入50個字" v-model="modal.remark">
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" @click="submitForm">
                            儲存
                        </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            取消
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        new Vue({
            el: '#app',
            data: {
                id: null,
                action: null,
                method: null,
                today: '{{ \Carbon\Carbon::today()->toDateString() }}',
                baseUrl: '{{ route('installment_interest_rates.store') }}',
                targetUrl: null,
                checkExistedUrl: '{{ route('installment_interest_rates.check_existed') }}',
                //下拉選單
                options: {
                    banks: @json($banks),
                    numberOfInstallments: @json($numberOfInstallments),
                    statuses: @json($statuses)
                },
                searchParameters: {
                    bank_no: '{{ request('bank_no') }}',
                    numberOfInstallments: '{{ request('number_of_installments') }}',
                    status: '{{ request('status') }}',
                },
                installmentInterestRates:@json($installmentInterestRates),
                auth: @json($share_role_auth),
                modal: {
                    title: null,
                    bankNo: null,
                    bankNoDisabled: null,
                    numberOfInstallments: null,
                    numberOfInstallmentsDisabled: null,
                    startedAt: {
                        name: 'started_at',
                        date: '{{ request('started_at') }}',
                        configs: {
                            minDate: null,
                            maxDate: null,
                        },
                    },
                    endedAt: {
                        name: 'ended_at',
                        date: '{{ request('ended_at') }}',
                        configs: {
                            minDate: null,
                            maxDate: null,
                        },
                    },
                    interestRate: null,
                    interestRateDisabled: null,
                    minConsumption: null,
                    minConsumptionDisabled: null,
                    active: null,
                    remark: null,
                },
                Validator: null
            },
            methods: {
                setValidator(rules) {
                    // 驗證表單
                    this.Validator = $("#submit-date-form").validate({
                        submitHandler: function (form) {
                            self.updateExpectedDate();
                        },
                        rules: rules,
                        messages: {
                            interest_rate: {
                                min: '僅可輸入0或小於100的數字，最多輸入二位小數。',
                                max: '僅可輸入0或小於100的數字，最多輸入二位小數。',
                                step: '僅可輸入0或小於100的數字，最多輸入二位小數。',
                            },
                            started_at: {
                                greaterSameThan: '不可超過{0}'
                            },
                            ended_at: {
                                greaterSameThan: '不可超過{0}'
                            }
                        },
                        errorClass: "help-block",
                        errorElement: "span",
                        errorPlacement: function (error, element) {
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
                        highlight: function (element, errorClass, validClass) {
                            $(element).closest(".form-group").addClass("has-error");
                        },
                        success: function (label, element) {
                            $(element).closest(".form-group").removeClass("has-error");
                        },
                    });
                },
                resetForm() {
                    let self = this;
                    Object.keys(self.searchParameters).forEach(function (key) {
                        self.searchParameters[key] = null;
                    });
                },
                submitForm() {
                    //驗證資料
                    if ($('#submit-date-form').valid() == false) {
                        return false;
                    }

                    if (this.action == 'create') {
                        //狀態為啟用，需先確認資料是否已存在
                        if (this.modal.active == 1) {
                            this.checkExisted();
                            return false;
                        }

                        this.$swal({
                            title: '確定要新增?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '確定',
                            cancelButtonText: '取消'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.save();
                            }
                        });
                        return false;
                    }

                    this.save();
                },
                save() {

                    axios({
                        method: this.method,
                        url: this.targetUrl,
                        data: {
                            id: this.modal.id,
                            issuing_bank_no: this.modal.bankNo,
                            number_of_installments: this.modal.numberOfInstallments,
                            started_at: this.modal.startedAt.date,
                            ended_at: this.modal.endedAt.date,
                            interest_rate: this.modal.interestRate,
                            min_consumption: this.modal.minConsumption,
                            active: this.modal.active,
                            remark: this.modal.remark,
                        },
                    }).then((response) => {

                        //失敗
                        if (response.data.status == false) {
                            this.$swal({
                                icon: 'error',
                                title: response.data.message,
                            });
                            return false;
                        }

                        //成功
                        $('#form-modal').modal('hide');

                        if (this.action == 'edit') {
                            this.$swal({
                                icon: 'success',
                                title: '更新成功',
                            });
                            return false;
                        }

                        Swal.fire({
                            title: '分期利率新增成功!',
                            showCancelButton: true,
                            confirmButtonText: '回主畫面',
                            cancelButtonText: '繼續新增'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                                return false;
                            }

                            $('#form-modal').modal('show');
                        });
                    }).catch((error) => {

                        if (error.response) {
                            this.$swal({
                                icon: 'error',
                                title: error.response.data.message,
                            });
                        }
                    });
                },
                getInstallmentInterestRate(id) {

                    return axios({
                        method: 'get',
                        url: `${this.baseUrl}/${id}`,
                    }).then(function (response) {
                        return response.data.data;
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
                //確認資料
                checkExisted(excludeId) {

                    axios({
                        method: 'get',
                        url: this.checkExistedUrl,
                        params: {
                            issuing_bank_no: this.modal.bankNo,
                            number_of_installments: this.modal.numberOfInstallments,
                            started_at: this.modal.startedAt.date,
                            ended_at: this.modal.endedAt.date,
                            exclude_id: excludeId,
                        },
                    }).then((response) => {
                        //資料有誤無法新增
                        if (response.data.status == false) {
                            this.$swal({
                                icon: 'error',
                                title: response.data.message,
                            })
                            return false;
                        }

                        this.$swal({
                            title: '確定要新增?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '確定',
                            cancelButtonText: '取消'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.save();
                            }
                        });
                    });
                },
                //點擊新增按鈕
                onCreate() {

                    //銀行
                    this.modal.bankNoDisabled = false;
                    //期數
                    this.modal.numberOfInstallmentsDisabled = false;
                    //利率
                    this.modal.interestRateDisabled = false;
                    //門檻
                    this.modal.minConsumptionDisabled = false;
                    //限制最小日期
                    this.modal.startedAt.configs.minDate = 'today';
                    this.modal.endedAt.configs.minDate = 'today';
                    //重置參數
                    this.resetParameters();

                    this.modal.title = '分期利率新增';
                    this.targetUrl = this.baseUrl;
                    this.action = 'create';
                    this.method = 'post';

                    let rules = {
                        bank_no: {
                            required: true,
                        },
                        number_of_installments: {
                            required: true,
                        },
                        started_at: {
                            required: true,
                            date: true,
                            greaterSameThan: this.today
                        },
                        ended_at: {
                            required: true,
                            date: true,
                            greaterSameThan: this.today
                        },
                        interest_rate: {
                            required: true,
                            min: 0,
                            max: 99,
                            step: 0.01
                        },
                        min_consumption: {
                            required: true,
                            digits: true
                        },
                        active: {
                            required: true,
                        },
                        remark: {
                            maxlength: 50,
                        },
                    };

                    this.setValidator(rules);
                    this.Validator.resetForm();

                    $('#form-modal').modal('show');
                },
                //點擊編輯按鈕
                async onEdit(id) {

                    let installmentInterestRate = await this.getInstallmentInterestRate(id);
                    //銀行
                    this.modal.bankNoDisabled = true;
                    //期數
                    this.modal.numberOfInstallmentsDisabled = true;
                    //利率
                    this.modal.interestRateDisabled = true;
                    //門檻
                    this.modal.minConsumptionDisabled = true;

                    //不限制最小日期
                    this.modal.startedAt.configs.minDate = null;
                    this.modal.endedAt.configs.minDate = null;

                    var now = new Date();
                    var startedAt = new Date(installmentInterestRate.started_at);
                    var endedAt = new Date(installmentInterestRate.ended_at);

                    let rules = {
                        interest_rate: {
                            required: true,
                            min: 0,
                            max: 99,
                            step: 0.01
                        },
                        min_consumption: {
                            required: true,
                            digits: true
                        },
                        active: {
                            required: true,
                        },
                        remark: {
                            maxlength: 50,
                        },
                    };

                    //關閉 不允許修改起訖日
                    if (installmentInterestRate.active == 0) {

                        //下架 不允許修改起訖日
                    } else if (now > endedAt) {
                        console.log('下架');
                        //待上架
                    } else if (now < startedAt) {

                        this.modal.interestRateDisabled = false;
                        this.modal.minConsumptionDisabled = false;
                        this.modal.startedAt.configs.minDate = 'today';
                        this.modal.endedAt.configs.minDate = 'today';

                        rules.started_at = {
                            required: true,
                            date: true,
                            greaterSameThan: this.today
                        };

                        rules.ended_at = {
                            required: true,
                            date: true,
                            greaterSameThan: this.today
                        };
                        //已上架
                    } else if (now >= startedAt && now <= endedAt) {
                        this.modal.endedAt.configs.minDate = 'today';

                        rules.ended_at = {
                            required: true,
                            date: true,
                            greaterSameThan: this.today
                        };
                    }

                    this.setValidator(rules);
                    this.Validator.resetForm();

                    this.modal.id = installmentInterestRate.id;
                    this.modal.bankNo = installmentInterestRate.issuing_bank_no;
                    this.modal.numberOfInstallments = installmentInterestRate.number_of_installments;
                    this.modal.startedAt.date = installmentInterestRate.started_at;
                    this.modal.endedAt.date = installmentInterestRate.ended_at;
                    this.modal.interestRate = installmentInterestRate.interest_rate;
                    this.modal.minConsumption = parseInt(installmentInterestRate.min_consumption, 10);
                    this.modal.active = installmentInterestRate.active;
                    this.modal.remark = installmentInterestRate.remark;

                    this.modal.title = '分期利率編輯';
                    this.targetUrl = `${this.baseUrl}/${id}`;
                    this.action = 'edit';
                    this.method = 'put';
                    //調整驗證規則

                    $('#form-modal').modal('show');
                },
                resetParameters() {
                    this.modal.bankNo = null;
                    this.modal.numberOfInstallments = null;
                    this.modal.startedAt.date = null;
                    this.modal.endedAt.date = null;
                    this.modal.interestRate = null;
                    this.modal.minConsumption = null;
                    this.modal.active = null;
                    this.modal.remark = null;
                },
                onStartedAtChange(selectedDates, dateStr) {
                    this.modal.endedAt.configs.minDate = dateStr;
                },
                onEndedAtChange(selectedDates, dateStr) {
                    this.modal.startedAt.configs.maxDate = dateStr;
                },
            }
        })
    </script>
@endsection
