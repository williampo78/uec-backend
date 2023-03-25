<!-- 進貨單明細 -->
<div class="modal fade" id="show_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 進貨單明細</h4>
            </div>
            <form id="productModal">
                <div class="modal-body">
                    <div id="show_blade_init">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row form-group">
                                    <div class="col-sm-2"><label> 進貨單號</label></div>
                                    <div class="col-sm-4">@{{ purchase.number }}</div>
                                    <div class="col-sm-2"><label> 供應商</label></div>
                                    <div class="col-sm-4">@{{ purchase.supplier_name }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2"><label> 進貨日期</label></div>
                                    <div class="col-sm-4">@{{ purchase.trade_date }}</div>
                                    <div class="col-sm-2"><label> 採購單號</label></div>
                                    <div class="col-sm-4">
                                        @{{ purchase . order_supplier_number }}
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2"><label> 稅別 </label></div>
                                    <div class="col-sm-4">
                                        @{{ purchase.txa_name }}
                                    </div>
                                    <div class="col-sm-2"><label> 幣別</label></div>
                                    <div class="col-sm-4">@{{ purchase.currency_code }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2"><label> 原幣稅額 </label></div>
                                    <div class="col-sm-4">@{{ purchase.original_total_tax_price }}</div>
                                    <div class="col-sm-2"><label> 原幣總金額</label></div>
                                    <div class="col-sm-4">@{{ purchase.original_total_price }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2"><label> 稅額 </label></div>
                                    <div class="col-sm-4">@{{ purchase.total_tax_price }}</div>
                                    <div class="col-sm-2"><label> 總金額 </label></div>
                                    <div class="col-sm-4">@{{ purchase.original_total_price }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2"><label> 發票地址</label></div>
                                    <div class="col-sm-4">@{{ purchase.invoice_address }}</div>
                                    <div class="col-sm-2"><label> </label></div>
                                    <div class="col-sm-4"></div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2"><label> 發票號碼 </label></div>
                                    <div class="col-sm-4">@{{ purchase.invoice_number }}</div>
                                    <div class="col-sm-2"><label> 發票日期</label></div>
                                    <div class="col-sm-4">@{{ purchase.invoice_date }}</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-2"><label> 備註</label></div>
                                    <div class="col-sm-4">@{{ purchase.remark }}</div>
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
                                            <th>POS品號</th>
                                            <th>商品名稱</th>
                                            <th>到期日</th>
                                            <th>庫別</th>
                                            <th>單價</th>
                                            <th>數量</th>
                                            <th>小計</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr  v-for="(detail, key) in purchaseDetail">
                                            <td>@{{detail.item_no}}</td>
                                            <td>@{{detail.pos_item_no}}</td>
                                            <td>@{{detail.combination_name}}</td>
                                            <td>@{{detail.expiry_date}}</td>
                                            <td>@{{detail.warehouse_name}}</td>
                                            <td>@{{detail.item_price}}</td>
                                            <td>@{{detail.item_qty}}</td>
                                            <td>@{{detail.original_subtotal_price}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><i
                            class="fa-solid fa-xmark"></i>
                        關閉視窗</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
