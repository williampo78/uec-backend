<form id="productModal">
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="row form-group">
                    <div class="col-sm-1"><label> 報價單號</label></div>
                    <div class="col-sm-3">{{ $quotation->doc_number }}</div>
                    <div class="col-sm-1"><label> 供應商</label></div>
                    <div class="col-sm-3">{{ $quotation->supplier_name }}</div>
                    <div class="col-sm-1"><label> 狀態</label></div>
                    <div class="col-sm-3">{{ $quotation->status_code }}</div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-1"><label> 幣別</label></div>
                    <div class="col-sm-3">{{ $quotation->currency_code }}</div>
                    <div class="col-sm-1"><label> 匯率</label></div>
                    <div class="col-sm-3">{{ $quotation->exchange_rate }}</div>
                    <div class="col-sm-1"><label> 稅別</label></div>
                    <div class="col-sm-3">{{ $taxlist[$quotation->tax] ?? 'error' }}
                        {{ $quotation->tax != 2 ? '' : ($quotation->is_tax_included == 1 ? ' (含稅價)' : ' (未稅價)') }}
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-sm-1"><label> 備註</label></div>
                    <div class="col-sm-10">{{ $quotation->remark }}</div>
                </div>
            </div>
        </div>
        <div id="DivAddRow">
            <div class='col-ms-12'>
                <table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>
                    <thead>
                        <tr>
                            <th>商品編號</th>
                            <th>商品名稱</th>
                            <th>POS品號</th>
                            <th>單價</th>
                            <th>最小採購量</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quotationDetails as $val)
                            <tr>
                                <td>{{ $val->product_items_no }}</td>
                                <td>
                                    @if (!$val->combination_name)
                                        <span class="redtext">該商品異常</span>
                                    @else
                                        {{ $val->combination_name }}
                                    @endif
                                </td>
                                <td>{{ $val->ean }}</td>
                                <td>{{ $val->original_unit_price }}</td>
                                <td>{{ $val->min_purchase_qty }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            <label>簽核紀錄</label>
            <div class='col-ms-12'>
                <table class='table table-striped table-bordered table-hover' style='width:100%' id='table_list2'>
                    <thead>
                        <tr>
                            <th>次序</th>
                            <th>簽核人員</th>
                            <th>簽核時間</th>
                            <th>簽核結果</th>
                            <th>簽核備註</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quotationReviewLog as $key => $val)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $val->user_name }}</td>
                                <td>{{ $val->review_at ?? '' }}</td>
                                <td>
                                    @if ($val->review_result == '1')
                                        核准
                                    @elseif($val->review_result == '0')
                                        駁回
                                    @else
                                        尚未簽核
                                    @endif
                                </td>
                                <td>
                                    {{ $val->review_remark }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
            </div>
        </div>
    </div>
    </div>
