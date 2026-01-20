@extends('admin.layouts.master')
@section('title', 'Add General Expense')
@section('breadcrumb', 'Add General Expense')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if ($user->hasRole('super-admin') || $user->can('add-production-expense'))
        <li><a href="{{ route('expense.production.create') }}"><span class="glyphicon glyphicon-plus-sign"></span> Add
                Production Expense</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>Add General Expense</h1>
                    </div>
                </div>
                <div class="block-fluid clearfix">
                    <form action="{{ route('expense.general.store') }}" method="post" enctype="multipart/form-data"
                        id="gen_expense_form" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row-form clearfix" style="display: none">
                            <label class="col-md-3">Engineer Tips Due</label>
                            <div class="col-md-4">
                                <h5 class="color-h5 bold" style="margin: 0;" id="tips_due"></h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Branch</label>
                            @if ($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('branch-list')))
                                <select name="branchId" id="branchId" class="form-control">
                                    <option value="">----- Select Branch -----</option>
                                    @foreach ($branches as $branch)
                                        {
                                        <option value="{{ $branch->id }}"
                                            {{ old('branchId') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}" />
                                <input type="text" value="{{ $user->branch->name }}" class="form-control" readonly />
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label>Select a type*</label>
                            <select name="expense_type_id" class="form-control" id="expense_type_id" required>
                                <option value="">choose a option...</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}"
                                        {{ old('expense_type_id') == $type->id ? 'selected' : '' }}>{{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" style="padding-right: 0;" id="customer_id_box">
                            <label>Select Customer*</label>
                            <select name="customer_id" class="form-control" id="customer_id">
                                <option value="">Select a Customer...</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-md-4">
                                <label>Select Customer*</label>
                                <select name="customer_id" class="form-control" id="customer_id" required>
                                    <option value="">Select a Customer...</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id')==$customer->id?'selected':'' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                        <div class="col-md-4">
                            <label>Date*</label>
                            <input type="text" value="{{ old('date') }}" name="date"
                                class="form-control datepicker" id="date" required />
                        </div>
                        <div class="col-md-6">
                            <label>Expense Name*</label>
                            <input type="text" value="{{ old('expense_name') }}" name="expense_name"
                                class="form-control" id="expense_name" required />
                        </div>
                        <div class="col-md-3">
                            <label>Amount*</label>
                            <input type="text" value="{{ old('amount') }}" name="amount" class="form-control"
                                id="amount" required />
                        </div>
                        <div class="col-md-3">
                            <label>Adjustment</label>
                            <input type="text" value="{{ old('adjustment', 0) }}" name="adjustment" class="form-control"
                                id="adjustment" />
                        </div>

                        <div class="col-md-12">
                            @if ($user->branchId == '')
                                <div class="col-md-4" style="padding-left: 0;">
                                    <label>Payment Mode*</label>
                                    <select name="payment_mode" class="form-control" id="payment_mode" required>
                                        <option value="">choose a option...</option>
                                        <option value="Cash" {{ old('payment_mode') == 'Cash' ? 'selected' : '' }}>Cash
                                        </option>
                                        <option value="Bank" {{ old('payment_mode') == 'Bank' ? 'selected' : '' }}>Bank
                                        </option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="payment_mode" value="Cash" />
                            @endif
                            <div class="col-md-3" id="bank_info" style="display: none;">
                                <label>Select Bank*</label>
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="">choose a option...</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}"
                                            {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                            {{ $bank->bank_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Cheque/Receipt no</label>
                                <input type="text" value="{{ old('cheque_no') }}" name="cheque_no" id="cheque_no"
                                    class="form-control" />
                            </div>
                            <div class="col-md-2">
                                <label>Check/Receipt Date</label>
                                <input type="text" value="{{ old('cheque_date') }}" name="cheque_date" id="cheque_date"
                                    class="form-control datepicker" />
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
            $state.change(function() {
                ///alert('Chenged');
                if ($state.val() == 'Bank') {
                    bank_info.style.display = "block";
                    $("#bank_id").prop('disabled', false);
                } else {
                    bank_info.style.display = "none";
                }

            }).trigger('change');

            // Handle Engineer Tips type with customer
            $(document).ready(function() {
                const expense_type_id = $("#expense_type_id");
                const customer_id = $("#customer_id");
                const customer_id_box = $("#customer_id_box");
                const tips_due = $("#tips_due");

                const ENGINEER_TIPS_ID = 113; // Fixed ID according to DB

                // Function to toggle customer field visibility
                function toggleCustomerBox() {
                    if (expense_type_id.val() == ENGINEER_TIPS_ID) {
                        customer_id_box.show();
                        customer_id.attr('required', true);
                    } else {
                        customer_id_box.hide();
                        customer_id.removeAttr('required');
                    }
                }

                // Initial toggle state on page load
                toggleCustomerBox();

                // Handle expense type change
                expense_type_id.change(function() {
                    toggleCustomerBox();
                });

                // Set tips amount when customer is selected or preloaded
                if (customer_id.val() >= 0 && expense_type_id.val() == ENGINEER_TIPS_ID) {
                    setTipsAmount();
                }

                $('#customer_id').change(function() {
                    setTipsAmount();
                });

                // Function to fetch and display tips amount
                async function setTipsAmount() {
                    tips_due.html("Loading...");

                    try {
                        const tipsAmount = await getTipsAmount(customer_id.val()); // Fetch tips amount
                        tips_due.html(tipsAmount || "No data available");
                    } catch (error) {
                        console.error("Error fetching tips amount:", error);
                        tips_due.html("Error fetching data");
                    }
                }

                // Function to fetch tips amount via AJAX
                async function getTipsAmount(customerId) {
                    const url = "{{ route('engineer-tips') }}"; // Using named route

                    try {
                        const response = await $.ajax({
                            url: url,
                            type: "POST",
                            data: {
                                customer_id: customerId,
                                _token: "{{ csrf_token() }}"
                            }
                        });
                        return response.balance; // Return the balance value
                    } catch (error) {
                        console.error("Error: ", error);
                        throw error;
                    }
                }
            });

            $('#amount').on('blur', function() {
                var amount = parseInt($('#amount').val());
                var tips_due = parseInt($('#tips_due').html());
                if (amount > tips_due) {
                    alert('This amount is getter than of Due balance');
                }
            })
        });
    </script>
@endsection
