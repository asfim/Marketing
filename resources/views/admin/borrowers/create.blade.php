@extends('admin.layouts.master')
@section('title', 'Add Borrower')

@section('content')
    <div class="workplace">
        <div class="container">
            <h4>Add New Borrower</h4>

            <form action="{{ route('admin.borrowers.store') }}" method="POST">
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





                    <div class="col-md-12 mt-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control" style="height: 150px; width: 100%">{{ old('address') }}</textarea>
                        @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div style="margin-top: 10px">
                    <button class="btn btn-primary">Save Borrower</button>
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

                if (selectedType === 'Bank') {
                    $bankDiv.show();
                    $bankSelect.prop('disabled', false);
                } else {
                    $bankDiv.hide();
                    $bankSelect.prop('disabled', true);
                }
            }

            $depositType.on('change', toggleBankField);

            // Run on page load
            toggleBankField();
        });
    </script>
@endsection
