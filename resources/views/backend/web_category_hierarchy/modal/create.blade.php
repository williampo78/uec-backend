<div class="modal fade" :id="modal.create.id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                    <i class="fa-solid fa-gear"></i> @{{ modal.create.title }}
                </h4>
            </div>

            <div class="modal-body">
                <form id="create-form" class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label>分類層級</label>
                                </div>
                                <div class="col-sm-10">
                                    @{{ modal.create.levelTitle }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">
                                        分類名稱 <span class="text-red">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="categoryName" v-model="modal.create.categoryName">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="modal.create.categoryLevel < 2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">
                                        毛利門檻 <span class="text-red">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="grossMarginThreshold" min="0.00" max="100.00" step="0.01"
                                            v-model="modal.create.grossMarginThreshold">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <p class="form-control-static text-primary">低於此門檻者為低毛商品</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="modal.create.categoryLevel < 2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">
                                        (漢堡) 短名稱 <span class="text-red">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="categoryShortName" v-model="modal.create.categoryShortName">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="modal.create.categoryLevel < 2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">(漢堡) 圖檔</label>
                                </div>
                                <div class="col-sm-10">
                                    <div class="row" v-show="modal.create.icon.showInputFile">
                                        <div class="col-sm-12">
                                            <input
                                                type="file"
                                                ref="icon"
                                                accept="image/*"
                                                @change="onIconChange"
                                            >
                                            <br>
                                            <p>檔案大小不可超過1MB，副檔名須為JPG、JPEG、PNG</p>
                                            <p>圖檔比例須為1:1，至少須為96 * 96</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <img :src="modal.create.icon.url" width="100%">
                                        </div>
                                        <div class="col-sm-1" v-if="modal.create.icon.showDeleteButton">
                                            <button type="button" class="btn btn-danger" @click="deleteIcon()" title="刪除">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" @click="submitCreateForm()">
                    <i class="fa-solid fa-floppy-disk"></i> 儲存
                </button>

                <button type="button" class="btn btn-danger hidden-model" data-dismiss="modal">
                    <i class="fa-solid fa-ban"></i> 取消
                </button>
            </div>
        </div>
    </div>
</div>
