@extends('admin.layouts.master')
@section('title', 'Trial Balance')
@section('breadcrumb', 'Trial Balance')
@section('content')
    <div class="workplace">

        <div class="row" id="trial-balance">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>
                            Trial Balance
                            <span class="src-info">{{ request('date_range') == ''?'':'- '. request('date_range') }}</span>
                        </h1>
                    </div>

                    <div class="col-md-5 search_box hidden-print" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"  autocomplete="off" class="form-control" placeholder="Date Range" />
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default search-btn">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


                    <div class="col-md-2 text-right" style="margin-top: 4px;">
                        <span class="btn btn-sm btn-info hidden-print" onclick="printContent('trial-balance')"> <i class="fa fa-print"></i></span>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" style="font-size: 16px;">
                        <thead>
                        <tr>
                            <th>Head Of Accounts </th>
                           
                            <th class="text-right">Debit</th>
                             <th class="text-right">Credit</th>
                            <th class="text-right">Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr style="font-weight: bold;">
                            <td colspan="4">Cash In Hand</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 50px !important;">*** MAIN BRANCH ***</td>
                              <td class="text-right">{{ number_format($data['main_cash_balance']->debit,2) }}</td>
                            <td class="text-right">{{ number_format($data['main_cash_balance']->credit,2) }}</td>
                                                      @php($_main_cash_balance = $data['main_cash_balance']->credit - $data['main_cash_balance']->debit)
                            <td class="text-right">{{ number_format($_main_cash_balance,2) }}</td>
                        </tr>
                        <?php
                            $total_cash_credit=$data['main_cash_balance']->credit;
                            $total_cash_debit=$data['main_cash_balance']->debit;
                            $total_cash_balance=$_main_cash_balance;
                        ?>
                        @foreach($data['branch_balances'] as $branch)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $branch->name }}</td>
                                 <td class="text-right">{{ number_format($branch->debit,2) }}</td>
                                <td class="text-right">{{ number_format($branch->credit,2) }}</td>                          
                                @php($_balance = $branch->credit-$branch->debit)
                                {{-- <td class="text-right">{{ number_format($_balance,2) }}</td> --}}
                                <td class="text-right">      @if($_balance < 0)
                                        ({{ number_format(abs($_balance), 2) }})
                                    @else
                                        {{ number_format($_balance, 2) }}
                                    @endif</td>
                            </tr>
                            </tr>
                            <?php
                                $total_cash_debit+=$branch->debit;
                                $total_cash_credit+=$branch->credit;
                                $total_cash_balance+=$_balance;
                            ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">{{ number_format($total_cash_debit,2) }}</td>
                            <td class="text-right">{{ number_format($total_cash_credit,2) }}</td>
                            <td class="text-right">{{ number_format($total_cash_balance,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">Cash At Bank</td>
                        </tr>
                        <?php $total_bank_credit=0;$total_bank_debit=0;$total_bank_balance=0; ?>
                        @foreach($data['bank_balances'] as $bank)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $bank->bank_name }}</td>
                                <td class="text-right">{{ number_format($bank->debit,2) }}</td>
                                <td class="text-right">{{ number_format($bank->credit,2) }}</td>
                                
                                @php($_balance = $bank->credit-$bank->debit)
                                {{-- <td class="text-right">{{ number_format($_balance,2) }}</td> --}}
                                <td class="text-right">      @if($_balance < 0)
                                        ({{ number_format(abs($_balance), 2) }})
                                    @else
                                        {{ number_format($_balance, 2) }}
                                    @endif</td>
                            </tr>
                            </tr>
                            <?php
                                $total_bank_credit+=$bank->credit;
                                $total_bank_debit+=$bank->debit;
                                $total_bank_balance+=$_balance;
                            ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">{{ number_format($total_bank_debit,2) }}</td>
                            <td class="text-right">{{ number_format($total_bank_credit,2) }}</td>
                            
                            <td class="text-right">{{ number_format($total_bank_balance,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">Accounts/ Trade Receivable</td>
                        </tr>
                        <?php $total_customer_credit=0;$total_customer_debit=0;$total_customer_balance=0; ?>
                        @foreach($data['ac_receivable'] as $customer)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $customer->name }}</td>
                                <td class="text-right">{{ number_format($customer->debit,2) }}</td>
                                <td class="text-right">{{ number_format($customer->credit,2) }}</td>
                                
                                @php($_balance = $customer->credit-$customer->debit)
                                {{-- <td class="text-right">{{ number_format($_balance,2) }}</td> --}}
                                <td class="text-right">      @if($_balance < 0)
                                        ({{ number_format(abs($_balance), 2) }})
                                    @else
                                        {{ number_format($_balance, 2) }}
                                    @endif</td>
                            </tr>
                            <?php
                             $total_customer_debit+=$customer->debit;
                                $total_customer_credit+=$customer->credit;
                               
                                $total_customer_balance+=$_balance;
                            ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">{{ number_format($total_customer_debit,2) }}</td>
                            <td class="text-right">{{ number_format($total_customer_credit,2) }}</td>
                            
                            <td class="text-right">{{ number_format($total_customer_balance,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">General Stock</td>
                        </tr>
                        <?php $total_stock_value=0; ?>
                        @foreach($data['product_stocks'] as $product)
                            <tr>
                                <?php
                                    $_stock_value = $product->quantity * $product->product_name->unit_price;
                                ?>
                                <td style="padding-left: 50px !important;">{{ $product->product_name->name }}</td>
                                <td class="text-right">0.00</td>
                                <td class="text-right">{{ number_format($_stock_value,2) }}</td>
                                
                                <td class="text-right">{{ number_format($_stock_value,2) }}</td>
                            </tr>
                            <?php $total_stock_value+=$_stock_value; ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">0.00</td>
                            <td class="text-right">{{ number_format($total_stock_value,2) }}</td>
                            
                            <td class="text-right">{{ number_format($total_stock_value,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">Assets</td>
                        </tr>
                        <?php $total_asset_value=0; ?>
                        @foreach($data['asset_types'] as $asset_type)
                            <tr>
                                @php($asset_value = $asset_type->asset_value())
                                <td style="padding-left: 50px !important;">{{ $asset_type->name }}</td>
                                <td class="text-right">0.00</td>
                                <td class="text-right">{{ number_format($asset_value,2) }}</td>
                                
                                <td class="text-right">{{ number_format($asset_value,2) }}</td>
                            </tr>
                            <?php $total_asset_value+=$asset_value; ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">0.00</td>
                            <td class="text-right">{{ number_format($total_asset_value,2) }}</td>
                            
                            <td class="text-right">{{ number_format($total_asset_value,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">General Expenses</td>
                        </tr>
                        <?php $total_general_expense=0; ?>
                        
                        @foreach($data['general_expenses'] as $expense)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $expense->type_name }}</td>
                                
                                <td class="text-right">{{ number_format($expense->total_expense,2) }}</td>
                                <td class="text-right">0.00</td>
                                      {{-- Third Column with negative formatting --}}
                                <td class="text-right">
                                    @if($expense->total_expense < 0)
                                        ({{ number_format(abs($expense->total_expense), 2) }})
                                    @else
                                        {{ number_format($expense->total_expense, 2) }}
                                    @endif
                                </td>
                                {{-- <td class="text-right">{{ number_format($expense->total_expense,2) }}</td> --}}
                            </tr>
                            <?php $total_general_expense+=$expense->total_expense; ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            
                            <td class="text-right">{{ number_format($total_general_expense,2) }}</td>
                            <td class="text-right">0.00</td>
                            <td class="text-right">{{ number_format($total_general_expense,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">Production Expenses</td>
                        </tr>
                        <?php $total_production_expense=0; ?>
                        @foreach($data['production_expenses'] as $expense)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $expense->type_name }}</td>
                                
                                <td class="text-right">{{ number_format($expense->total_expense,2) }}</td>
                                <td class="text-right">0.00</td>
                                <td class="text-right">{{ number_format($expense->total_expense,2) }}</td>
                            </tr>
                            <?php $total_production_expense+=$expense->total_expense; ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            
                            <td class="text-right">{{ number_format($total_production_expense,2) }}</td>
                            <td class="text-right">0.00</td>
                            <td class="text-right">{{ number_format($total_production_expense,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">Accounts/ Trade Payable</td>
                        </tr>
                        <?php $total_supplier_credit=0;$total_supplier_debit=0;$total_supplier_balance=0; ?>
                        @foreach($data['ac_payable'] as $supplier)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $supplier->name }}</td>
                                <td class="text-right">{{ number_format($supplier->debit,2) }}</td>
                                <td class="text-right">{{ number_format($supplier->credit,2) }}</td>
                                
                                @php($_balance = $supplier->credit-$supplier->debit)
                                {{-- <td class="text-right">{{ number_format($_balance,2) }}</td> --}}
                                <td class="text-right">      @if($_balance < 0)
                                        ({{ number_format(abs($_balance), 2) }})
                                    @else
                                        {{ number_format($_balance, 2) }}
                                    @endif</td>
                            </tr>
                            <?php
                            $total_supplier_credit+=$supplier->credit;
                            $total_supplier_debit+=$supplier->debit;
                            $total_supplier_balance+=$_balance;
                            ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">{{ number_format($total_supplier_debit,2) }}</td>
                            <td class="text-right">{{ number_format($total_supplier_credit,2) }}</td>
                            
                            <td class="text-right">{{ number_format($total_supplier_balance,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">Bank Loans</td>
                        </tr>
                        <?php $total_bank_loan=0;$total_bank_loan_paid=0;$total_loan_balance=0; ?>
                        @foreach($data['bank_loans'] as $loan)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $loan->bank_name }}</td>
                                <td class="text-right">{{ number_format($loan->total_loan_paid,2) }}</td>
                                <td class="text-right">{{ number_format($loan->total_loan,2) }}</td>
                                
                                @php($_loan_balance = $loan->total_loan-$loan->total_loan_paid)
                                <td class="text-right">{{ number_format($_loan_balance,2) }}</td>
                            </tr>
                            <?php
                            $total_bank_loan_paid+=$loan->total_loan_paid;
                            $total_bank_loan+=$loan->total_loan;
                            
                            $total_loan_balance+=$_loan_balance;
                            ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">{{ number_format($total_bank_loan_paid,2) }}</td>
                            <td class="text-right">{{ number_format($total_bank_loan,2) }}</td>
                            
                            <td class="text-right">{{ number_format($total_loan_balance,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">General Incomes</td>
                        </tr>
                        <?php $total_general_income=0; ?>
                        @foreach($data['general_incomes'] as $income)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $income->type_name }}</td>
                                <td class="text-right">0.00</td>
                                <td class="text-right">{{ number_format($income->total_income,2) }}</td>
                                <td class="text-right">{{ number_format($income->total_income,2) }}</td>
                            </tr>
                            <?php $total_general_income+=$income->total_income; ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">0.00</td>
                            <td class="text-right">{{ number_format($total_general_income,2) }}</td>
                            <td class="text-right">{{ number_format($total_general_income,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">Waste Incomes</td>
                        </tr>

                        <?php $total_waste_income=0; ?>
                        @foreach($data['waste_incomes'] as $income)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $income->type_name }}</td>
                                <td class="text-right">0.00</td>
                                <td class="text-right">{{ number_format($income->total_income,2) }}</td>
                                <td class="text-right">{{ number_format($income->total_income,2) }}</td>
                            </tr>
                            <?php $total_waste_income+=$income->total_income; ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">0.00</td>
                            <td class="text-right">{{ number_format($total_waste_income,2) }}</td>
                            <td class="text-right">{{ number_format($total_waste_income,2) }}</td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="4">Raw Material Purchase</td>
                        </tr>
                        <?php $total_purchase_amount=0; ?>
                        @foreach($data['product_purchases'] as $purchase)
                            <tr>
                                <td style="padding-left: 50px !important;">{{ $purchase->name }}</td>
                                <td class="text-right">{{ number_format($purchase->total_material_cost,2) }}</td>
                                <td class="text-right">0.00</td>
                                <td class="text-right">{{ number_format($purchase->total_material_cost,2) }}</td>
                            </tr>
                            <?php $total_purchase_amount+=$purchase->total_material_cost; ?>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td class="text-right">Sub Group Total: </td>
                            <td class="text-right">{{ number_format($total_purchase_amount,2) }}</td>
                            <td class="text-right">0.00</td>
                            <td class="text-right">{{ number_format($total_purchase_amount,2) }}</td>
                        </tr>
                        </tbody>

                        <?php
                          $total_debit = $total_cash_debit+$total_bank_debit+$total_customer_debit+$total_supplier_debit+$total_bank_loan_paid+$total_general_income+$total_waste_income;
                            $total_credit = $total_cash_credit+$total_bank_credit+$total_customer_credit+$total_stock_value+$total_asset_value+$total_general_expense+$total_production_expense+$total_supplier_credit+$total_bank_loan+$total_purchase_amount;
                          
                            $total_balance = $total_cash_balance+$total_bank_balance+$total_customer_balance+$total_stock_value+$total_asset_value+$total_general_expense+$total_production_expense+$total_supplier_balance+$total_loan_balance+$total_general_income+$total_waste_income+$total_purchase_amount;
                        ?>
                        <tfoot>
                        <tr style="font-weight: bold; font-size: 20px;">
                            <td class="text-right">Grand Total: </td>
                            <td class="text-right">{{ number_format($total_debit,2) }}</td>
                            <td class="text-right">{{ number_format($total_credit,2) }}</td>
                            
                            <td class="text-right">{{ number_format($total_balance,2) }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>


            </div>
        </div>

    </div>
    </div>


    <div class="dr"><span></span></div>

    </div>

@endsection

@section('page-script')
    <script>

        $(document).ready(function() {

            $('#tSortable_2').DataTable({
                dom: 'lBrtip',
                buttons: [
                    {
                        extend: 'print',
                        text: 'Print page',
                        autoPrint: true,
                        exportOptions: {
                            columns: '1,2',
                        },
                        customize: function ( win ) {
                            $(win.document.body).find('.dataTable')
                                .after(
                                    $('#bank_result,#asset_result,#all_total')
                                );
                            $(win.document.body).find('h1').css('text-align','center');
                        },
                    }
                ],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],



            } );
        } );
    </script>
@endsection



