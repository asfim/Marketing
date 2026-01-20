@extends('admin.layouts.master')
@section('title', 'Add Cash Investment')
@section('breadcrumb', 'Add Cash Investment')
<?php $userauth = Auth::user(); ?>
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-9">
                <div class="head clearfix">
                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>Cash Investment List <span class="src-info">
                                @if(request()->filled('date_range'))
                                    {{request()->get('date_range')}}
                                @else
                                    Last 30 days
                                @endif
                            </span></h1>
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
                            <th>Date</th>
                            <th>Trx Id</th>
                            <th>Description</th>
                            <th>Advance Cash</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1;$total = 0; ?>
                        @foreach ($ad_cash as $cash)
                            <tr>
                                <td>{{ date('d-M-y',strtotime($cash->posting_date)) }}</td>
                                <td>{{ $cash->transaction_id }}</td>
                                <td>{{ $cash->description }}</td>
                                <td>{{ number_format($cash->credit,2) }}</td>
                                <td class="hidden-print">
                                    @if($userauth->hasRole('super-admin') || $userauth->can('delete-cash'))
                                        <a href="{{ route('cash.delete',$cash->transaction_id) }}" onclick='return confirm("Are you sure you want to delete?");' class="fa fa-trash"></a>
                                    @endif
                                </td>
                            </tr>
                            <?php $i++; $total += $cash->credit; ?>
                        @endforeach

                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total = </b></td>
                            <td><b>{{'BDT '. number_format($total,2) }}</b></td>
                            <td class="hidden-print"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($userauth->hasRole('super-admin') || $userauth->can('add-cash'))
            <div class="col-md-3">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Add Cash Investment</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('cash.store') }}" method="post" id="cash_add_form" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Amount*</label>
                                <input type="text" value="{{ old('credit') }}" name="credit" class="form-control" id="credit" required />
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
                                    <button type="submit" class="btn btn-primary">Add Cash</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif
        </div>

        <div class="dr"><span></span></div>

    </div>

@endsection



