<div class="modal fade" :id="modal.review.id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> @{{ modal.review.title }}</h4>
            </div>

            <div class="modal-body">
                <dl class="list-content d-block d-md-grid">
                    <dt>退出單號</dt>
                    <dd>@{{ modal.review.requestNo }}</dd>
                    <dt>庫別</dt>
                    <dd>@{{ modal.review.warehouseName }}</dd>
                    <dt>申請總量</dt>
                    <dd>@{{ modal.review.expectedQty }}</dd>
                    <dt>申請時間</dt>
                    <dd>@{{ modal.review.requestDate }}</dd>
                    <dt>送審時間</dt>
                    <dd>@{{ modal.review.submittedAt }}</dd>
                    <dt>預計出庫日</dt>
                    <dd>@{{ modal.review.expectedDate }}</dd>
                    <dt>稅別</dt>
                    <dd>@{{ modal.review.tax }}</dd>
                    <dt>申請稅額</dt>
                    <dd>@{{ modal.review.expectedTaxAmount }}</dd>
                    <dt>申請總金額</dt>
                    <dd>@{{ modal.review.expectedAmount }}</dd>
                    <dt class="border-none">備註</dt>
                    <dd class="column-full border-none">@{{ modal.review.remark }}</dd>
                </dl>

                <hr style="border-top: 1px solid gray;">

                <form id="review-form" class="form-horizontal">
                    <div class="check-content block">
                        <label class="label-title">簽核清單 <span class="text-red">*</span></label>
                        <div class="label-content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="radio-inline">
                                        <input type="radio" name="reviewResult" value="CHECK" @change="checkAll">全勾選
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="reviewResult" value="CANCEL" @change="cancelAll">全取消
                                    </label>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped" style="width:100%">
                                            <thead>
                                                <tr style="border: 1px solid #ddd; border-radius: 5px;">
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
                        <label class="label-title">簽核結果 <span class="text-red">*</span></label>
                        <div class="label-content">
                            <label class="radio-inline">
                                <input type="radio" name="reviewResult" value="APPROVE"
                                    v-model="modal.review.form.reviewResult">核准
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="reviewResult" value="REJECT"
                                    v-model="modal.review.form.reviewResult">駁回
                            </label>
                        </div>
                        <label class="label-title border-none">簽核備註</label>
                        <div class="label-content border-none">
                            <textarea class="form-control" rows="5" name="reviewRemark" v-model="modal.review.form.reviewRemark"></textarea>
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
