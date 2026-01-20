@extends('admin.layouts.master')
@section('title', 'Edit Purchase Product')
@section('breadcrumb', 'Edit Purchase Product')
@php($user = Auth::user())
@section('content')

    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Edit Purchase Product</h1>
                </div>
                <div class="block-fluid clearfix">
                    <form action="{{ route('product.purchase.update') }}" method="post" enctype="multipart/form-data" id="product_form" class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" value="{{ $purchase->id }}" name="purchase_id"/>
                        <div class="col-md-4">
                            <label>DMR No*</label>
                            <input type="text" value="{{ $purchase->dmr_no }}" name="dmr_no" class="form-control" id="dmr_no" required/>
                        </div>
                        <div class="col-md-4">
                            <label>Challan No*</label>
                            <input type="text" value="{{ $purchase->chalan_no }}" name="chalan_no" class="form-control" id="chalan_no" required/>
                        </div>
                        <div class="col-md-4">
                            <label>Received Date*</label>
                            <input type="text" value="{{ $purchase->received_date }}" name="received_date" class="form-control datepicker" id="received_date" required/>
                        </div>
                        <div class="col-md-4">
                            <label>Select a supplier*</label>
                            <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                                <option value="">choose an option...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}" {{ ($purchase->supplier_id == $supplier->id) ? 'selected':'' }}>{{$supplier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Product Name*</label>
                            <select name="product_name_id" id="product_name_id" class="form-control select2" required>
                                <option value="">choose an option...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ ($purchase->product_name_id == $product->id) ? 'selected':'' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Unit Type*</label>
                            <select name="unit_type" id="unit_type" class="form-control" required>
                                <option value="">choose a option...</option>
                                <option value="CFT" {{ ($purchase->unit_type =='CFT') ? 'selected':'' }}>CFT</option>
                                <option value="Ton" {{ ($purchase->unit_type =='Ton') ? 'selected':'' }}>Ton</option>
                                <option value="KG" {{ ($purchase->unit_type =='KG') ? 'selected':'' }}>KG</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Quantity*</label>
                            <input type="text" value="{{ $purchase->product_qty }}" name="product_qty" class="form-control" id="product_qty" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Rate per Unit*</label>
                            <input type="text" value="{{ $purchase->rate_per_unit }}" name="rate_per_unit" class="form-control" id="rate_per_unit" required/>
                        </div>
                        <div class="col-md-3">
                            <label>Material Cost*</label>
                            <input type="text" value="{{ $purchase->material_cost }}" name="material_cost" class="form-control" id="material_cost" readonly required/>
                        </div>

                        <div class="col-md-3">
                            <label>Truck Rent</label>
                            <input type="text" value="{{ $purchase->truck_rent }}" name="truck_rent" class="form-control" id="truck_rent"/>
                        </div>

                        <div class="col-md-3">
                            <label>Unload Bill</label>
                            <input type="text" value="{{ $purchase->unload_bill }}" name="unload_bill" class="form-control" id="unload_bill"/>
                        </div>
                        <div class="col-md-3">
                            <label>Total Material Cost*</label>
                            <input type="text" value="{{ $purchase->total_material_cost }}" name="total_material_cost" class="form-control" id="total_material_cost" required readonly/>
                        </div>
                        <div class="col-md-4">
                            <label>Vehicle No</label>
                            <input type="text" value="{{ $purchase->vehicle_no }}" name="vehicle_no" class="form-control" id="vehicle_no"/>
                        </div>

                        <div class="col-md-4">
                            <label>Files</label><br>
                            <input type="file" name="file[]" id="file" class="form-control" multiple />
                        </div>
                        <div class="col-md-4">
                            <label>Download/View File</label>
                            <?php

                            $file_text = $purchase->file;
                            $files = explode(",", $file_text);

                            $created_date   = date("Y-m-d", strtotime($purchase->created_at));
                            $create_date_separator  = explode("-", $created_date);
                            $updated_date   = date("Y-m-d", strtotime($purchase->updated_at));
                            $update_date_separator  = explode("-", $updated_date);

                            $path   = public_path();
                            if(is_dir($path)){
                                $img_path   = public_path('img/files');
                                $img_url    = asset('/img/files');
                            }else{
                                $img_path   = asset('/img/files');
                                $img_url    = asset('/img/files');
                            }
                            foreach ($files as $file)
                            {
                                if($created_date == $updated_date) {

                                    $file_name_url  = $img_url.'/'.$create_date_separator[0].'/'.$create_date_separator[1].'/'.$create_date_separator[2].'/'.$file;
                                    $file_name  = $img_path.'/'.$create_date_separator[0].'/'.$create_date_separator[1].'/'.$create_date_separator[2].'/'.$file;
                                    if(file_exists($file_name)){
                                        echo '<a href="'.URL::to($file_name_url).'" rel="tag">'.$file.'</a><br>';
                                    }
                                }else{
                                    $file_name_url  = $img_url.'/'.$create_date_separator[0].'/'.$create_date_separator[1].'/'.$create_date_separator[2].'/'.$file;
                                    $file_name  = $img_path.'/'.$create_date_separator[0].'/'.$create_date_separator[1].'/'.$create_date_separator[2].'/'.$file;
                                    if(file_exists($file_name)){
                                        echo '<a href="'.URL::to($file_name_url).'" rel="tag" target="_blank">'.$file.'</a><br>';
                                    }
                                }
                            }

                            ?>
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control">{{ $purchase->description }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
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
        $(document).ready(function(e) {
            ///calculate mat cost
            $("#total_material_cost").bind('click',function(){
                var trent = $("#truck_rent").val();
                var ubill = $("#unload_bill").val();
                var unitp = $("#rate_per_unit").val();
                var pqty = $("#product_qty").val();
                if(trent === "" )
                {
                    trent=0;
                    // alert('Truck rent is null..Enter a digit');
                    // return false;
                }
                if(ubill === "" )
                {
                    ubill=0;
                    // alert('Unload bill is null..Enter a digit');
                    // return false;
                }
                if(unitp === "" )
                {
                    alert('Unit price is null..Enter a digit');
                    return false;
                }
                if(pqty === "" )
                {
                    alert('Product quantity is null..Enter a digit');
                    return false;
                }

                var mat_cost_f = parseFloat(unitp) * parseFloat(pqty);
                var mat_cost = mat_cost_f.toFixed(2);
                $("#material_cost").val(mat_cost);
                var tmat_cost_f = parseFloat(mat_cost) + parseFloat(trent) + parseFloat(ubill);
                console.log(mat_cost,trent,ubill);
                var tmat_cost = tmat_cost_f.toFixed(2);
                $("#total_material_cost").val(tmat_cost);

            });
            $("#product_qty, #rate_per_unit, #truck_rent, #unload_bill").bind('blur',function(){
                var trent = $("#truck_rent").val();
                var ubill = $("#unload_bill").val();
                var unitp = $("#rate_per_unit").val();
                var pqty = $("#product_qty").val();
                if(trent === "" )
                {
                    trent=0;
                }
                if(ubill === "" )
                {
                    ubill=0;
                }
                if(unitp === "" )
                {
                    return false;
                }
                if(pqty === "" )
                {
                    return false;
                }

                var mat_cost_f = parseFloat(unitp) * parseFloat(pqty);
                var mat_cost = mat_cost_f.toFixed(2);
                $("#material_cost").val(mat_cost);
                var tmat_cost_f = parseFloat(mat_cost) + parseFloat(trent) + parseFloat(ubill);
                console.log(mat_cost,trent,ubill);
                var tmat_cost = tmat_cost_f.toFixed(2);
                $("#total_material_cost").val(tmat_cost);

            });
            $("#product_form").bind('submit',function(){
                var trent = $("#truck_rent").val();
                var ubill = $("#unload_bill").val();
                var unitp = $("#rate_per_unit").val();
                var pqty = $("#product_qty").val();
                if(trent === "" )
                {
                    trent=0;
                }
                if(ubill === "" )
                {
                    ubill=0;
                }
                if(unitp === "" )
                {
                    return false;
                }
                if(pqty === "" )
                {
                    return false;
                }

                var mat_cost_f = parseFloat(unitp) * parseFloat(pqty);
                var mat_cost = mat_cost_f.toFixed(2);
                $("#material_cost").val(mat_cost);
                var tmat_cost_f = parseFloat(mat_cost) + parseFloat(trent) + parseFloat(ubill);
                console.log(mat_cost,trent,ubill);
                var tmat_cost = tmat_cost_f.toFixed(2);
                $("#total_material_cost").val(tmat_cost);

            });
        });
    </script>
@endsection
