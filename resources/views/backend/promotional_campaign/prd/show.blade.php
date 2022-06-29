<div class="modal fade" id="prd-campaign-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> 單品活動 檢視資料</h4>
            </div>

            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">活動名稱</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static">@{{ modal.campaignName }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">狀態</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static">@{{ modal.active }}</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">活動類型</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static">@{{ modal.campaignType }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label class="control-label">N (滿額) = </label>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">@{{ modal.nValue }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6" v-if="modal.showXValue">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label class="control-label">X (折扣) = </label>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="form-control-static">@{{ modal.xValue }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">上架時間起</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static">@{{ modal.startAt }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">上架時間訖</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static">@{{ modal.endAt }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">前台文案</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static">@{{ modal.campaignBrief }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid gray;" />
                </div>

                <div class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="control-label">適用對象</label>
                                </div>
                                <div class="col-sm-9">
                                    <p class="form-control-static">@{{ modal.targetGroups }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid gray;" />
                </div>

                <div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="control-label">單品清單</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class='table table-striped table-bordered table-hover' style='width:100%'>
                            <thead>
                                <tr>
                                    <th class="text-nowrap">項次</th>
                                    <th class="text-nowrap">商品序號</th>
                                    <th class="text-nowrap">商品名稱</th>
                                    <th class="text-nowrap">售價(含稅)</th>
                                    <th class="text-nowrap">上架日期</th>
                                    <th class="text-nowrap">上架狀態</th>
                                    <th class="text-nowrap">毛利(%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, index) in modal.products" :key="index">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ product.productNo }}</td>
                                    <td>@{{ product.productName }}</td>
                                    <td>@{{ product.sellingPrice }}</td>
                                    <td>@{{ product.launchedAt }}</td>
                                    <td>@{{ product.launchedStatus }}</td>
                                    <td>@{{ product.grossMargin }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr style="border-top: 1px solid gray;" />
                </div>

                <div v-if="modal.showGiveaway">
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="control-label">贈品清單</label>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class='table table-striped table-bordered table-hover' style='width:100%'>
                            <thead>
                                <tr>
                                    <th class="text-nowrap">項次</th>
                                    <th class="text-nowrap">商品序號</th>
                                    <th class="text-nowrap">商品名稱</th>
                                    <th class="text-nowrap">贈品數量</th>
                                    <th class="text-nowrap">庫存數</th>
                                    <th class="text-nowrap">上架狀態</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(giveaway, index) in modal.giveaways" :key="index">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ giveaway.productNo }}</td>
                                    <td>@{{ giveaway.productName }}</td>
                                    <td>@{{ giveaway.assignedQty }}</td>
                                    <td>@{{ giveaway.stockQty }}</td>
                                    <td>@{{ giveaway.launchStatus }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr style="border-top: 1px solid gray;" />
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> 關閉視窗
                </button>
            </div>
        </div>
    </div>
</div>
