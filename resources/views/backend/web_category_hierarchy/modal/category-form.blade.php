<div class="modal fade" :id="modal.categoryForm.id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">
                    <i class="fa-solid fa-gear"></i> @{{ modal.categoryForm.title }}
                </h4>
            </div>

            <div class="modal-body">
                <form id="category-form" class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">分類層級</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static" v-if="modal.categoryForm.categoryLevel == 1">
                                        @{{ modal.categoryForm.levelTitle }}
                                    </p>
                                    <p class="form-control-static" v-else-if="modal.categoryForm.categoryLevel == 2">
                                        【<span class="text-primary">@{{ modal.categoryForm.levelTitle }}</span>】的中分類
                                    </p>
                                    <p class="form-control-static" v-else-if="modal.categoryForm.categoryLevel == 3">
                                        【<span class="text-primary">@{{ modal.categoryForm.levelTitle }}</span>】的小分類
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="modal.categoryForm.mode == 'edit'">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">分類原名稱</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.categoryForm.originalCategoryName }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">
                                        分類名稱 <span class="text-red" v-show="modal.categoryForm.mode == 'create'">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="categoryName" v-model="modal.categoryForm.categoryName">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="modal.categoryForm.categoryLevel < 2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">
                                        毛利門檻 <span class="text-red">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="grossMarginThreshold" min="0" max="100" step="0.01"
                                            v-model="modal.categoryForm.grossMarginThreshold">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <p class="form-control-static text-primary">低於此門檻者為低毛商品</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="modal.categoryForm.categoryLevel < 2 && modal.categoryForm.mode == 'edit'">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">(漢堡) 原短名稱</label>
                                </div>
                                <div class="col-sm-10">
                                    <p class="form-control-static">@{{ modal.categoryForm.originalCategoryShortName }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="modal.categoryForm.categoryLevel < 2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">
                                        (漢堡) 短名稱 <span class="text-red" v-show="modal.categoryForm.mode == 'create'">*</span>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="categoryShortName" v-model="modal.categoryForm.categoryShortName">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="modal.categoryForm.categoryLevel < 2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">(漢堡) 圖檔</label>
                                </div>
                                <div class="col-sm-10">
                                    <div class="row" v-show="modal.categoryForm.icon.showInputFile">
                                        <div class="col-sm-12">
                                            <input
                                                type="file"
                                                name="icon"
                                                ref="icon"
                                                :accept="ICON_MIME"
                                                :data-image-width="modal.categoryForm.icon.width"
                                                :data-image-height="modal.categoryForm.icon.height"
                                                @change="onIconChange"
                                            >
                                            <p>檔案大小不可超過1MB，副檔名須為JPG、JPEG、PNG</p>
                                            <p>圖檔比例須為1:1，至少須為96 * 96</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <img :src="modal.categoryForm.icon.url" width="100%">
                                        </div>
                                        <div class="col-sm-1" v-if="modal.categoryForm.icon.showDeleteButton">
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
                <button type="button" class="btn btn-success" @click="submitCategoryForm()">
                    <i class="fa-solid fa-floppy-disk"></i> 儲存
                </button>

                <button type="button" class="btn btn-danger hidden-model" data-dismiss="modal">
                    <i class="fa-solid fa-ban"></i> 取消
                </button>
            </div>
        </div>
    </div>
</div>
