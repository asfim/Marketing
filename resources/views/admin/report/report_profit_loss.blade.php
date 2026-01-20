@extends('admin.layouts.master')
@section('title', 'Profit & Loss Report')
@section('breadcrumb', 'Profit & Loss Report')
@section('content')
    <div class="workplace">

        <div class="row" id="pl-report">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>
                            Profit & Loss Report
                            <span class="src-info">{{ request('date_range') == ''?'- Last 1 Year':'- '. request('date_range') }}</span>
                        </h1>
                    </div>

                    <div class="col-md-5 search_box hidden-print" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"  autocomplete="off" class="form-control" placeholder="Date Range" />
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default search-btn">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="col-md-2 text-right" style="margin-top: 4px;">
                        <span class="btn btn-sm btn-info hidden-print" onclick="printContent('pl-report')"> <i class="fa fa-print"></i></span>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" style="font-size: 16px;">
                        <thead>
                        <tr>
                            <th>Profit and Loss Head</th>
                            <th class="text-right">Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr style="font-weight: bold;">
                            <td><strong>Sales</strong></td>
                            <td class="text-right">{{ number_format($data['total_sale'],3) }}</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td>Less: Cost Of Good Sold</td>
                            <td class="text-right">({{ number_format($data['raw_material_purchase'],2) }})</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px !important;">Raw Materials Purchase</td>
                            <td class="text-right">({{ number_format($data['raw_material_purchase'],2) }})</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td>Gross Profit</td>
                            <td class="text-right">{{ number_format($data['total_sale']-$data['raw_material_purchase'],2) }}</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td>Less: Production Expense</td>
                            <td class="text-right">({{ number_format($data['total_production_expense'],2) }})</td>
                        </tr>
                        @foreach($data['production_expenses'] as $expense)
                        <tr>
                            <td style="padding-left: 50px !important;">{{ $expense->type_name }}</td>
                            <td class="text-right">({{ number_format($expense->total_expense,2) }})</td>
                        </tr>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td>Less: General Expense</td>
                            <td class="text-right">({{ number_format($data['total_general_expense'],2) }})</td>
                        </tr>
                        @foreach($data['general_expenses'] as $expense)
                        <tr>
                            <td style="padding-left: 50px !important;">{{ $expense->type_name }}</td>
                            <td class="text-right">({{ number_format($expense->total_expense,2) }})</td>
                        </tr>
                        @endforeach
                        <tr style="font-weight: bold;">
                            @php($total_profit = $data['total_sale']-$data['raw_material_purchase']-$data['total_production_expense']-$data['total_general_expense'])
                            <td>Net Commulative Profit </td>
                            <td class="text-right">({{ number_format($total_profit,2) }})</td>
                        </tr>
                        </tbody>
                    </table>
                </div>


            </div>
        </div>

    </div>
    </div>


    <div class="dr"><span></span></div>

    </div>

@endsection

@section('page-script')
    <script>

        $(document).ready(function() {

            $('#tSortable_2').DataTable({
                dom: 'lBrtip',
                buttons: [
                    {
                        extend: 'print',
                        text: 'Print page',
                        autoPrint: true,
                        exportOptions: {
                            columns: '1,2',
                        },
                        customize: function ( win ) {
                            $(win.document.body).find('.dataTable')
                                .after(
                                    $('#bank_result,#asset_result,#all_total')
                                );
                            $(win.document.body).find('h1').css('text-align','center');
                        },
                    }
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],



            } );
        } );
    </script>
@endsection



