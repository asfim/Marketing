@extends('admin.layouts.master')
@section('title', 'Purchase Product')
@section('breadcrumb', 'Purchase Product')
@php($user  = Auth::user())
@section('content')

    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Purchase Product</h1>
                </div>
                <div class="block-fluid clearfix">
                    <form action="{{ route('product.purchase.store') }}" method="post" enctype="multipart/form-data" id="product_form" class="form-horizontal">
                        @csrf

                        {{-- Branch --}}
                        <div class="col-md-3">
                            <label>Branch</label>
                            @if($user->branchId == '')
                                <select name="branchId" id="branchId" class="form-control">
                                    <option value="">----- Select Branch -----</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                                {{ session('form_input.branchId', old('branchId')) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}">
                                <input type="text" value="{{ $user->branch->name }}" class="form-control" readonly>
                            @endif
                        </div>

                        {{-- DMR No --}}
                        <div class="col-md-3">
                            <label>DMR No*</label>
                            
                            <input type="text" name="dmr_no" id="dmr_no" value="{{ session('form_input.dmr_no', old('dmr_no')) }}" class="form-control" required/>
                        </div>

                        {{-- Challan No --}}
                        <div class="col-md-3">
                            <label>Challan No*</label>
                            <input type="text" name="chalan_no" id="chalan_no" value="{{ session('form_input.chalan_no', old('chalan_no')) }}" class="form-control" required/>
                        </div>

                        {{-- Received Date --}}
                        <div class="col-md-3">
                            <label>Received Date*</label>
                            {{-- @dump (session('form_input.received_date')) --}}
                            <input type="text" name="received_date" id="received_date" value="{{ session('form_input.received_date', old('received_date'))  }}" class="form-control" required/>
                        </div>

                        {{-- Supplier --}}
                        <div class="col-md-4">
                            <label>Select a supplier*</label>
                            <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                                <option value="">choose an option...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                            {{ session('form_input.supplier_id', old('supplier_id')) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Product Name --}}
                        <div class="col-md-4">
                            <label>Product Name*</label>
                            <select name="product_name_id" id="product_name_id" class="form-control select2" required>
                                <option value="">choose an option...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            {{ session('form_input.product_name_id', old('product_name_id')) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Unit Type --}}
                        <div class="col-md-4">
                            <label>Unit Type*</label>
                            <select name="unit_type" id="unit_type" class="form-control" required>
                                <option value="">choose an option...</option>
                                <option value="CFT" {{ session('form_input.unit_type', old('unit_type')) == 'CFT' ? 'selected' : '' }}>CFT</option>
                                <option value="Ton" {{ session('form_input.unit_type', old('unit_type')) == 'Ton' ? 'selected' : '' }}>Ton</option>
                                <option value="KG" {{ session('form_input.unit_type', old('unit_type')) == 'KG' ? 'selected' : '' }}>KG</option>
                            </select>
                        </div>

                        {{-- Quantity --}}
                        <div class="col-md-3">
                            <label>Quantity*</label>
                            <input type="text" name="product_qty" id="product_qty" value="{{ session('form_input.product_qty', old('product_qty')) }}" class="form-control" required/>
                        </div>

                        {{-- Rate per Unit --}}
                        <div class="col-md-3">
                            <label>Rate per Unit*</label>
                            <input type="text" name="rate_per_unit" id="rate_per_unit" value="{{ session('form_input.rate_per_unit', old('rate_per_unit')) }}" class="form-control" required/>
                        </div>

                        {{-- Material Cost --}}
                        <div class="col-md-3">
                            <label>Material Cost*</label>
                            <input type="text" name="material_cost" id="material_cost" value="{{ session('form_input.material_cost', old('material_cost')) }}" class="form-control" readonly required/>
                        </div>

                        {{-- Truck Rent --}}
                        <div class="col-md-3">
                            <label>Truck Rent</label>
                            <input type="text" name="truck_rent" id="truck_rent" value="{{ session('form_input.truck_rent', old('truck_rent')) }}" class="form-control"/>
                        </div>

                        {{-- Unload Bill --}}
                        <div class="col-md-3">
                            <label>Unload Bill</label>
                            <input type="text" name="unload_bill" id="unload_bill" value="{{ session('form_input.unload_bill', old('unload_bill')) }}" class="form-control"/>
                        </div>

                        {{-- Total Material Cost --}}
                        <div class="col-md-3">
                            <label>Total Material Cost*</label>
                            <input type="text" name="total_material_cost" id="total_material_cost" value="{{ session('form_input.total_material_cost', old('total_material_cost')) }}" class="form-control" readonly required/>
                        </div>

                        {{-- Vehicle No --}}
                        <div class="col-md-3">
                            <label>Vehicle No</label>
                            <input type="text" name="vehicle_no" id="vehicle_no" value="{{ session('form_input.vehicle_no', old('vehicle_no')) }}" class="form-control"/>
                        </div>

                        {{-- Files --}}
                        <div class="col-md-3">
                            <label>Files</label><br>
                            <input type="file" name="file[]" id="file" class="form-control" multiple/>
                        </div>

                        {{-- Description --}}
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control">{{ session('form_input.description', old('description')) }}</textarea>
                        </div>

                        {{-- Submit --}}
                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('purchase.reset') }}" class="btn btn-danger">Reset</a>

                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

    <div class="dr"><span></span></div>

