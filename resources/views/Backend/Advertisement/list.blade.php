@extends('Backend.master')

@section('title', '供應商資料')

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-truck"></i>廣告版位</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕 -->
                    <div class="panel-heading">
                        <form role="form" id="select-form" method="GET" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <h5>適用頁面</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2-department" name="supplier" id="supplier">
                                            {{-- @foreach ($data['supplier'] as $v)
                                                <option value='{{ $v['id'] }}'
                                                    {{ isset($data['getData']['supplier']) && $v['id'] == $data['getData']['supplier'] ? 'selected' : '' }}>
                                                    {{ $v['name'] }}</option>
                                            @endforeach --}}
                                            <option value=''></option>
                                            <option value='index_page'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'drafted' ? 'selected' : '' }}>
                                                首頁</option>
                                            <option value='product_page'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'reviewing' ? 'selected' : '' }}>
                                                商品頁</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>適用設備</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="status" id="status">
                                            <option value=''></option>
                                            <option value='desktop'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'drafted' ? 'selected' : '' }}>
                                                Desktop</option>
                                            <option value='mobile'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'reviewing' ? 'selected' : '' }}>
                                                Mobile</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>狀態</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="status" id="status">
                                            <option value=''></option>
                                            <option value='enabled'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'drafted' ? 'selected' : '' }}>
                                                啟用</option>
                                            <option value='disabled'
                                                {{ isset($data['getData']['status']) && $data['getData']['status'] == 'reviewing' ? 'selected' : '' }}>
                                                關閉</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2 text-right">
                                    <div class="col-sm-12">
                                        {{-- @if ($share_role_auth['auth_query']) --}}
                                        <button class="btn btn-warning"><i class="fa fa-search  "></i> 查詢</button>
                                        {{-- @endif --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div id="table_list_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr role="row">
                                        <th class="col-sm-1 ">功能</th>
                                        <th class="col-sm-1 ">適用頁面</th>
                                        <th class="col-sm-1 ">代碼</th>
                                        <th class="col-sm-1 ">描述</th>
                                        <th class="col-sm-1 ">Mobile適用</th>
                                        <th class="col-sm-1 ">Desktop適用</th>
                                        <th class="col-sm-1 ">上架類型</th>
                                        <th class="col-sm-1 ">狀態</th>
                                        <th class="col-sm-1 ">備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ad_slots as $obj)
                                        <tr>
                                            <td>
                                                <button class="btn btn-info btn-sm supplier_detail_show"
                                                    data-supplier="{{ $obj->id }}">
                                                    <i class="fa fa-search"></i>
                                                    <button data-toggle="modal" id="hideShowMod" style="display:none;"
                                                        data-target="#supplier_detail">Click me</button>

                                                </button>

                                                @if ($share_role_auth['auth_update'])
                                                    <a class="btn btn-info btn-sm"
                                                        href="{{ route('supplier') }}/{{ $obj->id }}/edit" value="1">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $obj->description }}</td>
                                            <td>{{ $obj->slot_code }}</td>
                                            <td>{{ $obj->slot_desc }}</td>
                                            <td>
                                                @if ($obj->is_mobile_applicable)
                                                    V
                                                @endif
                                            </td>
                                            <td>
                                                @if ($obj->is_desktop_applicable)
                                                    V
                                                @endif
                                            </td>
                                            <td>
                                                {{--I：圖檔(image)、II：母子圖檔(image+image)、T：文字(text)、S：商品、IS：圖檔+商品、X：非人工上稿--}}
                                                @switch($obj->slot_type)
                                                    @case('I')
                                                        圖檔
                                                        @break
                                                    @case('II')
                                                        母子圖檔
                                                        @break
                                                    @case('T')
                                                        文字
                                                        @break
                                                    @case('S')
                                                        商品
                                                        @break
                                                    @case('IS')
                                                        圖檔 + 商品
                                                        @break
                                                    @case('X')
                                                        非人工上稿
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if ($obj->active)
                                                    啟用
                                                @else
                                                    關閉
                                                @endif
                                            </td>
                                            <td>{{ $obj->remark }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="modal fade" id="supplier_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-fw fa-gear"></i>供應商基本資料</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 供應商編號</label></div>
                                <div class="col-sm-4" id="show_display_number"></div>
                                <div class="col-sm-2"><label> 供應商簡稱</label></div>
                                <div class="col-sm-4" id="show_short_name"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 完整名稱</label></div>
                                <div class="col-sm-4" id="show_name"></div>
                                <div class="col-sm-2"><label> 付款條件</label></div>
                                <div class="col-sm-4" id="show_pay_condition_id"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 公司統編</label></div>
                                <div class="col-sm-4" id="show_company_number"></div>
                                <div class="col-sm-2"><label> 負責人名稱</label></div>
                                <div class="col-sm-4" id="show_contact_name"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 電子信箱</label></div>
                                <div class="col-sm-4" id="show_email"></div>
                                <div class="col-sm-2"><label> 聯絡電話</label></div>
                                <div class="col-sm-4" id="show_telephone"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 傳真號碼</label></div>
                                <div class="col-sm-4" id="show_fax"></div>
                                <div class="col-sm-2"><label> 手機號碼</label></div>
                                <div class="col-sm-4" id="show_cell_phone"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 地址</label></div>
                                <div class="col-sm-10" id="show_address"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 地址2</label></div>
                                <div class="col-sm-10" id="show_address2"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 地址3</label></div>
                                <div class="col-sm-10" id="show_address3"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 地址4</label></div>
                                <div class="col-sm-10" id="show_address4"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 地址5</label></div>
                                <div class="col-sm-10" id="show_address5"></div>
                            </div>
                            {{-- <div class="row form-group">
                                <div class="col-sm-2"><label> 收款銀行</label></div>
                                <div class="col-sm-4" id="bank_name"></div>
                                <div class="col-sm-2"><label> 支行名稱</label></div>
                                <div class="col-sm-4" id=""></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 銀行戶名</label></div>
                                <div class="col-sm-4" id=""></div>
                                <div class="col-sm-2"><label> 銀行帳號</label></div>
                                <div class="col-sm-4" id=""></div>
                            </div> --}}
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 備註</label></div>
                                <div class="col-sm-10" id="show_remark"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-fw fa-close"></i>
                        關閉</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- <script>
        $(document).ready(function() {
            var supplier_json = @json($supplier);
            $('.supplier_detail_show').click(function() {
                var supplier_id = $(this).data("supplier");
                $.each(supplier_json, function(index, val) {
                    if (supplier_id == val.id) {
                        $('#show_display_number').html(val.display_number);
                        $('#show_short_name').html(val.short_name);
                        $('#show_name').html(val.name);
                        $('#show_pay_condition_id').html(val.pay_condition_id);
                        $('#show_email').html(val.email);
                        $('#show_telephone').html(val.telephone);
                        $('#show_fax').html(val.fax);
                        $('#show_cell_phone').html(val.cell_phone);
                        $('#show_address').html(val.address);
                        $('#show_address2').html(val.address2);
                        $('#show_address3').html(val.address3);
                        $('#show_address4').html(val.address4);
                        $('#show_address5').html(val.address5);
                        $('#show_remark').html(val.address5);
                        $('#hideShowMod').trigger("click");
                        return false;
                    }
                });
            })
        });
    </script> --}}
@endsection
