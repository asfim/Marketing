@extends('admin.layouts.master')
@section('title', $owner->type.' Rent Payment')
@section('breadcrumb', $owner->type.' Rent Payment')
<?php $user  = Auth::user() ?>
@section('content')

    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Pay Rent to {{$owner->type}}</h1>
                    <div class="col-md-2" style="margin-top: 4px; float: right;">
                        <a href="#rModal" role="button" class="btn btn-default pull-right" data-toggle="modal">Add Renewal Amount</a>
                    </div>
                </div>
                <div class="block-fluid">
                    <div class="row-form clearfix">
                        <div class="col-md-3"><h5 class="color-h5 bold">Name:</h5> {{$owner->name}}</div>
                        <div class="col-md-3"><h5 class="color-h5 bold">Type:</h5> {{$owner->type}} </div>
                        <div class="col-md-3"><h5 class="color-h5 bold">Last Paid Month:</h5> <b id="month"></b> </div>
                        @if($owner->type == "Land Owner")
                            <div class="col-md-3"><h5 class="color-h5 bold">Due:</h5> <b id="due"></b></div>
                        @endif
                    </div>

                    <form action="{{ route('property.rent.pay.store') }}" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="profile_id" required value="{{$owner->id}}" id="profile_id" />
                        <input type="hidden" name="type" required value="{{$owner->type}}" id="owner_type" />
                        <div class="row-form clearfix">
                            <div class="col-md-4">
                                <label>Select a Location*</label>
                                <select name="location_id" id="location_id" class="form-control" required>
                                    <option value="">choose a option...</option>
                                    @foreach($owner_locations as $location)
                                        <option value="{{$location->id}}" {{ old('location_id')==$location->id?'selected':'' }}>{{$location->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Branch</label>
                                @if($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('branch-list')))
                                    <select name="branchId" id="branchId" class="form-control">
                                        <option value="">----- Select Branch -----</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->branchId }}" {{ old('branchId')==$branch->id?'selected':'' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}" />
                                    <input type="text" value="{{ $user->branch->name }}" readonly/>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label>Select Rent Type*</label>
                                <select name="rent_type" id="rent_type" class="form-control" required>
                                    <option value="">choose a option...</option>
                                    <option value="Advance" {{ old('rent_type')=='Advance'?'selected':'' }}>Advance</option>
                                    <option value="Rent" {{ old('rent_type')=='Rent'?'selected':'' }}>Rent</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Select Month*</label>
                                <select name="month" id="rent_month" class="form-control" required>
                                    <option value="">choose a option...</option>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Payment Date*</label>
                                <input type="text" value="{{ old('payment_date') }}" name="payment_date" id="payment_date" class="form-control datepicker" readonly/>
                            </div>

                            <div class="col-md-4">
                                <label>Amount to pay*</label>
                                <input type="text" value="{{ old('paid_amount') }}" name="paid_amount" class="form-control" id="paid_amount"/>
                            </div>

                            <div class="col-md-12">
                                @if($user->branchId == '')
                                    <div class="col-md-4">
                                        <label>Payment Mode*</label>
                                        <select name="payment_mode" id="payment_mode" class="form-control" required>
                                            <option value="">choose a option...</option>
                                            <option value="Cash" {{ old('payment_mode')=='Cash'?'selected':'' }}>Cash</option>
                                            <option value="Bank" {{ old('payment_mode')=='Bank'?'selected':'' }}>Bank</option>
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="payment_mode" value="Cash"/>
                                @endif
                                <div class="col-md-3" id="bank_info" style="display: none;">
                                    <label>Select Bank*</label>
                                    <select name="bank_id" id="bank_id" class="form-control">
                                        <option value="">choose a option...</option>
                                        @foreach($banks as $bank)
                                            <option value="{{$bank->id}}" {{ old('bank_id')==$bank->id?'selected':'' }}>{{$bank->bank_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Cheque No</label>
                                    <input type="text" value="{{ old('cheque_no') }}" name="cheque_no" class="form-control" id="cheque_no"/>
                                </div>
                                <div class="col-md-2">
                                    <label>Cheque/Receipt Date</label>
                                    <input type="text" value="{{ old('cheque_date') }}" name="cheque_date" class="form-control datepicker" id="cheque_date"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <div class="footer" style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">Pay Rent</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
                            </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

    <!--renewal model -->
    <div class="modal fade" id="rModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Land Rent Info</h4>
                </div>
                <form action="{{ route('property.rent.update') }}" method="post" enctype="multipart/form-data" id="land_rent_edit_form" class="form-horizontal">
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            {{csrf_field()}}
                            <input type="hidden" name="type_of_owner" value="{{$owner->type}}" id="type_of_owner"/>
                            <input type="hidden" name="prof_id" value="{{$owner->id}}" id="prof_id"/>
                            <input type="hidden" name="l_id" value="" id="l_id"/>
                            <div class="col-md-12"><h5>Please select a location first for update the amount.</h5></div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Due Amount: </div>
                                <div class="col-md-6">
                                    <input type="text" value="" required readonly name="due_amount" id="due_amount"/>
                                </div>
                            </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Renewal Amount: </div>
                                <div class="col-md-6">
                                    <input type="text" value="" required name="renewal_amount" id="renewal_amount"/>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div clas="col-md-6" id="er_div" style="display:none;margin-left: 15px;">
                        <span id="er_prof_id"></span><br>
                        <span id="er_l_id"></span><br>
                        <span id="er_due"></span><br>
                        <span id="er_amt"></span>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-primary" type="submit" id="btn_update">Save Updates</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
                </form>
            </div>
        </div>
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

            //load rent according to location
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#location_id").on('change', function(){
                var location_id = $('#location_id').val();
                var owner_type  = $('#owner_type').val();
                $('#l_id').val(location_id);
                $.ajax({
                    type:"POST",
                    url:"{{ route('rent.info.load') }}",
                    data:{
                        _token: CSRF_TOKEN,
                        location_id:location_id,
                        owner_type: owner_type

                    },
                    dataType: 'JSON',
                    success: function(resp){
                        if(resp.success != true) {
                            alert('Oop\'s something went wrong, please first added to land rent info!');
                            window.location.reload(true);
                        }else{
                            $('#month').html(resp.month);
                            if(resp.due !== "") {
                                $('#due').html(resp.due);
                                $("#due_amount").val(resp.due);
                            }
                        }

                    }
                });
            });

            //validate renewal
            $("#btn_update").click(function(){
                var due = $("#due_amount").val();
                var l_id = $("#l_id").val();
                var renew_amt = $("#renewal_amount").val();
                var prof_id = $("#prof_id").val();
                if(due === "")
                {
                    $("#er_div").show();
                    $("#er_due").html("Select a location first");
                }
                if(l_id === "")
                {
                    $("#er_div").show();
                    $("#er_l_id").html("Select a location first");
                }
                if(renew_amt === "")
                {
                    $("#er_div").show();
                    $("#er_amt").html("Enter Renew amount");
                }

                if(prof_id === "")
                {
                    $("#er_div").show();
                    $("#er_prof_id").html("Enter Renew amount");
                }
            });

        });
    </script>
@endsection