@endsection

@section('page-script')
    <script>
        var t = 0;
        var new_item = 0;

        $(document).ready(function(e) {
            /*$("#discount").bind('keyup',function(){
                var pprice = $("#pprice").val();
                var val = parseFloat(this.value);
                if(!val) val = 0;
                var dis_p = pprice - (pprice*val/100);

                new_item = parseFloat(dis_p);
                $("#net_price").val(new_item);
            });*/

            $("#total_material_cost").bind('click',function(){
                var trent = $("#truck_rent").val();
                var ubill = $("#unload_bill").val();
                var unitp = $("#rate_per_unit").val();
                var pqty = $("#product_qty").val();
                var unit_type = $("#unit_type").val();
                if(trent === "" ) {
                    trent=0;
                    // alert('Truck rent is null..Enter a digit');
                    // return false;
                }
                if(ubill === "" ) {
                    ubill=0;
                    // alert('Unload bill is null..Enter a digit');
                    // return false;
                }
                if(unitp === "" ) {
                    alert('Unit price is null..Enter a digit');
                    return false;
                }
                if(pqty === "" ) {
                    alert('Product quantity is null..Enter a digit');
                    return false;
                }

                var mat_cost_f = parseFloat(unitp) * parseFloat(pqty);
                var mat_cost =mat_cost_f.toFixed(2);
                $("#material_cost").val(mat_cost);
                var tmat_cost_f = parseFloat(mat_cost) + parseFloat(trent) + parseFloat(ubill);
                var tmat_cost = tmat_cost_f.toFixed(2);
                $("#total_material_cost").val(tmat_cost);


                /*var dis = $("#discount").val();
                var vat = $("#vat").val();
                var gov_t = $("#gov_tax").val();
                var ext_cost = $("#extra_cost").val();
                if(ext_cost == '') ext_cost = 0;
                var val = parseFloat(this.value);
                if(!val) val = 0;
                var dis_p = pprice - (pprice*dis/100);
                var net_fare = parseFloat(dis_p) + parseFloat(vat);
                var gov_t = net_fare*0.3/100;
                var net_price = parseFloat(net_fare) + parseFloat(gov_t) + parseInt(ext_cost);
                new_item = parseFloat(net_price);
                $("#net_price").val(new_item);*/
            });


            $("#product_qty, #rate_per_unit, #truck_rent, #unload_bill").bind('blur',function(){
                var trent = $("#truck_rent").val();
                var ubill = $("#unload_bill").val();
                var unitp = $("#rate_per_unit").val();
                var pqty = $("#product_qty").val();
                var unit_type = $("#unit_type").val();
                if(trent === "" ) {
                    trent=0;
                }
                if(ubill === "" ) {
                    ubill=0;
                }
                if(unitp === "" ) {
                    return false;
                }
                if(pqty === "" ) {
                    return false;
                }

                var mat_cost_f = parseFloat(unitp) * parseFloat(pqty);
                var mat_cost =mat_cost_f.toFixed(2);
                $("#material_cost").val(mat_cost);
                var tmat_cost_f = parseFloat(mat_cost) + parseFloat(trent) + parseFloat(ubill);
                var tmat_cost = tmat_cost_f.toFixed(2);
                $("#total_material_cost").val(tmat_cost);


                /*var dis = $("#discount").val();
                var vat = $("#vat").val();
                var gov_t = $("#gov_tax").val();
                var ext_cost = $("#extra_cost").val();
                if(ext_cost == '') ext_cost = 0;
                var val = parseFloat(this.value);
                if(!val) val = 0;
                var dis_p = pprice - (pprice*dis/100);
                var net_fare = parseFloat(dis_p) + parseFloat(vat);
                var gov_t = net_fare*0.3/100;
                var net_price = parseFloat(net_fare) + parseFloat(gov_t) + parseInt(ext_cost);
                new_item = parseFloat(net_price);
                $("#net_price").val(new_item);*/
            });
        });
         $(document).ready(function () {
        $('#received_date').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            immediateUpdates: true,
            todayBtn: true,
            todayHighlight: true,
        });
    });
    </script>
@endsection
