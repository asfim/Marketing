@extends('admin.layouts.master')
@section('title', 'Over Head Report')
@section('breadcrumb', 'Over Head Report')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h3>Over Head Report</h3>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Amount</th>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Total Expense(Gen Ex without Eng Tips + Rent + Installment)</td>
                            <td>{{round($total_expense,3)}}</td>
                        </tr>
                        <tr style="border-bottom: 3px solid black;">
                            <td>Total Asset Depreciation (+)</td>
                            <td>{{round($total_asset_depreciation,3)}}</td>
                        </tr>
                        <tr>
                            <td><b>Total =</b></td>
                            <td>{{round($total_expense + $total_asset_depreciation,3)}}</td>
                        </tr>
                        <tr style="border-bottom: 3px solid black;">
                            <td>Total Sell Qty CFT (/)</td>
                            <td>{{round($total_sell_qty,3)}}</td>
                        </tr>
                        <tr>
                            <td><b>Over Head Cost =</b> </td>
                            <td><b>{{'BDT '.round($total_over_head_cost,3)}}</b></td>
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


