@extends('admin.layouts.master')
@section('title', 'View Product Consumption ')
@section('breadcrumb', 'View Product Consumption')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">

                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>View Product Consumption
                            <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}</span>
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
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Unit Type</th>
                            <th>Consumption Qty</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($prod_consump as $prod_cons)
                            <tr>
                                <td>{{$prod_cons->transaction_id}}</td>
                                <td>{{ date('d-M-y',strtotime($prod_cons->consumption_date)) }}</td>
                                <td>{{$prod_cons->product_names->name}}</td>
                                <td>{{$prod_cons->unit_type}}</td>
                                <td>{{ number_format($prod_cons->consumption_qty,2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script type="text/javascript">
        $("#search_name").autocomplete({
            source : '{!!URL::route('autoComplete',['table_name' => 'product_names'])!!}',
            minLenght:1,
            autoFocus:true,
        });
    </script>
@endsection