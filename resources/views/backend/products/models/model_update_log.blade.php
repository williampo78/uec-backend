<div class="modal fade" id="model_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="width:100%;">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"> 修改紀錄 - {{ $products->product_no }}</h4>
                <input type='hidden' name="get_modal_id" id="get_modal_id" value="" />
            </div>
            <div class="row">
                <div class="col-sm-12">
                    {{-- <div class="panel panel-default">
                        <div class="panel-body">
                        </div>
                    </div> --}}

                    <!-- Table list -->
                    <div class="panel-body">

                        {{-- <br> --}}
                        <table class="table table-striped table-bordered table-hover" style="width:100%"
                            id="products_model_list" data-page-length='100'>
                            <thead>
                                <tr>
                                    <th style="width: 50%">修改時間</th>
                                    <th style="width: 50%">修改人員</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_audit_log as $val)
                                    <tr>
                                        <td>{{ $val->updated_at }}</td>
                                        <td>
                                            {{ $val->user_name }}
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i> 關閉
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
