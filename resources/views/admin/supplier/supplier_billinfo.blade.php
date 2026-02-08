@extends('admin.layouts.master')
@section('title', 'Supplier Bill Info List')
@section('breadcrumb', 'Supplier Bill Info List')

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
                                {{ request('billinfo_date_range') ? '- ' . request('billinfo_date_range') : '- Last 30 Days' }}
                            </span>
                        </h1>
                    </div>
                    
                    <div class="col-md-8 search_box" style="margin-top: 4px;">
                        <form action="" method="GET" class="form-horizontal">
                            <div align="right">
                                <div class="col-md-6">
                                    <input type="text" name="billinfo_search_text" id="search_name"
                                        value="{{ request('billinfo_search_text') ?? '' }}" class="form-control"
                                        placeholder="Search (Supplier, Product, Bill No...)" />
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
                    <div class="table-responsive">
                        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">#</th>
                                    <th>Bill No</th>
                                    <th>DMR No</th>
                                    <th>Chalan No</th>
                                    <th>Rec Date</th>
                                    <th>Supplier Name</th>
                                    <th>Items</th>
                                    <th>Qty (Ton)</th>
                                    <th>Mat Cost (৳)</th>
                                    <th>Truck Rent (৳)</th>
                                    <th>Unload Bill (৳)</th>
                                    <th>Grand Total (৳)</th>
                                    <th class="hidden-print" style="width: 80px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $serial = 1;
                                    $t_items = 0;
                                    $t_qty = 0;
                                    $t_mat_cost = 0;
                                    $t_truck_rent = 0;
                                    $t_unload_bill = 0;
                                    $t_grand_total = 0;
                                @endphp

                                @forelse ($check_pb as $bill)
                                    <tr style=" font-weight: bold;">
                                        <td>{{ $serial++ }}</td>
                                        <td><strong>{{ $bill->bill_no ?? 'N/A' }}</strong></td>
                                        <td>{{ $bill->dmr_no ?? '-' }}</td>
                                        <td>{{ $bill->chalan_no ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($bill->received_date)->format('d-m-Y') }}</td>
                                        <td>{{ $bill->supplier->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $bill->total_items ?? 0 }}</td>
                                        <td class="text-end">{{ number_format($bill->total_qty ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($bill->total_material_cost ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($bill->total_truck_rent ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($bill->total_unload_bill ?? 0, 2) }}</td>
                                        <td class="text-end" >
                                            {{ number_format($bill->grand_total ?? 0, 2) }}
                                        </td>
                                        <td class="hidden-print text-center">
                                            <a href="{{ route('purchase.checked.details', $bill->bill_no) }}" 
                                                target="_blank" class="btn btn-sm btn-info" title="View Details">
                                                <span class="fa fa-eye"></span> Details
                                            </a>
                                        </td>
                                    </tr>

                                    @php
                                        $t_items += $bill->total_items ?? 0;
                                        $t_qty += $bill->total_qty ?? 0;
                                        $t_mat_cost += $bill->total_material_cost ?? 0;
                                        $t_truck_rent += $bill->total_truck_rent ?? 0;
                                        $t_unload_bill += $bill->total_unload_bill ?? 0;
                                        $t_grand_total += $bill->grand_total ?? 0;
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-muted" style="padding: 40px;">
                                            <i class="fa fa-inbox fa-3x"></i>
                                            <p class="mt-2">No records found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if($check_pb->count() > 0)
                            <tfoot>
                                <tr style="background-color: #28a745; color: #fff; font-weight: bold; font-size: 16px;">
                                    <td colspan="6" class="text-end">Total ({{ $check_pb->count() }}Item Bill):</td>
                                    <td class="text-center">{{ $t_items }}</td>
                                    <td class="text-end">{{ number_format($t_qty, 2) }}</td>
                                    <td class="text-end">{{ number_format($t_mat_cost, 2) }}</td>
                                    <td class="text-end">{{ number_format($t_truck_rent, 2) }}</td>
                                    <td class="text-end">{{ number_format($t_unload_bill, 2) }}</td>
                                    <td class="text-end">{{ number_format($t_grand_total, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
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
            $('#datatable').DataTable({
                retrieve: true,
                paging: true,
                pageLength: 25,
                searching: true,
                info: true,
                ordering: true,
                order: [[0, 'asc']], // Order by first column (serial)
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ bills",
                    infoEmpty: "No bills found",
                    infoFiltered: "(filtered from _MAX_ total bills)",
                    zeroRecords: "No matching bills found",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // AutoComplete for search
            $("#search_name").autocomplete({
                source: '{{ route('autoComplete', ['table_name' => 'suppliers']) }}',
                minLength: 1,
                autoFocus: true
            });
        });
    </script>
@endsection