@extends('admin.layouts.master')

@section('content')
    <div class="workplace">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="d-flex justify-content-center mb-4">
            <div class="row w-100 justify-content-center">
                <div class="col-md-4">
                    <div class="wBlock red clearfix">
                        <div class="dSpace text-center">
                            <h4>Total Lent Loan</h4>

                            <span class="number">৳ {{ number_format($totalDebit, 2) }}</span>
                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="wBlock yellow clearfix">
                        <div class="dSpace text-center">
                            <h4>Total Return</h4>
                            <span class="number">৳ {{ number_format($totalCredit, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="wBlock green clearfix">
                        <div class="dSpace text-center">
                            <h4>Remaining Amount</h4>
                            <span class="number">৳ {{ number_format($totalDebit -$totalCredit, 2) }}</span>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div id="lender-profile-tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
            {{-- Tab Nav --}}
            <div class="head clearfix" style="display: flex; justify-content: space-between; align-items: center;">
                {{-- Left side tabs --}}
                <ul class="buttons ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist" style="margin-bottom: 0;">
                    <li class="ui-state-default ui-corner-top {{ $selected_tab=='tab-lender-info' ? 'ui-tabs-active ui-state-active' : '' }}" role="tab">
                        <a href="#tab-lender-info" class="ui-tabs-anchor"><i class="glyphicon glyphicon-user"></i> Lender Info</a>
                    </li>
                    <li class="ui-state-default ui-corner-top {{ $selected_tab=='tab-repayment-history' ? 'ui-tabs-active ui-state-active' : '' }}" role="tab">
                        <a href="#tab-repayment-history" class="ui-tabs-anchor"><i class="glyphicon glyphicon-repeat"></i> Return Lending History</a>
                    </li>
                    <li class="ui-state-default ui-corner-top {{ $selected_tab=='tab-loan-history' ? 'ui-tabs-active ui-state-active' : '' }}" role="tab">
                        <a href="#tab-loan-history" class="ui-tabs-anchor"><i class="glyphicon glyphicon-usd"></i> Lending History</a>
                    </li>
                    <li class="ui-state-default ui-corner-top {{ $selected_tab=='tab-loan-statement' ? 'ui-tabs-active ui-state-active' : '' }}" role="tab">
                        <a href="#tab-loan-statement" class="ui-tabs-anchor"><i class="glyphicon glyphicon-list"></i>Statements</a>
                    </li>
                </ul>

                {{-- Right side action buttons --}}
                <style>
                    .inline-btn {
                        background-color: white !important;
                        color: black !important;
                        border: 1px solid #ccc;
                        padding: 8px 16px;
                        font-weight: bold;
                        transition: background-color 0.3s ease;
                    }

                    .inline-btn:hover {
                        background-color: #f0f0f0 !important;
                        color: black !important;
                    }
                </style>

                <div class="action-buttons" style="display: flex; gap: 10px;">
                     @if(auth()->user()->hasRole(['super-admin']))
                    <a href="{{ route('admin.lenders.addLoan', $lender->id) }}" class="btn inline-btn">
                        <i class="glyphicon glyphicon-plus"></i> Give Loan
                    </a>
                   

                    <a href="{{ route('lenders.return.form', $lender->id) }}" class="btn inline-btn btn-warning">
                        <i class="glyphicon glyphicon-arrow-up"></i> Return Loan
                    </a>
                    @endif
                </div>

            </div>


            {{-- Lender Info --}}
            <div id="tab-lender-info" class="ui-tabs-panel ui-widget-content ui-corner-bottom {{ $selected_tab=='tab-lender-info' ? '' : 'ui-helper-hidden' }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="block ucard">
                            <div class="info">
                                <ul class="rows">
                                    <li class="heading">Lender Info</li>
                                    <li>
                                        <div class="title">Name:</div>
                                        <div class="text">{{ $lender->name }}</div>
                                    </li>
                                    <li>
                                        <div class="title">Phone:</div>
                                        <div class="text">{{ $lender->phone }}</div>
                                    </li>
                                    <li>
                                        <div class="title">Address:</div>
                                        <div class="text">{{ $lender->address }}</div>
                                    </li>
                                    {{-- Additional info like loan type or bank details can be added here if applicable --}}
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{--             Repayment History--}}
            <div id="tab-repayment-history" class="ui-tabs-panel ui-widget-content ui-corner-bottom {{ $selected_tab=='tab-repayment-history' ? '' : 'ui-helper-hidden' }}">
                <div class="row">
                    <div class="col-md-12">
                        {{--                         Header--}}
                        <div class="head clearfix">
                            <div class="col-md-4">
                                <h3>
                                    Loan Repayment History
                                    <span class="src-info">
                            {{-- {{ request('repayment_date_range') ? '- ' . request('repayment_date_range') : '- All Time' }} --}}
                            <span class="src-info">{{ (request('repayment_search_text') == '' && request('repayment_date_range') == '')?'- Last 30 Days':'- '. request('repayment_date_range') }}</span>
                        </span>
                                </h3>
                            </div>
                            <div class="col-md-8 search_box" style="margin-top: 4px;">
                                <form action="" class="form-horizontal">
                                    <input type="hidden" name="tab_type" value="tab-repayment-history"/>
                                    <div class="" align="right">
                                        <div class="col-md-6">
                                            <input type="text" name="repayment_search_text" class="form-control" value="{{ request('repayment_search_text') ?? '' }}" placeholder="Search note or method"/>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="text" name="repayment_date_range" value="{{ request('repayment_date_range') ?? '' }}" class="date_range form-control" placeholder="Date Range" autocomplete="off"/>
                                                <div class="input-group-btn">
                                                    <button type="submit" class="btn btn-default search-btn">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{--                         Table--}}
                        <div class="block-fluid table-sorting clearfix">
                            <table class="table" width="100%">
                                <thead>
                                <tr>
                                    <th width="3%"><input type="checkbox" name="checkall"/></th>
                                    <th>#</th>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Return Amount</th> {{-- Changed title to Return Amount --}}
                                    <th>Method</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                                </thead>




                                <tbody>
                                @php
                                    $totalLoan = 0;
                                @endphp

                                @forelse ($investmentHistory as $index => $loan)
                                    @php
                                        $totalLoan += $loan->credit;
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $loan->transaction_id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($loan->posting_date)->format('Y-m-d') }}</td>
                                        <td>৳ {{ number_format($loan->credit, 2) }}</td>
                                        {{-- <td>{{ ucfirst($loan->payment_mode ?? 'N/A') }}</td> --}}
                                        <td>  @if(strtolower($loan->payment_mode ?? '') === 'bank')
                                            {{ $loan->bank_name }}
                                        @elseif(strtolower($loan->payment_mode ?? '') === 'cash')
                                            Cash
                                        @else
                                            {{ ucfirst($loan->payment_mode ) }}
                                        @endif</td>
                                        <td>{{ $loan->description ?? 'N/A' }}</td>
                                         @if(auth()->user()->hasRole(['super-admin']))
                                        <td>
                                            <form action="{{ route('admin.repayments.destroy', $loan->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-xs"
                                                        onclick="return confirm('Are you sure you want to delete this repayment? This action cannot be undone.')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No loan history available.</td>
                                    </tr>
                                @endforelse

                                @if(count($investmentHistory))
                                    <tr style="background-color:#999999; color: #fff;">
                                        <td></td>
                                        <td colspan="3"><b>Total</b></td>
                                        <td><b>৳ {{ number_format($totalLoan, 2) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                @endif
                                </tbody>



                            </table>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Loan History --}}
            <div id="tab-loan-history" class="ui-tabs-panel ui-widget-content ui-corner-bottom {{ $selected_tab == 'tab-loan-history' ? '' : 'ui-helper-hidden' }}">
                <div class="row">
                    <div class="col-md-12">
                        {{-- Header --}}
                        <div class="head clearfix">
                            <div class="col-md-4">
                                <div class="isw-documents"></div>
                                <h3>
                                    Loan History
                                    <span class="src-info">
                            {{-- {{ request('loan_date_range') ? '- ' . request('loan_date_range') : '- All Time' }} --}}
                            <span class="src-info">{{ (request('loan_search_text') == '' && request('loan_date_range') == '')?'- Last 30 Days':'- '. request('loan_date_range') }}</span>
                        </span>
                                </h3>
                            </div>
                            <div class="col-md-8 search_box" style="margin-top: 4px;">
                                <form action="" class="form-horizontal">
                                    <input type="hidden" name="tab_type" value="tab-loan-history"/>
                                    <div align="right">
                                        <div class="col-md-6">
                                            <input type="text" name="loan_search_text" class="form-control" value="{{ request('loan_search_text') ?? '' }}" placeholder="Search description"/>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="text" name="loan_date_range" value="{{ request('loan_date_range') ?? '' }}" class="date_range form-control" placeholder="Date Range" autocomplete="off"/>
                                                <div class="input-group-btn">
                                                    <button type="submit" class="btn btn-default search-btn">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="block-fluid table-sorting clearfix">
                            <table class="table" width="100%">
                                <thead>
                                <tr>
                                    <th width="3%"><input type="checkbox" name="checkall"/></th>
                                    <th>#</th>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Loan Amount</th>
                                    <th>Method</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @php $totalReturn = 0; @endphp
                                @forelse ($returnHistory as $index => $return)
                                    @php $totalReturn += $return->debit; @endphp
                                    <tr>
                                        <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $return->transaction_id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($return->posting_date)->format('Y-m-d') }}</td>
                                        <td>৳ {{ number_format($return->debit, 2) }}</td>
                                        <td> @if(strtolower($return->deposit_type) === 'bank')
                                            {{ $return->bank_name }} 
                                        @elseif(strtolower($return->deposit_type) === 'cash')
                                            Cash
                                        @else
                                            {{ $return->deposit_type ?? 'N/A' }}
                                        @endif</td>
                                        <td>{{ ucfirst($return->description ?? 'N/A') }}</td>
                                         @if(auth()->user()->hasRole(['super-admin']))
                                        <td>
                                            <form action="{{ route('loan.repayment.destroy', $return->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-xs"
                                                        onclick="return confirm('Are you sure you want to delete this repayment? This action cannot be undone.')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                        @endif
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No return history available.</td>
                                    </tr>
                                @endforelse

                                @if(count($returnHistory))
                                    <tr style="background-color:#999999; color: #fff;">
                                        <td></td>
                                        <td colspan="3"><b>Total</b></td>
                                        <td><b>৳ {{ number_format($totalReturn, 2) }}</b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                @endif
                                </tbody>



                            </table>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Lender Statements --}}
            <div id="tab-loan-statement" class="ui-tabs-panel ui-widget-content ui-corner-bottom {{ $selected_tab == 'tab-loan-statement' ? '' : 'ui-helper-hidden' }}">
                <div class="row">
                    <div class="col-md-12">
                        {{-- Header --}}
                        <div class="head clearfix">
                            <div class="col-md-4">
                                <div class="isw-documents"></div>
                                <h3>
                                    Lender Statements
                                    <span class="src-info">
                            {{-- {{ request('investment_date_range') ? '- ' . request('investment_date_range') : '- All Time' }} --}}
                            <span class="src-info">{{ (request('investment_search_text') == '' && request('investment_date_range') == '')?'- Last 30 Days':'- '. request('investment_date_range') }}</span>
                        </span>
                                </h3>
                            </div>
                            <div class="col-md-8 search_box" style="margin-top: 4px;">
                                <form action="" class="form-horizontal">
                                    <input type="hidden" name="tab_type" value="tab-loan-statement"/>
                                    <div class="" align="right">
                                        <div class="col-md-6">
                                            <input type="text" name="investment_search_text" class="form-control" value="{{ request('investment_search_text') ?? '' }}" placeholder="Search description"/>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="text" name="investment_date_range" value="{{ request('investment_date_range') ?? '' }}" class="date_range form-control" placeholder="Date Range" autocomplete="off"/>
                                                <div class="input-group-btn">
                                                    <button type="submit" class="btn btn-default search-btn">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="block-fluid table-sorting clearfix">
                            <table class="table" width="100%">
                                <thead>
                                <tr>
                                    <th width="3%"><input type="checkbox" name="checkall"/></th>
                                    <th>#</th>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Payment Details</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $balance = 0;
                                    $totalDebit = 0;
                                    $totalCredit = 0;
                                @endphp

                                @forelse ($statements as $index => $stmt)
                                    @php
                                        $balance += ($stmt->debit - $stmt->credit);
                                        $totalDebit += $stmt->debit;
                                        $totalCredit += $stmt->credit;
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stmt->transaction_id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($stmt->posting_date)->format('Y-m-d') }}</td>
                                        <td>{{ $stmt->description ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($stmt->payment_mode ?? 'N/A') }}</td>
                                        <td>{{ $stmt->debit > 0 ? '৳ '.number_format($stmt->debit, 2) : '৳0' }}</td>
                                        <td>{{ $stmt->credit > 0 ? '৳ '.number_format($stmt->credit, 2) : '৳0' }}</td>
                                        <td>৳ {{ number_format($stmt->balance, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No lender statements found.</td>
                                    </tr>
                                @endforelse
                                </tbody>

                                @if($statements->count() > 0)
                                    <tfoot>
                                    <tr style="font-weight: bold; background: #f0f0f0;">
                                        <td></td>
                                        <td colspan="5" class="text-right">Total:</td>
                                        <td>৳ {{ number_format($totalDebit, 2) }}</td>
                                        <td>৳ {{ number_format($totalCredit, 2) }}</td>
                                        <td>৳ {{ number_format($balance, 2) }}</td>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
@section('page-script')
    <script>
        $(function () {
            $("#lender-profile-tabs").tabs();
        });
    </script>
@endsection

