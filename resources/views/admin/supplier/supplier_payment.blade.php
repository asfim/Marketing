@extends('admin.layouts.master')
@section('title', 'Supplier Payment')
@section('breadcrumb', 'Supplier Payment')
<?php $user = Auth::user(); ?>
@section('content')
<div class="workplace">

    <div class="row">
        <div class="col-md-10">
            <div class="head clearfix">
                <div class="col-md-4">
                    <div class="isw-documents"></div>
                    <h1>Pay to a Supplier</h1>
                </div>
            </div>
            <div class="block-fluid clearfix">
                <form action="{{ route('supplier.payment.store') }}" method="post" enctype="multipart/form-data" id="supplier_payment_form" class="form-horizontal">
                    {{csrf_field()}}
                    <div class="row-form clearfix">
                        <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}" />
                        <div class="col-md-3">
                            <label>Payment Voucher No</label>
                            <input type="text" readonly value="{{ $voucher_no }}" name="voucher_no" id="voucher_no" required class="form-control"/>
                        </div>
                        <div class="col-md-4">
                            <label>Select a supplier*</label>
                            <select name="supplier_id" id="supplier_id" required class="form-control select2"
                                    {{ request('supplier_id')!=''?'disabled':'' }}>
                                <option value="">---- Select Supplier ----</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}"
                                            {{ old('supplier_id')==$supplier->id?'selected':'' }}
                                            {{ request('supplier_id')==$supplier->id?'selected':'' }}
                                    >{{$supplier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <h4 id="balance" style="display: none; font-weight: bold;"></h4>
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
                        <label>Amount to pay*</label>
                        <input type="text" value="{{ old('paid_amount') }}" required name="paid_amount" id="paid_amount" class="form-control"/>
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
                        <textarea name="description" id="description" class="form-control" rows="5"></textarea>
                    </div>

                    <div class="col-md-12">
                        <div class="footer" style="text-align: center;">
                            <button type="submit" class="btn btn-primary">Save Payment</button>
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
                    $("#bank_id").prop('disabled', false);
                } else {
                    bank_info.style.display = "none";
                }

            }).trigger('change');
        });

        //load balance supplier wise
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#supplier_id").on('change', function(){
                //$(this).preventDefault();
                //$(this).off("click").attr('href', "javascript: void(0);");
                var supplier_id = $('#supplier_id').val();
                $.ajax({
                    type:"POST",
                    url:"{{ route('supplier.balance')  }} ",
                    data:{
                        _token: CSRF_TOKEN,
                        supplier_id:supplier_id
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
