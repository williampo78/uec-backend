{{-- modal div --}}
<div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
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
            <form id="productModal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <label> 分類層級</label>
                        </div>
                        <div class="col-sm-10">
                            @{{ addCategory.show_title }}
                        </div>
                        {{-- <div class="col-sm-4">
                            <div class="form-group"><label for="receiver_name">收件人名稱</label> <input
                                    name="receiver_name" id="receiver_name" value="" class="form-control"></div>
                        </div> --}}
                    </div>
                    <br>
                    <div v-if="addCategory.act == 'edit'">
                        <div class="row">
                            <div class="col-sm-2 "><label> 分類原名稱</label></div>
                            <div class="col-sm-4 ">
                                <input v-model="addCategory.old_category_name" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-2 "><label> 分類名稱</label></div>
                        <div class="col-sm-4 ">
                            <div class="form-group">
                                <input name="receiver_name" id="receiver_name" v-model="addCategory.category_name"
                                    class="form-control">
                                {{-- <span class="help-block">@{{ msg.receiver_name }}</span> --}}
                            </div>
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
