@extends('admin.layouts.master')
@section('title', 'Edit Bank Installment Info')
@section('breadcrumb', 'Edit Bank Installment Info')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Edit Bank Installment Info</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('bank.installment.update') }}" method="post" enctype="multipart/form-data" id="bank_installment-info_form" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id" value="{{$installment->id}}" />
                        <div class="row-form clearfix">
                            <div class="col-md-4">
                                <label>Bank Name</label>
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="">----- choose a option -----</option>
                                    @foreach($banks as $bank)
                                    <option value="{{$bank->id}}" {{ $installment->bank_id == $bank->id? 'selected':'' }}>{{$bank->bank_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Status</label>
                                <select name="status" required>
                                    <option value="">choose a option...</option>
                                    <option value="1" {{ ($installment->status == 1)?'selected':'' }}>Active</option>
                                    <option value="1" {{ ($installment->status == 2)?'selected':'' }}>Inactive</option>
                                    <option value="1" {{ ($installment->status == 3)?'selected':'' }}>Ended</option>
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <div class="col-md-4">
                                <label>Installment Name</label>
                                <input type="text" value="{{ $installment->installment_name }}" name="installment_name" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Installment Number</label>
                                <input type="text" value="{{ $installment->installment_number }}" name="installment_number" id="installment_number" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Monthly Installment Amount</label>
                                <input type="text" value="{{ $installment->monthly_amount }}" name="monthly_amount" id="monthly_amount" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Interest Rate</label>
                                <input type="text" value="{{ $installment->interest_rate }}" name="interest_rate" id="interest_rate" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Total Installment / Loan</label>
                                <input type="text" value="{{ $installment->total_loan }}" name="total_loan" id="total_loan" class="form-control" readonly required/>
                            </div>
                            <div class="col-md-2">
                                <label>Installment Start Date</label>
                                <input type="text" value="{{ $installment->start_date }}" name="start_date" class="form-control datepicker" required/>
                            </div>
                            <div class="col-md-2">
                                <label>Installment End Date</label>
                                <input type="text" value="{{ $installment->end_date }}" name="end_date" class="form-control datepicker" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Description</label>
                                <textarea name="description" id="description">{{ $installment->description }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label>Files</label><br>
                                <div class="uploader" id="uniform-photo">
                                    <input type="file" name="file[]" id="file" multiple class="form-control"/>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="footer" style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ route('bank.installment.index') }}" class="btn btn-danger">Back</a>
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
                if ($state.val() == 'Bank') {
                    bank_info.style.display = "block";
                } else {
                    bank_info.style.display = "none";
                }
            }).trigger('change');

            //load bank amount according to bank
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#bank_id").on('change', function(){
                var bank_id = $('#bank_id').val();
                $.ajax({
                    type:"POST",
                    url:"<?php echo URL::to('/load-bank-info');?>",
                    data:{
                        _token: CSRF_TOKEN,
                        bank_id:bank_id

                    },
                    dataType: 'JSON',
                    success: function(resp){
                        $('#ac_no').html(resp.account_no);
                        $('#ac_name').html(resp.account_name);
                        $('#ac_type').html(resp.account_type);
                        $('#b_name').html(resp.branch_name);
                    }
                });
            });

            //total loan

            $("#total_loan").bind('click',function(){
                var i_number = $("#installment_number").val();
                var m_amnt = $("#monthly_amount").val();

                var total_loan = parseInt(i_number) * parseInt(m_amnt);
                $("#total_loan").val(total_loan);
            });

            $("#installment_number,#monthly_amount").bind('blur',function(){
                var i_number = $("#installment_number").val();
                var m_amnt = $("#monthly_amount").val();
                if(i_number =='' || m_amnt == ''){
                    return false
                }

                var total_loan = parseInt(i_number) * parseInt(m_amnt);
                $("#total_loan").val(total_loan);
            });
        });
    </script>
@endsection
