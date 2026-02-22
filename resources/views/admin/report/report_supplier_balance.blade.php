@extends('admin.layouts.master')
@section('title', 'All Supplier Balance Report')
@section('breadcrumb', 'All Supplier Balance Report')
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h3>All Supplier Balance Report</h3>
                    <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                        <form action="{{ route('searchAllSupplierBalance') }}" method="post" enctype="multipart/form-data" id="search_all_form" class="form-horizontal">

                            <div class="" align="right">
                                {{csrf_field()}}
                                <input type="text" name="search_name" id="search_name" placeholder="Enter supplier Name" />
                                <input type="date" name="from_date" id="from_date" placeholder="From Date" />
                                <input type="date" name="to_date" id="to_date" placeholder="To Date" />

                                <button type="submit" id="btn_search" class="btn btn-default">Search</button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <div id="statement_info">
                        <h5>{{$date_info}}</h5>
                    </div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th style="font-size: 15px">Supplier Name</th>
                            <th style="font-size: 15px">Debit</th>
                            <th style="font-size: 15px">Credit</th>
                            <th style="font-size: 15px">Advance</th>
                            <th style="font-size: 15px">Due</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php 
                                $i = 1; 
                                $total_advance = 0; 
                                $total_due = 0; 
                                $payable = 0; 
                                $receivable = 0; 
                            @endphp

                            @foreach ($all_statements as $statement)
                                <tr>
                                    <td style="font-size: 15px">{{ $statement->name }}</td>
                                    <td style="font-size: 15px">{{ number_format($statement->payable, 2) }}</td>
                                    <td style="font-size: 15px">{{ number_format($statement->receivable, 2) }}</td>
                                    <td style="font-size: 15px !important">
                                        @if($statement->balance < 0)
                                            <span style="background:#007bff;color:#fff;padding:6px 12px;border-radius:6px;">{{ number_format(abs($statement->balance), 2) }} TK</span>
                                        @else
                                            <span style="background:#929191;color:#fff;padding:6px 12px;border-radius:6px;">0.00 TK</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 15px !important">
                                        @if($statement->balance > 0)
                                            <span style="background:#dc3545;color:#fff;padding:6px 12px;border-radius:6px;">{{ number_format(abs($statement->balance), 2) }} TK</span>
                                        @else
                                            <span style="background:#929191;color:#fff;padding:6px 12px;border-radius:6px;">0 TK</span>
                                        @endif
                                    </td>
                                </tr>

                                @php 
                                    $i++; 
                                    if($statement->balance < 0){
                                        $total_advance += abs($statement->balance);
                                    } else {
                                        $total_due += abs($statement->balance);
                                    }
                                    $payable += $statement->payable;
                                    $receivable += $statement->receivable;
                                @endphp
                            @endforeach

                            
                            </tbody>
                            <tfoot><tr>
                                <td style="font-size: 15px"><b>Total:</b></td>
                                <td style="font-size: 15px"><b>{{ number_format($payable,2) }}</b></td>
                                <td style="font-size: 15px"><b>{{ number_format($receivable,2) }}</b></td>
                                <td style="font-size: 15px"><b>{{ number_format($total_advance,2) }}</b></td>
                                <td style="font-size: 15px"><b>{{ number_format($total_due,2) }}</b></td>
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
            $('#btn_search').on('click',function(){
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                if(to_date < from_date)
                {
                    alert('The To date is less then from date');
                    return false;
                }
            });

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'suppliers'])!!}',
                minLenght:1,
                autoFocus:true,

            });
            $('#tSortable_2').DataTable({
                dom: 'flBrtip',
                "lengthMenu": [[100, 200, 500, 1000, -1], [100, 200, 500, 1000, "All"]],
                buttons: [
                    {
                        extend: 'print',
                        text: 'Print page',
                        autoPrint: true,
                        exportOptions: {
                            columns: '1,2,3,4',
                        },
                        customize: function ( win ) {
                            $(win.document.body).find('h1')
                                .after(
                                    $("#statement_info")
                                ).css('text-align','center');;
                        },
                    }
                ],

            } );
        } );
    </script>
@endsection

