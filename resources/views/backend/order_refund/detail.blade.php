<style>
.return-outer{ display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); color: #333333; gap: 2rem; }
.return-outer h5{ font-size: 15px; font-weight: bold;}
.list-content{ display: grid; grid-template-columns: 110px 1fr 110px 1fr; border: 1px solid #DDDDDD; border-radius: 2px; margin-bottom: 0;}
.list-content dt{ padding: 10px; background-color: #F5F5F5; border-bottom:  1px solid #DDDDDD;}
.list-content dd{ padding: 10px; border-bottom:  1px solid #DDDDDD;}
.border-none{ border: none!important;}
.column-full{ grid-column: 2/5;}
.red{ color: #FF0F0F;}
@media screen and (max-width: 1100px){
    .return-outer{ grid-template-columns: repeat(1,minmax(0,1fr)); }
}
</style>
<div class="modal fade" id="order_refund_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary" style="max-width: 1300px; margin: 0 auto;">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 退貨申請單管理 檢視資料</h4>
            </div>

            <div class="modal-body">
                <div class="return-outer">
                    <div>
                        <h5>退貨單資訊</h5>
                        <dl class="list-content">
                            <dt>退貨申請單號</dt>
                            <dd id="modal-request-no"></dd>
                            <dt>訂單編號</dt>
                            <dd id="modal-order-no"></dd>
                            <dt>退貨申請時間</dt>
                            <dd id="modal-request-date"></dd>
                            <dt>退貨單狀態</dt>
                            <dd>
                                <span id="modal-status-code"></span>
                                <span id="modal-prompt-text" class="red"></span>
                                <button id="manual-refund-button" type="button" class="btn btn-danger" data-toggle="modal" data-target="#manual-refund-modal" data-dismiss="modal">人工退款</button>
                            </dd>
                            <dt class="border-none">退貨結案時間</dt>
                            <dd id="modal-completed-at" class="border-none"></dd>
                            <dt class="border-none">物流方式</dt>
                            <dd id="modal-lgst-method" class="border-none"></dd>
                        </dl>
                    </div>
                    <div>
                        <h5>取件資訊</h5>
                        <dl class="list-content">
                            <dt>取件聯絡人</dt>
                            <dd id="modal-req-name"></dd>
                            <dt>取件聯絡手機</dt>
                            <dd id="modal-req-mobile"></dd>
                            <dt>取件聯絡電話</dt>
                            <dd id="modal-req-telephone" class="column-full"></dd>
                            <dt class="border-none">取件地址</dt>
                            <dd id="modal-req-fulladdress" class="border-none column-full"></dd>
                        </dl>
                    </div>
                    <div>
                        <h5>訂購人</h5>
                        <dl class="list-content">
                            <dt>會員編號</dt>
                            <dd id="modal-member-account" class="column-full"></dd>
                            <dt class="border-none">訂購人</dt>
                            <dd id="modal-buyer-name" class="border-none column-full"></dd>
                        </dl>
                    </div>
                    <div>
                        <h5>退貨說明</h5>
                        <dl class="list-content">
                            <dt>退貨原因</dt>
                            <dd id="modal-req-reason-description" class="column-full"></dd>
                            <dt class="border-none">退貨備註</dt>
                            <dd id="modal-req-remark" class="border-none column-full"></dd>
                        </dl>
                    </div>
                </div>

                <hr style="border-top: 1px solid gray;" />

                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab-order-detail" data-toggle="tab">退貨明細</a>
                    </li>
                    <li>
                        <a href="#tab-invoice-info" data-toggle="tab">退款資訊</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="tab-order-detail">
                        <div class="table-responsive">
                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                    <tr>

                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">功能</th>
                                        <th class="text-nowrap">退貨檢驗單號</th>
                                        <th class="text-nowrap">檢驗單狀態</th>
                                        <th class="text-nowrap">供應商</th>
                                        <th class="text-nowrap">派車確認時間</th>
                                        <th class="text-nowrap">派車物流</th>
                                        <th class="text-nowrap">
                                            <span id="number_or_logistics_name_column_name">物流單號</span>
                                        </th>
                                        <th class="text-nowrap">回件/檢驗回報時間</th>
                                        <th class="text-nowrap">回件/檢驗結果</th>
                                        <th class="text-nowrap">回件/檢驗結果說明</th>
                                        <th class="text-nowrap">協商結果</th>
                                        <th class="text-nowrap">協商退款金額</th>
                                        <th class="text-nowrap">協商內容備註</th>
                                    </tr>
                                </thead>
                                <tbody id="return_details_content">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-invoice-info">
                        <div class="table-responsive">
                            <table class='table table-striped table-bordered table-hover' style='width:100%'>
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">時間</th>
                                        <th class="text-nowrap">類型</th>
                                        <th class="text-nowrap">對象</th>
                                        <th class="text-nowrap">金額</th>
                                        <th class="text-nowrap">狀態</th>
                                        <th class="text-nowrap">金流方式</th>
                                        <th class="text-nowrap">備註</th>
                                    </tr>
                                </thead>
                                <tbody id="return_information_content">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> 關閉視窗
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
