@extends('admin.layouts.master')
@section('title', 'Balance Sheet')
@section('breadcrumb', 'Balance Sheet')
@section('content')
    <div class="workplace">

        <div class="row" id="balance-sheet">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>
                            Balance Sheet
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
                        <span class="btn btn-sm btn-info hidden-print" onclick="printContent('balance-sheet')"> <i class="fa fa-print"></i></span>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" style="font-size: 16px;">
                        <thead>
                        <tr>
                            <th>Particulars</th>
                            <th class="text-right">Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr style="font-weight: bold;">
                            <td colspan="2">Assets</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px !important;">Fixed Assets</td>
                            <td class="text-right">{{ number_format($data['fixed_asset'],2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px !important;">Cash Balance</td>
                            <td class="text-right">{{ number_format($data['cash_balance'],2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px !important;">Bank Balance</td>
                            <td class="text-right">{{ number_format($data['bank_balance'],2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px !important;">Accounts/ Trade Receivable</td>
                            <td class="text-right">{{ number_format($data['ac_receivable'],2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px !important;">Inventories/ Stock</td>
                            <td class="text-right">{{ number_format($data['current_stock_value'],2) }}</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            @php($_total_asset=$data['fixed_asset']+$data['cash_balance']+$data['bank_balance']+$data['ac_receivable']+$data['current_stock_value'])
                            <td>Total Assets</td>
                            <td class="text-right">{{ number_format($_total_asset,2) }}</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td colspan="2">Liabilities</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px !important;">Bank Loan</td>
                            <td class="text-right">{{ number_format($data['total_loan'],2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px !important;">Accounts/ Trade Payable</td>
                            <td class="text-right">{{ number_format($data['ac_payable'],2) }}</td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td>Profit and Loss Balance</td>
                            <td class="text-right">{{ number_format($data['total_profit'],2) }}</td>
                        </tr>
                        @php($_total_liabilities=$data['total_loan']+$data['ac_payable']+$data['total_profit'])
                        <tr style="font-weight: bold;">
                            <td>Total Liabilities</td>
                            <td class="text-right">{{ number_format($_total_liabilities,2) }}</td>
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



