@extends('admin.layouts.master')
@section('title', 'Income Report')
@section('breadcrumb', 'Income Report')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">

                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>
                            Income Report
                            <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}</span>
                        </h1>
                    </div>
                    <div class="col-md-8 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-4">
                                    @if($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('branch-list')))
                                        <select name="branchId" id="branchId" class="form-control">
                                            <option value="">All Branch</option>
                                            <option value="head_office" {{ request('branchId')=='head_office'?'selected':'' }}>** Head Office Only **</option>
                                            @foreach ($branches as $branch){
                                            <option value="{{ $branch->id }}" {{ request('branchId')==$branch->id?'selected':'' }}>{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}" />
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}" class="form-control" placeholder="Enter Search Text" />
                                </div>
                                <div class="col-md-4">
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
                    <h5 id="date_info"></h5>
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Tran Id</th>
                            <th>Income Name</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Payment Details</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Branch</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_income=0)
                        @foreach($incomes as $income)
                            <tr>
                                <td>{{ $income->transaction_id }}</td>
                                <td>{{ $income->income_name }}</td>
                                <td>{{ date('d-M-y',strtotime($income->date)) }}</td>
                                <td>{{ $income->income_type->type_name }}</td>
                                <?php
                                $payment_details = "";
                                if($income->payment_mode == 'Cash') {
                                    if($income->cash_statements != "")
                                        $payment_details = 'Cash, Receipt no: '.$income->cash_statements->receipt_no;
                                }

                                if($income->payment_mode == 'Bank') {
                                    if($income->bank_statements != "")
                                    {
                                        $payment_details = "Bank: ".$income->bank_statements->bank_info->short_name??''.', A/C no: '.$income->bank_statements->bank_info->account_no.', Check no: '.$income->bank_statements->cheque_no;
                                    }
                                }
                                ?>
                                <td>{{ $payment_details }}</td>
                                <td>{{ $income->description }}</td>
                                <td>{{ $income->amount }}</td>
                                <td>{{ $income->branch->name??'-' }}</td>
                            </tr>
                            @php($total_income+=$income->amount)
                        @endforeach

                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total:</b></td>
                            <td><b>{{ number_format($total_income,2) }}</b></td>
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
                source : '{!!URL::route('autoComplete',['table_name' => 'income_type'])!!}',
                minLenght:1,
                autoFocus:true,
            });
        } );
    </script>
@endsection