@extends('admin.layouts.master')
@section('title', 'Withdraw Cash Amount')
@section('breadcrumb', 'Withdraw Cash Amount')
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
@section('content')
    <div class="workplace">

        <div class="dr"><span></span></div>
        <div class="col-md-12">
            <div class="block-fluid table-sorting clearfix">
                <div class="col-md-12">
                    <h5 class="color-h5 bold" style="float: left;"></h5>

                    <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                        <form action="{{ route('searchBlanceTransfer') }}" method="post" enctype="multipart/form-data" id="search_all_form" class="form-horizontal">

                            <div class="" align="right">
                                {{csrf_field()}}
                                <input type="text" name="search_name" id="search_name" placeholder="Enter Search Text" />
                                <input type="date" name="from_date" id="from_date" placeholder="From Date" />
                                <input type="date" name="to_date" id="to_date" placeholder="To Date" />

                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>

                        </form>
                    </div>
                </div>

                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction Id</th>
                        <th>Description</th>
                        <th>Withdraw Cash</th>
                        <th>Branch</th>
                        <th class="hidden-print">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; $total = 0;?>
                    @foreach ($with_cash as $wcash)
                        <tr>
                            <td>{{$wcash["posting_date"]}}</td>
                            <td>{{$wcash["transaction_id"]}}</td>
                            <td>{{$wcash["description"]}}</td>
                            <td>{{$wcash["debit"]}}</td>
                            <td>{{ ($wcash->branches) ? $wcash->branches->branchName : '' }}</td>
                            <td class="hidden-print">
                                @if($userauth->hasRole('super-admin') || $userauth->can('withdraw-cash-delete'))
                                    <a href="{{URL::to('delete-cash-amount/'.$wcash['transaction_id'].'/withdraw')}}" onclick='return confirm("Are you sure you want to delete?");' class="glyphicon glyphicon-remove"></a>
                                @endif
                            </td>
                        </tr>
                        <?php $i++; $total += $wcash->debit; ?>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total = </b></td>
                        <td><b>{{'BDT '.$total}}</b></td>
                        <td></td>
                        <td class="hidden-print"></td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
    </div>


@endsection




