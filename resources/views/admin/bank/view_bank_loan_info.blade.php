@extends('admin.layouts.master')
@section('title', 'View Bank Installment Info')
@section('breadcrumb', 'View Bank Installment info')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('add-bank-installment-info')))
        <li><a href="{{ route('bank.installment.create') }}"><span class="glyphicon glyphicon-plus"></span> Add Bank Loan Info</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>
                            Bank Loan List
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

                    {{--@if($user->can('add-bank-installment-info'))--}}
                        {{--<ul class="buttons mini-nav">--}}
                            {{--<li class="tipb" data-original-title="Add Bank Installment" style="cursor: pointer;">--}}
                                {{--<a href="{{ route('bank.installment.create') }}" class="isw-plus"></a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--@endif--}}

                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Bank Name</th>
                            <th>Installment Remain</th>
                            <th>Loan Due</th>
                            <th>Total Paid</th>
                            <th>Status</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i= 1; $gtotal=0; ?>
                        @foreach($installments as $installment)
                            <?php $payable_install = \App\Models\BankInstallmentLog::where('installment_info_id', $installment->id)->sum('paid_amount'); ?>
                            <tr>
                                <td>{{ $installment->installment_name }}</td>
                                <td>{{ date('d-M-y', strtotime($installment["start_date"])) }}</td>
                                <td>{{ date('d-M-y', strtotime($installment["end_date"])) }}</td>
                                <td>{{ $installment->bank_info->bank_name }}</td>
                                <td>{{ $installment->installment_number - $installment->installment_paid }}</td>
                                <td>{{ $installment->total_loan - $installment->total_loan_paid }}</td>
                                <td>{{ $payable_install }}</td>
                                <td>
                                    @if($installment->status == '1') Active
                                    @elseif($installment->status == '2') Inactive @else Ended @endif
                                </td>
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('edit-bank-installments'))
                                        <a href="{{ route('bank.installment.edit',$installment->id) }}">
                                            <span class="fa fa-edit"></span>
                                        </a>
                                    @endif
                                        <a role="button" class="view_btn"
                                           data-bank_name="{{ $installment->bank_info->bank_name }}"
                                           data-ac_no="{{ $installment->bank_info->account_no }}"
                                           data-branch_name="{{ $installment->bank_info->branch_name }}"
                                           data-account_type="{{ $installment->bank_info->account_type }}"
                                           data-installment_name="{{ $installment->installment_name }}"
                                           data-total_installment="{{ $installment->installment_number }}"
                                           data-remain_installment="{{ $installment->installment_number-$installment->installment_paid }}"
                                           data-monthly_amount="{{ $installment->monthly_amount }}"
                                           data-interest_rate="{{ $installment->interest_rate }}"
                                           data-start_date="{{ date('d-M-y', strtotime($installment->start_date)) }}"
                                           data-end_date="{{ date('d-M-y', strtotime($installment->end_date)) }}"
                                           data-total_loan="{{ $installment->total_loan }}"
                                           data-due_loan="{{ $installment->total_loan - $installment->total_loan_paid }}"
                                           data-status="{{ $installment->status }}"
                                           data-description="{{ $installment->description }}"
                                           data-toggle="modal"
                                           data-target="#detailsModal">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                    @if($installment["status"] == '3')
                                        <a href="#" class="fa fa-trash"></a>
                                    @endif

                                    @if($user->hasRole('super-admin') || $user->can('show-installment-payments'))
                                        <a href="{{ route('bank.installment.payment',$installment->id) }}" title="Show Installment Payments">
                                            <span class="fa fa-file-invoice"></span>
                                        </a>
                                    @endif

                                    @if($user->hasRole('super-admin') || $user->can('pay-installments'))
                                        <a href="{{ route('bank.installment.payment.create',$installment->id) }}" title="Pay Installment">
                                            <span class="fab fa-product-hunt" style="font-size: 18px;"></span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            <?php $i++; $gtotal += $payable_install; ?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr style="background-color:#999999; color: #fff;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total =</b></td>
                            <td><b>{{ 'BDT '. number_format($gtotal,2) }}</b></td>
                            <td></td>
                            <td class="hidden-print"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>

        <div class="dr"><span></span></div>
    </div>

    <!-- Details modal form -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Details Installment Info</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            <div class="row-form clearfix">
                                <div class="col-md-4">
                                    <label>Bank Name:</label>
                                    <div id="bank_name"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>A/C No: </label>
                                    <div id="account_no"></div>
                                </div>
                                <div class="col-md-4">
                                    <label>Branch: </label>
                                    <div id="branch_name"></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Type: </label>
                                    <div id="account_type"></div>
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Installment Name: </label>
                                <div class="col-md-6" id="installment_name"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Total Installment Number : </label>
                                <div class="col-md-3" id="total_installment"></div>
                                <label class="col-md-3">Installment Number Remain : </label>
                                <div class="col-md-3" id="remain_installment"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Monthly Amount : </label>
                                <div class="col-md-6" id="monthly_amount"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Interest Rate : </label>
                                <div class="col-md-6" id="interest_rate"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Start Date :</label>
                                <div class="col-md-3" id="start_date"></div>
                                <label class="col-md-3">End Date :</label>
                                <div class="col-md-3" id="end_date"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Total Loan :</label>
                                <div class="col-md-3" id="total_loan"></div>
                                <label class="col-md-3">Due Loan :</label>
                                <div class="col-md-3" id="due_loan"></div>
                            </div>

                            <div class="row-form clearfix">
                                <label class="col-md-3">Status</label>
                                <div class="col-md-6" id="status"></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Description</label>
                                <div class="col-md-6" id="description"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        jQuery(document).ready(function($) {

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'bank_ins'])!!}',
                minLenght:1,
                autoFocus:true,

            });

            $(document).on('click','.view_btn', function(){
                $('#bank_name').html($(this).data('bank_name'));
                $('#account_no').html($(this).data('account_no'));
                $('#branch_name').html($(this).data('branch_name'));
                $('#account_type').html($(this).data('account_type'));
                $('#installment_name').html($(this).data('installment_name'));
                $('#total_installment').html($(this).data('total_installment'));
                $('#remain_installment').html($(this).data('remain_installment'));
                $('#monthly_amount').html($(this).data('monthly_amount'));
                $('#interest_rate').html($(this).data('interest_rate'));
                $('#start_date').html($(this).data('start_date'));
                $('#end_date').html($(this).data('end_date'));
                $('#total_loan').html($(this).data('total_loan'));
                $('#due_loan').html($(this).data('due_loan'));
                $('#status').html($(this).data('status'));
                $('#description').html($(this).data('description'));

            });
        });
    </script>
@endsection
