@extends('admin.layouts.master')
@section('title', 'View Bills')
@section('breadcrumb', 'View Bills')
<?php $userauth   = Auth::user(); ?>
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">

                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>
                            View Bills
                            <span class="src-info">{{ request('date_range') == '' ? '- Last 30 Days':'- '. request('date_range') }}</span>
                        </h1>
                    </div>

                    <div class="col-md-8 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-6">
                                    <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}" class="form-control" placeholder="Enter Search Text" />
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"
                                               class="form-control" placeholder="Date Range" autocomplete="off"/>
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
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
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
                        <?php $vat_total = 0; $ait_total = 0; $bill_total = 0;$total_eng_tips = 0; $total_cft = 0; $total_cum = 0;?>
                        @foreach ($bills as $bill)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $bill->invoice_no }}</td>
                                <td>{{ date('d-M-y', strtotime($bill->bill_date)) }}</td>
                                <td>{{ $bill->customer->name }}</td>
                                <td>{{ $bill->psi }}</td>
                                <td>{{ $bill->concrete_method }}</td>
                                <td>{{ number_format($bill->total_cuM,2) }}</td>
                                <td>{{ number_format($bill->total_cft,2) }}</td>
                                <td>{{ number_format($bill->eng_tips,2) }}</td>
                                @php($_vat = $bill->total_amount * $bill->vat / 100)
                                @php($_ait = $bill->total_amount * $bill->ait / 100)
                                <td>{{ number_format($_vat,2) }}</td>
                                <td>{{ number_format($_ait,2) }}</td>
                                <td>{{ number_format($bill->total_amount,2) }}</td>
                                <td>{{ $bill->description }}</td>
                                <td class="hidden-print">
                                    @if($userauth->hasRole('super-admin') || $userauth->can('bill-delete'))
                                        <a href="{{ route('customer.bill.delete',$bill->id) }}" onclick='return confirm("Are you sure you want to delete?");' title="Delete" class="fa fa-trash"></a>
                                    @endif
                                    @if($userauth->hasRole('super-admin') || $userauth->can('bill-details'))
                                       <a href="{{ route('customer.bill.details',$bill->invoice_no) }}" title="Bill Details" target="_blank" class="fa fa-eye"></a>
                                    @endif
                                </td>
                            </tr>
                            <?php
                            ///$bill->pump_charge
                            $vat_total += $_vat;
                            $ait_total += $_ait / 100;
                            $bill_total += $bill->total_amount;
                            $total_cft += $bill->total_cft;
                            $total_cum += $bill->total_cuM;
                            $total_eng_tips += $bill->eng_tips;
                            ?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr style="background-color:#999999; color: #fff;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b> Total:</b></td>
                            <td><b>{{ number_format($total_cum,2) }}</b></td>
                            <td><b>{{ number_format($total_cft,2) }}</b></td>
                            <td><b>{{ number_format($total_eng_tips,2) }}</b></td>
                            <td><b>{{ number_format($vat_total,2) }}</b></td>
                            <td><b>{{ number_format($ait_total,2) }}</b></td>
                            <td><b>{{ number_format($bill_total,2) }}</b></td>
                            <td></td>
                            <td class="hidden-print"></td>
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
    <script type="text/javascript">
        $("#search_name").autocomplete({
            source : '{!!URL::route('autoComplete',['table_name' => 'customers'])!!}',
            minLenght:1,
            autoFocus:true,
        });


        $(document).on('click','.bill-edit-btn', function(){
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

    </script>
@endsection

