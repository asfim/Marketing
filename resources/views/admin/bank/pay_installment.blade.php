@extends('admin.layouts.master')
@section('title', 'Pay Installment')
@section('breadcrumb', 'Pay Installment')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Pay Bank Installment</h1>

                    @if($user->hasRole('super-admin') || $userauth->can('show-installment-payments'))
                        <ul class="buttons mini-nav">
                            <li class="tipb" data-original-title="Installment Payment List" style="cursor: pointer;">
                                <a href="{{ route('bank.installment.payment',$installment_info->id) }}" class="isw-list"></a>
                            </li>
                        </ul>
                    @endif
                </div>
                <div class="block-fluid">
                    <div class="row-form clearfix">
                        <div class="col-md-4"><h5 class="color-h5 bold">Installment Name:</h5> {{$installment_info->installment_name}}</div>
                        <div class="col-md-4"><h5 class="color-h5 bold">Bank Name:</h5> {{$bank_info->bank_name}} </div>
                        <div class="col-md-2"><h5 class="color-h5 bold">Loan Remain:</h5> {{$installment_info->total_loan - $installment_info->total_loan_paid}} </div>
                        <div class="col-md-2"><h5 class="color-h5 bold">Installment Remain:</h5> {{$installment_info->installment_number - $installment_info->installment_paid}} </div>
                    </div>

                    <form action="{{ route('bank.installment.payment.store') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="bank_id" id="bank_id" value="{{ $installment_info->bank_id }}" />
                        <input type="hidden" name="installment_info_id" id="installment_info_id" value="{{ $installment_info->id }}" />
                        <div class="row-form clearfix">
                            <div class="col-md-4">
                                <label>Amount to pay</label>
                                <input type="text" value="{{$installment_info->monthly_amount}}" name="monthly_amount" id="monthly_amount" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Installment Number</label>
                                <input type="number" name="installment_paid" class="form-control" id="installment_paid" required placeholder="How many installment you want to pay?"/>
                            </div>
                            <div class="col-md-4">
                                <label>Total Amount</label>
                                <input type="text" value="" name="paid_amount" id="paid_amount" class="form-control" required readonly/>
                            </div>
                            <div class="col-md-12">
                                @if($user->branchId == '')
                                    <div class="col-md-3">
                                        <label>Payment Mode</label>
                                        <select name="payment_mode" id="payment_mode" class="form-control" required>
                                            <option value="">choose a option...</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="payment_mode" value="Cash"/>
                                @endif
                                <div class="col-md-3" id="bank_info" style="display:none;">
                                    <label>Select a bank</label>
                                    <select name="bank_id" id="bank_id" class="form-control">
                                        <option value="">choose a option...</option>
                                        @foreach($all_banks as $bank)
                                            <option value="{{$bank->id}}">{{$bank->bank_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Cheque/Receipt no</label>
                                    <input type="text" value="" name="cheque_no" class="form-control" id="cheque_no"/>
                                </div>
                                <div class="col-md-3">
                                    <label>Cheque/Receipt Date</label>
                                    <input type="text" value="" name="cheque_date" class="form-control datepicker" id="cheque_date"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Description</label>
                                <textarea placeholder="Mention Month name and other details" name="description" id="description" class="form-control"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label>File Upload</label><br>
                                <input type="file" name="file[]" id="file" class="form-control" multiple />
                            </div>

                            <div class="col-md-12">
                                <div class="footer" style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">Pay Installment</button>
                                    <a href="{{ route('bank.installment.index') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

@endsection

@section('page-script')
    <script>
        jQuery(document).ready(function($) {
            var $state = $('#payment_mode');
            var bank_info = document.getElementById("bank_info");
            $state.change(function () {
                //alert('Chenged');
                if ($state.val() == 'Bank') {
                    bank_info.style.display = "block";
                } else {
                    bank_info.style.display = "none";
                }
            }).trigger('change');

            //total loan
            $("#installment_paid,#monthly_amount").bind('keyup',function(){
                var i_number = $("#installment_paid").val();
                var m_amnt = $("#monthly_amount").val();

                var total_loan = parseFloat(i_number) * parseFloat(m_amnt);
                $("#paid_amount").val(total_loan);
            });
        });
    </script>
@endsection





