@extends('admin.layouts.master')
@section('title', 'Add Investment')

@section('content')
    <div class="workplace">
    <div class="container">
        <h4>Add New Investor</h4>

        <form action="{{ route('admin.investors.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <label>Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

{{--                <div class="col-md-6 mt-3">--}}
{{--                    <label>Amount *</label>--}}
{{--                    <input type="number" step="0.0001" name="total_investment" class="form-control" value="{{ old('total_investment') }}" required>--}}
{{--                    @error('total_investment') <small class="text-danger">{{ $message }}</small> @enderror--}}
{{--                </div>--}}

{{--                <div class="col-md-6 mt-3">--}}
{{--                    <label>Deposit Type *</label>--}}
{{--                    <select name="deposit_type" class="form-control" id="depositType" required>--}}
{{--                        <option value="">Select Type</option>--}}
{{--                        <option value="Cash" {{ old('deposit_type') == 'Cash' ? 'selected' : '' }}>Cash</option>--}}
{{--                        <option value="Bank" {{ old('deposit_type') == 'Bank' ? 'selected' : '' }}>Bank</option>--}}
{{--                    </select>--}}
{{--                    @error('deposit_type') <small class="text-danger">{{ $message }}</small> @enderror--}}
{{--                </div>--}}

{{--                <div class="col-md-6 mt-3" id="bankSelectDiv" style="display: none;">--}}
{{--                    <label>Select Bank *</label>--}}
{{--                    <select name="bank_id" id="bank_id" class="form-control" {{ old('deposit_type') == 'Bank' }}>--}}
{{--                        <option value="">Select Bank</option>--}}
{{--                        @foreach($banks as $bank)--}}
{{--                            <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>--}}
{{--                                {{ $bank->account_name }} - {{ $bank->bank_name }}--}}
{{--                            </option>--}}
{{--                        @endforeach--}}
{{--                    </select>--}}
{{--                    @error('bank_id') <small class="text-danger">{{ $message }}</small> @enderror--}}
{{--                </div>--}}

                <div class="col-md-12 mt-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control" style="height: 150px; width: 100%">{{ old('address') }}</textarea>

                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div style="margin-top: 10px">
                <button class="btn btn-primary">Save Investor</button>
            </div>
        </form>
    </div>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            const $depositType = $('#depositType');
            const $bankDiv = $('#bankSelectDiv');
            const $bankSelect = $('#bank_id');

            function toggleBankField() {
                const selectedType = $depositType.val();
                console.log("Selected Deposit Type:", selectedType);

                if (selectedType === 'Bank') {
                    $bankDiv.show();
                    $bankSelect.prop('disabled', false);
                    console.log("Bank selected → showing bank dropdown");
                } else {
                    $bankDiv.hide();
                    $bankSelect.prop('disabled', true);
                    console.log("Cash selected → hiding bank dropdown");
                }
            }

            $depositType.on('change', toggleBankField);

            // Run on page load
            toggleBankField();
        });
    </script>


@endsection
