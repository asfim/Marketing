@extends('admin.layouts.master')
@section('title', 'Supplier Statement')
@section('breadcrumb', 'Supplier Statement')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h3>
                            View Supplier Statement
                            <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 30days':'- '. request('date_range') }}</span>
                        </h3>
                    </div>
                    <div class="col-md-8 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">

                            <div class="" align="right">
                                <div class="col-md-3">
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
                                <div class="col-md-4">
                                    <input type="text" name="search_text" id="search_name" class="form-control" value="{{ request('search_text')??'' }}" placeholder="Enter Supplier Name" />
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range" value="{{ old('date_range')??'' }}"
                                               class="form-control" placeholder="Date Range" autocomplete="off" />
                                        <div class="input-group-btn">
                                            <button type="submit" id="btn_search" class="btn btn-default search-btn">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <div id="statement_info">
                        {{--<h4 align="center">{{ $supplier->name }}</h4>--}}
                        {{--<h5 align="center">{{ $supplier->address }}</h5>--}}
                    </div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped" id="datatable">
                        <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th>Trx Id</th>
                            <th>Date</th>
                            <th>Sup Name</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Truck Rent</th>
                            <th>Unload Bill</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Adj Amount</th>
                            <th>Balance</th>
                            @if($user->branchId == '')
                            <th>Branch</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1;$debit = 0; $credit = 0; $qty = 0; $total_truck_rent = 0; $total_unload = 0; $total_adjustment_amount = 0; ?>
                        @foreach ($statements as $statement)
                            <?php
                                $pay_row = $statement->supplier_payment;

                                if($pay_row != "" && $pay_row->bank_id != ""){
                                    $payment_details = "{$pay_row->payment_mode}: {$pay_row->bank_info->short_name}, AC No:{$pay_row->bank_info->account_no},Cheque/Receipt no:{$pay_row->cheque_no}";
                                } else {
                                    $payment_details = 'Cash';
                                }

                                $adjustment_amount = 0;
                                if($pay_row != ""){
                                    $adjustment_amount=$pay_row->adjustment_amount;
                                }

                                $quantity = "";
                                $truck_rent = "";
                                $unload = "";
                                $pp_row = $statement->product_purchase;

                                if($pp_row != ""){
                                    $quantity = round($pp_row->product_qty,3)." ".$pp_row->unit_type;
                                    $truck_rent = $pp_row->truck_rent;
                                    $unload = $pp_row->unload_bill;
                                }

                                $trans_id = $statement->transaction_id;
                                $eti= explode("-",$trans_id);
                                $sep_trans_id=($eti[0]);
                                $bill_adjustment = 0;
                                if($sep_trans_id=='BILLAD'){
                                    $bill_adjustment = $statement->debit;
                                }

                                $two_adjust = $bill_adjustment + $adjustment_amount;
                            ?>

                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $statement->transaction_id }}</td>
                                <td>{{ date('d-M-y', strtotime($statement->posting_date)) }}</td>
                                <td>{{ $statement->supplier->name }}</td>
                                <td>{{ $statement->description }}</td>
                                <td>
                                    <?php if($sep_trans_id=='BILLAD' && $statement->adjustment_qty>0){
                                        echo -$statement->adjustment_qty;
                                    }else{
                                        echo $quantity;
                                    }
                                    ?>
                                </td>

                                <td>{{ number_format((float) $truck_rent, 2) }}</td>
                                <td>{{ number_format((float) $unload, 2) }}</td>
                                
                                <td>{{ number_format((float) ($statement->debit - $two_adjust), 2) }}</td>
                                <td>{{ number_format((float) ($statement->credit ), 2) }}</td>
                                <td>{{ number_format((float) $two_adjust, 2) }}</td>
                                <td>{{ number_format((float) $statement->balance, 2) }}</td>

                                @if($user->branchId == '')
                                    <td>{{ $statement->branch->name??'-' }}</td>
                                @endif

                            </tr>
                            <?php
                                $i++; $qty += (int)$quantity; $debit += (int)$statement->debit - $two_adjust;
                                $credit += (int)$statement->credit - $two_adjust; $total_truck_rent += (int)$truck_rent;
                                $total_unload += (int)$unload; $total_adjustment_amount += $two_adjust;
                            ?>
                        @endforeach

                        </tbody>
                        <tfoot>
                        <tr class="">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total:</b></td>
                            <td><b>{{ $qty - $total_adjustment_amount }}</b></td>
                            <td><b>{{ number_format($total_truck_rent,2) }}</b></td>
                            <td><b>{{ number_format($total_unload,2) }}</b></td>
                            
                            <td><b>{{ number_format($debit,2) }}</b></td>
                            <td><b>{{ number_format($credit,2) }}</b></td>
                            <td><b>{{ number_format($total_adjustment_amount,2) }}</b></td>
                            <td></td>
                            @if($user->branchId == '')
                                <td></td>
                            @endif
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

@endsection

@section('page-script')
    <script>
        $(document).ready(function() {

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'suppliers'])!!}',
                minLenght:1,
                autoFocus:true,
            });
        } );
    </script>
@endsection
