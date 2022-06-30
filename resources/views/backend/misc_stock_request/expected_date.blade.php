<div class="modal fade" :id="modal.expectedDate.id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i> @{{ modal.expectedDate.title }}</h4>
            </div>

            <div class="modal-body">
                <form id="expected-date-form" class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">退出單號</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.expectedDate.requestNo }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">預計出庫日 <span class="text-red">*</span></label>
                                </div>
                                <div class="col-sm-10">
                                    <vue-flat-pickr :setting="modal.expectedDate.expectedDate"></vue-flat-pickr>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">【物流資訊】收件人 <span class="text-red">*</span></label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="shipToName" v-model="modal.expectedDate.shipToName">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">【物流資訊】聯絡電話 <span class="text-red">*</span></label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="shipToMobile" v-model="modal.expectedDate.shipToMobile">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">【物流資訊】地址 <span class="text-red">*</span></label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="shipToAddress" v-model="modal.expectedDate.shipToAddress">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" @click="saveExpectedDate">
                    儲存
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
