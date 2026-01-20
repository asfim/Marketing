@extends('admin.layouts.master')
@section('title', 'Customer Payment')
@section('breadcrumb', 'Customer Payment')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-10">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>Received Payment from Customer</h1>
                    </div>
                    {{--<div class="col-md-8 text-right">--}}
                        {{--<strong class="alert alert-info" id="balance" style="display: none; margin-bottom: 0;"></strong>--}}
                    {{--</div>--}}
                </div>
                <div class="block-fluid clearfix">
                    <form action="{{ route('customer.payment.store') }}" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Select a Customer*</label>
                                <select name="customer_id" id="customer_id" class="form-control select2" required>
                                    <option value="">choose a customer...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{$customer->id}}"
                                                {{ old('customer_id')==$customer->id?'selected':'' }}
                                                {{ request('customer')==$customer->id?'selected':'' }}
                                        >{{$customer->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <br><h4 id="balance" style="display: none; font-weight: bold; margin-top: 0;"></h4>
                            </div>
                        </div>

                        <div class="col-md-12" style="margin: 8px 0;">
                            @if($user->branchId == '')
                                <div class="col-md-4">
                                    <label>Payment Mode*</label>
                                    <select class="form-control" name="payment_mode" id="payment_mode" required>
                                        <option value="">choose a option...</option>
                                        <option value="Cash" {{ (collect(old('payment_mode'))->contains('Cash')) ? 'selected':'' }}>Cash</option>
                                        <option value="Bank" {{ (collect(old('payment_mode'))->contains('Bank')) ? 'selected':'' }}>Bank</option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="payment_mode" value="Cash"/>
                            @endif

                            <div class="col-md-4" id="bank_info" style="display: none;">
                                <label>Select Bank*</label>
                                <select class="form-control" name="bank_id" id="bank_id">
                                    <option value="">choose a option...</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ (collect(old('bank_id'))->contains($bank->id)) ? 'selected':'' }}>{{$bank->bank_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Cheque/Receipt no</label>
                                <input class="form-control" type="text" value="{{ old('cheque_no') }}" name="cheque_no" id="cheque_no"/>
                            </div>
                            <div class="col-md-2">
                                <label>Check/Receipt Date</label>
                                <input class="form-control datepicker" type="text" value="{{ old('cheque_date') }}" name="cheque_date" id="cheque_date"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Amount to Receive*</label>
                            <input type="text" value="{{ old('paid_amount') }}" name="paid_amount" id="paid_amount" class="form-control" required/>
                        </div>
                        <div class="col-md-4">
                            <label>Adjustment Amount</label>
                            <input type="text" value="{{ old('adjustment_amount') }}" name="adjustment_amount" id="paid_amount" class="form-control"/>
                        </div>
                        <div class="col-md-4">
                            <label>Files</label><br>
                            <input type="file" name="file[]" id="file" multiple class="form-control"/>
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Receive Payment</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
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
                if ($state.val() == 'Bank') {
                    bank_info.style.display = "block";
                    $("#bank_id").prop('disabled', false);
                } else {
                    bank_info.style.display = "none";
                }
            }).trigger('change');
        });

        //load balance supplier wise
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#customer_id").on('change', function(){
                var customer_id = $('#customer_id').val();
                $.ajax({
                    type:"POST",
                    url:"{{ route('customer.balance') }}?>",
                    data:{
                        _token: CSRF_TOKEN,
                        customer_id:customer_id
                    },
                    dataType: 'JSON',
                    success: function(resp){
                        document.getElementById("balance").style.display = "inline-block";
                        $("#balance").html('Balance: ' + resp.balance);
                    }
                });
            });
        });
    </script>
@endsection

