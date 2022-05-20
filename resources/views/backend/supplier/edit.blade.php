@extends('backend.master')

@section('title', '編輯供應商')

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
                    <h1 class="page-header"><i class="fa-solid fa-pencil"></i> 編輯供應商</h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <form id="edit-form" method="post" action="{{ route('supplier.update', $supplier->id) }}">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="supplier_type">供應商類別 <span
                                                    style="color: red;">*</span></label>
                                            <select2 class="form-control" :options="supplierTypes" v-model="form.supplierTypeId" name="supplier_type_id">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="display_number">供應商編號 <span
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control" name="display_number"
                                                id="display_number" v-model="form.displayNumber">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="company_number">統一編號 <span
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control" name="company_number"
                                                id="company_number" v-model="form.companyNumber">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="short_name">簡稱 <span
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control" name="short_name" id="short_name"
                                                v-model="form.shortName">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="name">完整名稱 <span
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                v-model="form.name">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="payment_term">付款條件</label>
                                            <select2 class="form-control" :options="paymentTerms" v-model="form.paymentTerm" name="payment_term">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="contact_name">負責人名稱</label>
                                            <input type="text" class="form-control" name="contact_name" id="contact_name"
                                                v-model="form.contactName">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="email">電子信箱</label>
                                            <input type="text" class="form-control" name="email" id="email"
                                                v-model="form.email">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="telephone">聯絡電話</label>
                                            <input type="text" class="form-control" name="telephone" id="telephone"
                                                v-model="form.telephone">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="fax">傳真號碼</label>
                                            <input type="text" class="form-control" name="fax" id="fax"
                                                v-model="form.fax">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="postal_code">郵遞區號</label>
                                            <input type="text" class="form-control" name="postal_code" id="postal_code"
                                                v-model="form.postalCode">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="cell_phone">手機號碼</label>
                                            <input type="text" class="form-control" name="cell_phone" id="cell_phone"
                                                v-model="form.cellPhone">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="tax_type">稅別</label>
                                            <select2 class="form-control" :options="taxTypeOptions" v-model="form.taxType" name="tax_type" :allow-clear="false">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="address">地址</label>
                                            <input type="text" class="form-control" name="address" id="address"
                                                v-model="form.address">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="address2">地址2</label>
                                            <input type="text" class="form-control" name="address2" id="address2"
                                                v-model="form.address2">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="address3">地址3</label>
                                            <input type="text" class="form-control" name="address3" id="address3"
                                                v-model="form.address3">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="address4">地址4</label>
                                            <input type="text" class="form-control" name="address4" id="address4"
                                                v-model="form.address4">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="address5">地址5</label>
                                            <input type="text" class="form-control" name="address5" id="address5"
                                                v-model="form.address5">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="active">狀態</label>
                                            <select2 class="form-control" :options="activeOptions" v-model="form.active" name="active" :allow-clear="false">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="bank_name">收款銀行</label>
                                            <input type="text" class="form-control" name="bank_name" id="bank_name"
                                                v-model="form.bankName">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label" for="bank_branch">支行名稱</label>
                                            <input type="text" class="form-control" name="bank_branch" id="bank_branch"
                                                v-model="form.bankBranch">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label" for="remark">備註</label>
                                            <textarea class="form-control" rows="3" name="remark" id="remark" v-model="form.remark"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr style="border-top: 1px solid gray;">

                                <h4>聯絡人</h4>
                                <div class="well" v-for="(contact, index) in form.contacts"
                                    :key="index"
                                    style="border-left-width: 8px; border-left-color: #1b809e; background:#f9f9f9;">
                                    <input type="hidden" :name="`contacts[${index}][id]`"
                                        :value="contact.id">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="control-label">姓名 <span style="color: red;">*</span></label>
                                                <input type="text" class="form-control  contact-name" v-model="contact.name"
                                                    :name="`contacts[${index}][name]`">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="control-label">電話</label>
                                                <input type="text" class="form-control contact-telephone" v-model="contact.telephone"
                                                    :name="`contacts[${index}][telephone]`">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="control-label">手機</label>
                                                <input type="text" class="form-control contact-cellPhone" v-model="contact.cellPhone"
                                                    :name="`contacts[${index}][cell_phone]`">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="control-label">傳真</label>
                                                <input type="text" class="form-control contact-fax" v-model="contact.fax"
                                                    :name="`contacts[${index}][fax]`">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="control-label">信箱</label>
                                                <input type="text" class="form-control contact-email" v-model="contact.email"
                                                    :name="`contacts[${index}][email]`">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="control-label">備註</label>
                                                <input type="text" class="form-control contact-remark" v-model="contact.remark"
                                                    :name="`contacts[${index}][remark]`">
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-danger" @click="deleteContact(index)">
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
                                <hr style="border-top: 1px solid gray;">

                                <h4>合約</h4>
                                <input type="hidden" name="supplier_contract_id" :value="form.contract.supplierContractId">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="date_from">合約起日</label>
                                            <div class="input-group" id="date_from_flatpickr">
                                                <input type="text" class="form-control" name="date_from" id="date_from"
                                                    autocomplete="off" data-input v-model="form.contract.dateFrom">
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
                                            <label class="control-label" for="date_to">合約訖日</label>
                                            <div class="input-group" id="date_to_flatpickr">
                                                <input type="text" class="form-control" name="date_to" id="date_to"
                                                    autocomplete="off" data-input v-model="form.contract.dateTo">
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
                                            <label class="control-label" for="status_code">合約狀態</label>
                                            <select2 class="form-control" :options="supplierContractStatusCodeOptions" v-model="form.contract.statusCode" name="status_code">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">結帳週期</label>
                                            <div class="row">
                                                <div class="col-sm-1">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="billing_cycle" id="biweekly"
                                                            value="biweekly" v-model="form.contract.billingCycle">雙週結
                                                    </label>
                                                </div>
                                                <div class="col-sm-1">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="billing_cycle" id="monthly"
                                                            value="monthly" v-model="form.contract.billingCycle">月結
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
                                        <tr v-for="(supplierContractTerm, index) in supplierContractTerms"
                                            :key="index">
                                            <td>@{{ index + 1 }}</td>
                                            <td>@{{ supplierContractTerm.description }}</td>
                                            <td>
                                                <input type="hidden" :name="`contract_terms[${index}][term_code]`"
                                                    :value="form.contract.terms[index].termCode">
                                                <input type="text" class="form-control"
                                                    :name="`contract_terms[${index}][term_value]`"
                                                    v-model="form.contract.terms[index].termValue">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr style="border-top: 1px solid gray;">

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
                    supplierId: null,
                    supplierTypeId: null,
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
                    active: 1,
                    bankName: "",
                    bankBranch: "",
                    remark: "",
                    contacts: [{
                        name: "",
                        telephone: "",
                        cellPhone: "",
                        fax: "",
                        email: "",
                        remark: "",
                    }],
                    contract: {
                        supplierContractId: null,
                        dateFrom: "",
                        dateTo: "",
                        statusCode: "",
                        billingCycle: "",
                        terms: [],
                    },
                },
                supplierTypes: [],
                paymentTerms: [],
                taxTypeOptions: [],
                activeOptions: [],
                supplierContractStatusCodeOptions: [],
                supplierContractTerms: [],
            },
            created() {
                let supplierTypes = @json($supplierTypes);
                let paymentTerms = @json($paymentTerms);
                let taxTypeOptions = @json($taxTypeOptions);
                let activeOptions = @json($activeOptions);
                let supplierContractStatusCodeOptions = @json($supplierContractStatusCodeOptions);
                let supplierContractTerms = @json($supplierContractTerms);
                let supplier = @json($supplier);

                if (supplierTypes) {
                    supplierTypes.forEach(supplierType => {
                        this.supplierTypes.push({
                            text: supplierType.name,
                            id: supplierType.id
                        });
                    });
                }

                if (paymentTerms) {
                    paymentTerms.forEach(paymentTerm => {
                        this.paymentTerms.push({
                            text: paymentTerm.description,
                            id: paymentTerm.code
                        });
                    });
                }

                if (taxTypeOptions) {
                    Object.entries(taxTypeOptions).forEach(([key, taxTypeOption]) => {
                        this.taxTypeOptions.push({
                            text: taxTypeOption,
                            id: key
                        });
                    });
                }

                if (activeOptions) {
                    Object.entries(activeOptions).forEach(([key, activeOption]) => {
                        this.activeOptions.push({
                            text: activeOption,
                            id: parseInt(key)
                        });
                    });
                }

                if (supplierContractStatusCodeOptions) {
                    Object.entries(supplierContractStatusCodeOptions).forEach(([key,
                        supplierContractStatusCodeOption
                    ]) => {
                        this.supplierContractStatusCodeOptions.push({
                            text: supplierContractStatusCodeOption,
                            id: key
                        });
                    });
                }

                if (supplierContractTerms) {
                    supplierContractTerms.forEach(supplierContractTerm => {
                        this.supplierContractTerms.push({
                            description: supplierContractTerm.description
                        });

                        this.form.contract.terms.push({
                            termCode: supplierContractTerm.code,
                            termValue: "",
                        });
                    });
                }

                if (supplier) {
                    this.form.supplierId = supplier.id;
                    this.form.supplierTypeId = supplier.supplier_type_id;
                    this.form.displayNumber = supplier.display_number;
                    this.form.companyNumber = supplier.company_number;
                    this.form.shortName = supplier.short_name;
                    this.form.name = supplier.name;
                    this.form.paymentTerm = supplier.payment_term;
                    this.form.contactName = supplier.contact_name;
                    this.form.email = supplier.email;
                    this.form.telephone = supplier.telephone;
                    this.form.fax = supplier.fax;
                    this.form.postalCode = supplier.postal_code;
                    this.form.cellPhone = supplier.cell_phone;
                    this.form.taxType = supplier.tax_type;
                    this.form.address = supplier.address;
                    this.form.address2 = supplier.address2;
                    this.form.address3 = supplier.address3;
                    this.form.address4 = supplier.address4;
                    this.form.address5 = supplier.address5;
                    this.form.active = supplier.active;
                    this.form.bankName = supplier.bank_name;
                    this.form.bankBranch = supplier.bank_branch;
                    this.form.remark = supplier.remark;

                    // 已存在的聯絡人
                    if (Array.isArray(supplier.contacts) && supplier.contacts.length) {
                        this.form.contacts = [];

                        supplier.contacts.forEach(contact => {
                            this.form.contacts.push({
                                id: contact.id,
                                name: contact.name,
                                telephone: contact.telephone,
                                cellPhone: contact.cell_phone,
                                fax: contact.fax,
                                email: contact.email,
                                remark: contact.remark,
                            });
                        });
                    }

                    // 已存在的供應商合約
                    if (supplier.supplier_contract) {
                        this.form.contract.supplierContractId = supplier.supplier_contract.id;
                        this.form.contract.dateFrom = supplier.supplier_contract.date_from;
                        this.form.contract.dateTo = supplier.supplier_contract.date_to;
                        this.form.contract.statusCode = supplier.supplier_contract.status_code;
                        this.form.contract.billingCycle = supplier.supplier_contract.billing_cycle;

                        if (Array.isArray(supplier.supplier_contract.supplier_contract_terms) && supplier
                            .supplier_contract.supplier_contract_terms.length) {
                            supplier.supplier_contract.supplier_contract_terms.forEach(supplier_contract_term => {
                                let termIndex = this.form.contract.terms.findIndex(term => term.termCode ==
                                    supplier_contract_term.term_code);

                                if (termIndex !== -1) {
                                    this.form.contract.terms[termIndex].termValue = supplier_contract_term
                                        .term_value;
                                }
                            });
                        }
                    }
                }
            },
            mounted() {
                let self = this;

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
                $("#edit-form").validate({
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
                                        url: "/backend/supplier/display-number-exists",
                                        type: "post",
                                        dataType: "json",
                                        cache: false,
                                        data: {
                                            supplier_id: self.form.supplierId,
                                        },
                                        dataFilter: function(response) {
                                            if (response) {
                                                let data = JSON.parse(response);

                                                if (data.result) {
                                                    return false;
                                                }
                                            }

                                            return true;
                                        },
                                    }
                                },
                                depends: function(element) {
                                    return $('#display_number').val();
                                },
                            },
                            isAlphaNumeric: true,
                        },
                        company_number: {
                            required: true,
                            isGUINumber: true,
                        },
                        short_name: {
                            required: true,
                        },
                    },
                    messages: {
                        display_number: {
                            remote: '供應商編號已存在',
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
                    success: function(label, element) {
                        $(element).closest(".form-group").removeClass("has-error");
                    },
                });
            },
            methods: {
                addContact() {
                    this.form.contacts.push({
                        name: "",
                        telephone: "",
                        cellPhone: "",
                        fax: "",
                        email: "",
                        remark: "",
                    });
                },
                deleteContact(index) {
                    if (confirm('確定要刪除嗎？')) {
                        this.form.contacts.splice(index, 1);
                    }
                },
                submitForm() {
                    $(`.contact-name`).each(function() {
                        $(this).rules("add", {
                            required: true,
                        });
                    });

                    $("#edit-form").submit();
                },
            }
        });
    </script>
@endsection
