@extends('admin.layouts.master')
@section('title', 'Quick Reports')
@section('breadcrumb', 'Quick Reports')
@section('content')
    <div class="workplace">

        <div class="row report_area">

            <div class="col-md-4">
                <div class="head clearfix">
                    <div class="isw-archive"></div>
                    <h1>Purchase</h1>
                    <ul class="buttons">
                        <li>
                            <a href="#" class="isw-settings"></a>
                            <ul class="dd-list">
                                <li><a href="{{URL::to('/view-products')}}"><span class="isw-list"></span> Show all</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="block-fluid accordion">

                    <?php
                    foreach($html_purchase_list as $list)
                    {
                        echo html_entity_decode($list);
                    }
                    ?>

                </div>
            </div>
            <!------------ end 1st ---------->

            <div class="col-md-4">
                <div class="head clearfix">
                    <div class="isw-archive"></div>
                    <h1>Sell</h1>
                    <ul class="buttons">
                        <li>
                            <a href="#" class="isw-settings"></a>
                            <ul class="dd-list">
                                <li><a href="{{ route('customer.challan.index') }}"><span class="isw-list"></span> Show all</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="block-fluid accordion">

                    <?php
                    foreach($html_sales_list as $list) {
                        echo html_entity_decode($list);
                    }
                    ?>

                </div>
            </div>

            <!----------end 2nd ----------->



            <div class="col-md-4">
                <div class="head clearfix">
                    <div class="isw-cloud"></div>
                    <h1>Products Stock </h1>
                    <ul class="buttons">
                        <li>
                            <a href="#" class="isw-settings"></a>
                            <ul class="dd-list">
                                <li><a href="{{ route('product.stock') }}"><span class="isw-list"></span> Show all</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="block users scrollBox">

                    <div class="scroll" style="height: 270px;">

                        <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
                            <thead>
                            <tr>
                                <th>Material <Na></Na>me</th>
                                <th>Qty</th>
                                <th>Unit</th>
                            </tr>
                            </thead>
                            <tbody>

                            @forelse($product_stocks as $stock)
                                <tr>
                                    <td>{{ $stock->product_name->name }}</td>
                                    <td>{{ round($stock->quantity,3) }}</td>
                                    <td>{{ $stock->unit_type }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="text-align: center"> No Data Available Here!</td></tr>
                            @endforelse
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>

        </div>

    </div>


    <div class="dr"><span></span></div>

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



