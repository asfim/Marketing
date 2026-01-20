@extends('admin.layouts.master')
@section('title', 'Add Bank Investment')
@section('breadcrumb', 'Bank Investments')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole('super-admin') || $user->can('add-bank-investment'))
    <li><a data-target="#createModal" data-toggle="modal" ><span class="glyphicon glyphicon-plus"></span> Add New Investment</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">

        <div class="col-md-12">
            <div class="head clearfix">
                <div class="col-md-4">
                    <div class="isw-documents"></div>
                    <h1>Total Bank Balance:
                        <span class="src-info">{{ 'BDT '. number_format($bank_balance,2) }}</span>
                    </h1>
                </div>

                <div class="col-md-7 search_box" style="margin-top: 4px;">
                    <form action="" class="form-horizontal">
                        <div class="" align="right">
                            <div class="col-md-5">
                                <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}" class="form-control" placeholder="Enter Search Text" />
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"
                                           class="form-control" placeholder="Date Range" autocomplete="off"/>
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="block-fluid table-sorting clearfix">
                <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped" id="datatable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transaction Id</th>
                        <th>Bank</th>
                        <th>Description</th>
                        <th>Advance Amount</th>
                        <th class="hidden-print">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $total = 0; ?>
                    @foreach ($bank_investments as $investment)
                        <tr>
                            <td>{{ $investment->posting_date }}</td>
                            <td>{{ $investment->transaction_id }}</td>
                            <td>{{ $investment->bank_info->bank_name }}</td>
                            <td>{{ $investment->description }}</td>
                            <td>{{ number_format($investment->credit,2) }}</td>
                            <td class="hidden-print">
                                @if($user->hasRole('super-admin') || $user->can('delete-bank-investment'))
                                    <a href="{{ route('bank.investment.delete',$investment->transaction_id) }}"
                                       onclick='return confirm("Are you sure you want to delete?");'
                                       class="fa fa-trash"></a>
                                @endif

                            </td>
                        </tr>
                        <?php $total += $investment->credit; ?>
                    @endforeach

                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><b>Total = </b></td>
                        <td><b>{{'BDT '. number_format($total,2) }}</b></td>
                        <td class="hidden-print"></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    @if($user->hasRole('super-admin') || $user->can('add-bank-investment'))
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Add Bank Investment</h4>
                </div>
                <form action="{{ route('bank.investment.store') }}" method="post" id="bank_amnt_add_form" class="form-horizontal">
                <div class="modal-body">
                    {{csrf_field()}}
                    <div class="row-form clearfix">
                        <label class="col-md-4">Select Bank</label>
                        <div class="col-md-8">
                            <select name="bank_id" id="bank_id" class="form-control" required>
                                <option value="">choose a option...</option>
                                @foreach($bank_infos as $bank)
                                    <option value="{{ $bank->id }}" {{ old('bank_id')==$bank->id?'selected':'' }}>{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row-form clearfix">
                        <label class="col-md-4">Cheque/Receipt no</label>
                        <div class="col-md-8">
                            <input type="text" value="{{ old('cheque_no') }}" name="cheque_no" id="cheque_no" class="form-control"/>
                        </div>
                    </div>
                    <div class="row-form clearfix">
                        <label class="col-md-4">Check/Receipt Date</label>
                        <div class="col-md-8">
                            <input type="text" value="{{ old('cheque_date') }}" name="cheque_date" id="cheque_date" class="form-control datepicker"/>
                        </div>
                    </div>
                    <div class="row-form clearfix">
                        <label class="col-md-4">Amount</label>
                        <div class="col-md-8">
                            <input type="text"  value="{{ old('credit') }}" name="credit" id="credit" class="form-control" required/>
                        </div>
                    </div>

                    <div class="row-form clearfix">
                        <label class="col-md-4">Description</label>
                        <div class="col-md-8">
                            <textarea name="description" id="description" class="form-control" required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-primary" type="submit" aria-hidden="true">Save Investment</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('page-script')
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'bank_infos'])!!}',
                minLenght:1,
                autoFocus:true,
            });
        });
    </script>
@endsection





