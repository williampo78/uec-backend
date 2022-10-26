<div class="modal fade" id="model_promotional" tabindex="-1" role="dialog" aria-labelledby="model_promotional"
    aria-hidden="true">
    <div class="modal-dialog" style="width:70%;max-width: 800px;">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"> 促銷活動 - {{ $products->product_no }}</h4>
                <input type='hidden' name="get_modal_id" id="get_modal_id" value="" />
            </div>
            <div class="row">
                <div class="col-sm-12">
                    {{-- <div class="panel panel-default">
                        <div class="panel-body">
                        </div>
                    </div> --}}

                    <!-- Table list -->
                    <div class="panel-body" style="max-height: 435px; overflow-y: auto;">

                        {{-- <br> --}}
                        <table class="table table-striped table-bordered table-hover" style="width:100%"
                            id="products_model_list" data-page-length='100'>
                            <thead>
                                <tr>
                                    <th>活動時間</th>
                                    <th>活動名稱</th>
                                    <th>活動ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($promotional_log as $obj)
                                    <tr>
                                        <td>{{$obj->start_at }} ~ {{$obj->end_at }}</td>
                                        <td>
                                            {{ $obj->campaign_name }}
                                        </td>
                                        <td>
                                            {{ $obj->id}}
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
