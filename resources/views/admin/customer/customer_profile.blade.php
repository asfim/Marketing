@extends('admin.layouts.master')
@section('title', 'Customer Profile | ' . $customer->name)
@section('breadcrumb', 'Customer Profile')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if ($user->hasRole('super-admin') || $user->can('customer-add-project'))
        <li><a role="button" data-target="#addProjectModal" data-toggle="modal"><span
                    class="glyphicon glyphicon-briefcase"></span> Add New Project</a></li>
    @endif
    @if ($user->hasRole('super-admin') || $user->can('challan-create'))
        <li><a role="button" data-target="#addChallanModal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span>
                Create Challan</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-3">
                <div class="wBlock red clearfix">
                    <div class="dSpace">
                        <h4>TOTAL BILL</h4>
                        <span class="number">{{ number_format($total_bill - $total_adjustment, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wBlock clearfix">
                    <div class="dSpace">
                        <h4>INVOICE AMOUNT</h4>
                        <span class="number">{{ number_format($total_billable, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wBlock yellow clearfix">
                    <div class="dSpace">
                        <h4>TOTAL PAYMENT</h4>
                        <span class="number">{{ number_format($total_payment - $total_adjustment, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wBlock green clearfix">
                    <div class="dSpace">
                        <h4>BALANCE</h4>
                        {{-- background-color: rgb(0, 145, 189) !important; padding: 3px  --}}
                        <span class="number" style="font-size: 19px !important; ">{!! strip_tags($customer->balanceText()) !!}</span>
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
                                    id="ui-id-5"> <i class="glyphicon glyphicon-user"></i> Customer Info</a>
                            </li>
                            @if ($user->hasRole('super-admin') || $user->can('challan-list'))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-challan' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-challan" aria-labelledby="ui-id-7"
                                    aria-selected="false">
                                    <a href="#tab-challan" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-7"><i class="glyphicon glyphicon-list-alt"></i> Challan List</a>
                                </li>
                            @endif

                            @if ($user->hasRole('super-admin') || $user->can('demo-bill-create'))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-demo' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-demo" aria-labelledby="ui-id-7"
                                    aria-selected="false">
                                    <a href="#tab-demo" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-7">
                                        <i class="glyphicon glyphicon-plus"></i> Demo Generate Bill
                                    </a>
                                </li>
                            @endif

                            @if ($user->hasRole('super-admin') || $user->can('demo-bill-list'))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-demo-bills' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-demo-bills" aria-labelledby="ui-id-6"
                                    aria-selected="false">
                                    <a href="#tab-demo-bills" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-6">
                                        <i class="glyphicon glyphicon-file"></i> Demo Bill Info
                                    </a>
                                </li>
                            @endif

                            @if ($user->branchId == '' && ($user->hasRole('super-admin') || $user->can('bill-create')))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-bill-generate' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-bill-generate"
                                    aria-labelledby="ui-id-7" aria-selected="false">
                                    <a href="#tab-bill-generate" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-7"><i class="glyphicon glyphicon-briefcase"></i> Generate
                                        Bill</a>
                                </li>
                            @endif
                            @if ($user->branchId == '14' || ($user->hasRole('super-admin') || $user->can('bill-list')))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-bills' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-bills" aria-labelledby="ui-id-6"
                                    aria-selected="false">
                                    <a href="#tab-bills" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-6"><i class="glyphicon glyphicon-check"></i> Bill Info</a>
                                </li>
                            @endif
                            @if ($user->hasRole(['super-admin']) || $user->can('customer-payment-details'))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-payment' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-payment" aria-labelledby="ui-id-7"
                                    aria-selected="false">
                                    <a href="#tab-payment" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-7"><i class="glyphicon glyphicon-list-alt"></i> Payment Details</a>
                                </li>
                            @endif
                            @if ($user->hasRole(['super-admin']) || $user->can('customer-statement-report'))
                                <li class="ui-state-default ui-corner-top {{ $selected_tab == 'tab-statement' ? 'ui-tabs-active ui-state-active' : '' }}"
                                    role="tab" tabindex="-1" aria-controls="tab-statement"
                                    aria-labelledby="ui-id-7" aria-selected="false">
                                    <a href="#tab-statement" class="ui-tabs-anchor" role="presentation" tabindex="-1"
                                        id="ui-id-7"><i class="glyphicon glyphicon-list"></i> Statements</a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div id="tab-profile" aria-labelledby="ui-id-5"
                        class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="true"
                        aria-hidden="false" style="display: block;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="block ucard">
                                    <div class="info">
                                        <ul class="rows">
                                            <li class="heading">Customer Info</li>
                                            <li>
                                                <div class="title">Name:</div>
                                                <div class="text">{{ $customer->name }}</div>
                                            </li>
                                            <li>
                                                <div class="title">Email:</div>
                                                <div class="text">{{ $customer->email }}</div>
                                            </li>
                                            <li>
                                                <div class="title">Mobile No:</div>
                                                <div class="text">{{ $customer->phone }}</div>
                                            </li>
                                            <li>
                                                <div class="title">Address:</div>
                                                <div class="text">{{ $customer->address }}</div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                @if ($user->hasRole('super-admin') || $user->can('customer-show-project'))
                                    <div class="col-md-12">
                                        <div class="block table-sorting clearfix">
                                            <div class="head clearfix">
                                                <div class="isw-documents"></div>
                                                <h1>Project List:</h1>
                                            </div>
                                            <table cellpadding="0" cellspacing="0" width="100%" class="table"
                                                id="">
                                                <thead>
                                                    <tr>
                                                        <th>Project Name</th>
                                                        <th>Address</th>
                                                        <th class="hidden-print">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($customer->projects as $project)
                                                        <tr>
                                                            <td>{{ $project->name }}</td>
                                                            <td>{{ $project->address }}</td>
                                                            <td class="hidden-print">
                                                                @if ($user->hasRole('super-admin') || $user->can('customer-project-edit'))
                                                                    <a role="button" class="edit-btn"
                                                                        data-id="{{ $project->id }}"
                                                                        data-name="{{ $project->name }}"
                                                                        data-address="{{ $project->address }}"
                                                                        data-target="#editProjectModal"
                                                                        data-toggle="modal">
                                                                        <span class="fa fa-edit"></span>
                                                                    </a>
                                                                @endif

                                                                @if ($user->hasRole('super-admin') || $user->can('customer-project-delete'))
                                                                    <a href="{{ route('customer.project.delete', $project->id) }}"
                                                                        onclick='return confirm("Are you sure you want to delete?");'
                                                                        class="fa fa-trash"></a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                @if ($user->hasRole('super-admin') || $user->can('customer-mix-design'))
                                    <div class="col-md-12">
                                        <div class="block-fluid table-sorting clearfix">
                                            <div class="head clearfix">
                                                <div class="isw-documents"></div>
                                                <h1>Mix Designs:</h1>
                                            </div>
                                            <table cellpadding="0" cellspacing="0" width="100%" class="table"
                                                id="">
                                                <thead>
                                                    <tr>
                                                        <th>PSI</th>
                                                        <th>Stone</th>
                                                        <th>Sand</th>
                                                        <th>Cement</th>
                                                        <th>Additive</th>
                                                        <th>Water</th>
                                                        <th>Rate</th>
                                                        <th>Description</th>
                                                        <th class="hidden-print">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($customer->mixDesigns as $design)
                                                        <tr>
                                                            <td>{{ $design->psi }}</td>
                                                            <td>
                                                                <?php
                                                                $a = 0;
                                                                $stone_name_txt = $design->stone_id;
                                                                $stone_id_array = array_filter(explode(',', $stone_name_txt));
                                                                $stone_qty_txt = $design->stone_quantity;
                                                                $stone_qty_array = array_filter(explode(',', $stone_qty_txt));
                                                                foreach ($stone_id_array as $stone_name) {
                                                                    if (!isset($stone_qty_array[$a])) {
                                                                        $stone_qty_array[$a] = null;
                                                                    }
                                                                    echo $name_sto = \App\Models\ProductName::where('id', $stone_name)->value('name') . ' : ' . $stone_qty_array[$a] . 'kg,<br> ';
                                                                    $a++;
                                                                } ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $sand_name_txt = $design->sand_id;
                                                                $sand_id_array = array_filter(explode(',', $sand_name_txt));
                                                                $sand_qty_txt = $design->sand_quantity;
                                                                $sand_qty_array = array_filter(explode(',', $sand_qty_txt));
                                                                $b = 0;
                                                                foreach ($sand_id_array as $sand_name) {
                                                                    if (!isset($sand_qty_array[$b])) {
                                                                        $sand_qty_array[$b] = null;
                                                                    }
                                                                    echo $name_sa = \App\Models\ProductName::where('id', $sand_name)->value('name') . ' : ' . $sand_qty_array[$b] . 'kg,<br> ';
                                                                    $b++;
                                                                }
                                                                ?>
                                                            </td>

                                                            <td>
                                                                <?php
                                                                
                                                                $cement_name_txt = $design->cement_id;
                                                                $cement_id_array = array_filter(explode(',', $cement_name_txt));
                                                                $cement_qty_txt = $design->cement_quantity;
                                                                $cement_qty_array = array_filter(explode(',', $cement_qty_txt));
                                                                $d = 0;
                                                                foreach ($cement_id_array as $cement_name) {
                                                                    if (!isset($cement_qty_array[$d])) {
                                                                        $cement_qty_array[$d] = null;
                                                                    }
                                                                    echo $name_ce = \App\Models\ProductName::where('id', $cement_name)->value('name') . ' : ' . $cement_qty_array[$d] . 'kg,<br> ';
                                                                    $d++;
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>

                                                                <?php
                                                                $chemical_name_txt = $design->chemical_id;
                                                                $chemical_id_array = array_filter(explode(',', $chemical_name_txt), 'strlen');
                                                                $chemical_qty_txt = $design->chemical_quantity;
                                                                $chemical_qty_array = array_filter(explode(',', $chemical_qty_txt), 'strlen');
                                                                $c = 0;
                                                                foreach ($chemical_id_array as $chemical_name) {
                                                                    if (!isset($chemical_qty_array[$c])) {
                                                                        $chemical_qty_array[$c] = null;
                                                                    }
                                                                    echo $name_c = \App\Models\ProductName::where('id', $chemical_name)->value('name') . ' : ' . $chemical_qty_array[$c] . 'kg,<br> ';
                                                                    $c++;
                                                                } ?>
                                                            </td>
                                                            <td>{{ $design->water . ' : ' . $design->water_quantity . 'lt' }}
                                                            </td>
                                                            <td>{{ $design->rate }}</td>
                                                            <td>{{ $design->description }}</td>
                                                            <td class="hidden-print">
                                                                <a href="{{ route('mix.design.edit', $design->id) }}"
                                                                    class="fa fa-edit"></a>
                                                                <a href="{{ route('mix.design.delete', $design->id) }}"
                                                                    onclick='return confirm("Are you sure, you want to delete?");'
                                                                    class="fa fa-trash"></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($user->hasRole('super-admin') || $user->can('challan-list'))
                        <div id="tab-challan" aria-labelledby="ui-id-7"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="head clearfix">
                                        <div class="col-md-3">
                                            {{-- <div class="isw-documents"></div> --}}
                                            <h1>
                                                Challan List
                                                <span
                                                    class="src-info">{{ request('challan_search_text') == '' && request('challan_date_range') == '' ? '- Last 30 Days' : '- ' . request('challan_date_range') }}</span>
                                            </h1>
                                        </div>

                                        <div class="col-md-9 search_box" style="margin-top: 4px;">
                                            <form action="" class="form-horizontal">
                                                <input type="hidden" name="tab_type" value="tab-challan">
                                                <div class="" align="right">
                                                    <div class="col-md-2">
                                                        <select style="height: 28px;" name="challan_status"
                                                            class="form-control">
                                                            <option value="">Select Type</option>
                                                            <option value="0">Submitted</option>
                                                            <option value="1">Non Submitted</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if ($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('branch-list')))
                                                            <select name="branchId" id="branchId" class="form-control">
                                                                <option value="">All Branch</option>
                                                                <option value="head_office"
                                                                    {{ request('branchId') == 'head_office' ? 'selected' : '' }}>
                                                                    ** Head Office Only **
                                                                </option>
                                                                @foreach ($branches as $branch)
                                                                    {
                                                                    <option value="{{ $branch->id }}"
                                                                        {{ request('branchId') == $branch->id ? 'selected' : '' }}>
                                                                        {{ $branch->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <input type="hidden" name="branchId" id="branchId"
                                                                value="{{ $user->branchId }}" />
                                                        @endif
                                                    </div>

                                                    <div class="col-md-2">
                                                        <select name="select_challan_psi" class="form-control">
                                                            <option value="">Select PSI</option>
                                                            @foreach ($customer->mixDesigns as $design)
                                                                <option value="{{ $design->psi }}"
                                                                    {{ request('select_challan_psi') == $design->psi ? 'selected' : '' }}>
                                                                    {{ $design->psi }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                    </div>

                                                    <div class="col-md-2">
                                                        <input type="text" name="challan_search_text"
                                                            id="challan_search_name"
                                                            value="{{ request('challan_search_text') ?? '' }}"
                                                            class="form-control" placeholder="Enter challan no" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <input type="text" name="challan_date_range"
                                                                value="{{ request('challan_date_range') ?? '' }}"
                                                                class="date_range form-control" placeholder="Date Range"
                                                                autocomplete="off" />
                                                            <div class="input-group-btn">
                                                                <button type="submit"
                                                                    class="btn btn-default search-btn">Search
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                    <div class="block table-sorting clearfix">

                                        <label
                                            style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px;">
                                            Show
                                            <input type="number" id="customLengthInput" min="1" value="50"
                                                style="width: 60px; padding: 4px 6px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px;">
                                            entries
                                        </label>


                                        {{-- slider pagination ---shizan --}}
                                        {{--                                        <label> --}}
                                        {{--                                            Show --}}
                                        {{--                                            <input type="range" id="customLengthSlider" min="10" max="1000" step="10" value="50" style="width: 200px;"> --}}
                                        {{--                                            <span id="sliderValue">50</span> entries --}}
                                        {{--                                        </label> --}}


                                        <table cellpadding="0" cellspacing="0" width="100%" class="table"
                                            id="datatable4">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" value="" name="checkall" />
                                                    </th>
                                                    <th>CL. no</th>
                                                    <th>PSI</th>
                                                    <th>Sell Date</th>
                                                    <th>Cus Name</th>
                                                    <th>Project Addr</th>
                                                    <th>Qty(Cu.M)</th>
                                                    <th>Qty(Cft)</th>
                                                    <th>Rate</th>
                                                    <th>Total</th>
                                                    <th>Bill Status</th>
                                                    @if ($user->branchId == '')
                                                        <th>Branch</th>
                                                    @endif
                                                    <th class="hidden-print">Actions</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php
                                                $i = 1;
                                                $total_cum = 0;
                                                $total_cft = 0;
                                                $total_amount = 0;
                                                
                                                // Preload challans with status = 2 from demo_product_sales for faster lookup
                                                $highlighted_challans = \DB::table('demo_product_sales')->where('status', 2)->pluck('challan_no')->toArray();
                                                ?>
                                                @foreach ($challans as $chalan)
                                                    <?php
                                                    $qty_cft = $chalan->cuM * 35.315;
                                                    $total = $qty_cft * $chalan->mix_design->rate;
                                                    $total2 = $qty_cft * $chalan->rate;
                                                    
                                                    // Check if this challan should be highlighted
                                                    $highlight = in_array($chalan->challan_no, $highlighted_challans);
                                                    ?>
                                                    <tr
                                                        @if ($highlight) style="background-color: #8ab86f;" @endif>
                                                        <th>
                                                            <input type="checkbox" value="" name="checkall" />
                                                        </th>
                                                        <td>{{ $chalan->challan_no }}</td>
                                                        <td>{{ $chalan->mix_design->psi }}</td>
                                                        <td>{{ date('d-M-y', strtotime($chalan->sell_date)) }}</td>
                                                        <td>{{ $chalan->customer->name }}</td>
                                                        <td>{{ $chalan->project->name }}, {{ $chalan->project->address }}
                                                        </td>
                                                        <td>{{ $chalan->cuM }}</td>
                                                        <td>{{ $qty_cft }}</td>
                                                        @if ($chalan->rate <= 0.0)
                                                            <td>{{ $chalan->mix_design->rate }}</td>
                                                            <td>{{ number_format($total, 2) }}</td>
                                                        @else
                                                            <td>{{ number_format($chalan->rate, 2) }}</td>
                                                            <td>{{ number_format($total2, 2) }}</td>
                                                        @endif

                                                        <td>{{ $chalan->status == 1 ? 'Not Submitted' : 'Submitted' }}</td>
                                                        @if ($user->branchId == '')
                                                            <td>{{ $chalan->branch->name ?? '-' }}</td>
                                                        @endif
                                                        <td class="hidden-print">
                                                            @if ($chalan->status == 1 && $chalan->demo_bill->status != 2)
                                                                @if ($user->hasRole('super-admin') || $user->can('challan-edit'))
                                                                    <a role="button" class="challan-edit-btn"
                                                                        data-challan_customer_id="{{ $chalan->customer_id }}"
                                                                        data-challan_id="{{ $chalan->id }}"
                                                                        data-challan_project_id="{{ $chalan->project_id }}"
                                                                        data-challan_mix_design_id="{{ $chalan->mix_design_id }}"
                                                                        data-challan_date="{{ date('m/d/Y', strtotime($chalan->sell_date)) }}"
                                                                        data-challan_cum="{{ $chalan->cuM }}"
                                                                        data-challan_rate="{{ $chalan->rate <= 0.0 ? $chalan->mix_design->rate : $chalan->rate }}"
                                                                        data-target="#challanEditModal"
                                                                        data-toggle="modal">
                                                                        <span class="fa fa-edit"></span>
                                                                    </a>
                                                                @endif
                                                                @if ($user->hasRole('super-admin') || $user->can('challan-delete'))
                                                                    <a href="{{ route('customer.challan.delete', $chalan->id) }}"
                                                                        onclick='return confirm("Are you sure you want to delete?");'
                                                                        class="fa fa-trash"></a>
                                                                @endif
                                                            @elseif($chalan->demo_bill && $chalan->demo_bill->status == 2)
                                                                <span style="color: white; font-size: medium">Demo Bill
                                                                    Generated</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $total_cum += $chalan->cuM;
                                                    $total_cft += $qty_cft;
                                                    
                                                    if ($chalan->rate <= 0.0) {
                                                        $total_amount += $total; // use mix_design->rate
                                                    } else {
                                                        $total_amount += $total2; // use challan->rate
                                                    }
                                                    
                                                    ?>
                                                @endforeach
                                                <tr style="background-color:#999999; color: #fff;">
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td><b>Total:</b></td>
                                                    <td><b>{{ number_format($total_cum, 2) }}</b></td>
                                                    <td><b>{{ number_format($total_cft, 2) }}</b></td>
                                                    <td></td>
                                                    <td><b>{{ number_format($total_amount, 2) }}</b></td>
                                                    <td></td>
                                                    @if ($user->branchId == '')
                                                        <td></td>
                                                    @endif
                                                    <td class="hidden-print"></td>
                                                </tr>
                                            </tbody>


                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif

                    @if ($user->branchId == '14' || ($user->hasRole('super-admin') || $user->can('bill-list')))
                        <div id="tab-bills" aria-labelledby="ui-id-6"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">

                            <div class="block-fluid table-sorting clearfix">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" value="" name="checkall" />
                                            </th>
                                            <th>Inv No</th>
                                            <th>Date</th>
                                            <th>Customer Name</th>
                                            <th>PSI</th>
                                            <th>Concrete Method</th>
                                            <th>Qty(Cu.M)</th>
                                            <th>Qty(Cft)</th>
                                            <th>Eng. Tips</th>
                                            <th>VAT</th>
                                            <th>AIT</th>
                                            <th>Total Amount</th>
                                            <th>Remarks</th>
                                            <th class="hidden-print">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $vat_total = 0;
                                        $ait_total = 0;
                                        $bill_total = 0;
                                        $total_eng_tips = 0;
                                        $total_cft = 0;
                                        $total_cum = 0; ?>
                                        @foreach ($customer->bills as $bill)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" id="checkbox" 
                                                        name="checkbox[]" />
                                                </td>
                                                <td>{{ $bill->invoice_no }}</td>
                                                <td>{{ date('d-M-y', strtotime($bill->bill_date)) }}</td>
                                                <td>{{ $bill->customer->name }}</td>
                                                <td>{{ $bill->psi }}</td>
                                                <td>{{ $bill->concrete_method }}</td>
                                                <td>{{ round($bill->total_cuM, 4) }}</td>
                                                <td>{{ round($bill->total_cft, 4) }}</td>
                                                <td>{{ number_format($bill->eng_tips, 2) }}</td>
                                                <td>{{ round(($bill->total_amount * $bill->vat) / 100, 4) }}</td>
                                                <td>{{ round(($bill->total_amount * $bill->ait) / 100, 4) }}</td>
                                                <td>{{ number_format($bill->total_amount, 2) }}</td>
                                                <td>{{ $bill->description }}</td>
                                                <td class="hidden-print">
                                                    @if ($user->hasRole('super-admin') || $user->can('bill-edit'))
                                                        <a href="#" title="Edit" class="fa fa-edit edit-bill"
                                                            data-id="{{ $bill->id }}"></a>
                                                    @endif
                                                    @if ($user->hasRole('super-admin') || $user->can('bill-delete'))
                                                        <a href="{{ route('customer.bill.delete', $bill->id) }}"
                                                            onclick='return confirm("Are you sure you want to delete?");'
                                                            title="Delete" class="fa fa-trash"></a>
                                                    @endif
                                                    @if ($user->hasRole('super-admin') || $user->can('bill-details'))
                                                        <a href="{{ route('customer.bill.details', $bill->invoice_no) }}"
                                                            target="_blank" title="Bill Details" class="fa fa-eye"></a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <?php
                                            ///$bill->pump_charge
                                            $vat_total += ($bill->total_amount * $bill->vat) / 100;
                                            $ait_total += ($bill->total_amount * $bill->ait) / 100;
                                            $bill_total += $bill->total_amount;
                                            $total_cft += $bill->total_cft;
                                            $total_cum += $bill->total_cuM;
                                            $total_eng_tips += $bill->eng_tips;
                                            ?>
                                        @endforeach
                                        <tr style="background-color:#999999; color: #fff;">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><b> Total:</b></td>
                                            <td><b>{{ $total_cum }}</b></td>
                                            <td><b>{{ $total_cft }}</b></td>
                                            <td><b>{{ number_format($total_eng_tips, 2) }}</b></td>
                                            <td><b>{{ number_format($vat_total, 2) }}</b></td>
                                            <td><b>{{ number_format($ait_total, 2) }}</b></td>
                                            <td><b>{{ number_format($bill_total, 2) }}</b></td>
                                            <td></td>
                                            <td class="hidden-print"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif


                    {{--                    SHIZAN WORKS==== DEMO BILL FOR OTHER USER too --}}
                    @if ($user->hasRole('super-admin') || $user->can('demo-bill-create'))
                        <div id="tab-demo" aria-labelledby="ui-id-7"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true"
                            style="display: {{ $selected_tab == 'tab-demo' ? 'block' : 'none' }};">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="head clearfix">
                                        <div class="col-md-4">
                                            <div class="isw-documents"></div>
                                            <h1>Non Submitted Demo Challan List</h1>
                                        </div>

                                        <div class="col-md-8 search_box" style="margin-top: 4px;">
                                            <form action="" id="demo_filter_list_form" class="form-horizontal">
                                                <input type="hidden" name="tab_type" value="tab-demo">

                                                <div class="col-md-4">
                                                    <select name="project_id" required class="form-control">
                                                        <option value="">choose project</option>
                                                        @foreach ($customer->projects as $project)
                                                            <option value="{{ $project->id }}"
                                                                {{ request('project_id') == $project->id && request('tab_type') == 'tab-demo' ? 'selected' : '' }}>
                                                                {{ $project->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <select name="mix_design_id" id="psi" required
                                                        class="form-control">
                                                        <option value="">Choose PSI</option>
                                                        @foreach ($customer->mixDesigns as $design)
                                                            <option value="{{ $design->id }}"
                                                                {{ request('mix_design_id') == $design->id && request('tab_type') == 'tab-demo' ? 'selected' : '' }}>
                                                                {{ $design->psi }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <input type="text" name="demo_date_range" {{-- @if (request()->has('demo_date_range'))

                                                               value="{{request()->get('demo_date_range')}}"
                                                               @else
                                                                   @php
                                                                       $demo_init_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
                                                                   @endphp
                                                                   value="{{$demo_init_date_range[0].' - '.$demo_init_date_range[1] }}"

                                                               @endif --}}
                                                            class="date_range form-control" placeholder="Date Range"
                                                            autocomplete="off" />
                                                        <div class="input-group-btn">
                                                            <button type="submit"
                                                                class="btn btn-default search-btn">Filter
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{--                                                <div class="col-md-2"> --}}
                                                {{--                                                    <button class="btn btn-default" type="submit" --}}
                                                {{--                                                            form="demo_filter_list_form">Filter --}}
                                                {{--                                                    </button> --}}
                                                {{--                                                </div> --}}
                                            </form>
                                        </div>

                                        <div class="col-md-2" style="margin-top: 4px;">
                                            <button class="btn btn-warning" id="demo_bill_btn_div" style="display: none;"
                                                type="submit" form="demo_bill_form">Generate
                                                Demo Bill
                                            </button>
                                        </div>
                                    </div>

                                    <div class="block table-sorting clearfix">
                                        <form action="{{ route('customer.demo.bill.generate') }}" method="post"
                                            id="demo_bill_form" class="form-horizontal">
                                            @csrf
                                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                            <input type="hidden" name="project_id" value="{{ request('project_id') }}">
                                            <input type="hidden" name="mix_design_id"
                                                value="{{ request('mix_design_id') }}">


                                            <table cellpadding="0" cellspacing="0" width="100%" class="table"
                                                id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <input type="checkbox" value="" name="checkall" />
                                                        </th>
                                                        <th>Cl. No</th>
                                                        <th>PSI</th>
                                                        <th>Sell Date</th>
                                                        <th>Cus Name</th>
                                                        <th>Project & Address</th>
                                                        <th>Qty(Cu.M)</th>
                                                        <th>Qty(Cft)</th>
                                                        <th>Rate</th>
                                                        <th>Total</th>
                                                        <th>Bill Status</th>
                                                        @if ($user->branchId == '')
                                                            <th>Branch</th>
                                                        @endif
                                                        <th class="hidden-print">Actions</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php $total_cum = 0;
                                                    $total_cft = 0;
                                                    $total_amount = 0; ?>
                                                    @foreach ($non_submitted_demo_challans as $chalan)
                                                        <?php $qty_cft = $chalan->cuM * 35.315;
                                                        $total = $qty_cft * $chalan->mix_design->rate;
                                                        $total2 = $qty_cft * $chalan->rate;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="checkbox[]" value="{{ $chalan->id }}">
                                                            </td>
                                                            <td>{{ $chalan->challan_no }}</td>
                                                            <td>{{ $chalan->mix_design->psi }}</td>
                                                            <td>{{ date('d-M-y', strtotime($chalan->sell_date)) }}</td>
                                                            <td>{{ $chalan->customer->name }}</td>
                                                            <td>{{ $chalan->project->name }}
                                                                , {{ $chalan->project->address }}</td>
                                                            <td>{{ $chalan->cuM }}</td>
                                                            <td>{{ $qty_cft }}</td>
                                                            @if (($user->can('challan-rate-view') || $user->hasRole(['super-admin'])) && $chalan->rate <= 0.0)
                                                                <td>{{ $chalan->mix_design->rate }}</td>
                                                                <td>{{ number_format($total, 2) }}</td>
                                                            @else
                                                                {{--                                    after edit rate show will be here --}}
                                                                <td>{{ number_format($chalan->rate, 2) }}</td>
                                                                <td>{{ number_format($total2, 2) }}</td>
                                                            @endif


                                                            <td>{{ $chalan->status == 1 ? 'Not Submitted' : 'Submitted' }}
                                                            </td>
                                                            @if ($user->branchId == '')
                                                                <td>{{ $chalan->branch->name ?? '-' }}</td>
                                                            @endif
                                                            <td class="hidden-print">
                                                                @if ($user->hasRole('super-admin'))
                                                                    <a role="button" class="challan-edit-btn"
                                                                        data-challan_customer_id="{{ $chalan->customer_id }}"
                                                                        data-challan_id="{{ $chalan->id }}"
                                                                        data-challan_psi="{{ $chalan->psi }}"
                                                                        data-challan_project_id="{{ $chalan->project->id }}"
                                                                        data-challan_date="{{ date('m/d/Y', strtotime($chalan->sell_date)) }}"
                                                                        data-challan_cuM="{{ $chalan->cuM }}"
                                                                        data-challan_rate="{{ $chalan->mix_design->rate }}"
                                                                        data-target="#challanEditModal"
                                                                        data-toggle="modal">
                                                                        <span class="fa fa-edit"></span>
                                                                    </a>
                                                                    <a href="{{ route('customer.challan.delete', $chalan->id) }}"
                                                                        onclick='return confirm("Are you sure you want to delete?");'
                                                                        class="fa fa-trash"></a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <?php $total_cum += $chalan->cuM;
                                                        $total_cft += $qty_cft;
                                                        //
                                                        if ($chalan->rate <= 0.0) {
                                                            $total_amount += $total; // use mix_design->rate
                                                        } else {
                                                            $total_amount += $total2; // use challan->rate
                                                        }
                                                        
                                                        ?>
                                                    @endforeach
                                                    <tr style="background-color:#999999; color: #fff;">
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><b> Total:</b></td>
                                                        <td><b>{{ number_format($total_cum, 2) }}</b></td>
                                                        <td><b>{{ number_format($total_cft, 2) }}</b></td>
                                                        <td></td>
                                                        <td><b>{{ number_format($total_amount, 2) }}</b></td>
                                                        <td></td>
                                                        @if ($user->branchId == '')
                                                            <td></td>
                                                        @endif
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


                    {{--                    SHIZAN WORKS==== DEMO BILL List FOR OTHER USER too --}}
                    @if ($user->hasRole('super-admin') || $user->can('demo-bill-list'))
                        <div id="tab-demo-bills" aria-labelledby="ui-id-6"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">
                            <div class="block-fluid table-sorting clearfix">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" value="" name="checkall" />
                                            </th>
                                            <th>Inv No</th>
                                            <th>Date</th>
                                            <th>Customer Name</th>
                                            <th>PSI</th>
                                            <th>Concrete Method</th>
                                            <th>Qty(Cu.M)</th>
                                            <th>Qty(Cft)</th>
                                            <th>Eng. Tips</th>
                                            <th>VAT</th>
                                            <th>AIT</th>
                                            <th>Total Amount</th>
                                            <th>Remarks</th>
                                            <th class="hidden-print">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $vat_total = 0;
                                        $ait_total = 0;
                                        $bill_total = 0;
                                        $total_eng_tips = 0;
                                        $total_cft = 0;
                                        $total_cum = 0; ?>
                                        @foreach ($customer->demoBills as $demoBill)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" id="checkbox" name="checkbox[]" />
                                                </td>
                                                <td>{{ $demoBill->invoice_no }}</td>
                                                <td>{{ date('d-M-y', strtotime($demoBill->bill_date)) }}</td>
                                                <td>{{ $demoBill->customer->name }}</td>
                                                <td>{{ $demoBill->psi }}</td>
                                                <td>{{ $demoBill->concrete_method }}</td>
                                                <td>{{ round($demoBill->total_cuM, 4) }}</td>
                                                <td>{{ round($demoBill->total_cft, 4) }}</td>
                                                <td>{{ number_format($demoBill->eng_tips, 2) }}</td>
                                                <td>{{ round(($demoBill->total_amount * $demoBill->vat) / 100, 4) }}</td>
                                                <td>{{ round(($demoBill->total_amount * $demoBill->ait) / 100, 4) }}</td>
                                                <td>{{ number_format($demoBill->total_amount, 2) }}</td>
                                                <td>{{ $demoBill->description }}</td>
                                                <td class="hidden-print">
                                                    {{--                                                @if ($user->hasRole('super-admin') || $user->can('bill-edit')) --}}
                                                    {{--                                                    <a href="#" title="Edit" class="fa fa-edit edit-bill" data-id="{{ $bill->id }}"></a> --}}
                                                    {{--                                                @endif --}}
                                                    @if ($user->hasRole('super-admin') || $user->can('demo-bill-delete'))
                                                        <a href="{{ route('customer.demo.bill.delete', $demoBill->id) }}"
                                                            onclick='return confirm("Are you sure you want to delete?");'
                                                            title="Delete" class="fa fa-trash"></a>
                                                    @endif
                                                    @if ($user->hasRole('super-admin') || $user->can('demo-bill-details'))
                                                        <a href="{{ route('customer.demo.bill.details', $demoBill->invoice_no) }}"
                                                            target="_blank" title="Bill Details" class="fa fa-eye"></a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <?php
                                            ///$bill->pump_charge
                                            $vat_total += ($demoBill->total_amount * $demoBill->vat) / 100;
                                            $ait_total += ($demoBill->total_amount * $demoBill->ait) / 100;
                                            $bill_total += $demoBill->total_amount;
                                            $total_cft += $demoBill->total_cft;
                                            $total_cum += $demoBill->total_cuM;
                                            $total_eng_tips += $demoBill->eng_tips;
                                            ?>
                                        @endforeach
                                        <tr style="background-color:#999999; color: #fff;">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><b> Total:</b></td>
                                            <td><b>{{ $total_cum }}</b></td>
                                            <td><b>{{ $total_cft }}</b></td>
                                            <td><b>{{ number_format($total_eng_tips, 2) }}</b></td>
                                            <td><b>{{ number_format($vat_total, 2) }}</b></td>
                                            <td><b>{{ number_format($ait_total, 2) }}</b></td>
                                            <td><b>{{ number_format($bill_total, 2) }}</b></td>
                                            <td></td>
                                            <td class="hidden-print"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($user->branchId == '' && ($user->hasRole('super-admin') || $user->can('bill-create')))
                        <div id="tab-bill-generate" aria-labelledby="ui-id-7"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="head clearfix">
                                        <div class="col-md-4">
                                            <div class="isw-documents"></div>
                                            <h1>
                                                Non Submitted Challan List
                                                <span class="src-info">
                                                    @if (request()->has('generate_date_range') && request()->get('generate_date_range') === '')
                                                        {{ $generate_date_range[0] }} - {{ $generate_date_range[1] }}
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </h1>
                                        </div>

                                        <div class="col-md-8 search_box" style="margin-top: 4px;">
                                            <form action="" id="filter_list_form" class="form-horizontal">
                                                <input type="hidden" name="tab_type" value="tab-bill-generate">

                                                <div class="col-md-4">
                                                    <select name="project_id" required class="form-control">
                                                        <option value="">choose project</option>
                                                        @foreach ($customer->projects as $project)
                                                            <option value="{{ $project->id }}"
                                                                {{ request('project_id') == $project->id && request('tab_type') == 'tab-bill-generate' ? 'selected' : '' }}>
                                                                {{ $project->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <select name="mix_design_id" id="psi" required
                                                        class="form-control">
                                                        <option value="">Choose PSI</option>
                                                        @foreach ($customer->mixDesigns as $design)
                                                            <option value="{{ $design->id }}"
                                                                {{ request('mix_design_id') == $design->id && request('tab_type') == 'tab-bill-generate' ? 'selected' : '' }}>
                                                                {{ $design->psi }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <input type="text" name="generate_date_range"
                                                            {{-- @if (request()->has('generate_date_range'))

                                                                   value="{{request()->get('generate_date_range')}}"
                                                               @else
                                                                   @php
                                                                       $generate_init_date_range = [date('Y-m-d', strtotime('-30 days')), date('Y-m-d')];
                                                                   @endphp
                                                                   value="{{$generate_init_date_range[0].' - '.$generate_init_date_range[1] }}"

                                                               @endif --}} class="date_range form-control"
                                                            placeholder="Date Range" autocomplete="off" />
                                                        <div class="input-group-btn">
                                                            <button type="submit"
                                                                class="btn btn-default search-btn">Filter
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>

                                        <div class="col-md-2" style="margin-top: 4px;">
                                            <button class="btn btn-danger" id="bill_btn_div" style="display: none;"
                                                type="submit" form="bill_form">Generate Bill
                                            </button>
                                        </div>
                                    </div>
                                    <div class="block table-sorting clearfix">
                                        <form action="{{ route('customer.bill.generate') }}" method="post"
                                            id="bill_form" class="form-horizontal">
                                            @csrf
                                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                            <input type="hidden" name="project_id"
                                                value="{{ request('project_id') }}">
                                            <input type="hidden" name="mix_design_id"
                                                value="{{ request('mix_design_id') }}">
                                            <label
                                                style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px;">
                                                Show
                                                <input type="number" id="customLengthInput2" min="1"
                                                    value="50"
                                                    style="width: 60px; padding: 4px 6px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px;">
                                                entries
                                            </label>

                                            <table cellpadding="0" cellspacing="0" width="100%" class="table"
                                                id="datatable5">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                           <input type="checkbox" value="" name="checkall" />
                                                        </th>
                                                        <th>Cl. No</th>
                                                        <th>PSI</th>
                                                        <th>Sell Date</th>
                                                        <th>Cus Name</th>
                                                        <th>Project & Address</th>
                                                        <th>Qty(Cu.M)</th>
                                                        <th>Qty(Cft)</th>
                                                        <th>Rate</th>
                                                        <th>Total</th>
                                                        <th>Bill Status</th>
                                                        @if ($user->branchId == '')
                                                            <th>Branch</th>
                                                        @endif
                                                        <th class="hidden-print">Actions</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php $total_cum = 0;
                                                    $total_cft = 0;
                                                    $total_amount = 0; ?>
                                                    @foreach ($non_submitted_challans as $chalan)
                                                        <?php $qty_cft = $chalan->cuM * 35.315;
                                                        $total = $qty_cft * $chalan->mix_design->rate;
                                                        $total2 = $qty_cft * $chalan->rate;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="checkbox[]" value="{{ $chalan->id }}">
                                                            </td>
                                                            <td>{{ $chalan->challan_no }}</td>
                                                            <td>{{ $chalan->mix_design->psi }}</td>
                                                            <td>{{ date('d-M-y', strtotime($chalan->sell_date)) }}</td>
                                                            <td>{{ $chalan->customer->name }}</td>
                                                            <td>{{ $chalan->project->name }}
                                                                , {{ $chalan->project->address }}</td>
                                                            <td>{{ $chalan->cuM }}</td>
                                                            <td>{{ $qty_cft }}</td>
                                                            @if (($user->can('challan-rate-view') || $user->hasRole(['super-admin'])) && $chalan->rate <= 0.0)
                                                                <td>{{ $chalan->mix_design->rate }}</td>
                                                                <td>{{ number_format($total, 2) }}</td>
                                                            @else
                                                                {{--                                    after edit rate show will be here --}}
                                                                <td>{{ number_format($chalan->rate, 2) }}</td>
                                                                <td>{{ number_format($total2, 2) }}</td>
                                                            @endif


                                                            <td>{{ $chalan->status == 1 ? 'Not Submitted' : 'Submitted' }}
                                                            </td>
                                                            @if ($user->branchId == '')
                                                                <td>{{ $chalan->branch->name ?? '-' }}</td>
                                                            @endif
                                                            <td class="hidden-print">
                                                                @if ($user->hasRole('super-admin'))
                                                                    <a role="button" class="challan-edit-btn"
                                                                        data-challan_customer_id="{{ $chalan->customer_id }}"
                                                                        data-challan_id="{{ $chalan->id }}"
                                                                        data-challan_psi="{{ $chalan->psi }}"
                                                                        data-challan_project_id="{{ $chalan->project->id }}"
                                                                        data-challan_date="{{ date('m/d/Y', strtotime($chalan->sell_date)) }}"
                                                                        data-challan_cuM="{{ $chalan->cuM }}"
                                                                        data-challan_rate="{{ $chalan->mix_design->rate }}"
                                                                        data-target="#challanEditModal"
                                                                        data-toggle="modal">
                                                                        <span class="fa fa-edit"></span>
                                                                    </a>
                                                                    <a href="{{ route('customer.challan.delete', $chalan->id) }}"
                                                                        onclick='return confirm("Are you sure you want to delete?");'
                                                                        class="fa fa-trash"></a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <?php $total_cum += $chalan->cuM;
                                                        $total_cft += $qty_cft;
                                                        //
                                                        if ($chalan->rate <= 0.0) {
                                                            $total_amount += $total; // use mix_design->rate
                                                        } else {
                                                            $total_amount += $total2; // use challan->rate
                                                        }
                                                        
                                                        ?>
                                                    @endforeach
                                                    <tr style="background-color:#999999; color: #fff;">
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><b> Total:</b></td>
                                                        <td><b>{{ number_format($total_cum, 2) }}</b></td>
                                                        <td><b>{{ number_format($total_cft, 2) }}</b></td>
                                                        <td></td>
                                                        <td><b>{{ number_format($total_amount, 2) }}</b></td>
                                                        <td></td>
                                                        @if ($user->branchId == '')
                                                            <td></td>
                                                        @endif
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


                    @if ($user->hasRole(['super-admin']) || $user->can('customer-payment-details'))
                        <div id="tab-payment" aria-labelledby="ui-id-7"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="head clearfix">
                                        <div class="col-md-4">
                                            <div class="isw-documents"></div>
                                            <h1>
                                                Customer Payment Details
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
                                                                placeholder="Date Range" autocomplete="off" />
                                                            <div class="input-group-btn">
                                                                <button type="submit"
                                                                    class="btn btn-default search-btn">Search
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>


                                    <div style="margin: 0; padding: 0;">
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                            style="border-collapse: collapse; font-size: 14px; line-height: 3; margin: 0; padding: 0; width: 100%;">
                                            <thead style="background: #4D7096; color: white">
                                                <tr
                                                    style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                    <th>
                                                        <input type="checkbox" value="" name="checkall" />
                                                    </th>
                                                    <th
                                                        style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                        Tran Id
                                                    </th>
                                                    <th
                                                        style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                        Ref date
                                                    </th>
                                                    <th
                                                        style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                        Description
                                                    </th>
                                                    <th
                                                        style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                        Payment Mode
                                                    </th>
                                                    <th
                                                        style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                        Branch Name
                                                    </th>
                                                    <th
                                                        style="padding-right: 5px; margin: 0; white-space: nowrap; text-align: right; border: 1px solid #ccc;">
                                                        Paid Amount
                                                    </th>
                                                    <th class="text-center"
                                                        style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                        Files
                                                    </th>

                                                    <th
                                                        style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                        Actions
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total = 0;
                                                    $ad_total = 0;
                                                @endphp
                                                @foreach ($payments as $payment)
                                                    <tr style="padding: 0; margin: 0;">
                                                        <td
                                                            style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                            <input type="checkbox" id="checkbox" name="checkbox[]" />
                                                        </td>
                                                        <td
                                                            style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                            {{ $payment->transaction_id }}</td>
                                                        <td
                                                            style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                            {{ date('d-m-Y', strtotime($payment->ref_date)) }}</td>
                                                        <td
                                                            style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                            {{ $payment->description }}</td>
                                                        <td
                                                            style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                            @if ($payment->payment_mode == 'Bank')
                                                                Bank: {{ $payment->bank_info->short_name }},
                                                                A/C: {{ $payment->bank_info->account_no }},
                                                                Chq: {{ $payment->cheque_no }}
                                                            @else
                                                                Cash
                                                            @endif
                                                        </td>
                                                        <td
                                                            style="padding: 1px; margin: 0; text-align: center; white-space: nowrap; border: 1px solid #ccc;">
                                                            {{ $payment->branch->name ?? '-' }}</td>
                                                        <td
                                                            style="padding-right: 5px; margin: 0; text-align: right; white-space: nowrap; border: 1px solid #ccc;">
                                                            {{ number_format($payment->paid_amount, 2) }}</td>
                                                        <td
                                                            style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                            @php $files = explode(",", $payment->file); @endphp
                                                            @foreach ($files as $file)
                                                                <a href="{{ asset('img/files/expense_files/customer_payment/' . trim($file)) }}"
                                                                    target="_blank" rel="tag"
                                                                    style="display: inline-block; margin-right: 5px; text-decoration: underline;">
                                                                    {{ $file }}
                                                                </a>
                                                            @endforeach
                                                        </td>
                                                        <td
                                                            style="padding: 1px; margin: 0; white-space: nowrap; border: 1px solid #ccc;">
                                                            @if ($user->branchId == '' && ($user->hasRole('super-admin') || $user->can('customer-payment-details')))
                                                                <a role="button"
                                                                    style="display: inline-block; margin-right: 5px;"
                                                                    class="view_payment_btn"
                                                                    data-customer_name="{{ $payment->customer->name }}"
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
                                                            @if ($user->branchId == '' && ($user->hasRole('super-admin') || $user->can('customer-payment-delete')))
                                                                <a href="{{ route('customer.payment.delete', $payment->transaction_id) }}"
                                                                    onclick="return confirm('Are you sure you want to delete?')"
                                                                    style="display: inline-block; margin-right: 5px;">
                                                                    <span class="fa fa-trash"></span>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $total += $payment->paid_amount;
                                                        $ad_total += $payment->adjustment_amount;
                                                    @endphp
                                                @endforeach
                                                <tr style="padding: 0; margin: 0;">
                                                    <td style="padding: 1px; border: 1px solid #ccc;"></td>
                                                    <td style="padding: 1px; border: 1px solid #ccc;"></td>
                                                    <td style="padding: 1px; border: 1px solid #ccc;"></td>
                                                    <td style="padding: 1px; border: 1px solid #ccc;"></td>
                                                    <td style="padding: 1px; border: 1px solid #ccc;"></td>
                                                    <td
                                                        style="padding: 1px; text-align: right; white-space: nowrap; border: 1px solid #ccc;">
                                                        <b>Total =</b>
                                                    </td>
                                                    <td
                                                        style="padding-right: 5px; text-align: right; white-space: nowrap; border: 1px solid #ccc;">
                                                        <b>{{ number_format($total, 2) }}</b>
                                                    </td>
                                                    <td style="padding: 1px; border: 1px solid #ccc;"></td>
                                                    <td style="padding: 1px; border: 1px solid #ccc;"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                        </div>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('customer-statement-report'))
                        <div id="tab-statement" aria-labelledby="ui-id-7"
                            class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel"
                            aria-expanded="false" aria-hidden="true" style="display: none;">


                            <div class="row">
                                <div class="col-md-12">
                                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px;">
                                        <button onclick="printStatement('tab-statement', 'pad')"
                                            class="btn btn-sm btn-info no-print">Print (With Pad)
                                        </button>
                                        <button onclick="printStatement('tab-statement', 'non-pad')"
                                            class="btn btn-sm btn-success no-print">Print (Without Pad)
                                        </button>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="col-md-4">
                                            <div class="isw-documents"></div>
                                            <h3>
                                                Customer Statement
                                                <span class="src-info">
                                                    {{ request('statement_search_text') == '' && request('statement_date_range') == '' ? '- Last 30 days' : '- ' . request('statement_date_range') }}
                                                </span>

                                            </h3>
                                        </div>

                                        <div class="col-md-8 search_box" style="margin-top: 4px;">
                                            <form action="" class="form-horizontal">
                                                <input type="hidden" name="tab_type" value="tab-statement" />
                                                <div class="" align="right">
                                                    <div class="col-md-3">
                                                        @if ($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('branch-list')))
                                                            <select name="branchId" id="branchId" class="form-control">
                                                                <option value="">All Branch</option>
                                                                <option value="head_office"
                                                                    {{ request('branchId') == 'head_office' ? 'selected' : '' }}>
                                                                    ** Head Office Only **
                                                                </option>
                                                                @foreach ($branches as $branch)
                                                                    <option value="{{ $branch->id }}"
                                                                        {{ request('branchId') == $branch->id ? 'selected' : '' }}>
                                                                        {{ $branch->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <input type="hidden" name="branchId" id="branchId"
                                                                value="{{ $user->branchId }}" />
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" name="statement_search_text"
                                                            id="search_name" class="form-control"
                                                            value="{{ request('statement_search_text') ?? '' }}"
                                                            placeholder="Enter Search Text" />
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="input-group">
                                                            <input type="text" name="statement_date_range"
                                                                value="{{ old('statement_date_range') ?? '' }}"
                                                                class="date_range form-control" placeholder="Date Range"
                                                                autocomplete="off" />
                                                            <div class="input-group-btn">
                                                                <button type="submit" id="btn_search"
                                                                    class="btn btn-default search-btn">Search
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="block-fluid table-sorting clearfix">
                                        <table cellpadding="0" cellspacing="0" width="100%" class="table"
                                            id="datatable">


                                            <thead style="font-size: 10px">
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" value="" name="checkall" />
                                                    </th>

                                                    <th>Trx ID</th>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Qty(CFT)</th>
                                                    <th>Rate</th>
                                                    <th>Payment Details</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                    <th>Adjust</th>
                                                    <th>Balance</th>
                                                    @if ($user->branchId == '')
                                                        <th class="no-print">Branch</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1;
                                                $bal = 0;
                                                $debit = 0;
                                                $total_adjustment = 0;
                                                $credit = 0;
                                                $total_qty = 0;
                                                $total_balance = 0; ?>
                                                @foreach ($statements as $statement)
                                                    <?php
                                                    $bill_date = '';
                                                    $payment_details = '';
                                                    $ref_date = '';
                                                    $adjustment = 0;
                                                    
                                                    $pay_rowb = $statement->bill;
                                                    $pay_row = $statement->customer_payment;
                                                    
                                                    if ($pay_row != '') {
                                                        $adjustment = round($pay_row->adjustment_amount, 3);
                                                        $ref_date = $pay_row->ref_date;
                                                    
                                                        if ($pay_row->bank_id != '') {
                                                            $payment_details = "{$pay_row->payment_mode}: {$pay_row->bank_info->short_name}, AC no: {$pay_row->bank_info->account_no}, Cheque/Receipt no: {$pay_row->cheque_no}";
                                                        } else {
                                                            $ref_date = $pay_row->ref_date;
                                                            $payment_details = 'Cash';
                                                        }
                                                    }
                                                    
                                                    $qty = 0;
                                                    $rate = 0;
                                                    $bill_row = $statement->bill;
                                                    if ($bill_row != '') {
                                                        $rate = round($bill_row->rate, 3);
                                                    }
                                                    if ($bill_row != '') {
                                                        $qty = round($bill_row->total_cft, 3);
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" id="checkbox" name="checkbox[]" />
                                                        </td>
                                                        <td style="width: 20px;">{{ $statement->transaction_id }}</td>

                                                        <td>
                                                            <?php
                                                            $trans_id = $statement->transaction_id;
                                                            $eti = explode('-', $trans_id);
                                                            $sep_trans_id = $eti[0];
                                                            
                                                            if ($sep_trans_id == 'CBILL') {
                                                                echo date('d-M-y', strtotime($pay_rowb->bill_date));
                                                            } elseif ($sep_trans_id == 'CUSP') {
                                                                echo date('d-M-y', strtotime($ref_date));
                                                            } elseif (str_contains($sep_trans_id, 'opening_balance')) {
                                                                // For opening balance, use posting_date
                                                                echo date('d-M-y', strtotime($statement->posting_date));
                                                            }
                                                            $final_balance = $statement->balance;
                                                            
                                                            if (str_contains($sep_trans_id, 'opening_balance')) {
                                                                // Opening balance হলে credit amount = balance
                                                                $final_balance = $statement->credit;
                                                            }
                                                            
                                                            ?>
                                                        </td>

                                                        <td>{{ $statement->description }}</td>
                                                        <td>{{ $qty }}</td>
                                                        <td>{{ $rate }}</td>
                                                        <td>{{ $payment_details }}</td>
                                                        <td>{{ number_format($statement->debit - $adjustment, 2) }}</td>
                                                        
                                                        <td>{{ number_format($statement->credit, 2) }}</td>
                                                        {{-- <td>{{ number_format($statement->balance, 2) }}</td> --}}
                                                        <td>{{ number_format($adjustment, 2) }}</td>
                                                        <td>{{ number_format($final_balance, 2) }}</td>

                                                        @if ($user->branchId == '')
                                                            <td class="no-print">{{ $statement->branch->name ?? '-' }}
                                                            </td>
                                                        @endif
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                    $total_qty += $qty;
                                                    $debit += $statement->debit;
                                                    $total_adjustment += $adjustment;
                                                    $credit += $statement->credit;
                                                    // $total_balance += $statement->balance;
                                                    // $total_balance += $final_balance;

                                                    ?>
                                                @endforeach
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td><b>Balance:</b></td>
                                                    <td></td>
                                                    <td><b>{{ number_format($total_qty, 3) }}</b></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td><b>{{ number_format($debit, 3) }}</b></td>
                                                    
                                                    <td><b>{{ number_format($credit, 3) }}</b></td>
                                                    <td><b>{{ number_format($total_adjustment, 3) }}</b></td>
                                                    <td><b>{{ number_format($credit - $debit, 2) }}</b></td>
                                                    @if ($user->branchId == '')
                                                        <td class="no-print"></td>
                                                    @endif
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

    <!-- EDIT PROJECT MODAL -->
    <div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('customer.project.update') }}" method="post" class="form-horizontal">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4>Edit Customer project</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" id="id" value="" />
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Name</label>
                                    <div class="col-md-9"><input type="text" value="" name="name"
                                            id="name" required />
                                    </div>
                                </div>

                                <div class="row-form clearfix">
                                    <label class="col-md-3">Address</label>
                                    <div class="col-md-9">
                                        <textarea name="address" id="address"></textarea>
                                    </div>
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

    {{-- ADD NEW PROJECT MODAL --}}
    @if ($user->hasRole('super-admin') || $user->can('customer-add-project'))
        <div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4>Edit Customer</h4>
                    </div>
                    <form action="{{ route('customer.project.store') }}" method="post" class="form-horizontal">
                        <div class="modal-body modal-body-np">
                            <div class="row">
                                <div class="block-fluid">
                                    {{ csrf_field() }}
                                    <div class="row-form clearfix">
                                        <label class="col-md-3">Customer Name</label>
                                        <div class="col-md-7">
                                            <input type="text" value="{{ $customer->name }}" id="customer_name"
                                                readonly />
                                            <input type="hidden" value="{{ $customer->id }}" id="customer_id"
                                                name="customer_id" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <label class="col-md-3">Project Name</label>
                                        <div class="col-md-7"><input type="text" value="" name="name"
                                                id="project_name" required /></div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <label class="col-md-3">Address</label>
                                        <div class="col-md-7">
                                            <textarea name="address" id="project_address" required></textarea>
                                        </div>
                                    </div>
                                </div>

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
    @endif

    {{-- ADD NEW CHALLAN MODAL --}}
    @if ($user->hasRole('super-admin') || $user->can('challan-create'))
        <div class="modal fade" id="addChallanModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4>Add Challan</h4>
                    </div>
                    <form action="{{ route('customer.challan.store') }}" method="post" class="form-horizontal">
                        <div class="modal-body modal-body-np">
                            <div class="row">
                                <div class="block-fluid">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                    <div class="row-form clearfix">
                                        <div class="col-md-6">
                                            <label>Challan No</label>
                                            <input type="text" value="" name="challan_no" id="challan_no"
                                                class="form-control" required />
                                        </div>
                                        <div class="col-md-6">
                                            <label>Branch</label>
                                            @if ($user->branchId == '')
                                                <select name="branchId" id="branchId" class="form-control">
                                                    <option value="">----- Select Branch -----</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="hidden" name="branchId" id="branchId"
                                                    value="{{ $user->branchId }}" />
                                                <input type="text" value="{{ $user->branch->branchName }}"
                                                    readonly />
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label>Select a Project</label>
                                            <select name="project_id" id="project_id" class="form-control" required>
                                                <option value="">choose a option...</option>
                                                @foreach ($customer->projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Select PSI</label>
                                            <select name="mix_design_id" id="psi" class="form-control" required>
                                                <option value="">choose a option...</option>
                                                @foreach ($customer->mixDesigns as $design)
                                                    <option value="{{ $design->id }}">{{ $design->psi }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Quantity(CuM)</label>
                                            <input type="text" value="" name="cuM" id="cuM"
                                                class="form-control" required />
                                        </div>
                                        <div class="col-md-6">
                                            <label>Date</label>
                                            <input type="text" value="" name="sell_date" id="sell_date"
                                                class="datepicker form-control" required />
                                        </div>
                                        <div class="col-md-12">
                                            <label>Description</label>
                                            <textarea name="description" id="description" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>

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
    @endif

    {{-- CHALLAN EDIT MODAL --}}

    <div class="modal fade" id="challanEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Challan Rate</h4>
                </div>
                <form action="{{ route('customer.challan.update') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <input type="hidden" name="customer_id" id="challan_customer_id" value="" />
                        <input type="hidden" name="id" id="challan_id" value="" />

                        <div class="row-form clearfix">
                            <label class="col-md-3">Project: </label>
                            <div class="col-md-6">
                                <select name="project_id" id="challan_project_id" required>
                                    <option value="">choose project</option>
                                    @foreach ($customer->projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">PSI: </label>
                            <div class="col-md-6">
                                <select name="mix_design_id" id="challan_mix_design_id" required>
                                    <option value="">choose PSI</option>
                                    @foreach ($customer->mixDesigns as $design)
                                        <option value="{{ $design->id }}">{{ $design->psi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row-form clearfix">
                            <label class="col-md-3">Quantity (CuM): </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="cuM" id="challan_cuM" required />
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Date: </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="sell_date" id="challan_date"
                                    class="datepicker" required />
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Rate: </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="rate" id="challan_rate" />
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer" style="text-align: center;">
                        <button class="btn btn-primary" type="submit">Save Updates</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <!-- PAYMENT DETAILS MODAL -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Customer Payment Details</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            {{ csrf_field() }}
                            <div class="row-form clearfix">
                                <div class="col-md-3">Customer Name:</div>
                                <div class="col-md-6" id="payment_customer_name"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Transaction Id:</div>
                                <div class="col-md-6" id=payment_"trx_id">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Bill No:</div>
                                <div class="col-md-6" id="payment_bill_no">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Payment Date :</div>
                                <div class="col-md-6" id="payment_payment_date">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Payment Mode :</div>
                                <div class="col-md-6" id="payment_payment_mode">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Bank Name :</div>
                                <div class="col-md-6" id="payment_bank_name">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Cheque No :</div>
                                <div class="col-md-6" id="payment_cheque_no">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Cheque Date :</div>
                                <div class="col-md-6" id="payment_cheque_date">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Amount :</div>
                                <div class="col-md-6" id="payment_payment_amount"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Adjustment Amount :</div>
                                <div class="col-md-6" id="payment_adjustment_amount"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Description</div>
                                <div class="col-md-6" id="payment_description"></div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Bill Modal -->
    <div class="modal fade" id="editBillModal" tabindex="-1" role="dialog" aria-labelledby="editBillModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Bill #<span id="modalBillId"></span></h4>
                </div>


                <form id="editBillForm" method="POST" action="{{ route('customer.bill.update') }}">
                    @csrf
                    <input type="hidden" name="bill_id" id="bill_id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="edit_bill_date">Bill Date *</label>
                                <input type="date" class="form-control" id="edit_bill_date" name="bill_date"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="edit_concrete_method">Concrete Method *</label>
                                <select class="form-control" id="edit_concrete_method" name="concrete_method"
                                    required>
                                    <option value="Pump">Pump</option>
                                    <option value="Non Pump">Non Pump</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="edit_total_cft">Total CFT *</label>
                                <input type="number" step="any" class="form-control" id="edit_total_cft"
                                    name="total_cft" required readonly>
                            </div>

                            <div class="col-md-4">
                                <label for="edit_total_cuM">Total Cu.M *</label>
                                <input type="number" step="any" class="form-control" id="edit_total_cuM"
                                    name="total_cuM" required readonly>
                            </div>

                            <div class="col-md-4">
                                <label for="edit_total_amount">Total Amount *</label>
                                <input type="number" step="any" class="form-control" id="edit_total_amount"
                                    name="total_amount" readonly>
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="returned_cft">Returned CFT</label>
                                <input type="number" step="any" class="form-control" id="returned_cft"
                                    name="returned_cft">
                            </div>
                            <div class="col-md-4">
                                <label for="returned_cum">Returned Cu.M</label>
                                <input type="number" step="any" class="form-control" id="returned_cum"
                                    name="returned_cum">
                            </div>
                            <div class="col-md-4">
                                <label for="discount_amount">Discount Amount</label>
                                <input type="number" step="any" class="form-control" id="discount_amount"
                                    name="discount_amount">
                            </div>
                        </div>


                        <div class="row mt-3">
                            <div class="col-md-4" id="edit_pump_div">
                                <label for="edit_pump_charge">Pump Charge</label>
                                <input type="number" step="any" class="form-control" id="edit_pump_charge"
                                    name="pump_charge">
                            </div>

                            <div class="col-md-4">
                                <label for="edit_eng_tips">Engineer Tips</label>
                                <input type="number" step="any" class="form-control" id="edit_eng_tips"
                                    name="eng_tips">
                            </div>

                            <div class="col-md-4">
                                <label for="edit_vat">VAT (%)</label>
                                <input type="number" step="any" class="form-control" id="edit_vat"
                                    name="vat">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="edit_ait">AIT (%)</label>
                                <input type="number" step="any" class="form-control" id="edit_ait"
                                    name="ait">
                            </div>

                            <div class="col-md-8">
                                <label for="edit_description">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Bill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection



@section('page-script')
    <script type="text/javascript">
        jQuery(document).ready(function($) {


            $(document).on('click', '.edit-btn', function() {
                $('#id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('#address').val($(this).data('address'));
            });

            $(document).on('click', '.bill-edit-btn', function() {
                $('#bill_id').val($(this).data('bill_id'));
                $('#bill_customer_name').html($(this).data('bill_customer_name'));
                $('#bill_date').val($(this).data('bill_date'));
                $('#vat').val($(this).data('vat'));
                $('#ait').val($(this).data('ait'));
                $('#bill_eng_tips').val($(this).data('bill_eng_tips'));
                $('#total_cuM').val($(this).data('total_cuM'));
                $('#total_cft').val($(this).data('total_cft'));
                $('#bill_rate').val($(this).data('bill_rate'));
                $('#bill_total_amount').val($(this).data('bill_total_amount'));
            });

            // $(document).on('click', '.challan-edit-btn', function () {
            //     $('#challan_customer_id').val($(this).data('challan_customer_id'));
            //     $('#challan_id').html($(this).data('challan_id'));
            //     $('#challan_psi').val($(this).data('challan_psi'));
            //     $('#challan_date').val($(this).data('challan_date'));
            //     $('#challan_cuM').val($(this).data('challan_cum'));
            //     $('#challan_project_id').val($(this).data('challan_project_id'));
            //     $('#challan_mix_design_id').val($(this).data('challan_mix_design_id'));
            // });


            $(document).on('click', '.view_payment_btn', function() {
                $('#payment_customer_name').html($(this).data('customer_name'));
                $('#payment_trx_id').html($(this).data('trx_id'));
                $('#payment_bill_no').html($(this).data('bill_no'));
                $('#payment_payment_date').html($(this).data('payment_date'));
                $('#payment_payment_mode').html($(this).data('payment_mode'));
                $('#payment_bank_name').html($(this).data('bank_name'));
                $('#payment_cheque_no').html($(this).data('cheque_no'));
                $('#payment_cheque_date').html($(this).data('cheque_date'));
                $('#payment_payment_amount').html($(this).data('payment_amount'));
                $('#payment_adjustment_amount').html($(this).data('adjustment_amount'));
                $('#payment_description').html($(this).data('description'));

            });

            //enable bill generate button
            var $checkbox = $("input[type=checkbox]");
            $checkbox.change(function() {
                var len = $("input[type=checkbox]:checked").length;
                if (len > 0) {
                    $("#bill_btn_div").css('display', 'block');
                } else {
                    $("#bill_btn_div").css('display', 'none');
                }
                len = 0;
            });


            var $checkbox1 = $("input[type=checkbox]");
            $checkbox1.change(function() {
                var len = $("input[type=checkbox]:checked").length;
                if (len > 0) {
                    $("#demo_bill_btn_div").css('display', 'block');
                } else {
                    $("#demo_bill_btn_div").css('display', 'none');
                }
                len = 0;
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            $(document).on('click', '.challan-edit-btn', function(e) {
                e.preventDefault(); // make sure it doesn't jump

                var project_id = $(this).data('challan_project_id');
                var mix_design_id = $(this).data('challan_mix_design_id');

                // Reset dropdowns
                $('#challan_project_id').empty().append($('<option>', {
                    value: '',
                    text: 'choose project'
                }));
                $('#challan_mix_design_id').empty().append($('<option>', {
                    value: '',
                    text: 'choose PSI'
                }));

                // Ajax load related projects & mix design
                $.ajax({
                    type: "POST",
                    url: "{{ route('customer.project.psi') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer_id: $(this).data('challan_customer_id')
                    },
                    dataType: 'JSON',
                    success: function(resp) {
                        $.each(resp.projects, function(i, member) {
                            $('#challan_project_id').append($('<option>', {
                                value: member.id,
                                text: member.name
                            }));
                        });
                        $.each(resp.mix_design_psi, function(i, member) {
                            $('#challan_mix_design_id').append($('<option>', {
                                value: member.id,
                                text: member.psi
                            }));
                        });

                        $('#challan_project_id').val(project_id);
                        $('#challan_mix_design_id').val(mix_design_id);
                    }
                });

                // Fill modal fields
                $('#challan_customer_id').val($(this).data('challan_customer_id'));
                $('#challan_id').val($(this).data('challan_id'));
                $('#challan_date').val($(this).data('challan_date'));
                $('#challan_cuM').val($(this).data('challan_cum'));
                $('#challan_rate').val($(this).data('challan_rate'));

                // Open modal manually if data-toggle not working
                $('#challanEditModal').modal('show');
            });

        });
    </script>

    <script>
        function toggleEditPumpCharge(method) {
            if (method === "Pump") {
                $("#edit_pump_div").show();
            } else {
                $("#edit_pump_div").hide();
                $("#edit_pump_charge").val(""); // Optional: clear value
            }
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle Concrete Method change (live)
            $('#edit_concrete_method').on('change', function() {
                const selectedMethod = $(this).val();
                toggleEditPumpCharge(selectedMethod);
            });

            // Edit Bill Button Click
            $(document).on('click', '.edit-bill', function(e) {
                e.preventDefault();

                let billId = $(this).data('id');
                let url = "{{ route('customer.bill.edit', ['id' => '__ID__']) }}".replace('__ID__',
                    billId);

                $('#editBillModal').modal('show');
                $('#editBillModal input, #editBillModal select, #editBillModal textarea').prop('disabled',
                    true);
                $('#modalBillId').text(billId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let bill = response.bill;

                            $('#bill_id').val(bill.id);
                            $('#edit_bill_date').val(bill.bill_date.split(' ')[0]);
                            $('#edit_concrete_method').val(bill.concrete_method);
                            toggleEditPumpCharge(bill.concrete_method); // <-- toggle visibility

                            $('#edit_total_cft').val(parseFloat(bill.total_cft).toFixed(4));
                            $('#edit_total_cuM').val(parseFloat(bill.total_cuM).toFixed(4));
                            $('#edit_total_amount').val(parseFloat(bill.total_amount).toFixed(
                                2));
                            $('#edit_pump_charge').val(parseFloat(bill.pump_charge).toFixed(2));
                            $('#edit_eng_tips').val(parseFloat(bill.eng_tips).toFixed(2));
                            $('#edit_vat').val(parseFloat(bill.vat).toFixed(2));
                            $('#edit_ait').val(parseFloat(bill.ait).toFixed(2));
                            $('#edit_description').val(bill.description);

                            $('#editBillModal input, #editBillModal select, #editBillModal textarea')
                                .prop('disabled', false);
                        } else {
                            alert('Error: ' + (response.message || 'Failed to load bill data'));
                            $('#editBillModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON?.message || 'Error loading bill data';
                        alert(errorMsg);
                        console.error('Error details:', xhr.responseJSON);
                        $('#editBillModal').modal('hide');
                    }
                });
            });

            // Update Bill Submit
            $('#editBillForm').submit(function(e) {
                e.preventDefault();
                let $form = $(this);
                let $submitBtn = $form.find('button[type="submit"]');

                $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert('Bill updated successfully!');
                            $('#editBillModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + (response.message || 'Update failed'));
                            $submitBtn.prop('disabled', false).html('Update Bill');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error updating bill';
                        if (xhr.responseJSON?.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors).join('\n');
                        } else if (xhr.responseJSON?.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        alert(errorMsg);
                        console.error('Update error:', xhr.responseJSON);
                        $submitBtn.prop('disabled', false).html('Update Bill');
                    }
                });
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {


            //Buttons examples
            $(document).ready(function() {
                let table = $('#datatable4').DataTable({
                    dom: 'Bfrtip',
                    select: true,
                    lengthMenu: [
                        [50, 100, 150, 200, 300, 500, 700, -1],
                        [50, 100, 150, 200, 300, 500, 700, "All"]
                    ],

                    fixedHeader: true,
                    pageLength: 50, // Show 50 rows by default
                    order: [],
                    buttons: [{
                            extend: 'pageLength', // Optional: you can remove this too if not needed
                            className: 'btn btn-danger',
                            exportOptions: {
                                columns: [':not(.hidden-print)']
                            },
                        },
                        {
                            extend: 'csv',
                            className: 'btn btn-info',
                            exportOptions: {
                                columns: [':not(.hidden-print)']
                            },
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-success',
                            exportOptions: {
                                columns: [':not(.hidden-print)']
                            },
                        },
                        {
                            extend: 'print',
                            text: 'Print All',
                            autoPrint: true,
                            className: 'btn btn-warning',
                            exportOptions: {
                                columns: [':not(.hidden-print)']
                            },
                            messageTop: function() {
                                return print_header;
                            },
                            messageBottom: 'Print: {{ date('d-M-Y') }}',
                            customize: function(win) {
                                $(win.document.body).find('h1').css('text-align', 'center');
                                $(win.document.body).find('table')
                                    .removeClass(
                                        'table-striped table-responsive-sm table-responsive-lg dataTable'
                                        )
                                    .addClass('compact')
                                    .css({
                                        'font-size': 'inherit',
                                        'color': '#000'
                                    });
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print Page',
                            autoPrint: true,
                            className: 'btn btn-success',
                            exportOptions: {
                                columns: [':not(.hidden-print)'],
                                modifier: {
                                    page: 'current'
                                }
                            },
                            messageTop: function() {
                                return print_header;
                            },
                            messageBottom: 'Print: {{ date('d-M-Y') }}',
                            customize: function(win) {
                                $(win.document.body).find('h1').css('text-align', 'center');
                                $(win.document.body).find('table')
                                    .removeClass(
                                        'table-striped table-responsive-sm table-responsive-lg dataTable'
                                        )
                                    .addClass('compact')
                                    .css({
                                        'font-size': 'inherit',
                                        'color': '#000'
                                    });
                            }
                        }
                    ]
                });

                //  Change page length based on input
                $('#customLengthInput').on('change keyup', function() {
                    let val = parseInt($(this).val());
                    if (!isNaN(val) && val > 0) {
                        table.page.len(val).draw();
                    }
                });

                // Optional: Hide the default length dropdown if it still shows up from the `pageLength` button
                $('#datatable4_length').hide();
            });


            //Buttons examples
            $(document).ready(function() {
                let table = $('#datatable5').DataTable({
                    dom: 'Bfrtip',
                    select: true,
                    lengthMenu: [
                        [50, 100, 150, 200, 300, 500, 700, -1],
                        [50, 100, 150, 200, 300, 500, 700, "All"]
                    ],

                    fixedHeader: true,
                    pageLength: 50, // Show 50 rows by default
                    order: [],
                    buttons: [{
                            extend: 'pageLength', // Optional: you can remove this too if not needed
                            className: 'btn btn-danger',
                            exportOptions: {
                                columns: [':not(.hidden-print)']
                            },
                        },
                        {
                            extend: 'csv',
                            className: 'btn btn-info',
                            exportOptions: {
                                columns: [':not(.hidden-print)']
                            },
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-success',
                            exportOptions: {
                                columns: [':not(.hidden-print)']
                            },
                        },
                        {
                            extend: 'print',
                            text: 'Print All',
                            autoPrint: true,
                            className: 'btn btn-warning',
                            exportOptions: {
                                columns: [':not(.hidden-print)']
                            },
                            messageTop: function() {
                                return print_header;
                            },
                            messageBottom: 'Print: {{ date('d-M-Y') }}',
                            customize: function(win) {
                                $(win.document.body).find('h1').css('text-align', 'center');
                                $(win.document.body).find('table')
                                    .removeClass(
                                        'table-striped table-responsive-sm table-responsive-lg dataTable'
                                        )
                                    .addClass('compact')
                                    .css({
                                        'font-size': 'inherit',
                                        'color': '#000'
                                    });
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print Page',
                            autoPrint: true,
                            className: 'btn btn-success',
                            exportOptions: {
                                columns: [':not(.hidden-print)'],
                                modifier: {
                                    page: 'current'
                                }
                            },
                            messageTop: function() {
                                return print_header;
                            },
                            messageBottom: 'Print: {{ date('d-M-Y') }}',
                            customize: function(win) {
                                $(win.document.body).find('h1').css('text-align', 'center');
                                $(win.document.body).find('table')
                                    .removeClass(
                                        'table-striped table-responsive-sm table-responsive-lg dataTable'
                                        )
                                    .addClass('compact')
                                    .css({
                                        'font-size': 'inherit',
                                        'color': '#000'
                                    });
                            }
                        }
                    ]
                });

                //  Change page length based on input
                $('#customLengthInput2').on('change keyup', function() {
                    let val = parseInt($(this).val());
                    if (!isNaN(val) && val > 0) {
                        table.page.len(val).draw();
                    }
                });

                // Optional: Hide the default length dropdown if it still shows up from the `pageLength` button
                $('#datatable5_length').hide();
            });


        });
    </script>

@endsection




<script>

  //preetom
    function printStatement(elementId, mode) {
        const FIRST_PAGE_ROW_LIMIT = 12; 
        const OTHER_PAGE_ROW_LIMIT = 14; 

        const container = document.getElementById(elementId);
        if (!container) {
            alert('Content container not found!');
            return;
        }

        const headingText = 'Customer Statement';

        // Get the statement table
        const table = container.querySelector('table#datatable');
        if (!table) {
            alert('Statement table not found!');
            return;
        }

        // Extract client info from the page
        const customerName = container.querySelector('.customer-name')?.innerText?.trim() || '';
        const customerAddress = container.querySelector('.customer-address')?.innerText?.trim() || '';
        const statementRange = container.querySelector('.src-info')?.innerText?.replace('- ', '').trim() ||
            'Last 30 days';

        // Get thead and rows
        const theadHtml = table.querySelector('thead').outerHTML;
        const tbodyRows = Array.from(table.querySelectorAll('tbody tr'));

        // ---- HYBRID COLUMN DETECTION (Name First, Then Position) ----
        let debitIdx = -1, creditIdx = -1, balanceIdx = -1;
        const headers = table.querySelectorAll('th');

        // 1. Try to find columns by Header Text (Most Reliable)
        headers.forEach((th, index) => {
            const text = th.innerText.trim().toLowerCase();
            if (debitIdx === -1 && (text.includes('debit') || text.includes('dr.'))) {
                debitIdx = index;
            }
            if (creditIdx === -1 && (text.includes('credit') || text.includes('cr.'))) {
                creditIdx = index;
            }
            if (balanceIdx === -1 && (text.includes('balance') || text.includes('bal'))) {
                balanceIdx = index;
            }
        });

        // 2. Fallback: If not found by name, find last 3 numeric columns
        if (debitIdx === -1 || creditIdx === -1 || balanceIdx === -1) {
            const sampleRow = Array.from(tbodyRows).find(tr => {
                const cells = tr.querySelectorAll('td');
                if (cells.length < 3) return false;
                const txt = tr.innerText.toLowerCase();
                return !txt.includes('total') && !txt.includes('trash');
            });

            if (sampleRow) {
                const cells = sampleRow.querySelectorAll('td');
                let numericIndices = [];
                
                cells.forEach((cell, index) => {
                    const val = parseFloat(cell.innerText.replace(/,/g, ''));
                    if (!isNaN(val)) {
                        numericIndices.push(index);
                    }
                });

                if (numericIndices.length >= 3) {
                    if (debitIdx === -1) debitIdx = numericIndices[numericIndices.length - 3];
                    if (creditIdx === -1) creditIdx = numericIndices[numericIndices.length - 2];
                    if (balanceIdx === -1) balanceIdx = numericIndices[numericIndices.length - 1];
                } else if (numericIndices.length === 2) {
                    if (creditIdx === -1) creditIdx = numericIndices[0];
                    if (balanceIdx === -1) balanceIdx = numericIndices[1];
                }
            }
        }

        // Final fallback if still failed (based on typical table length)
        if (debitIdx === -1) debitIdx = 6; // Default guess
        if (creditIdx === -1) creditIdx = 8;
        if (balanceIdx === -1) balanceIdx = 9;


        // ---- Calculate summary ----
        let openingBalance = 0;
        let closingBalance = 0;
        let debitTotal = 0;
        let creditTotal = 0;
        let debitCount = 0;
        let creditCount = 0;
        let isFirstRowFound = false;
        let totalBillableAmount = {{ $total_billable }};
        let balanceText = `{!! strip_tags($customer->balanceText()) !!}`;
let totalClosingBillableAmount = parseFloat(balanceText.replace(/[^0-9.-]/g, '')) || 0;
      

        // Collect valid rows (skip rows with 'total' in 2nd column or keywords)
        const validRows = tbodyRows.filter(tr => {
            const cells = tr.querySelectorAll('td');
            return cells.length >= 2 && !cells[1]?.innerText.trim().toLowerCase().startsWith('total');
        });

        validRows.forEach((tr) => {
            const cells = tr.querySelectorAll('td');
            
            // Ensure row has enough columns
            if (cells.length <= balanceIdx) return;

            const debitVal = parseFloat((cells[debitIdx]?.innerText || '0').replace(/,/g, '')) || 0;
            const creditVal = parseFloat((cells[creditIdx]?.innerText || '0').replace(/,/g, '')) || 0;
            const balanceVal = parseFloat((cells[balanceIdx]?.innerText || '0').replace(/,/g, '')) || 0;

            // --- OPENING BALANCE LOGIC (First Row Value) ---
            if (!isFirstRowFound) {
                openingBalance = balanceVal;
                isFirstRowFound = true;
            }

            // --- CLOSING BALANCE LOGIC (Last Row Value) ---
            closingBalance = balanceVal;

            // --- TOTALS LOGIC (Sum all Debit and Credit) ---
            debitTotal = debitVal;
            creditTotal = creditVal;
            
            if (debitVal > 0) debitCount++;
            if (creditVal > 0) creditCount++;
        });


        // ---- Build customer info block ----
        const clientInfoHtml = `
        <div class="client-info" style="margin-bottom:10px; font-size:13px;">
            <div><strong>Client Name:</strong> ${@json($customer->name)}</div>
            <div><strong>Client Address:</strong> ${@json($customer->address)}</div>
            <div><strong>Client Phone:</strong> ${@json($customer->phone)}</div>
            <div><strong>Statement Period:</strong> ${statementRange}</div>
        </div>
    `;

        // ---- Summary Table ----
        const summaryHtml = `
        <div class="statement-summary" style="margin-top:8px; font-size:11px; font-weight:bold; background:white; padding:10px; border-radius:6px;">
            <table>
               
            </table>
            <table style="width:100%; border-collapse:collapse; border:none; ">

                  <tr>
                      <td style="border:none;"></td>
                    <td style="border:none;"></td>
                    <td style="text-align:right; border:none;">
                    </td>
                    <td style="border:none;"></td>
                    <td style="border:none;">Invoice Amount</td>
                    <td style="text-align:right; border:none;">
                        ${totalBillableAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </td>
                    <td style="border:none;"></td>
                 </tr>
                <tr>
                    <td style="border:none;"></td>
                    <td style="border:none;">Opening Balance</td>
                    <td style="text-align:right; border:none;">
                        ${openingBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </td>
                        <td style="border:none;"></td>
                    <td style="border:none;">Total Closing Balance</td>
                    <td style="text-align:right; border:none;">
                        ${totalClosingBillableAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </td>
                    <td style="border:none;"></td>
                </tr>
                <tr>
                    <td style="border:none;"></td>
                    <td style="border:none;">Total Debit</td>
                    <td style="text-align:right; border:none;">
                        ${debitTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
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
                        ${creditTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </td>
                    <td style="border:none;"></td>
                    <td style="border:none;">Credit Count</td>
                    <td style="text-align:right; border:none;">${creditCount}</td>
                    <td style="border:none;"></td>
                </tr>
                
                 

                
            </table>
             <p style="text-align:center; font-size:11px;">Please note that if no reply is received from you within a 14 days, it will be assumed that you have accepted the balance shown Above.</p>

        </div>
    `;

        // ---- Split Pages ----
        const pages = [];
        let currentIndex = 0;
        let pageNumber = 0;
        
        // Use validRows for printing
        const rowsToPrint = validRows;

        while (currentIndex < rowsToPrint.length) {
            const rowLimit = (pageNumber === 0) ? FIRST_PAGE_ROW_LIMIT : OTHER_PAGE_ROW_LIMIT;
            
            const rowsSlice = rowsToPrint.slice(currentIndex, currentIndex + rowLimit);
            const rowsHtml = rowsSlice.map(r => r.outerHTML).join('');
            const isLastPage = (currentIndex + rowLimit >= rowsToPrint.length);

            const tableHtml = `
            <table cellpadding="0" cellspacing="0" width="100%" class="table">
                ${theadHtml}
                <tbody style="font-size: 8px">${rowsHtml}</tbody>
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
    ${pageNumber === 0 ? clientInfoHtml : ''}
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
                Head Office: 1 No. Joykali Mondir Road (1st Floor), Wari, Dhaka-1203. 📞02-226638877 ✉ccbd16@gmail.com 🅵/cclbd16
            </div>
        </div>
    `;

        // ---- Compose All Pages ----
        const allPagesHtml = pages.map((page, index) => `
        <div class="page ${index === pages.length - 1 ? 'last-page' : ''}">
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
            <title>Statement</title>
          <style>
    @page {
        size: A4;
        margin: 0;
    }
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        font-size: 12px;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
    }
    table tbody.client_info tr td {
        padding: 0;
    }
    .header, .footer {
        position: fixed;
        left: 0;
        right: 0;
        background: white;
        z-index: 10;
        box-sizing: border-box;
    }
   .header {
    height: 135px;
    padding: 15px 45px;
    border-bottom: 4px solid green;
    top: 0;
    display: flex;
    align-items: center;
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
    .header-content {
        display: flex;
        align-items: center;
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
    }
    .footer {
        height: 80px;
        bottom: 0;
        width: 100%;
        padding: 0 35px;
        box-sizing: border-box;
        page-break-inside: avoid;
        font-size: 13px;
        border-top: 4px solid black;
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
    .footer-content p {
        margin: 0;
    }
    .content {
        margin: 0;
        padding: 120px 20px 100px 20px;
        box-sizing: border-box;
        min-height: calc(297mm - 180px); 
        position: relative;
    }
    .invoice-title {
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        margin: 20px 0 10px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        page-break-inside: auto;
    }
    thead {
        display: table-header-group;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    table, th, td {
        border: 1px solid #ddd;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate, .dt-buttons {
        display: none !important;
    }
    .page {
        page-break-after: always;
        position: relative;
    }
    /* FIX FOR BLANK PAGE */
    .page.last-child, .page:last-child {
        page-break-after: auto;
    }
    @media print {
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


