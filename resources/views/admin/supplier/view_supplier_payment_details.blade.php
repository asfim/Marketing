@extends('admin.layouts.master')
@section('title', 'View Supplier Payment Details')
@section('breadcrumb', 'View Supplier Payment Details')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>
                            Supplier Payment Details
                            {{-- @if(request()->filled('date_range')) --}}

                                <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}</span>
                            {{-- @endif --}}
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
                    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-responsive" id="datatable">
                        <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th>Tran Id</th>
                            <th>Supplier Name</th>
                            <th>Ref date</th>
                            <th>Description</th>
                            <th>Payment Mode</th>
                            <th>Branch Name</th>
                            <th class="text-right">Paid Amount</th>
                            <th class="text-right">Adjustment</th>
                            <th>Files</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $total = 0;$ad_total = 0;?>
                        @foreach($sup_payments as $payment)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $payment->transaction_id }}</td>
                                <td>{{ $payment->supplier->name }}</td>
                                <td>{{ date('d-M-y',strtotime($payment->ref_date)) }}</td>
                                <td>{{$payment->description}}</td>
                                <td>
                                    @if($payment->payment_mode == 'Bank')
                                        Bank: {{$payment->bank_info->short_name }},
                                        A/C no: {{ $payment->bank_info->account_no }}, Cheque no: {{ $payment->cheque_no }}
                                    @else {{'Cash'}}
                                    @endif
                                </td>
                                <td>{{ $payment->branch->name??'-' }}</td>
                                <td class="text-right">{{ number_format($payment->paid_amount,2) }}</td>
                                <td class="text-right">{{ number_format($payment->adjustment_amount,2) }}</td>
                                <td>
                                    <?php $file_text = $payment->file;$files = explode(",", $file_text); ?>
                                    @foreach ($files as $file)
                                        <a href="{{URL::to('/img/files/expense_files/supplier_payment/'.$file)}}" target="_blank" rel="tag"><?php echo $file;?></a><br>
                                    @endforeach
                                </td>
                                <td class="hidden-print">
                                    @if($user->branchId == "")
                                        @if($user->hasRole('super-admin') || $user->can('supplier-payment-details'))
                                        <a role="button" class="view_btn"
                                                 data-supplier_name="{{ $payment->supplier->name }}"
                                                 data-trx_id="{{ $payment->transaction_id }}"
                                                 data-voucher_no="{{ $payment->voucher_no }}"
                                                 data-payment_date="{{ date('d-M-y',strtotime($payment->payment_date)) }}"
                                                 data-payment_mode="{{ $payment->payment_mode }}"
                                                 data-bank_name="{{ $payment->bank_info->short_name??'-' }}"
                                                 data-cheque_no="{{ $payment->cheque_no??'' }}"
                                                 data-cheque_date="{{ date('d-M-y',strtotime($payment->ref_date)) }}"
                                                 data-payment_amount="{{ $payment->paid_amount }}"
                                                 data-adjustment_amount="{{ $payment->adjustment_amount }}"
                                                 data-description="{{ $payment->description }}"
                                                 data-toggle="modal"
                                                 data-target="#detailsModal">
                                                <span class="fa fa-eye"></span>
                                            </a>
                                        @endif

                                        @if($user->hasRole('super-admin') || $user->can('supplier-payment-delete'))
                                            <a href="{{ route('supplier.payment.delete', $payment->transaction_id) }}"
                                               onclick="return confirm('Are you sure you want to delete?')"><span class="fa fa-trash"></span></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <?php $total += $payment->paid_amount; $ad_total += $payment->adjustment_amount;?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><b>Total = </b></td>
                            <td class="text-right"><b>{{ number_format($total,2) }}</b></td>
                            <td class="text-right"><b>{{ number_format($ad_total,2) }}</b></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>

        <div class="dr"><span></span></div>

    </div>


    <!-- Bootrstrap modal form -->
        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4>Supplier Payment Details</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                <div class="row-form clearfix">
                                    <div class="col-md-3">Supplier Name: </div>
                                    <div class="col-md-6" id="supplier_name"></div>
                                </div>

                                <div class="row-form clearfix">
                                    <div class="col-md-3">Transaction Id: </div>
                                    <div class="col-md-6" id="trx_id"></div>
                                </div>
                                <div class="row-form clearfix">
                                    <div class="col-md-3">Voucher No: </div>
                                    <div class="col-md-6" id="voucher_no"></div>
                                </div>
                                <div class="row-form clearfix">
                                    <div class="col-md-3">Payment Date : </div>
                                    <div class="col-md-6" id="payment_date">
                                    </div>
                                </div>
                                <div class="row-form clearfix">
                                    <div class="col-md-3">Payment Mode : </div>
                                    <div class="col-md-6" id="payment_mode"></div>
                                </div>
                                <div class="row-form clearfix">
                                    <div class="col-md-3">Bank Name : </div>
                                    <div class="col-md-6" id="bank_name"></div>
                                </div>
                                <div class="row-form clearfix">
                                    <div class="col-md-3">Cheque No : </div>
                                    <div class="col-md-6" id="cheque_no"></div>
                                </div>
                                <div class="row-form clearfix">
                                    <div class="col-md-3">Cheque Date : </div>
                                    <div class="col-md-6" id="cheque_date"></div>
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
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function(){
            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'suppliers'])!!}',
                minLenght:1,
                autoFocus:true,

            });

            $(document).on('click','.view_btn', function(){
                $('#supplier_name').html($(this).data('supplier_name'));
                $('#trx_id').html($(this).data('trx_id'));
                $('#voucher_no').html($(this).data('voucher_no'));
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
