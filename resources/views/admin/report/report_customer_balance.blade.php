@extends('admin.layouts.master')
@section('title', 'All Customer Balance Report')
@section('breadcrumb', 'All Customer Balance Report')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h3>All Customer Balance Report</h3>


                    <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                        <form action="{{ route('searchAllCustomerBalance') }}" method="post" enctype="multipart/form-data"
                            id="search_all_form" class="form-horizontal">

                            <div class="" align="right">
                                {{ csrf_field() }}
                                <input type="text" name="search_name" id="search_name"
                                    placeholder="Enter customer Name" />
                                <input type="date" name="from_date" id="from_date" placeholder="From Date" />
                                <input type="date" name="to_date" id="to_date" placeholder="To Date" />

                                <button type="submit" id="btn_search" class="btn btn-default">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <div id="statement_info"></div>
                    <div class="block-fluid table-sorting clearfix">
                        @if ($date_info)
                            <div id="statement_info" class="alert alert-info text-center mb-3">
                                <strong>{{ $date_info }}</strong>
                            </div>
                        @endif    
                        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th style ="font-size: 15px">Customer Name</th>
                                    <th style ="font-size: 15px">Debit</th>
                                    <th style ="font-size: 15px">Credit</th>
                                    <th style ="font-size: 15px">Balance</th>
                                </tr>
                            </thead>
                            @php
                                $i = 1;
                                $total_balance = 0;
                                $total_debit = 0;
                                $total_credit = 0;
                                $total_billable_all = 0;
                                $total_new_balance = 0;
                            @endphp

                            @foreach ($customer_statements as $statement)
                                @php
                                    $_balance = $statement->credit - $statement->debit;
                                    $_new_balance = $_balance - $statement->total_billable;
                                    $from = request('from_date') ?? null; //
                                    $to = request('to_date') ?? null; //

                                @endphp
                                <tr>
                                    <td style ="font-size: 15px">{{ $statement->name }}</td>
                                    <td style ="font-size: 15px">{{ number_format($statement->debitSum(), 2) }}</td>
                                    <td style ="font-size: 15px">{{ number_format($statement->creditSum(), 2) }}</td>
                                   
                                    <td style ="font-size: 15px">

                                     {!! $statement->balanceTextF($from, $to) !!} 

                                    </td>
                                    
                                </tr>
                                @php
                                    $i++;
                                    $total_balance += $statement->balance();
                                    $total_debit += $statement->debitSum();
                                    $total_credit += $statement->creditSum();
                                 
                                @endphp
                            @endforeach

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style ="font-size: 15px"><b>Total:</b></td>
                                    <td style ="font-size: 15px"><b>{{ 'BDT ' . number_format($total_debit, 2) }}</b></td>
                                    <td style ="font-size: 15px"><b>{{ 'BDT ' . number_format($total_credit, 2) }}</b></td>
                                   
                                    <td style ="font-size: 15px"><b>{{ 'BDT ' . number_format($total_balance, 2) }}</b></td>
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
                    source: '{!! URL::route('autoComplete', ['table_name' => 'customers']) !!}',
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

                // 🔎 Customer Name Autocomplete
                $("#search_name").autocomplete({
                    source: '{!! URL::route('autoComplete', ['table_name' => 'customers']) !!}',
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
