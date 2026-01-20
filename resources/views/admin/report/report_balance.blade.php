@extends('admin.layouts.master')
@section('title', 'Balance Report')
@section('breadcrumb', 'Balance Report')
@section('content')
    <div class="workplace">

        <div class="row" id="balance-report">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-8">
                        <div class="isw-documents"></div>
                        <h3>Balance Report -
                            <span class="src-info">Cash + Bank Total Balance = {{'BDT '. number_format($all_total,2) }}</span>
                        </h3>
                    </div>

                    <div class="col-md-4 text-right" style="margin-top: 4px;">
                        <span class="btn btn-sm btn-info hidden-print" onclick="printContent('balance-report')"> <i class="fa fa-print"></i></span>
                    </div>
                </div>
                <div class="col-md-6 block">
                    <h4>Cash Balances</h4>
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="">
                        <thead>
                        <tr>
                            <th>Branch Name</th>
                            <th>Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                        {!!$cash_result!!}
                        {{--@php($branch_totals=0)--}}
                        {{--@foreach ($branch_balances as $balance)--}}
                        {{--<tr>--}}
                            {{--<td>{{ $balance->branch->branchName }}</td>--}}
                            {{--<td>BDT {{ number_format($bal->total_amount,2) }}</td>--}}
                        {{--</tr>--}}
                        {{--@php($branch_totals += $bal->total_amount;)--}}
                        {{--@endforeach--}}
                        </tbody>
                        {{--<tfoot>--}}
                        {{--<tr>--}}
                            {{--<td>Total</td>--}}
                            {{--<td>{{ number_format($branch_totals,2) }}</td>--}}
                        {{--</tr>--}}
                        {{--</tfoot>--}}
                    </table>
                </div>
                <div class="col-md-6 block">
                    <h4>Bank Balances</h4>
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="bank_result">
                        <thead>
                        <tr>
                            <th>Bank Name</th>
                            <th>Balance</th>
                        </tr>
                        </thead>
                        <tbody>

                        {!!$bank_result!!}
                        {{--@php($total_bank_bal=0)--}}
                        {{--@foreach ($bank_infos as $bank)--}}
                            {{--@php($balance = $bank->balance())--}}
                            {{--<tr>--}}
                                {{--<td>{{ $bank->bank_name }}</td>--}}
                                {{--<td>BDT {{ number_format($balance,2) }}</td>--}}
                            {{--</tr>--}}
                            {{--@php($total_bank_bal += $balance)--}}
                        {{--@endforeach--}}
                        </tbody>
                        {{--<tfoot>--}}
                        {{--<tr>--}}
                            {{--<td>Total</td>--}}
                            {{--<td>{{ number_format($total_bank_bal,2) }}</td>--}}
                        {{--</tr>--}}
                        {{--</tfoot>--}}
                    </table>

                </div>
            </div>
            <div class="col-md-12 clearfix">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h3>Asset Value After Depreciation =
                        <span class="src-info">{{ number_format($total_ass_val,2) }}</span>
                    </h3>
                 </div>
                <div class="block-fluid table-sorting  clearfix" >
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Asset Category</th>
                            <th>Value</th>

                        </tr>
                        </thead>
                        <tbody>
                        {!!$asset_result!!}
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

            $('#btn_search').on('click',function(){
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                if(to_date < from_date)
                {
                    alert('This to date is less then from date');
                    return false;
                }
            });
        } );
    </script>
@endsection



