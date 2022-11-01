<div class="row">
    <div class="col-sm-12">
        <div class="row form-group">
            <div class="col-sm-2"><label> 進貨單號</label></div>
            <div class="col-sm-4">{{ $purchase->number }}</div>
            <div class="col-sm-2"><label> 供應商</label></div>
            <div class="col-sm-4">{{ $purchase->supplier_name }}</div>
        </div>
        <div class="row form-group">
            <div class="col-sm-2"><label> 進貨日期</label></div>
            <div class="col-sm-4">{{ $purchase->trade_date }}</div>
            <div class="col-sm-2"><label> 採購單號</label></div>
            <div class="col-sm-4">
                {{ $purchase->order_supplier_number }}
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm-2"><label> 稅別 </label></div>
            <div class="col-sm-4">
                {{ isset(config('uec.tax_option')[$purchase->tax]) ? config('uec.tax_option')[$purchase->tax] : 'error' }}
            </div>
            <div class="col-sm-2"><label> 幣別</label></div>
            <div class="col-sm-4">{{ $purchase->currency_code }}</div>
        </div>
        <div class="row form-group">
            <div class="col-sm-2"><label> 原幣稅額 </label></div>
            <div class="col-sm-4">{{ $purchase->original_total_tax_price }}</div>
            <div class="col-sm-2"><label> 原幣總金額</label></div>
            <div class="col-sm-4">{{ $purchase->original_total_price }}</div>
        </div>
        <div class="row form-group">
            <div class="col-sm-2"><label> 稅額 </label></div>
            <div class="col-sm-4">{{ $purchase->total_tax_price }}</div>
            <div class="col-sm-2"><label> 總金額 </label></div>
            <div class="col-sm-4">{{ $purchase->original_total_price }}</div>
        </div>
        <div class="row form-group">
            <div class="col-sm-2"><label> 發票地址</label></div>
            <div class="col-sm-4">{{ $purchase->invoice_address }}</div>
            <div class="col-sm-2"><label> </label></div>
            <div class="col-sm-4"></div>
        </div>
        <div class="row form-group">
            <div class="col-sm-2"><label> 發票號碼 </label></div>
            <div class="col-sm-4">{{ $purchase->invoice_number }}</div>
            <div class="col-sm-2"><label> 發票日期</label></div>
            <div class="col-sm-4">{{ $purchase->invoice_date }}</div>
        </div>
        <div class="row form-group">
            <div class="col-sm-2"><label> 備註</label></div>
            <div class="col-sm-4">{{ $purchase->remark }}</div>
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
                    <th>POS品號</th>
                    <th>商品名稱</th>
                    <th>到期日</th>
                    <th>庫別</th>
                    <th>單價</th>
                    <th>數量</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase_detail as $key => $val)
                    <tr>
                        {{-- 商品編號 --}}
                        <td>{{$val->item_no}}</td>
                        {{-- POS品號 --}}
                        <td>{{$val->pos_item_no}}</td>
                        {{-- 商品名稱 --}}
                        <td>{{$val->combination_name}}</td>
                        {{-- 到期日 --}}
                        <td>{{$val->expiry_date}}</td>
                        {{-- 庫別 --}}
                        <td>{{$val->warehouse_name}}</td>
                        {{-- 單價 --}}
                        <td>{{$val->item_price}}</td>
                        {{-- 數量 --}}
                        <td>{{$val->item_qty}}</td>
                        {{-- 小計 --}}
                        <td>{{$val->original_subtotal_price}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
