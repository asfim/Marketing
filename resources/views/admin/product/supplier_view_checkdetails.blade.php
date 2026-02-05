@extends('admin.layouts.master')
@section('title', 'Product Purchase Details')
@section('breadcrumb', 'Product Purchase Details')
@section('content')

    <div class="workplace">
        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Product Purchase Details </h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th><input type="checkbox" name="checkall"/></th>
                            <th>DMR No</th>
                            <th>Chalan No</th>
                            <th>Rec Date</th>
                            <th>Supplier Name</th>
                            <th>Product Name</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Mat Cost</th>
                            <th>Truck Rent</th>
                            <th>Unload Bill</th>
                            <th>Total Mat Cost</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $t_mat = 0; $tt_mat = 0; $total_qty = 0; $total_truck_rent = 0; $total_unload_bill = 0;
                            $user = Auth::user();
                        @endphp

                        @foreach($purchases as $purchase)
                            <tr @if($purchase->check_status == 1) style="color:#09b509;" @endif>
                                <td>
                                    @if($purchase->check_status == 0)
                                        <input type="checkbox" class="checkbox" value="{{ $purchase->id }}"
                                               name="checkbox[]"/>
                                    @else
                                        <span class="glyphicon glyphicon-warning-sign"></span>
                                    @endif
                                </td>
                                <td>{{ $purchase->dmr_no }}</td>
                                <td>{{ $purchase->chalan_no }}</td>
                                <td>{{ date('d-M-y', strtotime($purchase->received_date)) }}</td>
                                <td>{{ $purchase->supplier->name }}</td>
                                <td>{{ $purchase->product_name->name }}</td>
                                <td>{{ number_format($purchase->product_qty, 2) }} {{ $purchase->unit_type }}</td>
                                <td>{{ number_format($purchase->rate_per_unit, 2) }}</td>
                                <td>{{ number_format($purchase->material_cost, 2) }}</td>
                                <td>{{ number_format($purchase->truck_rent, 2) }}</td>
                                <td>{{ number_format($purchase->unload_bill, 2) }}</td>
                                <td>{{ number_format($purchase->total_material_cost, 2) }}</td>
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('product-purchase-list-details'))
                                        <a role="button" class="view_btn"
                                           data-dmr_no="{{ $purchase->dmr_no }}"
                                           data-chalan_no="{{ $purchase->chalan_no }}"
                                           data-purchase_date="{{ date('d-M-y', strtotime($purchase->purchase_date)) }}"
                                           data-received_date="{{ date('d-M-y', strtotime($purchase->received_date)) }}"
                                           data-product_name="{{ $purchase->product_name->name }}"
                                           data-supplier_name="{{ $purchase->supplier->name }}"
                                           data-quantity="{{ $purchase->product_qty }}"
                                           data-rate_per_unit="{{ $purchase->rate_per_unit }}"
                                           data-material_cost="{{ $purchase->material_cost }}"
                                           data-truck_rent="{{ $purchase->truck_rent }}"
                                           data-unload_bill="{{ $purchase->unload_bill }}"
                                           data-total_material_cost="{{ $purchase->total_material_cost }}"
                                           data-vehicle_no="{{ $purchase->vehicle_no }}"
                                           data-description="{{ $purchase->description }}"
                                           data-toggle="modal"
                                           data-target="#detailsModal">
                                            <span class="fa fa-eye"></span>
                                        </a>

                                        <!-- Edit button -->
                                        

                                    @endif
                                </td>
                            </tr>

                            @php
                                $t_mat += $purchase->material_cost;
                                $tt_mat += $purchase->total_material_cost;
                                $total_qty += $purchase->product_qty;
                                $total_truck_rent += $purchase->truck_rent;
                                $total_unload_bill += $purchase->unload_bill;
                            @endphp
                        @endforeach

                        <tr style="background-color:#999999; color: #fff;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total =</b></td>
                            <td></td>
                            <td><b>{{ number_format($total_qty, 2) }}</b></td>
                            <td></td>
                            <td><b>BDT {{ number_format($t_mat, 2) }}</b></td>
                            <td><b>BDT {{ number_format($total_truck_rent, 2) }}</b></td>
                            <td><b>BDT {{ number_format($total_unload_bill, 2) }}</b></td>
                            <td><b>BDT {{ number_format($tt_mat, 2) }}</b></td>
                            <td class="hidden-print"></td>
                        </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="dr"><span></span></div>
    </div>



    <!-- details modal form -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" id="close_modal"><span aria-hidden="true">&times;</span><span
                                class="sr-only">Close</span></button>
                    <h4>Product Purchase Details</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            <div class="row-form clearfix">
                                <label class="col-md-3">DMR No: </label>
                                <div class="col-md-6" id="dmr_no"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Chalan No: </label>
                                <div class="col-md-6" id="chalan_no"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Purchase Date : </label>
                                <div class="col-md-6" id="purchase_date"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Received Date : </label>
                                <div class="col-md-6" id="received_date"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Product Name </label>
                                <div class="col-md-6" id="product_name"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Supplier Name : </label>
                                <div class="col-md-6" id="supplier_name"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Quantity :</label>
                                <div class="col-md-6" id="quantity"></div>

                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Rate Per Unit :</label>
                                <div class="col-md-6" id="rate_per_unit"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Material Cost :</label>
                                <div class="col-md-6" id="material_cost"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Truck Rent :</label>
                                <div class="col-md-6" id="truck_rent"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Unload Bill :</label>
                                <div class="col-md-6" id="unload_bill"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Total Material Cost :</label>
                                <div class="col-md-6" id="total_material_cost"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Vehicle No</label>
                                <div class="col-md-6" id="vehicle_no"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Description</label>
                                <div class="col-md-6" id="description"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal" id="close_modal1" aria-hidden="true">Close
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('page-script')
    <script>

        $(document).ready(function (e) {
            $("#search_name").autocomplete({
                source: '{!!URL::route('autoComplete',['table_name' => 'suppliers'])!!}',
                minLenght: 1,
                autoFocus: true,

            });

            $(document).on('click', '.view_btn', function () {
                $('#dmr_no').html($(this).data('dmr_no'));
                $('#chalan_no').html($(this).data('chalan_no'));
                $('#purchase_date').html($(this).data('purchase_date'));
                $('#received_date').html($(this).data('received_date'));
                $('#product_name').html($(this).data('product_name'));
                $('#supplier_name').html($(this).data('supplier_name'));
                $('#quantity').html($(this).data('quantity'));
                $('#rate_per_unit').html($(this).data('rate_per_unit'));
                $('#material_cost').html($(this).data('material_cost'));
                $('#truck_rent').html($(this).data('truck_rent'));
                $('#unload_bill').html($(this).data('unload_bill'));
                $('#total_material_cost').html($(this).data('total_material_cost'));
                $('#vehicle_no').html($(this).data('vehicle_no'));
                $('#description').html($(this).data('description'));
            });
        });
    </script>
@endsection