<!-- 請購單明細 -->
<div class="modal fade" id="row_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 請購單明細</h4>
            </div>
            <form id="productModal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 請購單號</label></div>
                                <div class="col-sm-4">@{{ requisitionsPurchase . number }}</div>
                                <div class="col-sm-2"><label> 供應商</label></div>
                                <div class="col-sm-4">@{{ requisitionsPurchase . supplier_name }}</div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 幣別</label></div>
                                <div class="col-sm-4">新台幣 (匯率：1)</div>
                                <div class="col-sm-2"><label> 狀態</label></div>
                                <div class="col-sm-4">
                                    <div v-if="requisitionsPurchase.status == 'DRAFTED'">草稿</div>
                                    <div v-else-if="requisitionsPurchase.status == 'REVIEWING'">簽核中</div>
                                    <div v-else-if="requisitionsPurchase.status == 'APPROVED'">已核准</div>
                                    <div v-else-if="requisitionsPurchase.status == 'REJECTED'">已駁回</div>
                                    <div v-else>狀態異常</div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 原幣稅額</label></div>
                                <div class="col-sm-4">@{{ requisitionsPurchase . original_total_tax_price }}
                                </div>
                                <div class="col-sm-2"><label> 原幣總金額</label></div>
                                <div class="col-sm-4">@{{ requisitionsPurchase . original_total_price }}</div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 稅額</label></div>
                                <div class="col-sm-4">@{{ requisitionsPurchase . total_tax_price }}</div>
                                <div class="col-sm-2"><label> 總金額</label></div>
                                <div class="col-sm-4">@{{ requisitionsPurchase . total_price }}</div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 備註</label></div>
                                <div class="col-sm-10">@{{ requisitionsPurchase . remark }}</div>
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
                                        <th>請購量</th>
                                        <th>單位</th>
                                        <th>小計</th>
                                        <th>贈品</th>
                                        <th>最小採購量</th>
                                    </tr>
                                </thead>
                                <tbody v-for="(item, itemKey) in requisitionsPurchaseDetail">
                                    <tr>
                                        <td>@{{item.item_number}}</td>
                                        <td>@{{item.combination_name}}</td>
                                        <td>@{{item.item_price}}</td>
                                        <td>@{{item.item_qty}}</td>
                                        <td>@{{item.uom}}</td>
                                        <td>@{{item.subtotal_price}}</td>
                                        <td>
                                            <div v-if="item.is_gift">是</div>
                                            <div v-else>否</div>
                                        </td>
                                        {{-- 最小出貨量 --}}
                                        <td>@{{item.min_purchase_qty}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{--  --}}
                    <h5>簽核紀錄</h5>
                    <div class="row" id="DivAddRow">
                        <div class="col-sm-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>次序 </th>
                                        <th>簽核人員 </th>
                                        <th>簽核時間 </th>
                                        <th>簽核結果 </th>
                                        <th>簽核備註 </th>
                                    </tr>
                                </thead>
                                <tbody v-for="(PurchaseReview, PurchaseReviewKey) in getRequisitionPurchaseReviewLog">
                                    <td>@{{PurchaseReview.seq_no}}</td>
                                    <td>@{{PurchaseReview.user_name}}</td>
                                    <td>@{{PurchaseReview.review_at}}</td>
                                    <td>
                                        <div v-if="PurchaseReview.review_result == 1">核准</div>
                                        <div v-else-if="PurchaseReview.review_result == 0">駁回</div>
                                        <div v-else>簽核中</div>
                                    </td>
                                    <td>@{{PurchaseReview.review_remark}}</td>
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
