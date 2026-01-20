@extends('admin.layouts.master')
@section('title', 'Product Purchase List')
@section('breadcrumb', 'Product Purchase List')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>
                            Product Purchase List
                            <span
                                class="src-info">{{ request('search_text') == '' && request('date_range') == '' ? '- Last 30 Days' : '- ' . request('date_range') }}</span>
                        </h1>
                    </div>
                    <div class="col-md-1" style="margin-top: 4px;">
                        <a href="#bill_status_modal" role="button" data-toggle="modal" class="btn btn-warning"
                            id="check_btn_div" style="display: none;">Checked</a>
                    </div>
                    <div class="col-md-7 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-4">
                                    <input type="text" name="search_text" id="search_name"
                                        value="{{ request('search_text') ?? '' }}" class="form-control"
                                        placeholder="Enter Search Text" />
                                </div>
                                 <div class="col-md-4">
                                    <input type="text" name="search_text2" id="search_name2"
                                        value="{{ request('search_text2') ?? '' }}" class="form-control"
                                        placeholder="Enter Search product Name " />
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range"
                                            value="{{ request('date_range') ?? '' }}" class="form-control"
                                            placeholder="Date Range" autocomplete="off" />
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
                    <form action="{{ route('product.purchase.check') }}" method="post" id="checked_bill_form"
                        class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" name="bill_no" id="bill_no" value="" />
                        <input type="hidden" name="adjustment_qty" id="adjustment_qty" value="" />
                        <input type="hidden" name="adjustment_cost" id="adjustment_cost" value="" />
                        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="checkall" /></th>
                                    <th>DMR No</th>
                                    <th>Challan/ Bill No</th>
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
                                <?php
                                $i = 1;
                                $t_mat = 0;
                                $tt_mat = 0;
                                $total_qty = 0;
                                $total_truck_rent = 0;
                                $total_unload_bill = 0;
                                ?>
                                @foreach ($purchases as $purchase)
                                    <tr @if ($purchase->check_status == 1) style="color:#09b509;" @endif>
                                        <td>
                                            @if ($purchase->check_status == 0)
                                                <input type="checkbox" class="checkbox" value="{{ $purchase->id }}"
                                                    name="checkbox[]" />
                                            @else
                                                <span class="glyphicon glyphicon-warning-sign"></span>
                                            @endif
                                        </td>
                                        <td>{{ $purchase->dmr_no }}</td>
                                        <td>{{ $purchase->chalan_no }}</td>
                                        <td>{{ date('d-M-y', strtotime($purchase->received_date)) }}</td>
                                        <td>{{ $purchase->supplier->name }}</td>
                                        <td>{{ $purchase->product_name->name }}</td>
                                        <td>{{ $purchase->product_qty . ' ' . $purchase->unit_type }}</td>
                                        <td>{{ number_format($purchase->rate_per_unit, 2) }}</td>
                                        <td>{{ number_format($purchase->material_cost, 2) }}</td>
                                        <td>{{ number_format($purchase->truck_rent, 2) }}</td>
                                        <td>{{ number_format($purchase->unload_bill, 2) }}</td>
                                        <td>{{ number_format($purchase->total_material_cost, 2) }}</td>
                                        <td class="hidden-print">
                                            @if ($user->hasRole('super-admin') || $user->can('product-purchase-list-details'))
                                                <a role="button" class="view_btn" data-dmr_no="{{ $purchase->dmr_no }}"
                                                    data-chalan_no="{{ $purchase->chalan_no }}"
                                                    data-purchase_date="{{ $purchase->purchase_date }}"
                                                    data-received_date="{{ $purchase->received_date }}"
                                                    data-product_name="{{ $purchase->product_name->name }}"
                                                    data-supplier_name="{{ $purchase->supplier->name }}"
                                                    data-quantity="{{ $purchase->product_qty }}"
                                                    data-rate_per_unit="{{ $purchase->rate_per_unit }}"
                                                    data-material_cost="{{ $purchase->material_cost }}"
                                                    data-truck_rent="{{ $purchase->truck_rent }}"
                                                    data-unload_bill="{{ $purchase->unload_bill }}"
                                                    data-total_material_cost="{{ $purchase->total_material_cost }}"
                                                    data-vehicle_no="{{ $purchase->vehicle_no }}"
                                                    data-description="{{ $purchase->description }}" data-toggle="modal"
                                                    data-target="#detailsModal">
                                                    <span class="fa fa-eye"></span>
                                                </a>
                                            @endif
                                            @if ($user->hasRole('super-admin') || $user->can('product-purchase-edit'))
                                                <a href="{{ route('product.purchase.edit', $purchase->id) }}"
                                                    role="button" class="fa fa-edit"></a>
                                            @endif
                                            @if ($user->hasRole('super-admin') || $user->can('product-purchase-delete'))
                                                <a href="{{ route('product.purchase.delete', $purchase->transaction_id) }}"
                                                    onclick='return confirm("Are you sure you want to delete?");'
                                                    class="fa fa-trash"></a>
                                            @endif
                                        </td>
                                    </tr>

                                    <?php
                                    $i++;
                                    $t_mat += $purchase->material_cost;
                                    $tt_mat += $purchase->total_material_cost;
                                    $total_qty += $purchase->product_qty;
                                    $total_truck_rent += $purchase->truck_rent;
                                    $total_unload_bill += $purchase->unload_bill;
                                    ?>
                                @endforeach

                                @if (!empty($check_p))
                                    @foreach ($check_p as $purchase)
                                        <tr @if ($purchase->check_status == 1) style="color:#09b509;" @endif>
                                            <td>
                                                @if ($purchase->check_status == 0)
                                                    <input type="checkbox" id="checkbox" value="{{ $purchase->id }}"
                                                        name="checkbox[]" />
                                                @else
                                                    <span class="glyphicon glyphicon-warning-sign"></span>
                                                @endif
                                            </td>
                                            <td>{{ $purchase->dmr_no }}</td>
                                            <td>{{ $purchase->bill_no }}</td>
                                            <td>{{ date('d-M-y', strtotime($purchase->received_date)) }}</td>
                                            <td>{{ $purchase->supplier->name }}</td>
                                            <td>{{ $purchase->product_name->name }}</td>
                                            <td>{{ number_format($purchase->product_qty, 2) . ' ' . $purchase->unit_type }}
                                            </td>
                                            <td>{{ number_format($purchase->rate_per_unit, 2) }}</td>
                                            <td>{{ number_format($purchase->material_cost, 2) }}</td>
                                            <td>{{ number_format($purchase->truck_rent, 2) }}</td>
                                            <td>{{ number_format($purchase->unload_bill, 2) }}</td>
                                            <td>{{ number_format($purchase->total_material_cost, 2) }}</td>
                                            <td class="hidden-print">
                                                @if ($user->hasRole('super-admin') || $user->can('product-purchase-list-details'))
                                                    <a role="button" class="view_btn"
                                                        data-dmr_no="{{ $purchase->supplier->name }}"
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
                                                        data-toggle="modal" data-target="#detailsModal">
                                                        <span class="fa fa-eye"></span>
                                                    </a>
                                                    <a href="{{ route('purchase.checked.details', $purchase->bill_no) }}"
                                                        target="_blank"><span class="fa fa-info-circle"></span></a>
                                                @endif
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                        $t_mat += $purchase->material_cost;
                                        $tt_mat += $purchase->total_material_cost;
                                        $total_qty += $purchase->product_qty;
                                        $total_truck_rent += $purchase->truck_rent;
                                        $total_unload_bill += $purchase->unload_bill;
                                        ?>
                                    @endforeach
                                @endif

                            </tbody>
                            <tfoot>
                                <tr style="background-color:#999999; color: #fff;">
                                    <td></td>
                                    <td></td>
                                    <td>Total:</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ number_format($total_qty, 2) }}</td>
                                    <td></td>
                                    <td>{{ number_format($t_mat, 2) }}</td>
                                    <td>{{ number_format($total_truck_rent, 2) }}</td>
                                    <td>{{ number_format($total_unload_bill, 2) }}</td>
                                    <td>{{ number_format($tt_mat, 2) }}</td>
                                    <td class="hidden-print"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
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
                    <button type="button" class="close" data-dismiss="modal" id="close_modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
                    <button class="btn btn-danger" data-dismiss="modal" id="close_modal1"
                        aria-hidden="true">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bill_status_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Add Bill No</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            <div class="row-form clearfix">
                                <label class="col-md-3">Total Selected: </label>
                                <div class="col-md-6">
                                    <span id="total_selected"></span>
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Total Quantity: </label>
                                <div class="col-md-6">
                                    <span id="total_pro_qty"></span>
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Total Mat Cost: </label>
                                <div class="col-md-6">
                                    <span id="total_mat_cost"></span>
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Bill No: </label>
                                <div class="col-md-6">
                                    <input type="text" value="<?php echo mt_rand(10, 100000); ?>" required=""
                                        name="bill_no_modal" id="bill_no_modal" />
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Adjustment Qty: </label>
                                <div class="col-md-6">
                                    <input type="number" value="" required="" name="ad_qty_modal"
                                        id="ad_qty_modal" />
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Adjustment Cost: </label>
                                <div class="col-md-6">
                                    <input type="number" value="" required="" name="ad_cost_modal"
                                        id="ad_cost_modal" />
                                </div>
                            </div>
                            <div class="col-md-12" id="error_div" style=""></div>

                            <div class="modal-footer" style="text-align: center;">
                                <button class="btn btn-primary" type="button" id="btn_bill_update">Save Updates</button>
                                <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('page-script')
    <script>
        var t = 0;
        var new_item = 0;

        $(document).ready(function(e) {
            // Update bill no and change color
            $("#btn_bill_update").on('click', function() {
                $("#error_div").empty();
                var bill_no = $("#bill_no_modal").val();
                var ad_qty = $("#ad_qty_modal").val();
                var ad_cost = $("#ad_cost_modal").val();
                $("#bill_no").val(bill_no);
                $("#adjustment_qty").val(ad_qty);
                $("#adjustment_cost").val(ad_cost);
                if (bill_no === "") {
                    $("#error_div").append("<p>Please enter bill no</p>");
                } else {
                    $("#checked_bill_form").submit();
                }
            });

            // AutoComplete Search
            $("#search_name").autocomplete({
                source: '{!! URL::route('autoComplete', ['table_name' => 'suppliers']) !!}',
                minLength: 1,
                autoFocus: true,
            });
             $("#search_name2").autocomplete({
                source: '{!! URL::route('autoComplete', ['table_name' => 'product_name']) !!}',
                minLength: 1,
                autoFocus: true,
            });

            // Highlight selected rows when checkboxes are clicked
            $("#datatable tbody").on('click', 'tr', function() {
                if ($(this).find("input[type=checkbox]").is(':checked')) {
                    $(this).addClass('selected');
                } else {
                    $(this).removeClass('selected');
                }
            });

            // Select/Deselect all rows
            $("#datatable thead tr input[type=checkbox]").on('click', function() {
                if ($(this).is(':checked')) {
                    $("#datatable tbody tr").addClass('selected');
                    $(".checkbox").prop('checked', true);
                } else {
                    $("#datatable tbody tr").removeClass('selected');
                    $(".checkbox").prop('checked', false);
                }
                updateTotals();
            });

            // Handle checkbox change
            $(document).on('change', ".checkbox", function() {
                if ($(this).is(':checked')) {
                    $(this).closest('tr').addClass('selected');
                } else {
                    $(this).closest('tr').removeClass('selected');
                }
                updateTotals();
            });

            // Function to update total calculations
            function updateTotals() {
                let total_mat_cost = 0;
                let total_qty = 0;
                let len = $(".checkbox:checked").length;



                // Fetch selected rows correctly
                let selectedRows = table.rows(':has(:checked)').data();


                // Reset values before summing
                total_qty = 0;
                total_mat_cost = 0;

                $.each(selectedRows, function(index, value) {


                    // Extract and parse the quantity from column 6
                    let qtyStr = selectedRows[index][6].split(" ");
                    let qty = parseFloat(qtyStr[0]) || 0;
                    total_qty += qty;


                    // Extract and parse the material cost from column 8
                    let costStr = selectedRows[index][8].replace(/,/g, '').replace(/^0+/, '');
                    let cost = parseFloat(costStr) || 0;
                    total_mat_cost += cost;

                });

                // Update totals in the UI
                $("#total_pro_qty").html('<b>' + total_qty.toFixed(2) + '</b>');
                $("#total_mat_cost").html('<b>' + total_mat_cost.toFixed(2) + '</b>');
                $("#total_selected").html('<b>' + len + '</b>');

                // Show or hide button based on selection
                if (len > 0) {
                    $("#check_btn_div").css('display', 'inline-block');
                } else {
                    $("#check_btn_div").css('display', 'none');
                }
            }

            // View Button Click Event
            $(document).on('click', '.view_btn', function() {
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
