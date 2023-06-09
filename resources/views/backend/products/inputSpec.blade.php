<div id="SkuComponent">
    <div class="row form-group">
        <div class="col-sm-12">
            <div class="col-sm-1" style="padding: 0">
                <label class="control-label">商品規格<span class="text-red">*</span></label>
            </div>
            <div class="col-sm-11">
                <div class="col-sm-12" style="margin-bottom: 20px">
                    <div class="col-sm-2 ">
                        {{-- <input type="radio" id="one" value="One" na v-model="spec_dimension"> --}}
                        {{-- <label for="one">單規格</label> --}}
                        <label class="radio-inline">
                            <input type="radio" name="spec_dimension" value="0"
                                v-model="products.spec_dimension">
                            單規格
                        </label>
                    </div>
                    <div class="col-sm-2">
                        <label class="radio-inline">
                            <input type="radio" name="spec_dimension" value="1"
                                v-model="products.spec_dimension">
                            一維多規格
                        </label>
                    </div>
                    <div class="col-sm-2">
                        <label class="radio-inline">
                            <input type="radio" name="spec_dimension" value="2"
                                v-model="products.spec_dimension">
                            二維多規格
                        </label>
                    </div>
                </div>


                <div class="row form-group">
                    <div class="col-sm-5" v-if="products.spec_dimension >= 1">
                        <div class="col-sm-2" style="padding: 0">
                            <label class="control-label">規格一<span class="text-red">*</span></label>
                        </div>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <input class="form-control" type="text" name="spec_1" id="spec_1">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-5" v-if="products.spec_dimension == 2">
                        <div class="col-sm-2" style="padding: 0">
                            <label class="control-label">規格二<span class="text-red">*</span></label>
                        </div>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <input type="text" class="form-control" name="spec_2" id="spec_2">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 二維多規格 --}}
                <textarea style="display: none" name="SpecListJson" id="" cols="30" rows="10">@{{ SpecList }}</textarea>
                <div class="row ">
                    <div class="col-sm-5" v-if="products.spec_dimension >= 1">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <button class="btn btn-primary btn-sm" type="button"
                                            @click="AddSpecToSkuList('1')">新增項目
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(spec_1, spec_1_key) in SpecList.spec_1" @dragstart="drag"
                                    @dragover='dragover' @dragleave='dragleave' @drop="drop" draggable="true"
                                    :data-index="spec_1_key" :data-type="'spec_1'">
                                    <td>
                                        <div class="col-sm-1">
                                            <label class="control-label">
                                                <i class="fa-solid fa-list fa-lg"></i>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="form-group">
                                                <input class="form-control spec_1_va"
                                                    :name="'spec_1_va[' + spec_1_key + ']'" v-model="spec_1.name"
                                                    data-va="spec_1_va">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <button class="btn btn-danger btn-sm" type="button"
                                                @click="DelSpecList(spec_1 ,'spec_1' ,spec_1_key)">刪除</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-5" v-if="products.spec_dimension == 2">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <button class="btn btn-primary btn-sm" type="button"
                                            @click="AddSpecToSkuList('2')">新增項目</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- {{$category_products_list}} --}}
                                <tr v-for="(spec_2, spec_2_key) in SpecList.spec_2" @dragstart="drag"
                                    @dragover='dragover' @dragleave='dragleave' @drop="drop" draggable="true"
                                    :data-index="spec_2_key" :data-type="'spec_2'">
                                    <td>
                                        <div class="col-sm-1">
                                            <label class="control-label">
                                                <i class="fa-solid fa-list fa-lg"></i>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="form-group">
                                                <input class="form-control spec_2_va"
                                                    :name="'spec_2_va[' + spec_2_key + ']'" v-model="spec_2.name"
                                                    data-va="spec_2_va">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <button class="btn btn-danger btn-sm" type="button"
                                                @click="DelSpecList(spec_2 ,'spec_2' ,spec_2_key)">刪除</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <div class="col-sm-2 no-pa" style="padding: 0">
                            <label class="control-label">安全庫存量</label>
                        </div>
                        <div class="col-sm-8">
                            <input class="form-control" name="safty_qty_all" id="keyword" v-model="safty_qty_all">
                        </div>
                        <div class="cola-sm-2">
                            <button class="btn btn-primary btn-sm" type="button"
                                @click="change_safty_qty_all">套用</button>
                        </div>
                    </div>
                </div>
                <textarea style="display: none" name="SkuListdata" id="SkuListdata" cols="30" rows="10">@{{ SkuList }}</textarea>
                <table id="sku_table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th v-if="products.spec_dimension >= 1" style="width: 10%">規格一</th>
                            <th v-if="products.spec_dimension == 2" style="width: 10%">規格二</th>
                            <th style="width: 15%">Item編號</th>
                            <th style="width: 10%">廠商貨號</th>
                            <th style="width: 10%">國際條碼</th>
                            <th style="width: 10%">POS品號<span class="stock_type_list text-red">*</span></th>
                            <th style="width: 10%">安全庫存量<span class="text-red red-star">*</span></th>
                            <th style="width: 10%">是否追加<span class="text-red">*</span></th>
                            <th style="width: 10%">狀態<span class="text-red">*</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(Sku, SkuKey) in SkuList">
                            <td v-if="products.spec_dimension >= 1">@{{ Sku.spec_1_value }}</td>
                            <td v-if="products.spec_dimension == 2">@{{ Sku.spec_2_value }}</td>
                            <td><input class="form-control" v-model="Sku.item_no" readonly></td>
                            <td><input class="form-control" v-model="Sku.supplier_item_no"></td>
                            <td>
                                <div class="form-group" style="margin-right:0px;margin-left:0px;">
                                    <input class="form-control ean_va" v-model="Sku.ean"
                                        :name="'ean_va[' + SkuKey + ']'">
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input class="form-control pos_item_no_va" v-model="Sku.pos_item_no"
                                        :name="'pos_item_no[' + SkuKey + ']'" data-va="pos_item_no">
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input class="form-control safty_qty_va" type="number" min="0"
                                        v-model="Sku.safty_qty" :name="'safty_qty_va[' + SkuKey + ']'">
                                </div>
                            </td>
                            <td>
                                <select class="form-control js-select2" v-model="Sku.is_additional_purchase">
                                    <option value="1">是</option>
                                    <option value="0">否</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-control js-select2" v-model="Sku.status">
                                    <option value="1">啟用</option>
                                    <option value="0">停用</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
