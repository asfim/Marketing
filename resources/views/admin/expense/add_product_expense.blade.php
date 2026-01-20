@extends('admin.layouts.master')
@section('title', 'Add Production Expense')
@section('breadcrumb', 'Add Production Expense')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole('super-admin') || $user->can('add-general-expense'))
        <li><a href="{{ route('expense.general.create') }}"><span class="glyphicon glyphicon-plus"></span> Add General Expense</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>Add Production Expense</h1>
                    </div>
                </div>
                <div class="block-fluid clearfix">
                    <form action="{{ route('expense.production.store') }}" method="post" enctype="multipart/form-data" id="gen_expense_form" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="col-md-4">
                            <label>Branch</label>
                            @if($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('branch-list')))
                                <select name="branchId" id="branchId" class="form-control">
                                    <option value="" selected>*** MAIN BRANCH ***</option>
                                    @foreach ($branches as $branch){
                                    <option value="{{ $branch->id }}" {{ (old('branchId') == $branch->id) ? 'selected':'' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}" />
                                <input type="text" value="{{ $user->branch->name }}" class="form-control" readonly/>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label>Select Expense Type*</label>
                            <select name="expense_type_id" class="form-control select2" id="expense_type_id" required>
                                <option value="">choose a option...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('expense_type_id')==$type->id?'selected':'' }}>{{ $type->type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Date*</label>
                            <input type="text" value="{{ old('date') }}" name="date" class="form-control datepicker" id="date" required/>
                        </div>
                        <div class="col-md-6">
                            <label>Expense Name*</label>
                            <input type="text" value="{{ old('expense_name') }}" name="expense_name" class="form-control" id="expense_name"/>
                        </div>
                        <div class="col-md-6">
                            <label>Amount*</label>
                            <input type="text" value="{{ old('amount') }}" name="amount" class="form-control" id="amount" required/>
                        </div>


                        <div class="col-md-12">
                            @if($user->branchId == '')
                                <div class="col-md-4">
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
                            <div class="col-md-3" id="bank_info" style="display: none;">
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
                }else {
                    bank_info.style.display = "none";
                }

            }).trigger('change');
        });

    </script>
@endsection
