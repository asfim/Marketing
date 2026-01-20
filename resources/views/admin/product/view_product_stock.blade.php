@extends('admin.layouts.master')
@section('title', 'View Product Stock')
@section('breadcrumb', 'View Product Stock')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>View Product Stock</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Present Stock</th>
                            <th>Consumptionable</th>
                            <th>Adjustment Quantity</th>
                            <th>Consumption Quantity</th>
                            <th>Unit</th>
                            {{--<th>Actions</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($stocks as $stock)
                            <tr>
                                <td>{{ $stock->product_name->name }}</td>
                                <td>{{ number_format($stock->quantity,2) }}</td>
                                <td>{{ number_format($stock_consumptionable[$stock->product_name_id]??'00',2) }}</td>
                                <td>{{ number_format($stock->stock_adjustment->sum('adjustment_qty'),2) }}</td>
                                <td>{{ number_format($stock->consumption_qty,2) }}</td>
                                <td>{{ $stock->unit_type }}</td>
                                {{--<td>--}}
                                    {{--@if($user->hasRole('super-admin') || $user->can('product-stock-edit'))--}}
                                        {{--<a role="button" class="edit-btn"--}}
                                           {{--data-id="{{$stock->id}}"--}}
                                           {{--data-pro_name="{{$stock->product_name->name}}"--}}
                                           {{--data-quantity="{{$stock->quantity}}"--}}
                                           {{--data-unit_type="{{$stock->unit_type}}"--}}
                                           {{--data-target="#editModal"--}}
                                           {{--data-toggle="modal">--}}
                                            {{--<span class="fa fa-edit"></span>--}}
                                        {{--</a>--}}
                                    {{--@endif--}}
                                {{--</td>--}}
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>
    </div>
    <!-- Bootrstrap modal form -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Product Stock</h4>
                </div>
                <form action="{{ route('product.stock.update') }}" method="post" class="form-horizontal">
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            {{csrf_field()}}
                            <input type="hidden" required="" name="id" value="" />
                            <div class="row-form clearfix">
                                <label class="col-md-3">Product Name</label>
                                <div class="col-md-6"><input type="text" value="" id="pro_name" readonly/></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Stock</label>
                                <div class="col-md-6"><input type="text" value="" name="quantity" id="quantity" required/></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Unit Type</label>
                                <div class="col-md-6"><input type="text" value=""  name="unit_type" id="unit_type" readonly/></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-primary" type="submit" aria-hidden="true">Save Updates</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('page-script')
    <script>
        $(document).ready(function(){

            $(document).on('click','.edit-btn', function(){
                $('#id').val($(this).data('id'));
                $('#pro_name').val($(this).data('pro_name'));
                $('#quantity').val($(this).data('quantity'));
                $('#unit_type').val($(this).data('unit_type'));
            });
        });

    </script>
@endsection


