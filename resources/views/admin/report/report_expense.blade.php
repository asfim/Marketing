@extends('admin.layouts.master')
@section('title', 'Expense Report')
@section('breadcrumb', 'Expense Report')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">

                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>
                            Expense Report
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
                            <th>Expense Name</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Payment Details</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Branch</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_expense=0)
                        @foreach($expenses as $expense)
                        <tr>
                            <td>{{ $expense->transaction_id }}</td>
                            <td>{{ $expense->expense_name }}</td>
                            <td>{{ date('d-M-y',strtotime($expense->date)) }}</td>
                            <td>{{ $expense->expense_type->type_name }}</td>
                            <?php
                                $payment_details = "";
                                if($expense->payment_mode == 'Cash') {
                                    if($expense->cash_statements != "")
                                        $payment_details = 'Cash, Receipt no: '.$expense->cash_statements->receipt_no;
                                }

                                if($expense->payment_mode == 'Bank') {
                                    if($expense->bank_statements != "")
                                    {
                                        $payment_details = "Bank: ".$expense->bank_statements->bank_info->short_name??''.', A/C no: '.$expense->bank_statements->bank_info->account_no.', Check no: '.$expense->bank_statements->cheque_no;
                                    }
                                }
                            ?>
                            <td>{{ $payment_details }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ $expense->amount }}</td>
                            <td>{{ $expense->branch->name??'-' }}</td>
                        </tr>
                            @php($total_expense+=$expense->amount)
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
                            <td><b>{{ number_format($total_expense,2) }}</b></td>
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
                source : '{!!URL::route('autoComplete',['table_name' => 'expense_type'])!!}',
                minLenght:1,
                autoFocus:true,
            });
        } );
    </script>
@endsection