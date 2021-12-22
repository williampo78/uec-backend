<!-- 報價單明細 -->
<div class="modal fade" id="row_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content modal-primary panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-fw fa-gear"></i> 報價單</h4>
                <input type='hidden' name="get_modal_id"  id="get_modal_id" value=""/>
            </div>
            <div id="ajaxHtmlappendthis">

            </div>
           
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
    function row_detail(id)
    {
        $('#DivAddRow').html("");
        var data_id = id;
        $("#get_modal_id").val(data_id);
        $.ajax(
            {
                url: "/backend/quotation/ajax",
                type: "POST",
                data: {"get_type":"showQuotation" , "id": data_id, _token:'{{ csrf_token() }}' },
                enctype: 'multipart/form-data',
            })
            .done(function( data )
            {
                $('#ajaxHtmlappendthis').html(data) ; 
            });
    }
</script>
