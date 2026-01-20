@extends('admin.layouts.master')
@section('title', 'View Customer Payment Details')
@section('breadcrumb', 'View Customer Payment Details')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->can('customer-payment'))
        <li><a href="{{ route('customer.payment.create') }}"><span class="glyphicon glyphicon-credit-card"></span>
                Customer Payment</a></li>
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
                            Customer Payment Details
                            {{-- @if(request()->has('date_range')) --}}

                                <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}</span>
                            {{-- @endif --}}
                        </h1>
                    </div>

                    <div class="col-md-7 search_box" style="margin-top: 4px;">
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
                                               value="{{ request('date_range')??'' }}"
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
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th>Trx Id</th>
                            <th>Customer Name</th>
                            <th>Ref Date</th>
                            <th>Description</th>
                            <th>Files</th>
                            <th>Payment Mode</th>
                            <th>Paid Amount</th>
                            <th>Adjustment</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $total = 0;$ad_total = 0; ?>
                        @foreach($cus_payments as $payment)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{$payment->transaction_id}}</td>
                                <td>{{ $payment->customer->name }}</td>
                                <td>{{ date('d-M-y',strtotime($payment->ref_date)) }}</td>
                                <td>{{$payment->description}}</td>
                                <td>
                                        <?php $file_text = $payment->file; $files = explode(",", $file_text); ?>
                                    @foreach ($files as $file)
                                        <a href="{{URL::to('/img/files/income_files/customer_payment/'.$file)}}"
                                           target="_blank" rel="tag">{{ $file }}</a><br>
                                    @endforeach
                                </td>
                                <td>
                                    @if($payment->payment_mode == 'Bank')
                                        {{$payment->bank_info->short_name}}, AC No: {{$payment->bank_info->account_no}},
                                        Cheque No: {{$payment->cheque_no}}
                                    @else
                                        {{$payment->payment_mode}}
                                    @endif
                                </td>
                                <td>{{ number_format($payment->paid_amount,2) }}</td>
                                <td>{{ number_format($payment->adjustment_amount,2) }}</td>

                                <td class="hidden-print">
                                    @if($user->branchId == "")
                                        @if($user->hasRole('super-admin') || $user->can('customer-payment-details'))
                                            <a role="button" class="view_btn"
                                               data-customer_name="{{ $payment->customer->name }}"
                                               data-trx_id="{{ $payment->transaction_id }}"
                                               data-bill_no="{{ $payment->bill_no }}"
                                               data-payment_date="{{ $payment->payment_date }}"
                                               data-payment_mode="{{ $payment->payment_mode }}"
                                               data-bank_name="{{ $payment->bank_info->short_name??'-' }}"
                                               data-cheque_no="{{ $payment->cheque_no??'' }}"
                                               data-cheque_date="{{ $payment->ref_date }}"
                                               data-payment_amount="{{ $payment->paid_amount }}"
                                               data-adjustment_amount="{{ $payment->adjustment_amount }}"
                                               data-description="{{ $payment->description }}"
                                               data-toggle="modal"
                                               data-target="#detailsModal">
                                                <span class="fa fa-eye"></span>
                                            </a>
                                        @endif

                                        @if($user->hasRole('super-admin') || $user->can('customer-payment-delete'))
                                            <a href="{{ route('customer.payment.delete',$payment->transaction_id) }}"
                                               onclick="return confirm('Are you sure you want to delete?')"
                                               class="fa fa-trash"></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                                <?php $total += $payment->paid_amount;$ad_total += $payment->adjustment_amount; ?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr style="background-color:#999999; color: #fff;">
                            <td></td> {{-- For the checkbox column --}}
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total:</b></td>
                            <td><b>{{ number_format($total,2) }}</b></td>
                            <td><b>{{ number_format($ad_total,2) }}</b></td>
                            <td class="hidden-print"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

    <!-- details modal form -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Customer Payment Details</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            {{csrf_field()}}
                            <div class="row-form clearfix">
                                <div class="col-md-3">Customer Name:</div>
                                <div class="col-md-6" id="customer_name"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Transaction Id:</div>
                                <div class="col-md-6" id="trx_id">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Bill No:</div>
                                <div class="col-md-6" id="bill_no">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Payment Date :</div>
                                <div class="col-md-6" id="payment_date">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Payment Mode :</div>
                                <div class="col-md-6" id="payment_mode">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Bank Name :</div>
                                <div class="col-md-6" id="bank_name">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Cheque No :</div>
                                <div class="col-md-6" id="cheque_no">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Cheque Date :</div>
                                <div class="col-md-6" id="cheque_date">
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Amount :</div>
                                <div class="col-md-6" id="payment_amount"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Adjustment Amount :</div>
                                <div class="col-md-6" id="adjustment_amount"></div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Description</div>
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
        jQuery(document).ready(function ($) {
            $("#search_name").autocomplete({
                source: '{!!URL::route('autoComplete',['table_name' => 'customers'])!!}',
                minLenght: 1,
                autoFocus: true,
            });

            $(document).on('click', '.view_btn', function () {
                $('#customer_name').html($(this).data('customer_name'));
                $('#trx_id').html($(this).data('trx_id'));
                $('#bill_no').html($(this).data('bill_no'));
                $('#payment_date').html($(this).data('payment_date'));
                $('#payment_mode').html($(this).data('payment_mode'));
                $('#bank_name').html($(this).data('bank_name'));
                $('#cheque_no').html($(this).data('cheque_no'));
                $('#cheque_date').html($(this).data('cheque_date'));
                $('#payment_amount').html($(this).data('payment_amount'));
                $('#adjustment_amount').html($(this).data('adjustment_amount'));
                $('#description').html($(this).data('description'));

            });
        });
    </script>
@endsection




