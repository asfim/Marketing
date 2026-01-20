@extends('admin.layouts.master')
@section('title', 'View Stock Adjustment')
@section('breadcrumb', 'View Stock Adjustment')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole(['super-admin']) || $user->can('product-stock-view'))
        <li><a data-target="#createModal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> New Adjustment</a></li>
    @endif
@endsection
@section('content')
<div class="workplace">
    <div class="row">
        <div class="col-md-12">
            <div class="head clearfix">

                <div class="col-md-4">
                    <div class="isw-documents"></div>
                    <h1>
                        View Stock Adjustment
                        <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}</span>
                    </h1>
                </div>

                <div class="col-md-7 search_box" style="margin-top: 4px;">
                    <form action="" class="form-horizontal">
                        <div class="" align="right">
                            <div class="col-md-6">
                                <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}" class="form-control" placeholder="Enter Search Text" />
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"
                                           class="form-control" placeholder="Date Range" autocomplete="off"/>
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default search-btn">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="block-fluid table-sorting clearfix">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                    <thead>
                        <tr>
                            <th>Adjustment Date</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Unit Type</th>
                            <th>Adjustment Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockAdj as $stock)
                        <tr>
                            <td>{{ date('d-M-y',strtotime($stock->adjustment_date)) }}</td>
                            <td>{{ $stock->product_name->name }}</td>
                            <td>{{ $stock->description }}</td>
                            <td>{{ $stock->unit_type }}</td>
                            <td>{{ number_format($stock->adjustment_qty,2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4>New Stock Adjustment</h4>
            </div>
            <form action="{{ route('product.stock.adjustment.store') }}" method="post" class="form-horizontal">
                <div class="modal-body">
                    {{csrf_field()}}
                    <div class="row-form clearfix">
                        <label class="col-md-3">Adjustment Date</label>
                        <div class="col-md-7"><input type="text" value="{{ old('adjustment_date') }}" class="datepicker form-control" required name="adjustment_date" id="adjustment_date"/></div>
                    </div>

                    <div class="row-form clearfix">
                        <label class="col-md-3">Product Name</label>
                        <div class="col-md-7">
                            <select name="product_id" id="product_id" class="select2 form-control" required>
                                <option value="">choose a option...</option>
                                @foreach($product_names as $product_name)
                                    <option value="{{$product_name->id}}" {{ old('product_id')==$product_name->id?'selected':'' }}>{{$product_name->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row-form clearfix">
                        <label class="col-md-3">Unit Type</label>
                        <div class="col-md-7">
                            <select name="unit_type" id="unit_type" required class="form-control">
                                <option value="">choose a option...</option>
                                <option value="CFT" {{ old('unit_type')=='CFT'?'selected':'' }}>CFT</option>
                                <option value="KG" {{ old('unit_type')=='KG'?'selected':'' }}>KG</option>
                            </select>
                        </div>
                    </div>
                    <div class="row-form clearfix">
                        <label class="col-md-3">Adjustment Qty</label>
                        <div class="col-md-7"><input type="text" value="{{ old('adjustment_qty') }}" name="adjustment_qty" id="adjustment_qty" required class="form-control"/></div>
                    </div>

                    <div class="row-form clearfix">
                        <label class="col-md-3">Description</label>
                        <div class="col-md-7"><textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea></div>
                    </div>

                </div>

                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-primary" type="submit" aria-hidden="true">Submit</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page-script')
    <script type="text/javascript">
        $("#search_name").autocomplete({
            source : '{!!URL::route('autoComplete',['table_name' => 'product_names'])!!}',
            minLenght:1,
            autoFocus:true,
        });
    </script>
@endsection
