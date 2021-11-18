@extends('Backend.master')

@section('title', '供應商資料')

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-truck"></i>供應商資料</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <div class="row">
                            @if($share_role_auth['auth_create'])
                            <div class="col-sm-2">
                                <a href="{{ route('supplier') }}/create" class="btn btn-block btn-warning btn-sm"
                                    id="btn-new"><i class="fa fa-plus"></i>
                                    新增</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div id="table_list_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr role="row">
                                        <th class="col-sm-1 ">功能</th>
                                        <th class="col-sm-1 ">編號</th>
                                        <th class="col-sm-1 ">統編</th>
                                        <th class="col-sm-1 ">簡稱</th>
                                        <th class="col-sm-1 ">名稱</th>
                                        <th class="col-sm-1 ">付款條件</th>
                                        <th class="col-sm-1 ">電話</th>
                                        <th class="col-sm-1 ">地址</th>
                                        <th class="col-sm-1 ">備註</th>
                                        <th class="col-sm-1 ">顯示</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supplier as $obj)
                                        <tr>
                                            <td>
                                                <button class="btn btn-info btn-sm supplier_detail_show"
                                                    data-supplier="{{ $obj->id }}">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                <button data-toggle="modal" id="hideShowMod" style="display:none;"
                                                data-target="#supplier_detail">Click me</button>

                                                @if($share_role_auth['auth_update'])
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('supplier') }}/{{ $obj->id }}/edit" value="1">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                @endif
                                            </td>
                                            <td>{{ $obj->display_number }}</td>
                                            <td>{{ $obj->company_number }}</td>
                                            <td>{{ $obj->short_name }}</td>
                                            <td>{{ $obj->name }}</td>
                                            <td>{{-- $obj->number->pay_condition_id 需要left id --}}</td>
                                            <td>{{ $obj->telephone }}</td>
                                            <td>{{ $obj->address }}</td>
                                            <td>{{ $obj->remark }}</td>
                                            <td>{{ $obj->active }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
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
@section('js')
    <script>
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
    </script>
@endsection
@endsection
