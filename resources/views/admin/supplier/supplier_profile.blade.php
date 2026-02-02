@extends('admin.layouts.master')
@section('title', 'Supplier Profile')
@section('breadcrumb', 'Supplier Profile')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-4">
                <div class="wBlock red clearfix">
                    <div class="dSpace">
                        <h4>TOTAL PURCHASE</h4>
                        <span class="number">{{ number_format($total_purchase, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wBlock yellow clearfix">
                    <div class="dSpace">
                        <h4>TOTAL PAYMENT</h4>
                        <span class="number">{{ number_format($total_payment, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wBlock green clearfix">
                    <div class="dSpace">
                        <h4>BALANCE</h4>
                        <span class="number">{!! strip_tags($supplier->balanceText()) !!}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="block-fluid tabs ui-tabs ui-widget ui-widget-content ui-corner-all">
                    <div class="head clearfix">
                        <ul class="buttons ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all"
                            role="tablist">
                            <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-profile' ? 'ui-tabs-active ui-state-active' : '' }}"
                                role="tab" tabindex="0" aria-controls="tab-profile" aria-labelledby="ui-id-5"
                                aria-selected="true">
                                <a href="#tab-profile" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                    id="ui-id-5"> <i class="glyphicon glyphicon-user"></i> Supplier Info</a>
                            </li>
                            @if ($user->hasRole(['super-admin']) || $user->can('product-purchase-list'))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-purchase' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-purchase" aria-labelledby="ui-id-6"
                                    aria-selected="false">
                                    <a href="#tab-purchase" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-6"><i class="glyphicon glyphicon-check"></i> Purchase List</a>
                                </li>
                            @endif
                            @if ($user->hasRole(['super-admin']) || $user->can('product-purchase-list'))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-billinfo' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-purchase" aria-labelledby="ui-id-6"
                                    aria-selected="false">
                                    <a href="#tab-billinfo" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-6"><i class="glyphicon glyphicon-check"></i> Bill Info</a>
                                </li>
                            @endif
                            @if ($user->hasRole(['super-admin']) || $user->can('supplier-payment-details'))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-payment' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-payment" aria-labelledby="ui-id-7"
                                    aria-selected="false">
                                    <a href="#tab-payment" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-7"><i class="glyphicon glyphicon-list-alt"></i> Payment Details</a>
                                </li>
                            @endif
                            @if ($user->hasRole(['super-admin']) || $user->can('supplier-statement-report'))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-statement' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-statement" aria-labelledby="ui-id-7"
                                    aria-selected="false">
                                    <a href="#tab-statement" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-7"><i class="glyphicon glyphicon-list"></i> Statements</a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div id="tab-profile" aria-labelledby="ui-id-5" class="ui-tabs-panel ui-widget-content ui-corner-bottom"
                        role="tabpanel" aria-expanded="true" aria-hidden="false" style="display: block;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="block ucard">
                                    <div class="info">
                                        <ul class="rows">
                                            <li class="heading">Supplier Info</li>
                                            <li>
                                                <div class="title">Name:</div>
                                                <div class="text supplier-name">{{ $supplier->name }}</div>
                                            </li>
                                            <li>
                                                <div class="title ">Email:</div>
                                                <div class="text ">{{ $supplier->email }}</div>
                                            </li>
                                            <li>
                                                <div class="title">Mobile No:</div>
                                                <div class="text supplier-phone">{{ $supplier->phone }}</div>
                                            </li>
                                            <li>
                                                <div class="title">Address:</div>
                                                <div class="text supplier-address">{{ $supplier->address }}</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($user->hasRole(['super-admin']) || $user->can('product-purchase-list'))
                        <div id="tab-purchase" aria-labelledby="ui-id-6"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="head clearfix">
                                        <div class="col-md-4">
                                            <div class="isw-documents"></div>
                                            <h1>
                                                Product Purchase List
                                                <span
                                                    class="src-info">{{ request('purchase_search_text') == '' && request('purchase_date_range') == '' ? '- Last 30 Days' : '- ' . request('purchase_date_range') }}</span>
                                            </h1>
                                        </div>
                                        <div class="col-md-1" style="margin-top: 4px;">
                                            <a href="#bill_status_modal" role="button" data-toggle="modal"
                                                class="btn btn-warning" id="check_btn_div"
                                                style="display: none;">Checked</a>
                                        </div>
                                        <div class="col-md-7 search_box" style="margin-top: 4px;">
                                            <form action="" class="form-horizontal">
                                                <input type="hidden" name="tab_type" value="tab-purchase" />
                                                <div class="" align="right">
                                                    <div class="col-md-6">
                                                        <input type="text" name="purchase_search_text"
                                                            id="search_name" value="{{ request('search_text') ?? '' }}"
                                                            class="form-control" placeholder="Enter Search Text" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <input type="text" name="purchase_date_range"
                                                                value="" class="date_range form-control"
                                                                placeholder="Date Range" />
                                                            <div class="input-group-btn">
                                                                <button type="submit"
                                                                    class="btn btn-default search-btn">Search</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                    <div class="block-fluid table-sorting clearfix">
                                        <form action="{{ route('product.purchase.check') }}" method="post"
                                            id="checked_bill_form" class="form-horizontal">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="bill_no" id="bill_no" value="" />
                                            <input type="hidden" name="adjustment_qty" id="adjustment_amount"
                                                value="" />
                                            <input type="hidden" name="adjustment_cost" id="adjustment_cost"
                                                value="" />
                                            <table cellpadding="0" cellspacing="0" width="100%" class="table"
                                                id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" name="checkall" /></th>
                                                        <th>DMR No</th>
                                                        <th>Chalan/ Bill No</th>
                                                        <th>Rec Date</th>
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
                                                    {{-- @dump($purchases) --}}
                                                    @foreach ($purchases as $purchase)
                                                        <tr @if ($purchase->check_status == 1) style="color:#09b509;" @endif>
                                                            <td>
                                                                @if ($purchase->check_status == 0)
                                                                    <input type="checkbox" class="checkbox"
                                                                        value="{{ $purchase->id }}" name="checkbox[]" />
                                                                @else
                                                                    <span class="glyphicon glyphicon-warning-sign"></span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $purchase->dmr_no }}</td>
                                                            <td>{{ $purchase->chalan_no }}</td>
                                                            <td>{{ date('d-M-y', strtotime($purchase->received_date)) }}
                                                            </td>
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
                                                                        data-description="{{ $purchase->description }}"
                                                                        data-toggle="modal"
                                                                        data-target="#purchaseDetailsModal">
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


                                                </tbody>
                                            </table>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('product-purchase-list'))
                        <div id="tab-billinfo" aria-labelledby="ui-id-6"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Header Section -->
                                    <div class="head clearfix">
                                        <div class="col-md-4">
                                            <div class="isw-documents"></div>
                                            <h1>
                                                Bill Info
                                            </h1>
                                        </div>

                                        <div class="col-md-1" style="margin-top: 4px;">
                                            <a href="#bill_status_modal" role="button" data-toggle="modal"
                                                class="btn btn-warning" id="check_btn_div" style="display: none;">
                                                Checked
                                            </a>
                                        </div>

                                        <div class="col-md-7 search_box" style="margin-top: 4px;">
                                            <form action="" class="form-horizontal">
                                                <input type="hidden" name="tab_type" value="tab-billinfo" />
                                                <div class="" align="right">
                                                    <div class="col-md-6">
                                                        <input type="text" name="billinfo_search_text"
                                                            id="search_name"
                                                            value="{{ request('billinfo_search_text') ?? '' }}"
                                                            class="form-control" placeholder="Enter Search Text" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <input type="text" name="billinfo_date_range"
                                                                value="{{ request('billinfo_date_range') ?? '' }}"
                                                                class="date_range form-control"
                                                                placeholder="Date Range" />
                                                            <div class="input-group-btn">
                                                                <button type="submit"
                                                                    class="btn btn-default search-btn">Search</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Table Section -->
                                    <div class="block-fluid table-sorting clearfix">
                                        <form action="{{ route('product.purchase.check') }}" method="post"
                                            id="checked_bill_form" class="form-horizontal">
                                            @csrf
                                            <input type="hidden" name="bill_no" id="bill_no" value="" />
                                            <input type="hidden" name="adjustment_qty" id="adjustment_amount"
                                                value="" />
                                            <input type="hidden" name="adjustment_cost" id="adjustment_cost"
                                                value="" />

                                            <table cellpadding="0" cellspacing="0" width="100%" class="table"
                                                id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" name="checkall" /></th>
                                                        <th>DMR No</th>
                                                        <th>Chalan/ Bill No</th>
                                                        <th>Rec Date</th>
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

                                                    @if (!empty($check_pb))
                                                        @foreach ($check_pb as $checkp)
                                                            <tr
                                                                @if ($checkp->check_status == 1) style="color:#09b509;" @endif>
                                                                <td>
                                                                    @if ($checkp->check_status == 0)
                                                                        <input type="checkbox" id="checkbox"
                                                                            value="{{ $checkp->id }}"
                                                                            name="checkbox[]" />
                                                                    @else
                                                                        <span
                                                                            class="glyphicon glyphicon-warning-sign"></span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $checkp->dmr_no }}</td>
                                                                <td>{{ $checkp->bill_no }}</td>
                                                                <td>{{ date('d-m-Y', strtotime($checkp->received_date)) }}
                                                                </td>
                                                                <td>{{ $checkp->product_name->name }}</td>
                                                                <td>{{ number_format($checkp->product_qty, 2) . ' ' . $checkp->unit_type }}
                                                                </td>
                                                                <td>{{ number_format($checkp->rate_per_unit, 2) }}</td>
                                                                <td>{{ number_format($checkp->material_cost, 2) }}</td>
                                                                <td>{{ number_format($checkp->truck_rent, 2) }}</td>
                                                                <td>{{ number_format($checkp->unload_bill, 2) }}</td>
                                                                <td>{{ number_format($checkp->total_material_cost, 2) }}
                                                                </td>
                                                                <td class="hidden-print">
                                                                    @if ($user->hasRole('super-admin') || $user->can('product-purchase-list-details'))
                                                                        <a role="button" class="view_btn"
                                                                            data-dmr_no="{{ $checkp->supplier->name }}"
                                                                            data-chalan_no="{{ $checkp->chalan_no }}"
                                                                            data-purchase_date="{{ $checkp->purchase_date }}"
                                                                            data-received_date="{{ $checkp->received_date }}"
                                                                            data-product_name="{{ $checkp->product_name->name }}"
                                                                            data-supplier_name="{{ $checkp->supplier->name }}"
                                                                            data-quantity="{{ $checkp->product_qty }}"
                                                                            data-rate_per_unit="{{ $checkp->rate_per_unit }}"
                                                                            data-material_cost="{{ $checkp->material_cost }}"
                                                                            data-truck_rent="{{ $checkp->truck_rent }}"
                                                                            data-unload_bill="{{ $checkp->unload_bill }}"
                                                                            data-total_material_cost="{{ $checkp->total_material_cost }}"
                                                                            data-vehicle_no="{{ $checkp->vehicle_no }}"
                                                                            data-description="{{ $checkp->description }}"
                                                                            data-toggle="modal"
                                                                            data-target="#detailsModal">
                                                                            <span class="fa fa-eye"></span>
                                                                        </a>
                                                                        <a href="{{ route('purchase.checked.details', $checkp->bill_no) }}"
                                                                            target="_blank">
                                                                            <span class="fa fa-info-circle"></span>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            $i++;
                                                            $t_mat += $checkp->material_cost;
                                                            $tt_mat += $checkp->total_material_cost;
                                                            $total_qty += $checkp->product_qty;
                                                            $total_truck_rent += $checkp->truck_rent;
                                                            $total_unload_bill += $checkp->unload_bill;
                                                            ?>
                                                        @endforeach
                                                    @endif

                                                    <!-- Total Row -->
                                                    <tr style="background-color:#999999; color: #fff;">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Total:</td>
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
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('supplier-payment-details'))
                        <div id="tab-payment" aria-labelledby="ui-id-7"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="head clearfix">
                                        <div class="col-md-4">
                                            <div class="isw-documents"></div>
                                            <h1>
                                                Supplier Payment Details
                                                <span
                                                    class="src-info">{{ request('payment_search_text') == '' && request('payment_date_range') == '' ? '- Last 30 Days' : '- ' . request('payment_date_range') }}</span>
                                            </h1>
                                        </div>

                                        <div class="col-md-7 col-md-offset-1 search_box" style="margin-top: 4px;">
                                            <form action="" class="form-horizontal">
                                                <input type="hidden" name="tab_type" value="tab-payment" />
                                                <div class="" align="right">
                                                    <div class="col-md-6">
                                                        <input type="text" name="payment_search_text" id="search_name"
                                                            value="{{ request('payment_search_text') ?? '' }}"
                                                            class="form-control" placeholder="Enter Search Text" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <input type="text" name="payment_date_range"
                                                                value="" class="date_range form-control"
                                                                placeholder="Date Range" />
                                                            <div class="input-group-btn">
                                                                <button type="submit"
                                                                    class="btn btn-default search-btn">Search</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                    <div class="block-fluid table-sorting clearfix">
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                            class="table table-striped table-responsive" id="datatable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" value="" name="checkall" />
                                                    </th>
                                                    <th>Tran Id</th>
                                                    <th>Ref date</th>
                                                    <th>Description</th>
                                                    <th>Payment Mode</th>
                                                    <th>Branch Name</th>
                                                    <th class="text-right">Paid Amount</th>
                                                    <th>Files</th>
                                                    <th class="hidden-print">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $total = 0;
                                                $ad_total = 0; ?>
                                                @foreach ($payments as $payment)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" id="checkbox" name="checkbox[]" />
                                                        </td>
                                                        <td>{{ $payment->transaction_id }}</td>
                                                        <td>{{ date('d-m-Y', strtotime($payment->ref_date)) }}</td>
                                                        <td>{{ $payment->description }}</td>
                                                        <td>
                                                            @if ($payment->payment_mode == 'Bank')
                                                                Bank: {{ $payment->bank_info->short_name }},
                                                                A/C no: {{ $payment->bank_info->account_no }}, Cheque no:
                                                                {{ $payment->cheque_no }}
                                                            @else
                                                                {{ 'Cash' }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $payment->branch->name ?? '-' }}</td>
                                                        <td class="text-right">
                                                            {{ number_format($payment->paid_amount, 2) }}</td>
                                                        <td>
                                                            <?php $file_text = $payment->file;
                                                            $files = explode(',', $file_text); ?>
                                                            @foreach ($files as $file)
                                                                <a href="{{ URL::to('/img/files/expense_files/supplier_payment/' . $file) }}"
                                                                    target="_blank"
                                                                    rel="tag"><?php echo $file; ?></a><br>
                                                            @endforeach
                                                        </td>
                                                        <td class="hidden-print">
                                                            @if ($user->branchId == '')
                                                                @if ($user->hasRole('super-admin') || $user->can('supplier-payment-details'))
                                                                    <a role="button" class="view_btn"
                                                                        data-supplier_name="{{ $payment->supplier->name }}"
                                                                        data-trx_id="{{ $payment->transaction_id }}"
                                                                        data-voucher_no="{{ $payment->voucher_no }}"
                                                                        data-payment_date="{{ $payment->payment_date }}"
                                                                        data-payment_mode="{{ $payment->payment_mode }}"
                                                                        data-bank_name="{{ $payment->bank_info->short_name ?? '-' }}"
                                                                        data-cheque_no="{{ $payment->cheque_no ?? '' }}"
                                                                        data-cheque_date="{{ $payment->ref_date }}"
                                                                        data-payment_amount="{{ $payment->paid_amount }}"
                                                                        data-adjustment_amount="{{ $payment->adjustment_amount }}"
                                                                        data-description="{{ $payment->description }}"
                                                                        data-toggle="modal"
                                                                        data-target="#paymentDetailsModal">
                                                                        <span class="fa fa-eye"></span>
                                                                    </a>
                                                                @endif

                                                                @if ($user->hasRole('super-admin') || $user->can('supplier-payment-delete'))
                                                                    <a href="{{ route('supplier.payment.delete', $payment->transaction_id) }}"
                                                                        onclick="return confirm('Are you sure you want to delete?')"><span
                                                                            class="fa fa-trash"></span></a>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <?php $total += $payment->paid_amount;
                                                    $ad_total += $payment->adjustment_amount; ?>
                                                @endforeach
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-right">
                                                        <b>Total = </b>
                                                    </td>
                                                    <td class="text-right">
                                                        <b>{{ number_format($total, 2) }}</b>
                                                    </td>
                                                    <td></td>
                                                    <td class="hidden-print"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                        </div>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('supplier-statement-report'))
                        <div id="tab-statement" aria-labelledby="ui-id-7"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">

                                    {{-- PRINT BUTTONS --}}
                                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px;">
                                        <button onclick="printStatement('tab-statement', 'pad')"
                                            class="btn btn-sm btn-info no-print">Print (With Pad)</button>
                                        <button onclick="printStatement('tab-statement', 'non-pad')"
                                            class="btn btn-sm btn-success no-print">Print (Without Pad)</button>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="col-md-4">
                                            <div class="isw-documents"></div>
                                            <h3>
                                                View Supplier Statement
                                                <span class="src-info">
                                                    {{ request('statement_search_text') == '' && request('date_range') == '' ? '- Last 30 days' : '- ' . request('date_range') }}
                                                </span>
                                            </h3>
                                        </div>

                                        <div class="col-md-7 search_box" style="margin-top: 4px;">
                                            <form action="" class="form-horizontal">
                                                <input type="hidden" name="tab_type" value="tab-statement" />
                                                <div class="" align="right">
                                                    <div class="col-md-6">
                                                        <input type="text" name="statement_search_text"
                                                            id="search_name" class="form-control"
                                                            value="{{ request('statement_search_text') ?? '' }}"
                                                            placeholder="Enter Search Text" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <input type="text" name="date_range"
                                                                value="{{ request('date_range') ?? '' }}"
                                                                class="date_range form-control" placeholder="Date Range"
                                                                autocomplete="off" />
                                                            <div class="input-group-btn">
                                                                <button type="submit" id="btn_search"
                                                                    class="btn btn-default search-btn">Search</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="block-fluid table-sorting clearfix">
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                            class="table table-striped" id="datatable">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" value="" name="checkall" /></th>
                                                    <th>Trx Id</th>
                                                    <th>Chalan No</th>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Quantity</th>
                                                    <th>Truck Rent</th>
                                                    <th>Unload Bill</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                    <th>Adj Amount</th>
                                                    <th>Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Opening Balance Row --}}
                                                {{-- @if (isset($opening_balance))
                                                    <tr class="font-weight-bold" style="background-color: #f8f9fa;">
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ isset($opening_date) ? date('d-M-y', strtotime($opening_date)) : date('d-M-y') }}
                                                        </td>
                                                        <td>Opening Balance</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $opening_balance > 0 ? number_format($opening_balance, 2) : '' }}
                                                        </td>
                                                        <td>{{ $opening_balance < 0 ? number_format(abs($opening_balance), 2) : '' }}
                                                        </td>
                                                        <td>{{ number_format($opening_balance, 4) }}</td>
                                                    </tr>
                                                @endif --}}

                                                {{-- Initialize variables BEFORE the loop --}}
                                                @php
                                                    $total_qty = 0;
                                                    $total_truck_rent = 0;
                                                    $total_unload = 0;
                                                    $total_adjustment = 0;
                                                    $total_debit = 0;
                                                    $total_credit = 0;
                                                    $running_balance = 0;
                                                @endphp

                                                @foreach ($statements as $index => $statement)
                                                    @php
                                                        $payment_details = 'Cash';
                                                        if (
                                                            $statement->supplier_payment &&
                                                            $statement->supplier_payment->bank_id
                                                        ) {
                                                            $bank = $statement->supplier_payment->bank_info;
                                                            $payment_details = "{$statement->supplier_payment->payment_mode}: {$bank->short_name}, AC No:{$bank->account_no}, Cheque/Receipt no:{$statement->supplier_payment->cheque_no}";
                                                        }

                                                        $adjustment_amount = $statement->supplier_payment
                                                            ? $statement->supplier_payment->adjustment_amount
                                                            : 0;
                                                        $bill_adjustment = 0;

                                                        $trans_id_parts = explode('-', $statement->transaction_id);
                                                        $trans_prefix = $trans_id_parts[0] ?? '';

                                                        if ($trans_prefix == 'BILLAD') {
                                                            $bill_adjustment = $statement->debit;
                                                        }

                                                        $total_adjustment_row = $bill_adjustment + $adjustment_amount;

                                                        $quantity = 0;
                                                        $quantity_display = '';
                                                        $truck_rent = 0;
                                                        $unload = 0;

                                                        if ($statement->product_purchase) {
                                                            $pp = $statement->product_purchase;
                                                            $quantity = round($pp->product_qty, 3);
                                                            $quantity_display = $quantity . ' ' . $pp->unit_type;
                                                            $truck_rent = $pp->truck_rent ?? 0;
                                                            $unload = $pp->unload_bill ?? 0;
                                                        }

                                                        $display_qty = $quantity_display;

                                                        if (
                                                            $trans_prefix == 'BILLAD' &&
                                                            $statement->adjustment_amount > 0
                                                        ) {
                                                            $display_qty = -$statement->adjustment_amount;
                                                            $quantity = -$statement->adjustment_amount;
                                                        }

                                                        $display_debit = $statement->debit - $total_adjustment_row;
                                                        $display_credit = $statement->credit - $total_adjustment_row;

                                                        $running_balance =
                                                            $running_balance + $display_credit - $display_debit;
                                                    @endphp

                                                    <tr>
                                                        <td><input type="checkbox" name="checkbox[]"
                                                                value="{{ $statement->id }}" /></td>
                                                        <td>{{ $statement->transaction_id }}</td>
                                                        <td>{{ $statement->product_purchase->chalan_no ?? '' }}</td>
                                                        <td>{{ date('d-M-y', strtotime($statement->posting_date)) }}</td>
                                                        <td>{{ $statement->description }}</td>
                                                        <td>{{ is_numeric($display_qty) ? number_format($display_qty, 2) : $display_qty }}
                                                        </td>
                                                        <td>{{ number_format($truck_rent, 2) }}</td>
                                                        <td>{{ number_format($unload, 2) }}</td>
                                                        <td>{{ $display_debit > 0 ? number_format($display_debit, 2) : '' }}
                                                        </td>
                                                        <td>{{ $display_credit > 0 ? number_format($display_credit, 2) : '' }}
                                                        </td>
                                                        <td>{{ number_format($total_adjustment_row, 2) }}</td>
                                                        <td
                                                            class="{{ $running_balance < 0 ? 'text-danger' : 'text-dark' }}">
                                                            {{ number_format($running_balance, 2) }}
                                                        </td>
                                                    </tr>

                                                    @php

                                                        $total_qty += $quantity;
                                                        $total_truck_rent += $truck_rent;
                                                        $total_unload += $unload;
                                                        $total_adjustment += $total_adjustment_row;
                                                        $total_debit += $display_debit;
                                                        $total_credit += $display_credit;
                                                    @endphp
                                                @endforeach

                                                {{-- Display totals --}}
                                                <tr class="font-weight-bold">

                                                    <td colspan="4" class="text-right"><b>Balance:</b></td>
                                                    <td></td>
                                                    <td><b>{{ number_format($total_qty, 2) }}</b></td>
                                                    <td><b>{{ number_format($total_truck_rent, 2) }}</b></td>
                                                    <td><b>{{ number_format($total_unload, 2) }}</b></td>
                                                    <td><b>{{ number_format($total_debit, 2) }}</b></td>
                                                    <td><b>{{ number_format($total_credit, 2) }}</b></td>
                                                    <td><b>{{ number_format($total_adjustment, 2) }}</b></td>
                                                    <td><b>{{ number_format($running_balance, 2) }}</b></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="dr"><span></span></div>

    </div>

    <!-- purchase details modal form -->
    <div class="modal fade" id="purchaseDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
    <!-- payment details modal form -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Supplier Payment Details</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            <div class="row-form clearfix">
                                <div class="col-md-3">Supplier Name: </div>
                                <div class="col-md-6" id="supplier_name"></div>
                            </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Transaction Id: </div>
                                <div class="col-md-6" id="trx_id"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Voucher No: </div>
                                <div class="col-md-6" id="voucher_no"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Payment Date : </div>
                                <div class="col-md-6" id="payment_date">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Payment Mode : </div>
                                <div class="col-md-6" id="payment_mode"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Bank Name : </div>
                                <div class="col-md-6" id="bank_name"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Cheque No : </div>
                                <div class="col-md-6" id="cheque_no"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Cheque Date : </div>
                                <div class="col-md-6" id="cheque_date"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Amount :</div>
                                <div class="col-md-6" id="payment_amount"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Adjustment Amount :</div>
                                <div class="col-md-6" id="adjustment_amount"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Description</div>
                                <div class="col-md-6" id="description"></div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
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
            // Debug: Check if DataTable is initialized
            console.log("Table instance:", typeof table, table);

            //update no and change color
            $("#btn_bill_update").on('click', function() {
                $("#error_div").empty();
                var bill_no = $("#bill_no_modal").val();
                var ad_qty = $("#ad_qty_modal").val();
                var ad_cost = $("#ad_cost_modal").val();
                $("#bill_no").val(bill_no);
                $("#adjustment_amount").val(ad_qty);
                $("#adjustment_cost").val(ad_cost);

                if (bill_no === "") {
                    $("#error_div").append("<p>Please enter bill no</p>");
                    return false;
                }

                // Use setTimeout to ensure modal animation completes
                setTimeout(function() {
                    $("#checked_bill_form").submit();
                }, 300);
            });

            // Row click handler
            $("#datatable tbody").on('click', 'tr', function(e) {
                // Don't trigger on checkbox click
                if ($(e.target).is('input[type="checkbox"]')) return;

                var checkbox = $(this).find("input[type=checkbox]");
                var isChecked = checkbox.prop('checked');

                // Toggle checkbox
                checkbox.prop('checked', !isChecked);

                // Toggle selected class
                if (!isChecked) {
                    $(this).addClass('selected');
                } else {
                    $(this).removeClass('selected');
                }

                // Trigger change event
                checkbox.trigger('change');
            });

            // Select all checkbox handler
            $("#datatable thead tr input[type=checkbox]").on('click', function() {
                var isChecked = $(this).prop('checked');

                // Check/uncheck all checkboxes
                $("#datatable tbody input[type=checkbox]").prop('checked', isChecked);

                // Add/remove selected class
                if (isChecked) {
                    $("#datatable tbody tr").addClass('selected');
                } else {
                    $("#datatable tbody tr").removeClass('selected');
                }

                // Trigger change event on all checkboxes
                $("#datatable tbody input[type=checkbox]").trigger('change');
            });

            // Checkbox change handler
            $(document).on('change', '#datatable tbody input[type=checkbox]', function() {
                console.log("Checkbox changed");

                var checkedBoxes = $("#datatable tbody input[type=checkbox]:checked");
                var len = checkedBoxes.length;
                var total_mat_cost = 0;
                var total_qty = 0;

                console.log("Checked boxes count:", len);

                if (len > 0) {
                    checkedBoxes.each(function(index) {
                        console.log("Processing checkbox", index);

                        var $row = $(this).closest('tr');
                        var rowIndex = $row.index();

                        // Try multiple ways to get row data
                        var rowData = null;

                        // Method 1: Using DataTables API if available
                        if (typeof table !== 'undefined' && table) {
                            try {
                                rowData = table.row($row).data();
                                console.log("Row data from DataTable API:", rowData);
                            } catch (e) {
                                console.log("Error getting DataTable row:", e);
                            }
                        }

                        // Method 2: Get data from table cells directly
                        if (!rowData || rowData.length === 0) {
                            rowData = [];
                            $row.find('td').each(function(i, cell) {
                                rowData.push($(cell).text().trim());
                            });
                            console.log("Row data from DOM:", rowData);
                        }

                        if (rowData && rowData.length > 0) {
                            // Parse quantity (assuming column 6, index 5)
                            var qtyCell = rowData[5] || rowData[6] || '0';
                            console.log("Quantity cell text:", qtyCell);

                            // Extract numeric value from string
                            var qtyValue = 0;
                            if (typeof qtyCell === 'string') {
                                // Remove commas and extract number
                                var cleanQty = qtyCell.replace(/,/g, '');
                                var qtyMatch = cleanQty.match(/[-+]?[0-9]*\.?[0-9]+/);
                                qtyValue = qtyMatch ? parseFloat(qtyMatch[0]) : 0;
                            } else {
                                qtyValue = parseFloat(qtyCell) || 0;
                            }

                            console.log("Parsed quantity:", qtyValue);
                            total_qty += qtyValue;

                            // Parse material cost (assuming column 8, index 7)
                            var costCell = rowData[7] || rowData[8] || '0';
                            console.log("Cost cell text:", costCell);

                            // Extract numeric value from string
                            var costValue = 0;
                            if (typeof costCell === 'string') {
                                // Remove currency symbols, commas and extract number
                                var cleanCost = costCell.replace(/[$,]/g, '');
                                var costMatch = cleanCost.match(/[-+]?[0-9]*\.?[0-9]+/);
                                costValue = costMatch ? parseFloat(costMatch[0]) : 0;
                            } else {
                                costValue = parseFloat(costCell) || 0;
                            }

                            console.log("Parsed cost:", costValue);
                            total_mat_cost += costValue;
                        }
                    });

                    console.log("Total Quantity:", total_qty);
                    console.log("Total Mat Cost:", total_mat_cost);

                    // Update display with formatted numbers
                    $("#total_pro_qty").html('<b>' + total_qty.toFixed(2) + '</b>');
                    $("#total_mat_cost").html('<b>' + total_mat_cost.toFixed(2) + '</b>');
                    $("#total_selected").html('<b>' + len + '</b>');
                    $("#check_btn_div").css('display', 'inline-block');
                } else {
                    $("#total_pro_qty").html('<b>0.00</b>');
                    $("#total_mat_cost").html('<b>0.00</b>');
                    $("#total_selected").html('<b>0</b>');
                    $("#check_btn_div").css('display', 'none');
                }

                // Prevent default if needed
                return false;
            });

            // View button handler
            $(document).on('click', '.view_btn', function(e) {
                e.preventDefault();

                // Get data attributes
                var $btn = $(this);
                $('#dmr_no').html($btn.data('dmr_no') || '');
                $('#chalan_no').html($btn.data('chalan_no') || '');
                $('#purchase_date').html($btn.data('purchase_date') || '');
                $('#received_date').html($btn.data('received_date') || '');
                $('#product_name').html($btn.data('product_name') || '');
                $('#supplier_name').html($btn.data('supplier_name') || '');
                $('#quantity').html($btn.data('quantity') || '');
                $('#rate_per_unit').html($btn.data('rate_per_unit') || '');
                $('#material_cost').html($btn.data('material_cost') || '');
                $('#truck_rent').html($btn.data('truck_rent') || '');
                $('#unload_bill').html($btn.data('unload_bill') || '');
                $('#total_material_cost').html($btn.data('total_material_cost') || '');
                $('#vehicle_no').html($btn.data('vehicle_no') || '');
                $('#description').html($btn.data('description') || '');

                // Show modal
                $('#viewModal').modal('show');
            });

            // Initialize event for the first time
            setTimeout(function() {
                $("#datatable tbody input[type=checkbox]:checked").trigger('change');
            }, 1000);
        });
    </script>
