@extends('admin.layouts.master')

@section('title', 'New Investment')
@section('breadcrumb', 'New Investment')

@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-10">
                <div class="head clearfix">
                    <div>
                        <div class="isw-documents"></div>
                        <h1>Receive Investment to {{ $investor->name }}</h1>
                    </div>
                </div>

                <div class="block-fluid clearfix">
                    <form action="{{ route('admin.investments.saveInvestment') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        @csrf
                        <input type="hidden" name="investor_id" value="{{ $investor->id }}">

                        <div class="col-md-12" style="margin: 8px -14px;">
                            <div class="col-md-4">
                                <label>Payment Mode*</label>
                                <select class="form-control" name="deposit_type" id="deposit_type" required>
                                    <option value="">choose a option...</option>
                                    <option value="Cash" {{ old('deposit_type') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Bank" {{ old('deposit_type') == 'Bank' ? 'selected' : '' }}>Bank</option>
                                </select>
                                @error('deposit_type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4" id="bank_info" style="display: none;">
                                <label>Select Bank*</label>
                                <select class="form-control" name="bank_id" id="bank_id">
                                    <option value="">choose a option...</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                            {{ $bank->account_name }} - {{ $bank->bank_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-3">
                                <label>Cheque/Receipt no</label>
                                <input class="form-control" type="text" name="cheque_no" value="{{ old('cheque_no') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Check/Receipt Date</label>
{{--                                <input class="form-control datepicker" type="date" name="payment_date" value="{{ old('payment_date') }}" required>--}}
                                <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>

                                @error('payment_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label>Amount to Receive*</label>
                                <input type="text" name="paid_amount" value="{{ old('paid_amount') }}" class="form-control" required />
                                @error('paid_amount') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>



{{--                        <div class="col-md-4">--}}
{{--                            <label>Files</label><br>--}}
{{--                            <input type="file" name="file[]" multiple class="form-control" />--}}
{{--                        </div>--}}

                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Save Investment</button>
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
        document.addEventListener('DOMContentLoaded', function () {
            const depositType = document.getElementById("deposit_type");
            const bankInfo = document.getElementById("bank_info");
            const bankDropdown = document.getElementById("bank_id");

            function toggleBankFields() {
                if (depositType.value === 'Bank') {
                    bankInfo.style.display = "block";
                    bankDropdown.disabled = false;
                } else {
                    bankInfo.style.display = "none";
                    bankDropdown.disabled = true;
                }
            }

            depositType.addEventListener("change", toggleBankFields);
            toggleBankFields();
        });
    </script>
@endsection
