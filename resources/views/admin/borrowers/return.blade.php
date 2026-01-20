@extends('admin.layouts.master')

@section('title', 'Return Loan')
@section('breadcrumb', 'Return Loan')

@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-10">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>Return Loan for: {{ $borrower->name }}</h1>
                    </div>
                </div>

                <div class="block-fluid clearfix">
                    <form action="{{ route('borrowers.return.process', $borrower->id) }}" method="POST" class="form-horizontal">
                        @csrf

                        <div class="col-md-12" style="margin: 8px -14px;">


                            <div class="col-md-4">
                                <label>Method*</label>
                                <select name="method" class="form-control" required onchange="toggleBank(this.value)">
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>

                            <div class="col-md-4 bank-group" style="display: none;">
                                <label>Select Bank*</label>
                                <select name="bank_id" class="form-control">
                                    @foreach(\App\Models\BankInfo::all() as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Reference Date*</label>
                                <input type="date" name="ref_date" class="form-control" value="{{ old('ref_date', now()->format('Y-m-d')) }}" required>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <label>Amount*</label>
                            <input type="number" name="amount" class="form-control" required min="1">
                        </div>


                        <div class="col-md-12">
                            <label>Note</label>
                            <textarea name="note" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Return</button>
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
            const methodSelect = document.querySelector('[name="method"]');
            const bankGroup = document.querySelector('.bank-group');

            function toggleBank(value) {
                bankGroup.style.display = value === 'bank' ? 'block' : 'none';
            }

            methodSelect.addEventListener('change', function () {
                toggleBank(this.value);
            });

            toggleBank(methodSelect.value); // Init on page load
        });
    </script>
@endsection
