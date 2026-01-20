@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')
<?php $user = Auth::user(); ?>
@section('content')

    <div class="workplace">

        <div class="row clearfix">
            <div class="col-md-7 col-md-offset-2 search_box" style="margin-bottom: 15px;">
                <form action="" class="form-horizontal">
                    <div class="" align="right">

                        <div class="col-md-6">
                            @if($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('branch-list')))
                                <select name="branchId" id="branchId" class="form-control">
                                    <option value="">All Branch</option>
                                    <option value="head_office" {{ request('branchId')=='head_office'?'selected':'' }}>** Head Office Only **</option>
                                    @foreach ($branches as $branch){
                                    <option value="{{ $branch->id }}" {{ request('branchId')==$branch->id?'selected':'' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}" />
                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"  class="form-control" placeholder="Date Range"  autocomplete="off"/>
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-default search-btn">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <?php
            $cash_balance = $inflow['cash'] - $outflow['cash'];
            $bank_balance = $inflow['bank'] - $outflow['bank'];
        ?>
        @if($user->branchId == '' && (request('branchId') == '' || request('branchId') == 'head_office'))
            <div class="row">
                @if($user->hasRole(['super-admin']) || $user->can('product-purchase-list'))
                <div class="col-md-3">
                    <div class="wBlock clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('product.purchase.list') }}"> Total Purchase</a></h4>
                            <span class="number">{{ number_format($p_purchase['total']-$supplier['adjustment'],2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
                @if($user->hasRole(['super-admin']) || $user->can('supplier-payment-details'))
                <div class="col-md-3">
                    <div class="wBlock red clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('supplier.payment.details') }}"> Supplier Payment</a></h4>
                            <span class="number">{{ number_format($supplier['payment']-$supplier['adjustment'],2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock green clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('supplier.index') }}"> Supplier Payable</a></h4>
                            <span class="number">{{ number_format($supplier['due']-$supplier['adjustment'],2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
                @if($user->hasRole(['super-admin']) || $user->can('customer-payment-details'))
                <div class="col-md-3">
                    <div class="wBlock yellow clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('customer.payment.details') }}"> Customer Payment</a></h4>
                            <span class="number">{{ number_format($customer['payment']-$customer['adjustment'],2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock green clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('customer.list') }}"> Customer Receivable</a></h4>
                            <span class="number">{{ number_format($customer['due']-$customer['adjustment'],2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="wBlock yellow clearfix">
                        <div class="dSpace">
                            <h4>Truck Rent</h4>
                            <span class="number">{{ number_format($p_purchase['truck_rent'],2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock blue clearfix">
                        <div class="dSpace">
                            <h4>Unload Bill</h4>
                            <span class="number">{{ number_format($p_purchase['unload_bill'],2) }}</span>
                        </div>
                    </div>
                </div>
                @if($user->hasRole(['super-admin']) || $user->can('challan-list'))
                <div class="col-md-3">
                    <div class="wBlock red clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('customer.challan.index') }}"> Invoice Amount</a></h4>
                            <span class="number">{{ number_format($total_billable,2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
                @if($user->hasRole(['super-admin']) || $user->can('income-report'))
                <div class="col-md-3">
                    <div class="wBlock blue clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('report.income') }}"> Total Income</a></h4>
                            <span class="number">{{ number_format($income['total'],2) }}</span>
                        </div>
                        <div class="tSpace">
                            <h5>BDT {{ number_format($income['general'],2) }} <b>General</b></h5>
                            <h5>BDT {{ number_format($income['waste'],2) }} <b>Waste</b></h5>
                        </div>
                    </div>
                </div>
                @endif
                @if($user->hasRole(['super-admin']) || $user->can('expense-report'))
                <div class="col-md-3">
                    <div class="wBlock red clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('report.expense') }}"> Total Expense</a></h4>
                            <span class="number">{{ number_format($expense['total'],2) }}</span>
                        </div>
                        <div class="tSpace">
                            <h5>BDT {{ number_format($expense['general'],2) }} <b>General</b></h5>
                            <h5>BDT {{ number_format($expense['production'],2) }} <b>Production</b></h5>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="wBlock clearfix">
                        <div class="dSpace">
                            <h4>Total Inflow</h4>
                            <span class="number">{{ number_format($inflow['cash']+$inflow['bank'],2) }}</span>
                        </div>
                        <div class="tSpace">
                            <h5>BDT {{ number_format($inflow['cash'],2) }} <b>Cash</b></h5>
                            <h5>BDT {{ number_format($inflow['bank'],2) }} <b>Bank</b></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock yellow clearfix">
                        <div class="dSpace">
                            <h4>Total Outflow</h4>
                            <span class="number">{{ number_format($outflow['cash']+$outflow['bank'],2) }}</span>
                        </div>
                        <div class="tSpace">
                            <h5>BDT {{ number_format($outflow['cash'],2) }} <b>Cash</b></h5>
                            <h5>BDT {{ number_format($outflow['bank'],2) }} <b>Bank</b></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock green clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('report.balance') }}"> Total Balance</a></h4>
                            <span class="number">{{ number_format($cash_balance + $bank_balance,2) }}</span>
                        </div>
                        <div class="tSpace">
                            <h5>BDT {{ number_format($cash_balance,2) }} <b>Cash</b></h5>
                            <h5>BDT {{ number_format($bank_balance,2) }} <b>Bank</b></h5>
                        </div>
                    </div>
                </div>
                @if($user->hasRole(['super-admin']) || $user->can('investment-report'))
                <div class="col-md-3">
                    <div class="wBlock clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('report.investment') }}"> Total Investment</a></h4>
                            <span class="number">{{ number_format($investment['cash']+$investment['bank'],2) }}</span>
                        </div>
                        <div class="tSpace">
                            <h5>BDT {{ number_format($investment['cash'],2) }} <b>Cash</b></h5>
                            <h5>BDT {{ number_format($investment['bank'],2) }} <b>Bank</b></h5>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="wBlock blue clearfix">
                        <div class="dSpace" style="height: 135px; ">
                            @if($user->hasRole(['super-admin']) || $user->can('cash-in-hand-statement-report'))
                            <h4> <a href="{{ route('cash.statement') }}" style="color: #fff;"> <i class="fa fa-list-alt"></i> Cash Statement</a></h4>
                            @endif
                            @if($user->hasRole(['super-admin']) || $user->can('bank-statement-report'))
                            <h4> <a href="{{ route('bank.statement') }}" style="color: #fff;"> <i class="fa fa-university"></i> Bank Statement</a></h4>
                            @endif
                            @if($user->hasRole(['super-admin']) || $user->can('investment-report'))
                            <h4> <a href="{{ route('report.investment') }}" style="color: #fff;"> <i class="fa fa-book"></i> Investment Report</a></h4>
                            @endif
                            @if($user->hasRole(['super-admin']) || $user->can('balance-report'))
                            <h4> <a href="{{ route('report.balance') }}" style="color: #fff;"> <i class="fa fa-credit-card"></i> Balance Report</a></h4>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                   
                    <div class="wBlock red clearfix">
                        <div class="dSpace" style="height: 135px;">
                            @if($user->hasRole('super-admin') || $user->can('report-trial-balance'))
                            <h4> <a href="{{ route('report.trial.balance') }}" style="color: #fff;"> <i class="fa fa-retweet"></i> Trial Balance</a></h4>
                            @endif
                            @if($user->hasRole('super-admin') || $user->can('report-balance-sheet'))
                            <h4> <a href="{{ route('report.balance.sheet') }}" style="color: #fff;"> <i class="fa fa-briefcase"></i> Balance Sheet</a></h4>
                            @endif
                            @if($user->hasRole('super-admin') || $user->can('report-profit-loss'))
                            <h4> <a href="{{ route('report.pl') }}" style="color: #fff;"> <i class="fa fa-chart-line"></i> Profit & Loss</a></h4>
                            @endif
                            
                            <h4> <a href="{{ route('report.quick') }}" style="color: #fff;"> <i class="fa fa-clipboard"></i> Quick Report</a></h4>
                        </div>
                    </div>
                </div>
            </div>
        @else
        
            <div class="row">
             
                <div class="col-md-3">
                    <div class="wBlock clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('product.purchase.list') }}"> Total Purchase</a></h4>
                            <span class="number">{{ number_format($p_purchase['total'],2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock yellow clearfix">
                        <div class="dSpace">
                            <h4>Truck Rent</h4>
                            <span class="number">{{ number_format($p_purchase['truck_rent'],2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock clearfix">
                        <div class="dSpace">
                            <h4>Unload Bill</h4>
                            <span class="number">{{ number_format($p_purchase['unload_bill'],2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock red clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('customer.challan.index') }}"> Invoice Amount</a></h4>
                            <span class="number">{{ number_format($total_billable,2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock blue clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('report.income') }}"> Total Income</a></h4>
                            <span class="number">{{ number_format($income['total'],2) }}</span>
                        </div>
                        <div class="tSpace">
                            <h5>BDT {{ number_format($income['general'],2) }} <b>General</b></h5>
                            <h5>BDT {{ number_format($income['waste'],2) }} <b>Waste</b></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock red clearfix">
                        <div class="dSpace">
                            <h4><a href="{{ route('report.expense') }}"> Total Expense</a></h4>
                            <span class="number">{{ number_format($expense['total'],2) }}</span>
                        </div>
                        <div class="tSpace">
                            <h5>BDT {{ number_format($expense['general'],2) }} <b>General</b></h5>
                            <h5>BDT {{ number_format($expense['production'],2) }} <b>Production</b></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="wBlock yellow clearfix">
                        <div class="dSpace">
                            <h4>Cash Balance</h4>
                            <span class="number">{{ number_format($cash_balance,2) }}</span>
                        </div>
                        <div class="tSpace">
                            <h5>BDT {{ number_format($inflow['cash'],2) }} <b>Inflow</b></h5>
                            <h5>BDT {{ number_format($outflow['cash'],2) }} <b>Outflow</b></h5>
                        </div>
                    </div>
                </div>
                   <div class="col-md-3">
                   
                    <div class="wBlock red clearfix">
                        <div class="dSpace" style="height: 135px;">
                            
                            {{-- <h4> <a href="{{ route('report.trial.balance') }}" style="color: #fff;"> <i class="fa fa-retweet"></i> Trial Balance</a></h4>
                           
                            <h4> <a href="{{ route('report.balance.sheet') }}" style="color: #fff;"> <i class="fa fa-briefcase"></i> Balance Sheet</a></h4>
                          
                            <h4> <a href="{{ route('report.pl') }}" style="color: #fff;"> <i class="fa fa-chart-line"></i> Profit & Loss</a></h4> --}}
                            
                             <h4> <a href="{{ route('report.quick') }}" style="color: #fff; padding-top: 50%"> <i class="fa fa-clipboard"></i> Quick Report</a></h4>

                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="dr"><span></span></div>


    </div>
@endsection
