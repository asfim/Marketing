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
                            Supplier Bill Info 
                            <span class="src-info">
                                {{ request('search_text') == '' && request('search_text2') == '' && request('date_range') == '' ? '- Last 30 Days' : '- ' . request('date_range') }}
                            </span>
                        </h1>
                    </div>
                    
                    <div class="col-md-1" style="margin-top: 4px;">
                        <a href="#bill_status_modal" role="button" data-toggle="modal" class="btn btn-warning"
                            id="check_btn_div" style="display: none;">Checked</a>
                    </div>
                    <div class="col-md-7 search_box" style="margin-top: 4px;">
                        <form action="" method="GET" class="form-horizontal">
                            <input type="hidden" />
                            <div align="right">
                                <div class="col-md-6">
                                    <input type="text" name="billinfo_search_text" id="search_name"
                                        value="{{ request('billinfo_search_text') ?? '' }}" class="form-control"
                                        placeholder="Enter Supplier Name" />
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="billinfo_date_range"
                                            value="{{ request('billinfo_date_range') ?? '' }}"
                                            class="date_range form-control" placeholder="Date Range" autocomplete="off" />
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
                                    <th>Chalan / Bill No</th>
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
                                    $t_mat = 0;
                                    $tt_mat = 0;
                                    $total_qty = 0;
                                    $total_truck_rent = 0;
                                    $total_unload_bill = 0;
                                @endphp

                                @forelse ($check_pb as $checkp)
                                    <tr @if ($checkp->check_status == 1) style="color:#09b509;" @endif>
                                        <td>
                                            @if ($checkp->check_status == 0)
                                                <input type="checkbox" class="checkbox" value="{{ $checkp->id }}"
                                                    name="checkbox[]" />
                                            @else
                                                <span class="glyphicon glyphicon-warning-sign"></span>
                                            @endif
                                        </td>
                                        <td>{{ $checkp->dmr_no }}</td>
                                        <td>{{ $checkp->bill_no }}</td>
                                        <td>{{ date('d-m-Y', strtotime($checkp->received_date)) }}</td>
                                        <td>{{ $checkp->Supplier->name ?? '' }}</td>
                                        <td>{{ $checkp->product_name->name ?? '' }}</td>
                                        <td>{{ number_format($checkp->product_qty, 2) . ' ' . $checkp->unit_type }}</td>
                                        <td>{{ number_format($checkp->rate_per_unit, 2) }}</td>
                                        <td>{{ number_format($checkp->material_cost, 2) }}</td>
                                        <td>{{ number_format($checkp->truck_rent, 2) }}</td>
                                        <td>{{ number_format($checkp->unload_bill, 2) }}</td>
                                        <td>{{ number_format($checkp->total_material_cost, 2) }}</td>
                                        <td class="hidden-print">
                                            <a class="view_btn" role="button" data-dmr_no="{{ $checkp->dmr_no }}"
                                                data-chalan_no="{{ $checkp->bill_no }}"
                                                data-purchase_date="{{ $checkp->purchase_date }}"
                                                data-received_date="{{ $checkp->received_date }}"
                                                data-product_name="{{ $checkp->product_name->name }}"
                                                data-supplier_name="{{ $checkp->supplier->name ?? '' }}"
                                                data-quantity="{{ $checkp->product_qty }}"
                                                data-rate_per_unit="{{ $checkp->rate_per_unit }}"
                                                data-material_cost="{{ $checkp->material_cost }}"
                                                data-truck_rent="{{ $checkp->truck_rent }}"
                                                data-unload_bill="{{ $checkp->unload_bill }}"
                                                data-total_material_cost="{{ $checkp->total_material_cost }}"
                                                data-vehicle_no="{{ $checkp->vehicle_no }}"
                                                data-description="{{ $checkp->description }}" data-toggle="modal"
                                                data-target="#detailsModal">
                                                <span class="fa fa-eye"></span>
                                            </a>
                                            <a href="{{ route('purchase.checked.details', $checkp->bill_no) }}"
                                                target="_blank">
                                                <span class="fa fa-info-circle"></span>
                                            </a>
                                        </td>
                                    </tr>

                                    @php
                                        $t_mat += $checkp->material_cost;
                                        $tt_mat += $checkp->total_material_cost;
                                        $total_qty += $checkp->product_qty;
                                        $total_truck_rent += $checkp->truck_rent;
                                        $total_unload_bill += $checkp->unload_bill;
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-danger">No records found</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr style="background-color:#999999; color:#fff;">
                                    <td colspan="5">Total:</td>
                                    <td></td>
                                    <td>{{ number_format($total_qty, 2) }}</td>
                                    <td></td>
                                    <td>{{ number_format($t_mat, 2) }}</td>
                                    <td>{{ number_format($total_truck_rent, 2) }}</td>
                                    <td>{{ number_format($total_unload_bill, 2) }}</td>
                                    <td>{{ number_format($tt_mat, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span></button>
                        <h4>Suppliers Bill Info</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                               
                                <div class="row-form clearfix">
                                    <label class="col-md-3">DMR No: </label>
                                    <div class="col-md-6" id="dmr_no"></div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal">Close</button>
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
                                aria-hidden="true">&times;</span></button>
                        <h4>Add Bill No</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Total Selected: </label>
                                    <div class="col-md-6"><span id="total_selected">0</span></div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Total Quantity: </label>
                                    <div class="col-md-6"><span id="total_pro_qty">0.00</span></div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Total Mat Cost: </label>
                                    <div class="col-md-6"><span id="total_mat_cost">0.00</span></div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Bill No: </label>
                                    <div class="col-md-6">
                                        <input type="text" value="{{ mt_rand(10000, 999999) }}" required
                                            name="bill_no_modal" id="bill_no_modal" class="form-control" />
                                    </div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Adjustment Qty: </label>
                                    <div class="col-md-6">
                                        <input type="number" value="" name="ad_qty_modal" id="ad_qty_modal"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Adjustment Cost: </label>
                                    <div class="col-md-6">
                                        <input type="number" value="" name="ad_cost_modal" id="ad_cost_modal"
                                            class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-12" id="error_div"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: center;">
                        <button class="btn btn-primary" type="button" id="btn_bill_update">Save Updates</button>
                        <button class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // DataTable Initialization
            var table = $('#datatable').DataTable({
                retrieve: true,
                paging: false,
                searching: false,
                info: false,
                ordering: false
            });

            // Update totals when checkbox changes
            function updateTotals() {
                let total_qty = 0;
                let total_mat_cost = 0;
                let selectedCount = 0;

                // Get all selected rows
                table.rows(':has(input.checkbox:checked)').every(function() {
                    let row = this.node();
                    let qtyCell = $(row).find('td:eq(6)').text().trim(); // Qty column (index 6)
                    let matCostCell = $(row).find('td:eq(11)').text()
                .trim(); // Total Mat Cost column (index 11)

                    // Parse Quantity
                    let qtyMatch = qtyCell.match(/^([\d,.]+)/);
                    let qty = qtyMatch ? parseFloat(qtyMatch[1].replace(/,/g, '')) : 0;

                    // Parse Material Cost
                    let cost = parseFloat(matCostCell.replace(/,/g, '')) || 0;

                    total_qty += qty;
                    total_mat_cost += cost;
                    selectedCount++;
                });

                // Update UI
                $('#total_selected').html('<b>' + selectedCount + '</b>');
                $('#total_pro_qty').html('<b>' + total_qty.toFixed(2) + '</b>');
                $('#total_mat_cost').html('<b>' + total_mat_cost.toFixed(2) + '</b>');

                // Show/Hide Checked button
                if (selectedCount > 0) {
                    $('#check_btn_div').css('display', 'inline-block');
                } else {
                    $('#check_btn_div').css('display', 'none');
                }
            }

            // Checkbox change event
            $('#datatable').on('change', 'input.checkbox', function() {
                $(this).closest('tr').toggleClass('selected', this.checked);
                updateTotals();
            });

            // Select All checkbox
            $('input[name="checkall"]').on('change', function() {
                let isChecked = this.checked;
                $('input.checkbox').prop('checked', isChecked);
                $('input.checkbox').closest('tr').toggleClass('selected', isChecked);
                updateTotals();
            });

            // Row click to toggle checkbox
            $('#datatable tbody').on('click', 'tr', function(e) {
                if ($(e.target).is('input[type="checkbox"]') || $(e.target).closest('a').length) return;
                let checkbox = $(this).find('input.checkbox');
                if (checkbox.length) {
                    checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                }
            });

            // Save Bill Update
            $('#btn_bill_update').on('click', function() {
                $('#error_div').empty();
                let bill_no = $('#bill_no_modal').val().trim();
                let ad_qty = $('#ad_qty_modal').val().trim();
                let ad_cost = $('#ad_cost_modal').val().trim();

                $('#bill_no').val(bill_no);
                $('#adjustment_qty').val(ad_qty);
                $('#adjustment_cost').val(ad_cost);

                if (!bill_no) {
                    $('#error_div').html('<p class="text-danger">Please enter Bill No</p>');
                    return;
                }

                $('#checked_bill_form').submit();
            });

            // View Button Data Fill
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

            // AutoComplete
            $("#search_name").autocomplete({
                source: '{{ route('autoComplete', ['table_name' => 'suppliers']) }}',
                minLength: 1,
                autoFocus: true
            });

            $("#search_name2").autocomplete({
                source: '{{ route('autoComplete', ['table_name' => 'product_name']) }}',
                minLength: 1,
                autoFocus: true
            });
        });
    </script>
@endsection
