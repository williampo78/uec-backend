<!-- 請購單明細 -->
<div class="modal fade" id="show_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 請購單明細</h4>
            </div>
            <form id="productModal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 請購單號</label></div>
                                <div class="col-sm-4">1</div>
                                <div class="col-sm-2"><label> 供應商</label></div>
                                <div class="col-sm-4">1</div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 幣別</label></div>
                                <div class="col-sm-4">1</div>
                                <div class="col-sm-2"><label> 狀態</label></div>
                                <div class="col-sm-4">
                                    <div class="col-sm-4">1</div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 原幣稅額</label></div>
                                <div class="col-sm-4">1</div>
                                <div class="col-sm-2"><label> 原幣總金額</label></div>
                                <div class="col-sm-4">1</div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 稅額</label></div>
                                <div class="col-sm-4">1</div>
                                <div class="col-sm-2"><label> 總金額</label></div>
                                <div class="col-sm-4">1</div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2"><label> 備註</label></div>
                                <div class="col-sm-4">1</div>
                            </div>
                        </div>
                    </div>
                    {{-- 品項 --}}
                    <h5>品項</h5>
                    <div class="row" id="DivAddRow">
                        <div class="col-sm-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>商品編號</th>
                                        <th>商品名稱</th>
                                        <th>單價</th>
                                        <th>請購量</th>
                                        <th>單位</th>
                                        <th>小計</th>
                                        <th>贈品</th>
                                        <th>最小採購量</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>1</td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-fw fa-close"></i>
                        關閉視窗</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
