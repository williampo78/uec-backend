<div id="prd-block">
    <div class="row">
        <div class="col-sm-1">
            <p>單品清單</p>
        </div>
        <div class="col-sm-2">
            <button type="button" id="btn-new-prd" class="btn btn-warning btn-sm">
                <i class="fa fa-plus"></i> 新增單品
            </button>
        </div>
    </div>
    <br/>

    <div class="table-responsive">
        <table class='table table-striped table-bordered table-hover' style='width:100%'>
            <thead>
                <tr>
                    <th class="text-nowrap">項次</th>
                    <th class="text-nowrap">商品序號</th>
                    <th class="text-nowrap">商品名稱</th>
                    <th class="text-nowrap">售價(含稅)</th>
                    <th class="text-nowrap">上架日期</th>
                    <th class="text-nowrap">上架狀態</th>
                    <th class="text-nowrap">毛利(%)</th>
                    <th class="text-nowrap">功能</th>
                </tr>
            </thead>
            <tbody>
                <input type="hidden" id="prd-block-row-no" value="0" />
            </tbody>
        </table>
    </div>

    <hr style="border-top: 1px solid gray;" />
</div>
