@extends('admin.layouts.master')
@section('title', 'Fund Transfer')
@section('breadcrumb', 'Fund Transfer')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole(['super-admin']))
        <li><a href="{{ route('bank.statement') }}"><span class="glyphicon glyphicon-random"></span> Bank Statement</a></li>
    @endif
@endsection

@section('content')
    <div class="workplace" style="background-color: #f9f9f9; padding: 20px; border-radius: 12px;">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix" style="margin-bottom: 20px;">
                    <div class="col-md-4">
                        <div class="isw-random" style="font-size: 28px; color: #337ab7;"></div>
                        <h1 style="font-weight: bold;">Fund Transfer</h1>
                    </div>
                </div>

                <div class="block-fluid clearfix" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @elseif(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('admin.fund.transfer') }}" method="POST" class="form-horizontal" style="margin-top: 15px;">
                        @csrf

                        <div class="col-md-4" style="margin-bottom: 20px;">
                         
                            <label style="font-weight: 600;">Transfer Type *</label>
                            <select name="transfer_type" class="form-control" id="transfer_type" required>
                                <option value="">Select Type</option>
                                <option value="cash_to_bank" {{ old('transfer_type') == 'cash_to_bank' ? 'selected' : '' }}>Cash to Bank</option>
                                <option value="bank_to_cash" {{ old('transfer_type') == 'bank_to_cash' ? 'selected' : '' }}>Bank to Cash</option>
                                <option value="bank_to_bank" {{ old('transfer_type') == 'bank_to_bank' ? 'selected' : '' }}>Bank to Bank</option>
                            </select>
                        </div>
                       <div class="col-md-4 mb-3">
                            <label for="dateInput" class="form-label fw-bold">Date</label>
                            <input type="date" id="dateInput" name="transfer_date" class="form-control" style="border-radius: 8px; padding: 10px; border: 1px solid #ccc;" required>

                        </div>


                        <div class="col-md-4" id="from_bank_block" style="margin-bottom: 20px; display: none;">
                            <label style="font-weight: 600;">Transfer From (Bank)*</label>
                            <select name="from_bank_id" class="form-control" style="border-radius: 6px;">
                                <option value="">Select Account</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }} - Balance: {{ number_format($bank->balance ?? 0, 2) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4" id="to_bank_block" style="margin-bottom: 20px; display: none;">
                            <label style="font-weight: 600;">Transfer To (Bank)*</label>
                            <select name="to_bank_id" class="form-control" style="border-radius: 6px;">
                                <option value="">Select Account</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }} - Balance: {{ number_format($bank->balance ?? 0, 2) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4" style="margin-bottom: 20px;">
                            <label style="font-weight: 600;">Amount *</label>
                            <input type="number" name="amount" step="0.01" class="form-control" style="border-radius: 6px;" value="{{ old('amount') }}" required>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 20px;">
                            <label style="font-weight: 600;">Description</label>
                            <textarea name="description" class="form-control" rows="6" style="border-radius: 6px;">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <div class="footer" style="text-align: center; margin-top: 20px;">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 25px; border-radius: 6px;">Transfer</button>
                                <button type="reset" class="btn btn-danger" style="padding: 10px 25px; border-radius: 6px;">Reset</button>
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
        $(document).ready(function () {
            function toggleFields() {
                var type = $('#transfer_type').val();
                $('#from_bank_block, #to_bank_block').hide();

                if (type === 'cash_to_bank') {
                    $('#to_bank_block').show();
                } else if (type === 'bank_to_cash') {
                    $('#from_bank_block').show();
                } else if (type === 'bank_to_bank') {
                    $('#from_bank_block').show();
                    $('#to_bank_block').show();
                }
            }

            $('#transfer_type').change(function () {
                toggleFields();
            });

            toggleFields();
        });
    </script>
@endsection
