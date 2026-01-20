@extends('admin.layouts.master')
@section('title', 'Withdraw Cash Amount')
@section('breadcrumb', 'Withdraw Cash Amount')
<?php $user_data  = Auth::user();?>
@section('content')
    <div class="workplace">
        <div class="row">
        {{--@if($user_data->hasRole(['super-admin', 'admin']))--}}
            <div class="col-md-9">
                <div class="head clearfix">
                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>Cash Withdraw List -
                            <span class="src-info">
                                @if(request()->filled('date_range'))
                                    {{request()->get('date_range')}}
                                @else
                                    Last 30 days
                                @endif
                            </span>
                        </h1>
                    </div>
                    <div class="col-md-7 search_box" style="margin-top: 4px;">
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

                    <table class="table table-responsive table-striped" id="datatable" cellpadding="0" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th>Date</th>
                            <th>Trx Id</th>
                            <th>Description</th>
{{--                            <th>Debit</th>--}}
                            <th>Withdraw Cash</th>
                            <th>Branch</th>
                            <th class="hidden-print">Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php $total_dr = 0; $total_cr = 0; ?>
                        @foreach ($with_cash as $cash)
                            @if ($cash->credit > 0 && isset($cash->branch->name)) {{-- Exclude rows with credit 0 and missing branch --}}
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ date('d-M-y', strtotime($cash->posting_date)) }}</td>
                                <td>{{ $cash->transaction_id }}</td>
                                <td>{{ $cash->description }}</td>
{{--                                <td>{{ number_format($cash->debit, 2) }}</td>--}}
                                <td>{{ number_format($cash->credit, 2) }}</td>
                                <td>{{ $cash->branch->name }}</td>
                                <td class="hidden-print">
                                    @if ($user_data->hasRole('super-admin') || $user_data->can('delete-cash'))
                                        <a href="{{ route('cash.delete', $cash->transaction_id) }}"
                                           onclick='return confirm("Are you sure you want to delete?");'
                                           class="fa fa-trash"></a>
                                    @endif
                                </td>
                            </tr>
                                <?php $total_dr += $cash->debit; $total_cr += $cash->credit; ?>
                            @endif
                        @endforeach
                        </tbody>

                        <tfoot>
                        <tr>
                            <td></td>
                            <td colspan="3" style="text-align: right"><b>Total =</b></td>
{{--                            <td><b>{{ 'BDT ' . number_format($total_dr, 2) }}</b></td>--}}
                            <td><b>{{ 'BDT ' . number_format($total_cr, 2) }}</b></td>
                            <td colspan="2"></td>
                        </tr>
                        </tfoot>
                    </table>


                </div>
            </div>
        {{--@endif--}}

        @if($user_data->hasRole('super-admin') || $user_data->can('withdraw-cash'))
        <div class="col-md-3">
            <div class="head clearfix">
                <div class="isw-documents"></div>
                <h1>Withdraw Cash</h1>
            </div>
            <div class="block-fluid">
                <form action="{{ route('cash.withdraw.store') }}" method="post" class="form-horizontal">
                    {{csrf_field()}}
                    <div class="row-form clearfix">
                        <div class="col-md-12">
                            <label>Branch{{ ($user_data->branchId == '')?'*':'' }}</label>
                            <select class="form-control" name="branchId" id="branchId"
                                {{ ($user_data->branchId == '')?'required':'' }}>
                                <option value="">----- {{ ($user_data->branchId == '')?'Select Branch':'CCL Head Office' }} -----</option>

                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ (collect(old('branchId'))->contains($branch->id)) ? 'selected':'' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Amount*</label>
                            <input type="text" value="{{ old('amount') }}" name="amount" class="form-control" id="credit" required />
                        </div>
                        <div class="col-md-6">
                            <label>Date*</label>
                            <input type="text" value="{{ old('posting_date')??date('Y-m-d') }}" name="posting_date" class="form-control datepicker" id="posting_date" required/>
                        </div>
                        <div class="col-md-12">
                            <label>Receipt No</label>
                            <input type="text" value="{{ old('receipt_no') }}" name="receipt_no" class="form-control" id="receipt_no"/>
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Withdraw Cash</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        @endif


        </div>
    </div>

@endsection
@section('page-script')

    <script>
        jQuery(document).ready(function($){
            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'branches'])!!}',
                minLenght:1,
                autoFocus:true,

            });

        });
    </script>
@endsection
