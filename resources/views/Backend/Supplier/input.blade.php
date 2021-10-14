@extends('Backend.master')

@section('title', '功能名稱')

@section('content')
    <div id="page-wrapper" style="min-height: 508px;">
        {{-- Supplier 變數判斷現在是否是在編輯 --}}
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-plus"></i>{{ isset($Supplier) ? '編輯資料' : '新增資料' }}</h1>
            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        @if (isset($Supplier))
                            <form role="form" id="formData" method="POST"
                                action="{{ route('supplier.update', $Supplier->id) }}" enctype="multipart/form-data"
                                novalidate="novalidate">
                                {{ method_field('PUT') }}
                                {{ csrf_field() }}
                            @else
                                <form role="form" id="formData" method="post" action="{{ route('supplier') }}"
                                    enctype="multipart/form-data" novalidate="novalidate">
                        @endif

                        <div class="row">
                            @csrf
                            <!-- 欄位 -->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_supplier_type">
                                            <label for="supplier_type">供應商類別</label>
                                            <select class="form-control js-select2" name="supplier_type_id" id="">
                                                @foreach ($SupplierType as $obj)
                                                    <option value='{{ $obj->id }}'
                                                        {{ (old('bluesign') ?? (isset($Supplier) ? $Supplier->display_number : '')) == $obj->id ? 'selected' : '' }}>
                                                        {{ $obj->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_display_number">
                                            <label for="display_number">供應商編號</label>
                                            <input class="form-control" name="display_number" id="display_number"
                                                value="{{ old('display_number') ?? (isset($Supplier) ? $Supplier->display_number : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_company_number">
                                            <label for="company_number">統一編號</label>
                                            <input class="form-control" name="company_number" id="company_number"
                                                value="{{ old('company_number') ?? (isset($Supplier) ? $Supplier->company_number : '') }}">
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_short_name">
                                            <label for="short_name">簡稱</label>
                                            <input class="form-control" name="short_name" id="short_name"
                                                value="{{ old('short_name') ?? (isset($Supplier) ? $Supplier->short_name : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_name">
                                            <label for="name">完整名稱</label>
                                            <input class="form-control" name="name" id="name"
                                                value="{{ old('name') ?? (isset($Supplier) ? $Supplier->name : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_pay_condition">
                                            <label for="pay_condition">付款條件</label>

                                            <select class="form-control js-select2" name="pay_condition_id"
                                                id="pay_condition_id" tabindex="-1" aria-hidden="true">
                                                <option value="0">請選擇</option>
                                                <option value="1">付款條件A</option>
                                                <option value="2">付款條件B</option>
                                                <option value="3">付款條件C</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_contact_name">
                                            <label for="contact_name">負責人名稱</label>
                                            <input class="form-control" name="contact_name" id="contact_name"
                                                value="{{ old('contact_name') ?? (isset($Supplier) ? $Supplier->contact_name : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_email">
                                            <label for="email">電子信箱</label>
                                            <input class="form-control" name="email" id="email"
                                                value="{{ old('email') ?? (isset($Supplier) ? $Supplier->email : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_telephone">
                                            <label for="telephone">聯絡電話</label>
                                            <input class="form-control" name="telephone" id="telephone"
                                                value="{{ old('telephone') ?? (isset($Supplier) ? $Supplier->telephone : '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_fax">
                                            <label for="fax">傳真號碼</label>
                                            <input class="form-control" name="fax" id="fax"
                                                value="{{ old('fax') ?? (isset($Supplier) ? $Supplier->fax : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_postal_code">
                                            <label for="postal_code">郵遞區號</label>
                                            <input class="form-control" name="postal_code" id="postal_code"
                                                value="{{ old('postal_code') ?? (isset($Supplier) ? $Supplier->postal_code : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_cellphone">
                                            <label for="cellphone">手機號碼</label>
                                            <input class="form-control" name="cell_phone" id="cell_phone"
                                                value="{{ old('cell_phone') ?? (isset($Supplier) ? $Supplier->cell_phone : '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_address">
                                            <label for="address">地址</label>
                                            <input class="form-control" name="address" id="address"
                                                value="{{ old('address') ?? (isset($Supplier) ? $Supplier->address : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_address2">
                                            <label for="address2">地址2</label>
                                            <input class="form-control" name="address2" id="address2"
                                                value="{{ old('address2') ?? (isset($Supplier) ? $Supplier->address2 : '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_address3">
                                            <label for="address3">地址3</label>
                                            <input class="form-control" name="address3" id="address3"
                                                value="{{ old('address3') ?? (isset($Supplier) ? $Supplier->address3 : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_address4">
                                            <label for="address4">地址4</label>
                                            <input class="form-control" name="address4" id="address4"
                                                value="{{ old('address4') ?? (isset($Supplier) ? $Supplier->address4 : '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_address5">
                                            <label for="address5">地址5</label>
                                            <input class="form-control" name="address5" id="address5"
                                                value="{{ old('address5') ?? (isset($Supplier) ? $Supplier->address5 : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_active">
                                            <label for="active">狀態</label>
                                            <select class="form-control" name="active" id="active">
                                                <option value="1"
                                                    {{ (old('active') ?? (isset($Supplier) ? $Supplier->active : '')) == 1 ? 'selected' : '' }}>
                                                    顯示</option>
                                                <option value="0"
                                                    {{ (old('active') ?? (isset($Supplier) ? $Supplier->active : '')) == 0 ? 'selected' : '' }}>
                                                    隱藏</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_bank_name">
                                            <label for="bank_name">收款銀行</label>
                                            <input class="form-control" name="bank_name" id="bank_name"
                                                value="{{ old('bank_name') ?? (isset($Supplier) ? $Supplier->bank_name : '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_bank_branch">
                                            <label for="bank_branch">支行名稱</label>
                                            <input class="form-control" name="bank_branch" id="bank_branch"
                                                value="{{ old('bank_branch') ?? (isset($Supplier) ? $Supplier->bank_branch : '') }}">
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="row" style="display:none">
                                        <div class="col-sm-3">
                                            <div class="form-group" id="div_accountant_list">
                                                <label for="accountant_list">會計科目</label>
                                                <select class="form-control js-select2" name="accountant_list_id"
                                                    id="category">
                                                    <option value="0">無</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" id="div_bank_account_name">
                                                <label for="bank_account_name">銀行戶名</label>
                                                <input class="form-control" name="bank_account_name"
                                                    id="bank_account_name">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group" id="div_bank_number">
                                                <label for="bank_number">銀行帳號</label>
                                                <input class="form-control" name="bank_number" id="bank_number">
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="form-group" id="div_currency">
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
                                            <div class="form-group" id="div_is_related">
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
                                        <div class="form-group" id="div_remark">
                                            <label for="remark">備註</label>
                                            <textarea class="form-control" rows="3" name="remark"
                                                id="remark">{{ old('remark') ?? (isset($Supplier) ? $Supplier->remark : '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div id="contact_table">
                                    <textarea name="contact_json" style="display: none">@{{ contactData }}</textarea>

                                    @if (isset($Supplier))
                                        <h4><i class="fa fa-th-large"></i> 其他聯絡人 </h4>
                                        <div id="">
                                            <div class="well" v-for="(contact, contactkey) in contactData"
                                                :key="contactkey"
                                                style="border-left-width: 8px; border-left-color: #1b809e; background:#f9f9f9;">
                                                <div class="row">
                                                    <div class="col-sm-2"><label>姓名</label>
                                                        <input class="form-control" v-model="contact.name">
                                                    </div>
                                                    <div class="col-sm-2"><label>電話</label>
                                                        <input class="form-control" v-model="contact.telephone">
                                                    </div>
                                                    <div class="col-sm-2"><label>手機</label>
                                                        <input class="form-control" v-model="contact.cell_phone">
                                                    </div>
                                                    <div class="col-sm-2"><label>傳真</label>
                                                        <input class="form-control" v-model="contact.fax">
                                                    </div>
                                                    <div class="col-sm-2"><label>信箱</label>
                                                        <input class="form-control" v-model="contact.email">
                                                    </div>
                                                    <div class="col-sm-2"><label>備註</label>
                                                        <input class="form-control" v-model="contact.remark">
                                                    </div>
                                                </div>
                                                <br>
                                                <button type="button" class="btn btn-danger"
                                                    @click="delContact(contact.id,contactkey)">
                                                    <i class="fa fa-ban"></i>
                                                    刪除聯絡人
                                                </button>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <button type="button" class="btn btn-warning" @click="addContact"><i
                                                        class="fa fa-plus"></i>
                                                    新增聯絡人</button>
                                            </div>
                                        </div>
                                        <hr>
                                    @endif
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button class="btn btn-success" type="button"><i class="fa fa-check"
                                                        @click="submitBtn"></i> 完成</button>
                                                <a href="{{ route('supplier') }}" class="btn btn-danger"
                                                    id="btn-cancel"><i class="fa fa-ban"></i> 取消</a>
                                            </div>
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
@section('js')
    <script>
        var contact = Vue.extend({
            data: function() {
                return {
                    contactData: @json(isset($Contact) ? $Contact : '{}'),
                    contactDefault: {
                        id: '',
                        name: '',
                        remark: '',
                        table_id: '',
                        table_name: '',
                        telephone: '',
                        cell_phone: '',
                        email: '',
                        fax: '',
                        created_at: '',
                        updated_at: '',
                    }
                }
            },
            methods: {
                addContact() {
                    let contactDefault = this.contactDefault;
                    this.contactData.push(contactDefault);
                },
                delContact(id, key) {
                    var checkDel = confirm('你確定要刪除嗎？');
                    if (checkDel) {
                        this.$delete(this.contactData, key)
                        if (id !== '') { //如果ID 不等於空 就 AJAX DEL 
                            $.ajax({
                                type: "POST",
                                url: '/backend/contact/ajax/del',
                                dataType: "json",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    "id": id,
                                    "table_name": 'Supplier',
                                },
                                success: function(response) {
                                    console.log(response);
                                },
                                error: function(error) {
                                    console.log(error);
                                }
                            });
                        }
                    }

                },
                submitBtn() {
                    $("#formData").submit();
                },
            }
        })

        new contact().$mount('#contact_table')
    </script>
@endsection
@endsection
