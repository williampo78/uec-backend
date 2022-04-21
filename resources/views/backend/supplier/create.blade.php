@extends('backend.master')

@section('title', '新增供應商')

@section('content')
    <div id="app">
        @if ($errors->any())
            <div ref="errorMessage" style="display: none;">
                {{ $errors->first('message') }}
            </div>
        @endif

        <div id="page-wrapper">
            <!-- 表頭名稱 -->
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-plus"></i> 新增供應商</h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <form id="create-form" method="post" action="{{ route('supplier.store') }}" ref="createform">
                                @csrf

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="supplier_type">供應商類別 <span style="color: red;">*</span></label>
                                            <v-select v-model="form.supplierTypeId" :reduce="option => option.code"
                                                :options="[
                                                @isset($supplierTypes)
                                                    @foreach ($supplierTypes as $supplierType)
                                                        {label: '{{ $supplierType->name }}', code: '{{ $supplierType->id }}'},
                                                    @endforeach
                                                @endisset
                                                ]">
                                            </v-select>
                                            <input type="hidden" v-model="form.supplierTypeId"
                                                name="supplier_type_id" />
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="display_number">供應商編號 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" name="display_number" id="display_number">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="company_number">統一編號 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" name="company_number" id="company_number">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="short_name">簡稱 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" name="short_name" id="short_name">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="name">完整名稱 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" name="name" id="name">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="payment_term">付款條件</label>
                                            <v-select v-model="form.paymentTerm" :reduce="option => option.code"
                                                :options="[
                                                @isset($paymentTerms)
                                                    @foreach ($paymentTerms as $paymentTerm)
                                                        {label: '{{ $paymentTerm->description }}', code:'{{ $paymentTerm->code }}'},
                                                    @endforeach
                                                @endisset
                                                ]">
                                            </v-select>
                                            <input type="hidden" v-model="form.paymentTerm" name="payment_term" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="contact_name">負責人名稱</label>
                                            <input type="text" class="form-control" name="contact_name" id="contact_name">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="email">電子信箱</label>
                                            <input type="text" class="form-control" name="email" id="email">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="telephone">聯絡電話</label>
                                            <input type="text" class="form-control" name="telephone" id="telephone">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="fax">傳真號碼</label>
                                            <input type="text" class="form-control" name="fax" id="fax">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="postal_code">郵遞區號</label>
                                            <input type="text" class="form-control" name="postal_code" id="postal_code">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="cellphone">手機號碼</label>
                                            <input type="text" class="form-control" name="cell_phone" id="cell_phone">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="tax_type">稅別</label>
                                            <v-select v-model="form.taxType" :reduce="option => option.code" :options="[
                                                @isset($taxTypeOptions)
                                                    @foreach ($taxTypeOptions as $key => $taxTypeOption)
                                                        {label: '{{ $taxTypeOption }}', code: '{{ $key }}'},
                                                    @endforeach
                                                @endisset
                                                ]" :clearable="false">
                                            </v-select>
                                            <input type="hidden" v-model="form.taxType" name="tax_type" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="address">地址</label>
                                            <input type="text" class="form-control" name="address" id="address">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="address2">地址2</label>
                                            <input type="text" class="form-control" name="address2" id="address2">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="address3">地址3</label>
                                            <input type="text" class="form-control" name="address3" id="address3">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="address4">地址4</label>
                                            <input type="text" class="form-control" name="address4" id="address4">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="address5">地址5</label>
                                            <input type="text" class="form-control" name="address5" id="address5">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="active">狀態</label>
                                            <v-select v-model="form.active" :reduce="option => option.code" :options="[
                                                @isset($activeOptions)
                                                    @foreach ($activeOptions as $key => $activeOption)
                                                        {label: '{{ $activeOption }}', code: '{{ $key }}'},
                                                    @endforeach
                                                @endisset
                                                ]" :clearable="false">
                                            </v-select>
                                            <input type="hidden" v-model="form.active" name="active" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="bank_name">收款銀行</label>
                                            <input type="text" class="form-control" name="bank_name" id="bank_name">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="bank_branch">支行名稱</label>
                                            <input type="text" class="form-control" name="bank_branch" id="bank_branch">
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="row" style="display:none">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="accountant_list">會計科目</label>
                                                <select class="form-control js-select2" name="accountant_list_id"
                                                    id="category">
                                                    <option value="0">無</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="bank_account_name">銀行戶名</label>
                                                <input class="form-control" name="bank_account_name"
                                                    id="bank_account_name">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="bank_number">銀行帳號</label>
                                                <input class="form-control" name="bank_number" id="bank_number">
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="currency">幣別</label>
                                                <select class="form-control js-select2" name="currency" id="currency"
                                                    tabindex="-1" aria-hidden="true">
                                                    <option value="">請選擇</option>
                                                    <option value="1">新台幣(TWD)</option>
                                                    <option value="2">人民幣(CNY)</option>
                                                    <option value="3">美元(USD)</option>
                                                    <option value="4">港幣(HKD)</option>
                                                    <option value="5">馬來西亞林吉特(MYR)</option>
                                                    <option value="6">比利時法郎(BEF)</option>
                                                    <option value="7">加幣(CAD)</option>
                                                    <option value="8">法國法郎(FRF)</option>
                                                    <option value="9">義大利里拉(ITL)</option>
                                                    <option value="10">菲律賓比索(PHP)</option>
                                                    <option value="11">德國馬克(DEM)</option>
                                                    <option value="12">日圓(JPY)</option>
                                                    <option value="13">瑞士法郎(CHF)</option>
                                                    <option value="14">瑞典克郎(SEK)</option>
                                                    <option value="15">澳幣(AUD)</option>
                                                    <option value="16">紐西蘭幣(NZD)</option>
                                                    <option value="17">新加坡幣(SGD)</option>
                                                    <option value="18">西班牙比塞塔(ESP)</option>
                                                    <option value="19">丹麥克郎(DKK)</option>
                                                    <option value="20">印度盧比(INR)</option>
                                                    <option value="21">挪威克郎(NOK)</option>
                                                    <option value="22">荷蘭盾(NLG)</option>
                                                    <option value="23">芬蘭馬克(FIM)</option>
                                                    <option value="24">沙烏地里亞爾(SAR)</option>
                                                    <option value="25">銖(THB)</option>
                                                    <option value="26">印尼盧比(IDR)</option>
                                                    <option value="27">蘭特(ZAR)</option>
                                                    <option value="28">先令(ATS)</option>
                                                    <option value="29">英鎊(GBP)</option>
                                                    <option value="30">愛爾蘭鎊(IEP)</option>
                                                    <option value="31">歐元(EUR)</option>
                                                    <option value="32">澳門幣(MOP)</option>
                                                    <option value="33">墨西哥比索(MXN)</option>
                                                    <option value="34">茲羅提(PLN)</option>
                                                    <option value="35">捷克克郎(CZK)</option>
                                                    <option value="36">新土耳其里拉(TRY)</option>
                                                    <option value="37">富林特(HUF)</option>
                                                    <option value="38">越南盾(VND)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="is_related">關係人</label>
                                                <br>
                                                <div class="btn-group" data-toggle="buttons">
                                                    <label class="btn btn-default form-check-label ">
                                                        <input class="form-check-input" name="is_related" type="radio"
                                                            autocomplete="off" value="1">是
                                                    </label>

                                                    <label class="btn btn-default form-check-label ">
                                                        <input class="form-check-input" name="is_related" type="radio"
                                                            autocomplete="off" value="0">否
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="remark">備註</label>
                                            <textarea class="form-control" rows="3" name="remark" id="remark"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr style="border-top: 1px solid gray;" />

                                <h4>聯絡人</h4>
                                <div class="well" v-for="(contact, index) in form.contacts" :key="index"
                                    style="border-left-width: 8px; border-left-color: #1b809e; background:#f9f9f9;">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <label>姓名 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" v-model="contact.name" :name="`contacts[${index}][name]`">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>電話 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" v-model="contact.telephone" :name="`contacts[${index}][telephone]`">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>手機 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" v-model="contact.cellPhone" :name="`contacts[${index}][cell_phone]`">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>傳真 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" v-model="contact.fax" :name="`contacts[${index}][fax]`">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>信箱 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" v-model="contact.email" :name="`contacts[${index}][email]`">
                                        </div>
                                        <div class="col-sm-2">
                                            <label>備註 <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control" v-model="contact.remark" :name="`contacts[${index}][remark]`">
                                        </div>
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-danger"
                                        @click="deleteContact(index)">
                                        <i class="fa-solid fa-trash-can"></i> 刪除聯絡人
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-warning" @click="addContact">
                                            <i class="fa-solid fa-plus"></i> 新增聯絡人
                                        </button>
                                    </div>
                                </div>
                                <hr style="border-top: 1px solid gray;" />

                                <h4>合約</h4>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="date_from">合約起日 <span style="color: red;">*</span></label>
                                            <div class="input-group" id="date_from_flatpickr">
                                                <input type="text" class="form-control" name="date_from" id="date_from" value="" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="date_to">合約訖日 <span style="color: red;">*</span></label>
                                            <div class="input-group" id="date_to_flatpickr">
                                                <input type="text" class="form-control" name="date_to" id="date_to" value="" autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="status_code">合約狀態 <span style="color: red;">*</span></label>
                                            <v-select v-model="form.contract.statusCode" :reduce="option => option.code" :options="[
                                                @isset($supplierContractStatusCodeOptions)
                                                    @foreach ($supplierContractStatusCodeOptions as $key => $supplierContractStatusCodeOption)
                                                        {label: '{{ $supplierContractStatusCodeOption }}', code: '{{ $key }}'},
                                                    @endforeach
                                                @endisset
                                                ]" :clearable="false">
                                            </v-select>
                                            <input type="hidden" v-model="form.contract.statusCode" name="status_code" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">結帳週期 <span style="color: red;">*</span></label>
                                            <div class="row">
                                                <div class="col-sm-1">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="billing_cycle" id="biweekly"
                                                            value="biweekly" />雙週結
                                                    </label>
                                                </div>
                                                <div class="col-sm-1">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="billing_cycle" id="monthly"
                                                            value="monthly" />月結
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">項次</th>
                                            <th class="text-nowrap">項目名稱</th>
                                            <th class="text-nowrap">項目值</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($supplierContractTerms)
                                            @foreach ($supplierContractTerms as $key => $supplierContractTerm)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $supplierContractTerm->description }}</td>
                                                    <td>
                                                        <input type="hidden" name="contract_terms[{{ $key }}][term_code]" value="{{ $supplierContractTerm->code }}" />
                                                        <input type="text" class="form-control" name="contract_terms[{{ $key }}][term_value]" />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                </table>
                                <hr style="border-top: 1px solid gray;" />

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success" type="button" @click="submitForm"
                                                id="btn-save">
                                                <i class="fa-solid fa-floppy-disk"></i> 儲存
                                            </button>
                                            <a href="{{ route('supplier') }}" class="btn btn-danger" id="btn-cancel"><i
                                                    class="fa-solid fa-ban"></i> 取消
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                form: {
                    supplierTypeId: "",
                    displayNumber: "",
                    companyNumber: "",
                    shortName: "",
                    name: "",
                    paymentTerm: "",
                    contactName: "",
                    email: "",
                    telephone: "",
                    fax: "",
                    postalCode: "",
                    cellPhone: "",
                    taxType: "TAXABLE",
                    address: "",
                    address2: "",
                    address3: "",
                    address4: "",
                    address5: "",
                    active: "1",
                    bank_name: "",
                    bank_branch: "",
                    remark: "",
                    contacts: [
                        {
                            name: "",
                            telephone: "",
                            cellPhone: "",
                            fax: "",
                            email: "",
                            remark: "",
                        }
                    ],
                    contract: {
                        dateFrom: "",
                        dateTo: "",
                        statusCode: "",
                        billingCycle: "",
                    },
                },
            },
            mounted() {
                if (this.$refs.errorMessage) {
                    alert(this.$refs.errorMessage.innerText.trim());
                }

                let dateFromFlatpickr = flatpickr("#date_from_flatpickr", {
                    dateFormat: "Y-m-d",
                    maxDate: $("#date_to").val(),
                    onChange: function(selectedDates, dateStr, instance) {
                        dateToFlatpickr.set('minDate', dateStr);
                    },
                });

                let dateToFlatpickr = flatpickr("#date_to_flatpickr", {
                    dateFormat: "Y-m-d",
                    minDate: $("#date_from").val(),
                    onChange: function(selectedDates, dateStr, instance) {
                        dateFromFlatpickr.set('maxDate', dateStr);
                    },
                });

                // 驗證表單
                $("#create-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        $('#btn-save').prop('disabled', true);
                        form.submit();
                    },
                    rules: {
                        supplier_type_id: {
                            required: true,
                        },
                        name: {
                            required: true,
                        },
                        display_number: {
                            required: true,
                            remote: {
                                param: function(element) {
                                    return {
                                        url: "/backend/supplier/ajax",
                                        type: "post",
                                        dataType: "json",
                                        cache: false,
                                        data: {
                                            display_number: $('#display_number').val(),
                                            type: 'checkDisplayNumber',
                                        },
                                        dataFilter: function(data) {
                                            data = JSON.parse(data)
                                            return data.result
                                        },
                                    }
                                },
                                depends: function(element) {
                                    return $('#display_number').val();
                                },
                            },
                            isEnglishNumber: {
                                param: function() {
                                    let obj = {
                                        number: $('#display_number').val(),
                                    }
                                    return obj;
                                },
                                depends: function(element) {
                                    return true;
                                },
                            }
                        },
                        company_number: {
                            required: true,
                            isTWCompanyNumber: {
                                param: function() {
                                    let obj = {
                                        number: $('#company_number').val(),
                                    }
                                    return obj;
                                },
                                depends: function(element) {
                                    return true;
                                },
                            },
                        },
                        short_name: {
                            required: true,
                        }
                    },
                    messages: {
                        supplier_type_id: {
                            required: "請選擇類型",
                        },
                        name: {
                            required: "請填寫名稱",
                        },
                        display_number: {
                            remote: 'POS品號重複',
                        }
                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if (element.parent('.input-group').length) {
                            error.insertAfter(element.parent());
                            return;
                        }

                        if (element.closest(".form-group").length) {
                            element.closest(".form-group").append(error);
                            return;
                        }

                        error.insertAfter(element);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).closest(".form-group").addClass("has-error");
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).closest(".form-group").removeClass("has-error");
                    },
                    success: function(label, element) {
                        $(element).closest(".form-group").removeClass("has-error");
                    },
                });
            },
            methods: {
                addContact() {
                    this.form.contacts.push(
                        {
                            name: "",
                            telephone: "",
                            cellPhone: "",
                            fax: "",
                            email: "",
                            remark: "",
                        }
                    );
                },
                deleteContact(index) {
                    if (confirm('確定要刪除嗎？')) {
                        this.form.contacts.splice(index, 1);
                    }
                },
                submitForm() {
                    // this.$refs.createform.submit();
                    $("#create-form").submit();
                },
            }
        });
    </script>
@endsection
