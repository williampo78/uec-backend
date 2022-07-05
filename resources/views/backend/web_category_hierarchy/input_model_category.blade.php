{{-- modal div --}}
<div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa-solid fa-gear"></i>
                    <span v-if="addCategory.act == 'edit'"> 編輯分類 </span>
                    <span v-else>新增分類</span>
                </h4>
                <input type='hidden' name="get_modal_id" id="get_modal_id" value="" />
            </div>
            <form id="webCategoryHierarchyModal">
                <div class="form-horizontal modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label> 分類層級</label>
                                </div>
                                <div class="col-sm-10">
                                    @{{ addCategory.show_title }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="addCategory.act == 'edit'">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2 "><label class="control-label"> 分類原名稱</label></div>
                                <div class="col-sm-4 ">
                                    <input v-model="addCategory.old_category_name" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2 "><label class="control-label">分類名稱<span class="text-red">*</span></label></div>
                                <div class="col-sm-4 ">
                                    <input name="receiver_name" id="receiver_name" v-model="addCategory.category_name"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="addCategory.category_level == '1'">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2 "><label class="control-label"> 毛利門檻<span
                                            class="text-red">*</span></label></div>
                                <div class="col-sm-2 ">
                                    <input name="receiver_name" id="gross_margin_threshold" type="number" min="0.00" max="100.00" step="0.01"
                                        v-model="addCategory.gross_margin_threshold" class="form-control">
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label">
                                        <p>%</p>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <label class="control-label">
                                        <p style="color:#337ab7;">低於此門檻者為低毛商品</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="addCategory.act == 'edit'">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2 "><label class="control-label">(漢堡)原短名稱<span
                                            class="text-red">*</span></label></div>
                                <div class="col-sm-2 ">
                                    <input name="old_category_short_name" id="old_category_short_name"
                                        v-model="addCategory.old_category_short_name" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="addCategory.category_level == '1'">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2 "><label class="control-label">(漢堡)短名稱<span
                                            class="text-red">*</span></label></div>
                                <div class="col-sm-2 ">
                                    <input name="category_short_name" id="category_short_name"
                                        v-model="addCategory.category_short_name" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="addCategory.category_level == '1'">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">(漢堡) 圖檔</label>
                                </div>
                                <div class="col-sm-7">
                                    <input type="file" @change="uploadFile" multiple accept="image/*" :disabled="addCategory.icon_name !== null">
                                    <input type="file"  :ref="'images_files'" id="icon_name_file" name="icon_name_file" style="display: none;">
                                    <p class="help-block">檔案大小不可超過1MB，副檔名須為JPG、JPEG、PNG</p>
                                    <p class="help-block">圖檔比例須為1:1，至少須為96 * 96</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="addCategory.category_level == '1'">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="control-label">(漢堡) 圖檔預覽</label>
                                </div>
                                <div class="col-sm-3">
                                    <img :src="showPhotoSrc" style="max-width: 40%;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display: none;">
                        <div class="col-sm-2 "><label> 內容類型</label></div>
                        <div class="col-sm-4 ">
                            <select class="form-control js-select2" disabled name="content_type" id="content_type" v-model="addCategory.content_type">
                                <option value="P" selected>指定商品</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="CategoryToList()">
                        <span v-if="addCategory.act == 'edit'"> 編輯 </span>
                        <span v-else>新增</span>
                    </button>
                    <button type="button" class="btn btn-danger hidden-model" data-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i> 關閉
                    </button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
