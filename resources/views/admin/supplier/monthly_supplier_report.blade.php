@extends('admin.layouts.master')
@section('title', 'Monthly supllier Report')
@section('breadcrumb', 'Monthly supllier Report')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>
                            supllier Payment Details
                            {{-- @if (request()->has('date_range')) --}}

                            <span
                                class="src-info">{{ request('search_text') == '' && request('date_range') == '' ? '- Last 30 Days' : '- ' . request('date_range') }}</span>
                            {{-- @endif --}}
                        </h1>
                    </div>
                    <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                        <form action="{{ route('monthly.supplier.report.index') }}" method="GET" class="form-horizontal">
                            <input type="text" placeholder="Enter supllier Name" name="search_name" ... />
                            <input type="date" name="from_date" ... />
                            <input type="date" name="to_date" ... />
                            <button type="submit">Search</button>
                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <div id="statement_info"></div>
                    <div class="block-fluid table-sorting clearfix">
                       <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th width="2%"><input type="checkbox" name="checkall" /></th>
                                    <th>S/N</th>
                                    <th>Suppliers Name</th>
                                    {{-- <th>Previous Balance (Adv./Due)</th> --}}
                                    {{-- <th>QTY(Ton)</th> --}}
                                    <th>QTY(cft/Ton)</th>
                                    <th>Inv./Bill Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Balance(Adv./Due)</th>
                                    {{-- <th>Monthly Ending Balance (Adv./Due)</th> --}}
                                    <th>Outstanding (Adv./Due)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // $total_qty_ton = 0;
                                    $total_qty_cft = 0;
                                    $total_amount = 0;
                                    $total_collection = 0;
                                    $total_balance = 0;
                                    $total_prev_balance = 0;
                                    // $total_monthly_ending = 0;
                                    $total = 0;
                                    $i = 1; $total = 0;
                                @endphp

                                @foreach ($supplierReports as $report)
                                    @php
                                    
                                        // Parsing values back for calculation
                                        // $total_qty_ton += floatval(str_replace(',', '', $report['qty_ton']));
                                        $total_qty_cft += floatval(str_replace(',', '', $report['qty_cft']));
                                        $total_amount += floatval(str_replace(',', '', $report['total_amount']));
                                        $total_collection += floatval(str_replace(',', '', $report['collection_amount']));
                                        $total_balance += floatval(str_replace(',', '', $report['balance_amount']));
                                        // $total_prev_balance += floatval(str_replace(',', '', $report['previous_balance_amount']));
                                        // $total_monthly_ending += floatval(str_replace(',', '', $report['monthly_ending_amount']));
                                        $supplier = \App\Models\Supplier::find($report['supplier_id']);
                                        $total +=$supplier->balance();
                                         
                                        
                                    @endphp

                                    <tr>
                                        <td><input type="checkbox" class="row-checkbox"></td>
                                        <td>{{ $report['s_no'] }}</td>
                                        <td>{{ $report['supplier_name'] }}</td>
                                        {{-- <td style="text-align: right;">
                                            {{ $report['previous_balance_amount'] }}
                                            <span style="color: {{ $report['previous_balance_label'] == 'Due' ? 'red' : 'green' }}">
                                                {{ $report['previous_balance_label'] }}
                                            </span>
                                        </td> --}}
                                        {{-- <td style="text-align: right;">
                                            {{ $report['qty_ton'] ?? '' }}
                                        </td> --}}
                                        <td style="text-align: right;">
                                            {{ $report['qty_cft'] ?? '' }}
                                        </td>

                                        <td style="text-align: right;">{{ $report['total_amount'] }}</td>
                                        <td style="text-align: right;">{{ $report['collection_amount'] }}</td>
                                        <td style="text-align: right;">
                                            {{ $report['balance_amount'] }}
                                            <span style="color: {{ $report['balance_label'] == 'Due' ? 'red' : 'green' }}">
                                                {{ $report['balance_label'] }}
                                            </span>
                                        </td>

                                        {{-- <td style="text-align: right;">
                                            {{ $report['monthly_ending_amount'] }}
                                            <span style="color: {{ $report['monthly_ending_label'] == 'Due' ? 'red' : 'green' }}">
                                                {{ $report['monthly_ending_label'] }}
                                            </span>
                                        </td> --}}
                                <td style="width: 220px;font-size: 15px;">{!! $supplier->balanceText() !!}</td>
                                        
                                    </tr>
                                @endforeach

                            <tfoot>
                                <tr style="font-weight: bold; background:#f5f5f5;">
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;">TOTAL</td>
                                    {{-- <td style="text-align: right;">{{ number_format($total_prev_balance, 2) }}</td> --}}
                                    {{-- <td style="text-align: right;">{{ number_format($total_qty_ton, 2) }}</td> --}}
                                    <td style="text-align: right;">{{ number_format($total_qty_cft, 3) }}</td>
                                    <td style="text-align: right;">{{ number_format($total_amount, 2) }}</td>
                                    <td style="text-align: right;">{{ number_format($total_collection, 2) }}</td>
                                    <td style="text-align: right;">{{ number_format($total_balance, 2) }}</td>
                                    {{-- <td style="text-align: right;">{{ number_format($total_monthly_ending, 2) }}</td> --}}
                                    {{-- <td style="text-align: right;">{{ number_format($total_monthly_ending, 2) }}</td> --}}
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>

                        </table>

                    </div>
                </div>
            </div>
            <div class="dr"><span></span></div>
        </div>
    @endsection

    @section('page-script')
        <script>
            $(document).ready(function() {
                $("#search_name").autocomplete({
                    source: '{!! URL::route('autoComplete', ['table_name' => 'suplliers']) !!}',
                    minLenght: 1,
                    autoFocus: true,
                });

                $('#tSortable_2').DataTable({
                    dom: 'flBrtip',
                    "lengthMenu": [
                        [100, 200, 500, 1000, -1],
                        [100, 200, 500, 1000, "All"]
                    ],
                    buttons: [{
                        extend: 'print',
                        text: 'Print page',
                        autoPrint: true,
                        exportOptions: {
                            columns: '1,2,3,4',
                        },
                        customize: function(win) {
                            $(win.document.body).find('h1')
                                .after(
                                    $("#statement_info")
                                ).css('text-align', 'center');
                        },
                    }],
                });
            });



            $(document).ready(function() {

                // 🔍 Search button click event
                $('#btn_search').on('click', function() {

                    var from_date = $('#from_date').val();
                    var to_date = $('#to_date').val();

                    // Date validation
                    if (from_date !== '' && to_date !== '') {
                        if (to_date < from_date) {
                            alert('The To date is less than From date');
                            return false;
                        }
                    }
                });

                // 🔎 supllier Name Autocomplete
                $("#search_name").autocomplete({
                    source: '{!! URL::route('autoComplete', ['table_name' => 'suplliers']) !!}',
                    minLength: 1,
                    autoFocus: true,
                });

                // 📊 DataTable Setup
                $('#tSortable_2').DataTable({
                    dom: 'flBrtip',
                    lengthMenu: [
                        [100, 200, 500, 1000, -1],
                        [100, 200, 500, 1000, "All"]
                    ],
                    buttons: [{
                        extend: 'print',
                        text: 'Print page',
                        autoPrint: true,
                        exportOptions: {
                            columns: '1,2,3,4'
                        },
                        customize: function(win) {
                            $(win.document.body)
                                .find('h1')
                                .after($("#statement_info"))
                                .css('text-align', 'center');
                        }
                    }]
                });
            });
        </script>
    @endsection
