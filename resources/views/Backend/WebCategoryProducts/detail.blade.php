<!-- 使用者明細 -->
<div class="modal fade" id="row_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-user"></i> 使用者</h4>
                <input type='hidden' name="get_modal_id"  id="get_modal_id" value=""/>
            </div>
            <form id="productModal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 帳號</label></div>
                                <div class="col-sm-2" id="ModalUserAccount"></div>
                                <div class="col-sm-2"><label> 名稱</label></div>
                                <div class="col-sm-2" id="ModalUserName"></div>
                                <div class="col-sm-2"><label> 狀態</label></div>
                                <div class="col-sm-2" id="ModalUserStatus"></div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> e-Mail</label></div>
                                <div class="col-sm-2" id="ModalUserEmail"></div>
                                <div class="col-sm-2"><label> 供應商</label></div>
                                <div class="col-sm-6" id="ModalSupplier"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <div class="row form-group">
                            <div class="col-sm-2"><label> 授權角色</label></div>
                            <div class="col-sm-10" id="ModalUserRoles"></div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-fw fa-close"></i> 關閉視窗</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>

</script>
