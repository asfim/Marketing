@extends('admin.layouts.master')
@section('title', 'Add Bank Loan Info')
@section('breadcrumb', 'Add Bank Loan Info')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Add Bank Loan Info</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('bank.installment.store') }}" method="post" enctype="multipart/form-data" id="bank_installment-info_form" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="row-form clearfix">
                            <div class="col-md-4">
                                <label>Bank Name</label>
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="">----- choose a option -----</option>
                                    @foreach($banks as $bank)
                                        <option value="{{$bank->id}}" {{ (collect(old('bank_id'))->contains($bank->id)) ? 'selected':'' }}>{{$bank->bank_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <div class="col-md-3"><h5 class="color-h5 bold">A/C No:</h5> <b id="ac_no"></b></div>
                                <div class="col-md-3"><h5 class="color-h5 bold">A/C Name:</h5> <b id="ac_name"></b></div>
                                <div class="col-md-4"><h5 class="color-h5 bold">Branch Name:</h5> <b id="b_name"></b></div>
                                <div class="col-md-2"><h5 class="color-h5 bold">A/C Type:</h5> <b id="ac_type"></b></div>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <div class="col-md-4">
                                <label>Loan Name</label>
                                <input type="text" value="" name="installment_name" id="installment_name" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Number of Installment</label>
                                <input type="text" value="" name="installment_number" id="installment_number" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Monthly Installment Amount</label>
                                <input type="text" value="" name="monthly_amount" id="monthly_amount" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Interest Rate</label>
                                <input type="text" value="" name="interest_rate" id="interest_rate" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label>Total Loan Amount</label>
                                <input type="text" value="" name="total_loan" id="total_loan" class="form-control" readonly required/>
                            </div>
                            <div class="col-md-2">
                                <label>Installment Start Date</label>
                                <input type="text" value="" name="start_date" id="start_date" class="form-control datepicker" required/>
                            </div>
                            <div class="col-md-2">
                                <label>Installment End Date</label>
                                <input type="text" value="" name="end_date" id="end_date" class="form-control datepicker" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Description</label>
                                <textarea name="description" id="description"></textarea>
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
                                    <button type="reset" class="btn btn-danger">Reset</button>
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
