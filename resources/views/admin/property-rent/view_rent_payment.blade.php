@extends('admin.layouts.master')
@section('title', 'View Rent Payments')
@section('breadcrumb', 'View Rent Payments')
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">

                    <div class="col-md-7">
                        <div class="isw-documents"></div>
                        <h1>
                            {{$owner_info->type}} Payments of:
                            <span class="src-info">{{$owner_info->name}}

                                @if(request()->has('date_range'))

                                    {{ (request('search_text') == '' && request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}
                                @endif

                            </span>
                        </h1>
                    </div>

                    <div class="col-md-5 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-6">
                                    <input type="text" name="search_text" id="search_name"
                                           value="{{ request('search_text')??'' }}" class="form-control"
                                           placeholder="Enter Search Text"/>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range"
                                               value="{{ request('date_range')??'' }}" class="form-control"
                                               placeholder="Date Range"/>
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
                            <th>Tran Id</th>
                            <th>Rent Type</th>
                            <th>Payment Date</th>
                            <th>Month</th>
                            <th>Location</th>
                            <th>Payment Mode</th>
                            <th>Paid Amount</th>
                            <th>Description</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; $total_paid = 0; ?>
                        @foreach($rent_payments as $rent)
                            <tr>
                                <td>{{ $rent->transaction_id }}</td>
                                <td>{{ $rent->rent_type }}</td>
                                <td>{{ date("d-M-y", strtotime($rent->payment_date)) }}</td>
                                <td>{{ $rent->month }}</td>
                                <td>{{ $rent->location->name }}</td>
                                <td>{{ $rent->payment_mode }}</td>
                                <td>{{ number_format($rent->paid_amount,2) }}</td>
                                <td>{{ $rent->description }}</td>
                                @if(auth()->user()->hasRole(['super-admin']))
                                <td class="hidden-print">
                                    <a href="{{ route('property.rent.pay.delete',$rent->transaction_id) }}"
                                       onclick="return confirm('Are you sure you want to delete?')" class="fa fa-trash">
                                    </a>
                                </td>
                                @endif
                            </tr>
                                <?php $i++;$total_paid += $rent->paid_amount; ?>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total =</b></td>
                            <td><b>{{'BDT '. number_format($total_paid,2) }}</b></td>
                            <td></td>
                            <td class="hidden-print"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

@endsection



