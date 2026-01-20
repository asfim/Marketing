@extends('admin.layouts.master')
@section('title', 'View Cash Statement')
@section('breadcrumb', 'View Cash Statement')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">

                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>
                            Cash Statement
                            <span class="src-info">{{ (request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}</span>
                        </h1>
                    </div>
                    <div class="col-md-7 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="col-md-6">
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
                            <th>Cheque / Receipt No</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            @if($user->branchId == '')
                                <th>Branch</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <?php $total_balance = 0;$total_debit = 0; $total_credit = 0; ?>
                        @foreach ($cash_statements as $statement)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $statement->transaction_id }}</td>
                                <td>{{ date('d-M-Y', strtotime($statement->ref_date)) }}</td>
                                <td>{{ $statement->receipt_no }}</td>
                                <td>{{ $statement->description }}</td>
                                <td>{{ number_format($statement->debit,2) }}</td>
                                <td>{{ number_format($statement->credit,2) }}</td>
                                @if($user->branchId == '')
                                    <td>{{ $statement->branch->name??'-' }}</td>
                                @endif
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
                            <td class="text-right"><b>Total:</b></td>
                            <td><b>{{ number_format($total_debit,2) }}</b></td>
                            <td><b>{{ number_format($total_credit,2) }}</b></td>
                            @if($user->branchId == '')
                                <td></td>
                            @endif
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

@endsection

