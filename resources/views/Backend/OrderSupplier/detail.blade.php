<!-- 報價單明細 -->
<div class="modal fade" id="row_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 採購單明細</h4>
                <input type='hidden' name="get_modal_id" id="get_modal_id" value="" />
            </div>
            <form id="productModal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-2 text-right"><label> 採購單號</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . number }}</div>
                                    <div class="col-sm-2 text-right"><label> 供應商</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . supplier_name }}</div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="form-group">
                                    <div class="col-sm-2 text-right"><label> 幣別</label></div>
                                    <div class="col-sm-4">新台幣</div>
                                    <div class="col-sm-2 text-right"><label> 狀態</label></div>
                                    <div class="col-sm-4">
                                        <div v-if="show_supplier.status == 'DRAFTED'">草稿</div>
                                        <div v-else-if="show_supplier.status == 'REVIEWING'">簽核中</div>
                                        <div v-else-if="show_supplier.status == 'APPROVED'">已核准</div>
                                        <div v-else-if="show_supplier.status == 'REJECTED'">已駁回</div>
                                        {{-- status --}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="form-group">
                                    <div class="col-sm-2 text-right"><label> 原幣稅額</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . original_total_tax_price }}</div>
                                    <div class="col-sm-2 text-right"><label> 原幣總金額</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . original_total_price }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-2 text-right"><label> 稅額</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . total_tax_price }}</div>
                                    <div class="col-sm-2 text-right"><label> 總金額</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . total_price }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-2 text-right"><label> 採購單號</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . requisitions_purchase_number }}
                                    </div>
                                    <div class="col-sm-2 text-right"><label> 發票抬頭</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . invoice_name }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-2 text-right"><label> 發票統編</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . invoice_company_number }}</div>
                                    <div class="col-sm-2 text-right"><label> 發票地址</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . invoice_address }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-2 text-right"><label> 備註</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . remark }}</div>
                                    <div class="col-sm-2 text-right"><label> 倉庫</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . warehouse_name }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-2 text-right"><label> 廠商交貨日</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . supplier_deliver_date }}</div>
                                    <div class="col-sm-2 text-right"><label> 預計進貨日</label></div>
                                    <div class="col-sm-4">@{{ show_supplier . expect_deliver_date }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- 品項 --}}
                    <h5>品項</h5>
                    <div class="row" id="DivAddRow">
                        <div class="col-sm-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>商品編號</th>
                                        <th>商品名稱</th>
                                        <th>單價</th>
                                        <th>單位</th>
                                        <th>小計</th>
                                        <th>贈品</th>
                                        <th>最小採購量</th>
                                        <th>進貨量</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(detail, index) in show_detail">
                                        <td>@{{detail.item_no}}</td>
                                        <td>@{{detail.combination_name}}</td>
                                        <td>@{{detail.item_price}}</td>
                                        <td>@{{detail.uom}}</td>
                                        <td>@{{detail.subtotal_price}}</td>
                                        <td>
                                            <div v-if="detail.is_giveaway">
                                                是
                                            </div>
                                            <div v-else>
                                                否
                                            </div>
                                        </td>
                                        <td>@{{detail.min_purchase_qty}}</td>
                                        <td>@{{detail.purchase_qty}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-fw fa-close"></i>
                        關閉視窗</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
