<div class="modal fade" :id="modal.review.id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> @{{ modal.review.title }}</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">退出單號</label>
                            <p class="form-control-static">@{{ modal.review.requestNo }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">庫別</label>
                            <p class="form-control-static">@{{ modal.review.warehouseName }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">申請總量</label>
                            <p class="form-control-static">@{{ modal.review.expectedQty }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">申請時間</label>
                            <p class="form-control-static">@{{ modal.review.requestDate }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">送審時間</label>
                            <p class="form-control-static">@{{ modal.review.submittedAt }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">預計出庫日</label>
                            <p class="form-control-static">@{{ modal.review.expectedDate }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">稅別</label>
                            <p class="form-control-static">@{{ modal.review.tax }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">申請稅額</label>
                            <p class="form-control-static">@{{ modal.review.expectedTaxAmount }}</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">申請總金額</label>
                            <p class="form-control-static">@{{ modal.review.expectedAmount }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">備註</label>
                            <p class="form-control-static" style="white-space: pre-wrap">@{{ modal.review.remark }}</p>
                        </div>
                    </div>
                </div>
                <hr style="border-top: 1px solid gray;">

                <form id="review-form" class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">簽核清單 <span class="text-red">*</span></label>
                                </div>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <button type="button" class="btn btn-primary" @click="checkAll">
                                                <i class="fa-solid fa-check"></i> 全勾選
                                            </button>
                                            <button type="button" class="btn btn-primary" @click="cancelAll">
                                                <i class="fa-solid fa-xmark"></i> 全取消
                                            </button>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-nowrap"></th>
                                                            <th class="text-nowrap">項次</th>
                                                            <th class="text-nowrap">供應商</th>
                                                            <th class="text-nowrap">申請總量</th>
                                                            <th class="text-nowrap">申請總金額</th>
                                                            <th class="text-nowrap"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(supplier, index) in modal.review.suppliers" :key="index">
                                                            <td class="text-center">
                                                                <input type="checkbox" style="width: 20px; height: 20px; cursor: pointer;" v-model="supplier.checked">
                                                            </td>
                                                            <td>@{{ index + 1 }}</td>
                                                            <td>@{{ supplier.name }}</td>
                                                            <td>@{{ supplier.expectedQty }}</td>
                                                            <td>@{{ supplier.expectedAmount }}</td>
                                                            <td>
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    @click="viewSupplierDetail(supplier.id, supplier.name)">
                                                                    明細
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">簽核結果 <span class="text-red">*</span></label>
                                </div>
                                <div class="col-sm-10">
                                    <label class="radio-inline">
                                        <input type="radio" name="reviewResult" value="APPROVE"
                                            v-model="modal.review.form.reviewResult">核准
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="reviewResult" value="REJECT"
                                            v-model="modal.review.form.reviewResult">駁回
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">簽核備註</label>
                                </div>
                                <div class="col-sm-10">
                                    <textarea class="form-control" rows="5" name="reviewRemark" v-model="modal.review.form.reviewRemark"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" @click="saveReviewResult">
                    儲存
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
