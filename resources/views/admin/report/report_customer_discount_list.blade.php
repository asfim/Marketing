@extends('admin.layouts.master')
@section('title', 'Customer Discount Report')
@section('breadcrumb', 'Customer Discount Report')
@section('content')

    <div class="workplace">
        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h3>Customer Discount Report</h3>
                    <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                        <form action="{{ route('customer.discount') }}" method="GET" id="search_discount_form" class="form-horizontal">
                            <input type="text" name="search_name" id="search_name" placeholder="Enter Customer Name" value="{{ request('search_name') }}" />
                            <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" />
                            <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" />
                            <button type="submit" class="btn btn-default">Search</button>
                        </form>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="block-fluid table-sorting clearfix">
                    <div id="statement_info"><h5>{{ $date_info }}</h5></div>
                    <table class="table table-bordered" id="datatable">
                        <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th>Customer Name</th>
                            <th>Transaction ID</th>
                            <th>Bill ID</th>
                            <th>Total Amount (Before Discount)</th>
                            <th>Returned CFT</th>
                            <th>Returned CUM</th>
                            <th>Discount Amount</th>
                            <th>Discount Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $total_discount = 0; @endphp
                        @forelse ($discounts as $row)
                            @php $total_discount += $row->discount_amount; @endphp
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $row->customer_name }}</td>
                                <td>{{ $row->transaction_id }}</td>
                                <td>{{ $row->bill_id }}</td>
                                <td>{{ number_format($row->total_amount_before_discount, 2) }}</td>
                                <td>{{ number_format($row->returned_cft, 2) }}</td>
                                <td>{{ number_format($row->returned_cum, 2) }}</td>
                                <td>{{ number_format($row->discount_amount, 2) }}</td>
                                <td>{{ date('d M Y, h:i A', strtotime($row->discount_date)) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8">No records found.</td></tr>
                        @endforelse
                        @if($discounts->count())
                            <tr>
                                <td colspan="6"><strong>Total Discount</strong></td>
                                <td colspan="2"><strong>{{ number_format($total_discount, 2) }} BDT</strong></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            $('#search_discount_form').on('submit', function () {
                const from = $('#from_date').val();
                const to = $('#to_date').val();
                if (to && from && to < from) {
                    alert('The To date must be after From date');
                    return false;
                }
            });

            $("#search_name").autocomplete({
                source : '{!! URL::route("autoComplete", ["table_name" => "customers"]) !!}',
                minLength: 1,
                autoFocus: true
            });

            // Destroy existing DataTable instance if exists before reinitializing
            if ($.fn.dataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable().clear().destroy();
            }

            $('#datatable').DataTable({
                dom: 'flBrtip',
                "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
                buttons: [
                    {
                        extend: 'print',
                        text: 'Print',
                        autoPrint: true,
                        exportOptions: { columns: ':visible' },
                        customize: function (win) {
                            $(win.document.body).find('h1').after($("#statement_info")).css('text-align', 'center');
                        },
                    }
                ],
            });
        });
    </script>
@endsection

