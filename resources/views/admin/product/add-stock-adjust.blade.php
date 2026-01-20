@extends('admin.layouts.master')
@section('title', 'Add Stock Adjustment')
@section('breadcrumb', 'Add Stock Adjustment')
@section('content')

    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1> Add Stock Adjustment</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{URL::to('/save-stock-adjutment')}}" method="post" enctype="multipart/form-data" id="product_form" class="form-horizontal">
                        {{csrf_field()}}

                        <div class="row-form clearfix">
                            <div class="col-md-3">Adjustment Date</div>
                            <div class="col-md-6"><input type="date" value="" required="" name="adjustment_date" id="adjustment_date"/></div>
                        </div>

                        <div class="row-form clearfix">
                            <div class="col-md-3">Product Name</div>
                            <div class="col-md-6">
                                <select name="product_id" id="product_id" required="">
                                    <option value="">choose a option...</option>
                                    @foreach($product_names as $product_name)
                                        <option value="{{$product_name->id}}">{{$product_name->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        <div class="row-form clearfix">
                            <div class="col-md-3">Unit Type</div>
                            <div class="col-md-6">
                                <select name="unit_type" id="unit_type" required="">
                                    <option value="">choose a option...</option>
                                    <option value="CFT">CFT</option>
                                    <option value="KG">KG</option>
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <div class="col-md-3">Adjusment Quantity</div>
                            <div class="col-md-6"><input type="number" step="any" required="" value="" name="adjustment_qty" id="adjustment_qty"/></div>
                        </div>

                        <div class="row-form clearfix">
                            <div class="col-md-3">Description</div>
                            <div class="col-md-6"><textarea name="description" id="description"></textarea></div>
                        </div>
                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-default">Submit</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

@endsection
