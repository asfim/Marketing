@extends('admin.layouts.master')
@section('title', 'Add Waste Income')
@section('breadcrumb', 'Add Waste Income')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole('super-admin') || $user->can('add-general-income'))
        <li><a href="{{ route('income.general.create') }}"><span class="glyphicon glyphicon-plus"></span> Add General Income</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>Add Waste Income</h1>
                    </div>
                </div>
                <div class="block-fluid clearfix">
                    <form action="{{ route('income.waste.store') }}" method="post" enctype="multipart/form-data" id="gen_income_form" class="form-horizontal">
                        {{csrf_field()}}

                        <div class="col-md-4">
                            <label>Select Income Type*</label>
                            <select name="income_type_id" class="form-control" id="income_type_id" required>
                                <option value="">choose a option...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('income_type_id')==$type->id?'selected':'' }}>{{ $type->type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Date*</label>
                            <input type="text" value="{{ old('date') }}" name="date" class="form-control datepicker" id="date" required/>
                        </div>
                        <div class="col-md-6">
                            <label>Income Name*</label>
                            <input type="text" value="{{ old('income_name') }}" name="income_name" class="form-control" id="income_name"/>
                        </div>
                        <div class="col-md-6">
                            <label>Amount*</label>
                            <input type="text" value="{{ old('amount') }}" name="amount" class="form-control" id="amount" required/>
                        </div>

                        <div class="col-md-12">
                            @if($user->branchId == '')
                                <div class="col-md-3">
                                    <label>Payment Mode*</label>
                                    <select name="payment_mode" class="form-control" id="payment_mode" required>
                                        <option value="">choose a option...</option>
                                        <option value="Cash" {{ old('payment_mode')=='Cash'?'selected':'' }}>Cash</option>
                                        <option value="Bank" {{ old('payment_mode')=='Bank'?'selected':'' }}>Bank</option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="payment_mode" value="Cash"/>
                            @endif
                            <div class="col-md-4" id="bank_info" style="display: none;">
                                <label>Select Bank*</label>
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="">choose a option...</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ old('bank_id')==$bank->id?'selected':'' }}>{{ $bank->bank_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Cheque/Receipt no</label>
                                <input type="text" value="{{ old('cheque_no') }}" name="cheque_no" id="cheque_no" class="form-control"/>
                            </div>
                            <div class="col-md-2">
                                <label>Check/Receipt Date</label>
                                <input type="text" value="{{ old('cheque_date') }}" name="cheque_date" id="cheque_date" class="form-control datepicker"/>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label>Description</label>
                            <textarea name="description" class="form-control" id="description" rows="4">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label>Files</label><br>
                            <input type="file" name="file[]" id="file" multiple />
                        </div>
                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Submit</button>
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
                ///alert('Chenged');
                if ($state.val() == 'Bank') {
                    bank_info.style.display = "block";
                    $("#bank_id").prop('disabled', false);
                }
                else {
                    bank_info.style.display = "none";
                }

            }).trigger('change');


        });

    </script>
@endsection

