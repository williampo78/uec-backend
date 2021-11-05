      {{-- modal div --}}
      <div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
          aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content modal-primary panel-primary">
                  <div class="modal-header panel-heading">
                      <button type="button" class="close" data-dismiss="modal"
                          aria-hidden="true">&times;</button>
                      <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 補登預進日</h4>
                      <input type='hidden' name="get_modal_id" id="get_modal_id" value="" />
                  </div>
                  <form id="productModal">
                      <div class="modal-body">
                          <div class="row">
                                <div class="col-sm-12 text-left"><label> 分類名稱</label></div>

                                  <div class="col-sm-2 text-left"><label> 分類名稱</label></div>
                                  <br>
                                  <div class="col-sm-4">
                                      <input name="receiver_name" id="receiver_name" value="" class="form-control">
                                  </div>
                              {{-- <div class="col-sm-4">
                                  <div class="form-group"><label for="receiver_name">收件人名稱</label> <input
                                          name="receiver_name" id="receiver_name" value="" class="form-control"></div>
                              </div> --}}
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-success" onclick="saveDate();" data-dismiss="modal"><i
                                  class="fa fa-fw fa-save"></i> 儲存並關閉</button>
                          <button type="button" class="btn btn-warning" data-dismiss="modal"><i
                                  class="fa fa-fw fa-close"></i>
                              關閉視窗</button>
                      </div>
                  </form>
              </div>
              <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
      </div>
