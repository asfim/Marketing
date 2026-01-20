<?php $user = Auth::user(); ?>
<div class="menu hidden-print">

    <div class="breadLine">
        <div class="arrow"></div>
        <div class="adminControl" style="color:red; font-weight: bold;">
            @if ($user)
                {{ 'Hi, ' . $user->name }}
            @endif
        </div>
    </div>

    <div class="admin">
        <div class="image">
            <img src="{{ asset('assets/images/' . $settings['logo']) }}" class="img-thumbnail" />
        </div>
        <ul class="control">
            <li><span class="fa fa-sign-out-alt"></span>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();"
                    class="">Sign out</a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
    </div>

    <ul class="navigation">
        <li>
            <a href="{{ route('admin.home') }}">
                <span class="isw-grid"></span><span class="text">Dashboard</span>
            </a>
        </li>

        <!-- Customer Section -->
        @if (
            $user->hasRole(['super-admin']) ||
                $user->can('customer-create') ||
                $user->can('customer-list') ||
                $user->can('customer-payment') ||
                $user->can('customer-payment-details'))
            <li class="openable {{ request()->is('customer/*') ? 'active' : '' }}">
                <a href="#">
                    <span class="isw-users"></span><span class="text">Customers</span>
                </a>
                <ul>

                    @if ($user->hasRole(['super-admin']) || $user->can('customer-list'))
                        <li class="{{ \Route::is('customer.list') ? 'active' : '' }}">
                            <a href="{{ route('customer.list') }}">
                                <span class="glyphicon glyphicon-th-list"></span><span class="text">All
                                    Customer</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('customer-payment-details'))
                        <li class="{{ \Route::is('customer.payment.details') ? 'active' : '' }}">
                            <a href="{{ route('customer.payment.details') }}">
                                <span class="glyphicon glyphicon-th-list"></span><span class="text">Customer
                                    Payments</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('customer-statement-report'))
                        <li class="{{ \Route::is('customer.statement') ? 'active' : '' }}">
                            <a href="{{ route('customer.statement') }}">
                                <span class="glyphicon glyphicon-asterisk"></span><span class="text">Customer
                                    Statement</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('challan-list'))
                        <li>
                            <a href="{{ route('customer.challan.index') }}">
                                <span class="glyphicon glyphicon-list-alt"></span><span class="text">Customer Challan
                                    List</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('bill-list'))
                        <li>
                            <a href="{{ route('customer.bill.index') }}">
                                <span class="glyphicon glyphicon-th-list"></span><span class="text">View Customer
                                    Bills</span>
                            </a>
                        </li>
                    @endif

                


                </ul>
            </li>
        @endif

        <!-- Supplier Section -->
        @if (
            $user->hasRole(['super-admin']) ||
                $user->can('supplier-create') ||
                $user->can('supplier-list') ||
                $user->can('supplier-payment') ||
                $user->can('supplier-payment-details'))
                
            <li class="openable {{ request()->is('supplier/*') ? 'active' : '' }}">
                <a href="#">
                    <span class="isw-users"></span><span class="text">Suppliers</span>
                </a>
                <ul>
                    @if ($user->hasRole(['super-admin']) || $user->can('supplier-list'))
                        <li class="{{ \Route::is('supplier.index') ? 'active' : '' }}">
                            <a href="{{ route('supplier.index') }}">
                                <span class="glyphicon glyphicon-th-large"></span><span class="text">All
                                    Supplier</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('supplier-payment-details'))
                        <li class="{{ \Route::is('supplier.payment.details') ? 'active' : '' }}">
                            <a href="{{ route('supplier.payment.details') }}">
                                <span class="glyphicon glyphicon-th-list"></span><span class="text">Supplier Payment
                                    Details</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('supplier-statement-report'))
                        <li class="{{ \Route::is('supplier.statement') ? 'active' : '' }}">
                            <a href="{{ route('supplier.statement') }}">
                                <span class="glyphicon glyphicon-asterisk"></span><span class="text">Supplier
                                    Statement</span>
                            </a>
                        </li>
                    @endif

                    

                </ul>
            </li>
        @endif

        <!-- Invest Section-->
        @if ($user->hasRole(['super-admin']) || $user->id == 17)
            <li class="openable {{ request()->is(['investment/*', 'investor/*']) ? 'active' : '' }}">

                <a href="#">
                    <span class="isw-calc"></span><span class="text">Invest Management</span>
                </a>
                <ul>

                    @if ($user->hasRole(['super-admin']) || $user->id == 17)
                        <li class="{{ \Route::is('admin.investments.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.investments.index') }}">
                                {{-- Invest in CCL (All Investment) --}}
                                <span class="glyphicon glyphicon-user"></span><span class="text">All Investment</span>
                            </a>
                        </li>
                    @endif


                    @if ($user->hasRole(['super-admin']) || $user->id == 17)
                        <li class="{{ \Route::is('admin.investors.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.investors.index') }}">
                                <span class="glyphicon glyphicon-user"></span><span class="text">Investor</span>
                            </a>
                            <!-- Loan Taken from Another Company (Borrower) -->
                        </li>
                    @endif


                </ul>
            </li>
        @endif

        <!-- Loan Section-->
        @if ($user->hasRole(['super-admin']) || $user->id == 17)
            <li class="openable {{ request()->is(['lender/*', 'borrower/*']) ? 'active' : '' }}">

                <a href="#">
                    <span class="isw-calc"></span><span class="text">Loan Management</span>
                </a>
                <ul>

                    @if ($user->hasRole(['super-admin']) || $user->id == 17)
                        <li class="{{ \Route::is('admin.lenders.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.lenders.index') }}">
                                <span class="glyphicon glyphicon-user"></span><span class="text">All Lender</span>
                            </a>
                            {{--                            Loan Given by a Company (Lender) --}}
                        </li>
                    @endif
                    @if ($user->hasRole(['super-admin']) || $user->id == 17)
                        <li class="{{ \Route::is('admin.borrowers.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.borrowers.index') }}">
                                <span class="glyphicon glyphicon-user"></span><span class="text">All Borrower</span>
                            </a>
                            <!-- Loan Taken from Another Company (Borrower) -->
                        </li>
                    @endif


                </ul>
            </li>
        @endif

        <!-- Purchase Product -->
        @if (
            $user->hasRole(['super-admin']) ||
                $user->can('product-create') ||
                $user->can('product-purchase') ||
                $user->can('product-purchase-list') ||
                $user->can('product-stock-view'))
            <li class="openable {{ request()->is('product/*') ? 'active' : '' }}">
                <a href="#">
                    <span class="isw-archive"></span><span class="text">Manage Product</span>
                </a>
                <ul>
                    @if ($user->hasRole(['super-admin']) || $user->can('product-create'))
                        <li>
                            <a href="{{ route('product.list') }}">
                                <span class="glyphicon glyphicon-list"></span><span class="text">Product List</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('product-purchase'))
                        <li>
                            <a href="{{ route('product.purchase') }}">
                                <span class=" glyphicon glyphicon-shopping-cart"></span><span class="text">Product
                                    Purchase</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('product-purchase-list'))
                        <li class="{{ \Route::is('product.purchase.list') ? 'active' : '' }}">
                            <a href="{{ route('product.purchase.list') }}">
                                <span class="glyphicon glyphicon-list-alt"></span><span class="text">Product Purchase
                                    List</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('product-stock-view'))
                        <li class="{{ \Route::is('product.stock') ? 'active' : '' }}">
                            <a href="{{ route('product.stock') }}">
                                <span class="glyphicon glyphicon-tasks"></span><span class="text">View Product
                                    Stock</span>
                            </a>
                        </li>
                        <li class="{{ \Route::is('product.stock.adjustment') ? 'active' : '' }}">
                            <a href="{{ route('product.stock.adjustment') }}">
                                <span class="glyphicon glyphicon-th-list"></span><span class="text"> Stock
                                    Adjustment</span>
                            </a>
                        </li>
                        <li class="{{ \Route::is('product.consumption') ? 'active' : '' }}">
                            <a href="{{ route('product.consumption') }}">
                                <span class="glyphicon glyphicon-fire"></span><span class="text"> Product
                                    Consumption</span>
                            </a>
                        </li>
                    @endif

                </ul>

            </li>
        @endif


        <!-- Expense Sections -->
        @if (
            $user->hasRole(['super-admin']) ||
                $user->can('add-general-expense-type') ||
                $user->can('add-general-expense') ||
                $user->can('show-general-expense') ||
                $user->can('add-land-house-owner') ||
                $user->can('add-land-rent-info') ||
                $user->can('show-land-owners') ||
                $user->can('show-house-owners'))
            <li class="openable {{ request()->is('expense/*') ? 'active' : '' }}">
                <a href="#">
                    <span class="isw-calc"></span><span class="text">Expense</span>
                </a>
                <ul>
                    @if (
                        $user->hasRole(['super-admin']) ||
                            $user->can('add-general-expense-type') ||
                            $user->can('add-production-expense-type'))
                        <li class="{{ \Route::is('expense.type') ? 'active' : '' }}">
                            <a href="{{ route('expense.type') }}">
                                <span class="glyphicon glyphicon-random"></span><span class="text">Expense
                                    Types</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('show-general-expense'))
                        <li class="{{ \Route::is('expense.general.index') ? 'active' : '' }}">
                            <a href="{{ route('expense.general.index') }}">
                                <span class="glyphicon glyphicon-list"></span><span class="text">General
                                    Expense</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('show-production-expense'))
                        <li class="{{ \Route::is('expense.production.index') ? 'active' : '' }}">
                            <a href="{{ route('expense.production.index') }}">
                                <span class="glyphicon glyphicon-list"></span><span class="text">Production
                                    Expense</span>
                            </a>
                        </li>
                    @endif


                </ul>
            </li>
        @endif
        <!-- Expense Sections -->

        <!-- Income Section -->
        @if (
            $user->hasRole(['super-admin']) ||
                $user->can('add-general-income-type') ||
                $user->can('add-general-income') ||
                $user->can('show-general-income') ||
                $user->can('add-waste-income-type') ||
                $user->can('add-waste-income') ||
                $user->can('show-waste-income'))
            <li class="openable {{ request()->is('income/*') ? 'active' : '' }}">
                <a href="#">
                    <span class="isw-plus"></span><span class="text">Income</span>
                </a>
                <ul>
                    @if ($user->hasRole(['super-admin']) || $user->can('add-general-income-type') || $user->can('add-waste-income-type'))
                        <li class="{{ \Route::is('income.type') ? 'active' : '' }}">
                            <a href="{{ route('income.type') }}">
                                <span class="glyphicon glyphicon-random"></span><span class="text">Income
                                    Types</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('show-general-income'))
                        <li class="{{ \Route::is('income.general.index') ? 'active' : '' }}">
                            <a href="{{ route('income.general.index') }}">
                                <span class="glyphicon glyphicon-list"></span><span class="text">General
                                    Income</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('show-waste-income'))
                        <li class="{{ \Route::is('income.waste.index') ? 'active' : '' }}">
                            <a href="{{ route('income.waste.index') }}">
                                <span class="glyphicon glyphicon-list"></span><span class="text">Waste Income</span>
                            </a>
                        </li>
                    @endif

                </ul>
            </li>
        @endif

        <!-- Property Section -->
        @if (
            $user->hasRole(['super-admin']) ||
                $user->can('add-land-house-owner') ||
                $user->can('add-land-rent-info') ||
                $user->can('show-land-owners') ||
                $user->can('show-house-owners'))
            <li class="openable {{ request()->is('property/*') ? 'active' : '' }}">
                <a href="#">
                    <span class="isw-cloud"></span><span class="text">Property Rent</span>
                </a>
                <ul>
                    @if ($user->hasRole(['super-admin']) || $user->can('add-land-house-owner'))
                        <li class="{{ \Route::is('owner.create') ? 'active' : '' }}">
                            <a href="{{ route('owner.create') }}">
                                <span class="glyphicon glyphicon-ok-circle"></span><span class="text">Add Land/House
                                    owner</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('add-land-rent-info'))
                        <li class="{{ \Route::is('property.rent.create') ? 'active' : '' }}">
                            <a href="{{ route('property.rent.create') }}">
                                <span class="glyphicon glyphicon-info-sign"></span><span class="text">Add Land Rent
                                    Info</span>
                            </a>
                        </li>
                    @endif

                    {{-- @if ($user->can('show-land-owners')) --}}
                    {{-- <li> --}}
                    {{-- <a href="{{URL::to('/show-land-owner')}}"> --}}
                    {{-- <span class="glyphicon glyphicon-th-large"></span><span class="text">Show land owners</span> --}}
                    {{-- </a> --}}
                    {{-- </li> --}}
                    {{-- @endif --}}

                    @if ($user->hasRole(['super-admin']) || $user->can('show-house-owners') || $user->can('show-land-owners'))
                        <li>
                            <a href="{{ route('owner.index') }}">
                                <span class="glyphicon glyphicon-list"></span><span class="text">Land/House
                                    Owners</span>
                            </a>
                        </li>
                    @endif

                </ul>
            </li>
        @endif

        <!-- Asset Section -->
        @if (
            $user->hasRole(['super-admin']) ||
                $user->can('add-asset-type') ||
                $user->can('add-assets') ||
                $user->can('show-asstes'))
            <li class="openable {{ request()->is('asset/*') ? 'active' : '' }}">
                <a href="#">
                    <span class="isw-graph"></span><span class="text">Asset</span>
                </a>
                <ul>
                    @if ($user->hasRole(['super-admin']) || $user->can('add-asset-type'))
                        <li class="{{ \Route::is('asset.type') ? 'active' : '' }}">
                            <a href="{{ route('asset.type') }}">
                                <span class="glyphicon glyphicon-plus"></span><span class="text">Asset Type</span>
                            </a>
                        </li>
                    @endif

                    {{-- @if ($user->can('add-assets')) --}}
                    {{-- <li> --}}
                    {{-- <a href="{{ route('asset.create') }}"> --}}
                    {{-- <span class="glyphicon glyphicon-check"></span><span class="text">Add Assets</span> --}}
                    {{-- </a> --}}
                    {{-- </li> --}}
                    {{-- @endif --}}

                    @if ($user->hasRole(['super-admin']) || $user->can('show-asstes'))
                        <li class="{{ \Route::is('asset.index') ? 'active' : '' }}">
                            <a href="{{ route('asset.index') }}">
                                <span class="glyphicon glyphicon-list"></span><span class="text">Show Assets</span>
                            </a>
                        </li>
                    @endif

                </ul>
            </li>
        @endif

        <!-- Cash In Hand -->
        @if ($user->hasRole(['super-admin']) || $user->can('add-cash') || $user->can('withdraw-cash'))
            <li class="openable {{ request()->is('cash/*') ? 'active' : '' }}">
                <a href="#">
                    <span class="isw-favorite"></span><span class="text">Cash In Hand</span>
                </a>
                <ul>
                    @if ($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('add-cash')))
                        <li class="{{ \Route::is('cash.index') ? 'active' : '' }}">
                            <a href="{{ route('cash.index') }}">
                                <span class="glyphicon glyphicon-plus-sign"></span><span class="text">Cash
                                    Investment</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('withdraw-cash'))
                        <li class="{{ \Route::is('cash.withdraw') ? 'active' : '' }}">
                            <a href="{{ route('cash.withdraw') }}">
                                <span class="glyphicon glyphicon-minus-sign"></span><span class="text">Withdraw
                                    Cash</span>
                            </a>
                        </li>
                        {{-- @if (!$user->hasRole('super-admin')) --}}
                        {{-- <li> --}}
                        {{-- <a href="{{URL::to('/blance-transfer')}}"> --}}
                        {{-- <span class="glyphicon glyphicon-plus"></span><span class="text">Balance Transfer</span> --}}
                        {{-- </a> --}}
                        {{-- </li> --}}
                        {{-- @endif --}}
                    @endif
                </ul>
            </li>
        @endif

        <!-- Bank Section -->
        @if ($user->branchId == '')
            @if (
                $user->hasRole(['super-admin']) ||
                    $user->can('add-bank-info') ||
                    $user->can('show-bank-info') ||
                    $user->can('add-bank-investment') ||
                    $user->can('withdraw-bank-amount') ||
                    $user->can('add-bank-installment-info') ||
                    $user->can('view-bank-installments'))
                <li class="openable {{ request()->is('bank/*') ? 'active' : '' }}">
                    <a href="#">
                        <span class="isw-folder"></span><span class="text">Bank</span>
                    </a>
                    <ul>
                        @if ($user->hasRole(['super-admin']) || $user->can('show-bank-info'))
                            <li class="{{ \Route::is('bank.index') ? 'active' : '' }}">
                                <a href="{{ route('bank.index') }}">
                                    <span class="glyphicon glyphicon-th-list"></span><span class="text">Bank
                                        Information</span>
                                </a>
                            </li>
                        @endif

                        @if ($user->hasRole(['super-admin']) || $user->can('add-bank-investment'))
                            <li class="{{ \Route::is('bank.investment') ? 'active' : '' }}">
                                <a href="{{ route('bank.investment') }}">
                                    <span class="glyphicon glyphicon-plus-sign"></span><span class="text">Bank
                                        Investments</span>
                                </a>
                            </li>
                        @endif

                        @if ($user->hasRole(['super-admin']) || $user->can('withdraw-bank-amount'))
                            <li class="{{ \Route::is('bank.withdraw.index') ? 'active' : '' }}">
                                <a href="{{ route('bank.withdraw.index') }}">
                                    <span class="glyphicon glyphicon-minus-sign"></span><span class="text">Withdraw
                                        Bank Balance</span>
                                </a>
                            </li>
                        @endif

                        @if ($user->hasRole(['super-admin']) || $user->can('add-bank-installment-info'))
                            <li class="{{ \Route::is('bank.installment.create') ? 'active' : '' }}">
                                <a href="{{ route('bank.installment.create') }}">
                                    <span class="glyphicon glyphicon-info-sign"></span><span class="text">Add Bank
                                        Loan</span>
                                </a>
                            </li>
                        @endif

                        @if ($user->hasRole(['super-admin']) || $user->can('view-bank-installments'))
                            <li class="{{ \Route::is('bank.installment.index') ? 'active' : '' }}">
                                <a href="{{ route('bank.installment.index') }}">
                                    <span class="glyphicon glyphicon-tasks"></span><span class="text">Bank
                                        Loans</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
        @endif


        <!-- Reports Section -->
        <li class="openable {{ request()->is('report/*') ? 'active' : '' }}">
            <a href="#">
                <span class="isw-documents"></span><span class="text">Reports</span>
            </a>
            <ul>
                <li class="{{ \Route::is('report.customer') ? 'active' : '' }}">
                    <a href="{{ route('report.customer') }}">
                        <span class="glyphicon glyphicon-apple"></span><span class="text">All Customer
                            Balances</span>
                    </a>
                </li>
                    @if ($user->hasRole(['super-admin']) || $user->can('monthly-report'))
                        <li>
                            <a href="{{ route('monthly.report.index') }}">
                                <span class="glyphicon glyphicon-calendar"></span>
                                <span class="text">Monthly Customer Report</span>
                            </a>
                        </li>
                    @endif

                <li class="{{ \Route::is('report.supplier') ? 'active' : '' }}">
                    <a href="{{ route('report.supplier') }}">
                        <span class="glyphicon glyphicon-apple"></span><span class="text">All Supplier
                            Balances</span>
                    </a>
                </li>

                  @if ($user->hasRole(['super-admin']) || $user->can('cash-in-hand-statement-report'))
                    <li>
                        <a href="{{ route('monthly.supplier.report.index') }}">
                            <span class="glyphicon glyphicon-calendar"></span><span class="text">Monthly Supplier Report</span>
                        </a>
                    </li>
                @endif

                @if ($user->hasRole(['super-admin']) || $user->can('cash-in-hand-statement-report'))
                    <li>
                        <a href="{{ route('cash.statement') }}">
                            <span class="glyphicon glyphicon-fire"></span><span class="text">Cash Statement</span>
                        </a>
                    </li>
                @endif

                @if ($user->hasRole(['super-admin']) || $user->can('bank-statement-report'))
                    <li>
                        <a href="{{ route('bank.statement') }}">
                            <span class="glyphicon glyphicon-piggy-bank"></span><span class="text">Bank
                                Statement</span>
                        </a>
                    </li>
                @endif

                @if ($user->hasRole(['super-admin']))
                    <li>
                        <a href="{{ route('admin.fund.transfer.statement') }}">
                            <span class="glyphicon glyphicon-transfer"></span><span class="text">Fund Transfer
                                Statement</span>
                        </a>
                    </li>
                @endif

                @if ($user->hasRole(['super-admin']) || $user->can('expense-report'))
                    <li class="{{ \Route::is('report.expense') ? 'active' : '' }}">
                        <a href="{{ route('report.expense') }}">
                            <span class="glyphicon glyphicon-chevron-down"></span><span class="text">Expense
                                Report</span>
                        </a>
                    </li>
                @endif

                @if ($user->hasRole(['super-admin']) || $user->can('income-report'))
                    <li class="{{ \Route::is('report.income') ? 'active' : '' }}">
                        <a href="{{ route('report.income') }}">
                            <span class="glyphicon glyphicon-chevron-up"></span><span class="text">Income
                                Report</span>
                        </a>
                    </li>
                @endif

                @if ($user->hasRole(['super-admin']))
                    <li class="{{ \Route::is('customer.discount') ? 'active' : '' }}">
                        <a href="{{ route('customer.discount') }}">
                            <span class="glyphicon glyphicon-record"></span><span class="text">Customer Discount
                                Report</span>
                        </a>
                    </li>
                @endif

                @if ($user->branchId == '')
                    @if ($user->hasRole(['super-admin']) || $user->can('investment-report'))
                        <li class="{{ \Route::is('report.investment') ? 'active' : '' }}">
                            <a href="{{ route('report.investment') }}">
                                <span class="glyphicon glyphicon-duplicate"></span><span class="text">Investment
                                    Report</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('balance-report'))
                        <li class="{{ \Route::is('report.balance') ? 'active' : '' }}">
                            <a href="{{ route('report.balance') }}">
                                <span class="glyphicon glyphicon-record"></span><span class="text">Balance
                                    Report</span>
                            </a>
                        </li>
                    @endif

                    {{-- @if ($user->can('overhead-report')) --}}
                    {{-- <li> --}}
                    {{-- <a href="{{URL::to('/overhead-report')}}"> --}}
                    {{-- <span class="glyphicon glyphicon-pushpin"></span><span class="text">Over Head Report</span> --}}
                    {{-- </a> --}}
                    {{-- </li> --}}
                    {{-- @endif --}}

                    {{-- @if ($user->hasRole(['super-admin']) || $user->can('profit-report')) --}}
                    {{-- <li class="{{ \Route::is('report.profit') ? 'active' : '' }}"> --}}
                    {{-- <a href="{{ route('report.profit') }}"> --}}
                    {{-- <span class="glyphicon glyphicon-pushpin"></span><span class="text">Profit Report</span> --}}
                    {{-- </a> --}}
                    {{-- </li> --}}
                    {{-- @endif --}}

                    @if ($user->hasRole('super-admin') || $user->can('report-profit-loss'))
                        <li class="{{ \Route::is('report.pl') ? 'active' : '' }}">
                            <a href="{{ route('report.pl') }}">
                                <span class="glyphicon glyphicon-refresh"></span><span class="text">PL Report</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole('super-admin') || $user->can('report-balance-sheet'))
                        <li class="{{ \Route::is('report.balance.sheet') ? 'active' : '' }}">
                            <a href="{{ route('report.balance.sheet') }}">
                                <span class="glyphicon glyphicon-book"></span><span class="text">Balance
                                    Sheet</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole('super-admin') || $user->can('report-trial-balance'))
                        <li class="{{ \Route::is('report.trial.balance') ? 'active' : '' }}">
                            <a href="{{ route('report.trial.balance') }}">
                                <span class="glyphicon glyphicon-retweet"></span><span class="text">Trial
                                    Balance</span>
                            </a>
                        </li>
                    @endif
                    @if ($user->hasRole('super-admin'))
                        <li>
                            <a href="{{ route('activity.log') }}" target="_blank">
                                <span class="glyphicon glyphicon-th-list"></span><span class="text">Activity
                                    Log</span>
                            </a>
                        </li>
                    @endif
                @endif

            </ul>
        </li>



        {{--        Fund Transfer section --}}
        @if ($user->hasRole(['super-admin']))
            <li>
                <a href="{{ route('admin.fund.transfer.form') }}">
                    <span class="isw-refresh"></span><span class="text">Fund Transfer</span>

                </a>
            </li>
        @endif




        <!-- Settings Section -->
        @if ($user->branchId == '')
            <li class="openable {{ request()->is('settings/*') ? 'active' : '' }}">
                <a href="#">
                    <span class="isw-settings"></span><span class="text">Settings</span>
                </a>
                <ul>
                    @if ($user->hasRole('super-admin'))
                        <li class="{{ \Route::is('config.index') ? 'active' : '' }}">
                            <a href="{{ route('config.index') }}">
                                <span class="glyphicon glyphicon-cog"></span><span class="text">General
                                    Settings</span>
                            </a>
                        </li>
                    @endif
                    @if (
                        $user->hasRole(['super-admin']) ||
                            $user->can('branch-list') ||
                            $user->can('branch-create') ||
                            $user->can('branch-edit') ||
                            $user->can('branch-delete'))
                        <li class="{{ \Route::is('branches.index') ? 'active' : '' }}">
                            <a href="{{ route('branches.index') }}">
                                <span class="glyphicon glyphicon-th-large"></span><span class="text">Branches</span>
                            </a>
                        </li>
                    @endif
                    @if ($user->hasRole(['super-admin']) || $user->can('user-create'))
                        <li class="{{ \Route::is('user.create') ? 'active' : '' }}">
                            <a href="{{ route('user.create') }}">
                                <span class="glyphicon glyphicon-plus"></span><span class="text">Add User</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->hasRole(['super-admin']) || $user->can('user-list'))
                        <li class="{{ \Route::is('user.index') ? 'active' : '' }}">
                            <a href="{{ route('user.index') }}">
                                <span class="glyphicon glyphicon-user"></span><span class="text">User List</span>
                            </a>
                        </li>
                    @endif
                    @if ($user->hasRole(['super-admin']) || $user->can('role-create'))
                        <li class="{{ \Route::is('role.create') ? 'active' : '' }}">
                            <a href="{{ route('role.create') }}">
                                <span class="glyphicon glyphicon-plus"></span><span class="text">Add User
                                    Role</span>
                            </a>
                        </li>
                    @endif
                    @if ($user->hasRole(['super-admin']) || $user->can('role-list'))
                        <li class="{{ \Route::is('role.index') ? 'active' : '' }}">
                            <a href="{{ route('role.index') }}">
                                <span class="glyphicon glyphicon-repeat"></span><span class="text">View User
                                    Role</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif






    </ul>

    <div class="dr"><span></span></div>

    <h5 align="center"><b>Made With <i class="glyphicon glyphicon-heart"></i> by <a href="http://deelko.com"
                target="_blank">Deelko</a></b></h5>
</div>
