@extends('admin.layouts.master')
@section('title', 'View Bank Statement')
@section('breadcrumb', 'View Bank Statement')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">

                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>
                            Bank Statement
                            <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}</span>
                        </h1>
                    </div>
                    <div class="col-md-7 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="col-md-6">
                                @if($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('bank-list')))
                                    <select name="branchId" id="branchId" class="form-control">
                                        <option value="">All Bank</option>
                                        @foreach ($banks as $bank){
                                        <option value="{{ $bank->id }}" {{ request('bank_id')==$bank->id?'selected':'' }}>{{ $bank->bank_name }}</option>
                                        @endforeach
                                    </select>
                                @endif
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
                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th>Transaction Id</th>
                            <th>Ref Date</th>
                            <th>Bank</th>
                            <th>Cheque / Receipt No</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $total_balance = 0;$total_debit = 0; $total_credit = 0; ?>
                        @foreach ($bank_statements as $statement)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $statement->transaction_id }}</td>
                                <td>{{ date('d-M-Y', strtotime($statement->ref_date)) }}</td>
                                <td>{{ $statement->bank_info->bank_name }}</td>
                                <td>{{ $statement->receipt_no }}</td>
                                <td>{{ $statement->description }}</td>
                                <td>{{ number_format($statement->debit,2) }}</td>
                                <td>{{ number_format($statement->credit,2) }}</td>
                            </tr>
                            <?php $total_debit += $statement->debit; $total_credit += $statement->credit; ?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><b>Total:</b></td>
                            <td><b>{{ number_format($total_debit,2) }}</b></td>
                            <td><b>{{ number_format($total_credit,2) }}</b></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

@endsection