@endsection
<script>
    function printStatement(elementId, mode) {
        const FIRST_PAGE_ROW_LIMIT = 17;
        const OTHER_PAGE_ROW_LIMIT = 16;

        const container = document.getElementById(elementId);
        if (!container) {
            alert('Content container not found!');
            return;
        }

        const headingText = 'Supplier Statement';

        // Get the statement table
        const table = container.querySelector('table#datatable');
        if (!table) {
            alert('Statement table not found!');
            return;
        }

        // Extract supplier info from the page
        const supplierName = document.querySelector('.supplier-name')?.innerText?.trim() || '';
        const supplierAddress = document.querySelector('.supplier-address')?.innerText?.trim() || '';
        const supplierPhone = document.querySelector('.supplier-phone')?.innerText?.trim() || '';
        const statementRange = container.querySelector('.src-info')?.innerText?.replace('- ', '').trim() ||
            'Last 30 days';

        // Get thead and rows
        const thead = table.querySelector('thead');
        const theadHtml = thead.outerHTML;
        const tbodyRows = Array.from(table.querySelectorAll('tbody tr'));

        // Get column count from table header
        const headerCells = thead.querySelectorAll('th');
        const columnCount = headerCells.length;

        // ---- Calculate summary (Updated Logic for Footer Rows) ----
        let openingBalance = 0;
        let closingBalance = 0;
        let debitTotal = 0;
        let creditTotal = 0;
        let debitCount = 0;
        let creditCount = 0;
        let isFirstRowFound = false;

        tbodyRows.forEach((tr) => {
            const cells = tr.querySelectorAll('td');
            if (cells.length === 0) return;

            // Check if this row looks like a "Total" / Footer row
            const isTotalTextRow = Array.from(cells).some(cell =>
                cell.innerText.trim().toLowerCase().includes('total') ||
                cell.innerText.trim().toLowerCase().includes('trash')
            );

            // Column indices based on your table structure
            let debitIdx, creditIdx, balanceIdx;

            if (columnCount === 9) {
                debitIdx = 6;
                creditIdx = 7;
                balanceIdx = 8;
            } else if (columnCount >= 11) {
                debitIdx = 8;
                creditIdx = 9;
                balanceIdx = 10;
            } else {
                debitIdx = cells.length - 3;
                creditIdx = cells.length - 2;
                balanceIdx = cells.length - 1;
            }

            let balanceVal = 0;
            let debitVal = 0;
            let creditVal = 0;
            let isFooterStructure = false;

            // If indices are out of bounds, it means the row has merged columns (likely the Total row)
            if (debitIdx >= cells.length || creditIdx >= cells.length || balanceIdx >= cells.length) {
                isFooterStructure = true;
                // Try to find the numeric balance manually in the cells (usually the last cell with a number)
                for (let i = cells.length - 1; i >= 0; i--) {
                    const cellText = cells[i].innerText.replace(/,/g, '');
                    const val = parseFloat(cellText);
                    if (!isNaN(val)) {
                        balanceVal = val;
                        break;
                    }
                }
            } else {
                // Normal row structure
                debitVal = parseFloat((cells[debitIdx].innerText || '0').replace(/,/g, '')) || 0;
                creditVal = parseFloat((cells[creditIdx].innerText || '0').replace(/,/g, '')) || 0;
                balanceVal = parseFloat((cells[balanceIdx].innerText || '0').replace(/,/g, '')) || 0;
            }

            // --- CLOSING BALANCE LOGIC ---
            // Always update closingBalance. The last valid value found will persist.
            closingBalance = balanceVal;

            // Skip calculation for Total/Trash rows or Footer structures
            if (isTotalTextRow || isFooterStructure) {
                return;
            }

            // --- OPENING BALANCE LOGIC ---
            if (!isFirstRowFound) {
                openingBalance = balanceVal - debitVal + creditVal;
                isFirstRowFound = true;
            }

            // Accumulate totals
            debitTotal += debitVal;
            creditTotal += creditVal;
            if (debitVal > 0) debitCount++;
            if (creditVal > 0) creditCount++;
        });

        // ---- Build Supplier Info Block ----
        const supplierInfoHtml = `
      <div class="supplier-info" 
     style="margin-bottom:15px; font-size:13px; padding-left:10px; display:flex; flex-direction:column; gap:5px;">
    <div><strong>Supplier Name:</strong> ${supplierName}</div>
    <div><strong>Supplier Address:</strong> ${supplierAddress}</div>
    <div><strong>Supplier Phone:</strong> ${supplierPhone}</div>
    <div><strong>Statement Period:</strong> ${statementRange}</div>
</div>

    `;

        // ---- Summary Table ----
        const summaryHtml = `
       <div class="statement-summary" 
     style="margin-top:8px; font-size:15px; font-weight:bold; background:#f3f3f3; padding:10px; border-radius:6px;">
    
    <table style="width:100%; border-collapse:collapse; border:none;">
        <tr>
            <td style="border:none;"></td>
            <td style="border:none;">Opening Balance</td>
            <td style="text-align:right; border:none;">
                ${openingBalance.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}
            </td>
            <td style="border:none;"></td>
            <td style="border:none;">Closing Balance</td>
            <td style="text-align:right; border:none;">
                ${closingBalance.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}
            </td>
            <td style="border:none;"></td>
        </tr>

        <tr>
            <td style="border:none;"></td>
            <td style="border:none;">Total Debit</td>
            <td style="text-align:right; border:none;">
                ${debitTotal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}
            </td>
            <td style="border:none;"></td>
            <td style="border:none;">Debit Count</td>
            <td style="text-align:right; border:none;">${debitCount}</td>
            <td style="border:none;"></td>
        </tr>

        <tr>
            <td style="border:none;"></td>
            <td style="border:none;">Total Credit</td>
            <td style="text-align:right; border:none;">
                ${creditTotal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}
            </td>
            <td style="border:none;"></td>
            <td style="border:none;">Credit Count</td>
            <td style="text-align:right; border:none;">${creditCount}</td>
            <td style="border:none;"></td>
        </tr>
       
    </table>
 <p style="text-align:center; font-size:10px;">Please note that if no reply is received from you within a 14 days, it will be assumed that you have accepted the balance shown Above.</p>

</div>

    `;

        // ---- Split Pages with different row limits ----
        const pages = [];
        let currentIndex = 0;
        let pageNumber = 0;

        const filteredRows = tbodyRows.filter(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length === 0) return false;
            const anyCellHasTotal = Array.from(cells).some(cell =>
                cell.innerText.trim().toLowerCase().includes('total')
            );
            return !anyCellHasTotal;
        });

        while (currentIndex < filteredRows.length) {
            const rowLimit = pageNumber === 0 ? FIRST_PAGE_ROW_LIMIT : OTHER_PAGE_ROW_LIMIT;
            const rowsSlice = filteredRows.slice(currentIndex, currentIndex + rowLimit);
            const rowsHtml = rowsSlice.map(r => r.outerHTML).join('');
            const isLastPage = (currentIndex + rowLimit >= filteredRows.length);

            const tableHtml = `
            <table cellpadding="0" cellspacing="0" width="100%" class="table">
                ${theadHtml}
                <tbody>${rowsHtml}</tbody>
            </table>
            ${isLastPage ? summaryHtml : ''}
        `;

            const watermarkHtml = (mode !== 'pad') ? `
    <img src="{{ asset('assets/images/logo.png') }}" class="watermark" />
` : '';

            const pageContent = `
            <div class="content">
                ${watermarkHtml}
                <div class="invoice-title" style="text-align:center; font-weight:bold; font-size:18px;">
                    ${headingText}
                </div>
                ${pageNumber === 0 ? supplierInfoHtml : ''}
                ${tableHtml}
            </div>
            `;

            pages.push(pageContent);
            currentIndex += rowLimit;
            pageNumber++;
        }

        // ---- Header/Footer ----
        const headerHtml = `
        <div class="header">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo">
            <div class="company-name">Concept Concrete Limited</div>
        </div>
    `;

        const footerHtml = `
        <div class="footer">
            <div class="border-green"></div>
            <div class="border-black"></div>
            <div class="footer-content">
                Head Office: 1 No. Joykali Mondir Road (1st Floor), Wari, Dhaka-1203. 📞02-9592241 📧ccbd16@gmail.com 🌐/cclbd16
            </div>
        </div>
    `;

        // ---- Compose All Pages ----
        const allPagesHtml = pages.map((page, index) => `
        <div class="page">
            ${mode === 'non-pad' ? headerHtml : ''}
            ${page}
            ${mode === 'non-pad' ? footerHtml : ''}
        </div>
    `).join('');

        // ---- Final HTML ----
        const fullHtml = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${headingText}</title>
            <style>
                @page {
                    size: A4;
                    margin: 0;
                }
                body {
                    margin: 0;
                    font-family: Arial, sans-serif;
                    font-size: 10px;
                    background: #fff;
                }
                
                .page {
                    position: relative;
                    width: 100%;
                    height: 297mm;
                    min-height: 297mm;
                    box-sizing: border-box;
                    overflow: hidden;
                    page-break-after: always;
                    break-after: page;
                }
                
                .page:last-child {
                    page-break-after: auto;
                    break-after: auto;
                    height: auto;
                    min-height: 297mm;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                }
                
                thead {
                    display: table-header-group;
                }
                
                th, td {
                    padding: 4px;
                    text-align: left;
                    word-wrap: break-word;
                    font-size: 9px;
                    border: 1px solid #ddd;
                }

                .header {
                    position: relative;
                    width: 100%;
                    height: 135px;
                    padding: 30px 45px;
                    border-bottom: 4px solid green;
                    box-sizing: border-box;
                    display: flex;
                    align-items: center;
                    background: white;
                    z-index: 10;
                }
                .logo {
                    height: 70px;
                    margin-right: 15px;
                }
                .company-name {
                    font-size: 40px;
                    font-weight: bold;
                    color: #00aeef;
                    flex: 1;
                }

                .content {
                    padding: 20px 20px 90px 20px;
                    box-sizing: border-box;
                    position: relative;
                    z-index: 1;
                    height: calc(100% - 135px);
                }
                
                .invoice-title {
                    text-align: center;
                    font-size: 16px;
                    font-weight: bold;
                    margin: 20px 0 10px 0;
                }

                .footer {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    height: 80px;
                    padding: 0 35px;
                    font-size: 13px;
                    box-sizing: border-box;
                    border-top: 4px solid black;
                    background: white;
                    z-index: 10;
                }
                
                .footer .border-green {
                    height: 4px;
                    background-color: green;
                    width: 100%;
                }
                .footer .border-black {
                    height: 4px;
                    background-color: black;
                    width: 100%;
                }
                .footer-content {
                    text-align: center;
                    margin-top: 5px;
                }
                
                .watermark {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    opacity: 0.07;
                    width: 500px;
                    height: auto;
                    z-index: 0;
                    pointer-events: none;
                }
                
                .supplier-info, .statement-summary table, .statement-summary th, .statement-summary td {
                    position: relative;
                    z-index: 1;
                }
                .statement-summary table, .statement-summary th, .statement-summary td {
                    border: 1px solid #333;
                    border-collapse: collapse;
                }

                @media print {
                    body { -webkit-print-color-adjust: exact; }
                    .no-print, .hidden-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            ${allPagesHtml}
        </body>
        </html>
    `;

        // ---- Print ----
        const printWindow = window.open('', '_blank');
        printWindow.document.open();
        printWindow.document.write(fullHtml);
        printWindow.document.close();

        printWindow.onload = function() {
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        };
    }
</script>
