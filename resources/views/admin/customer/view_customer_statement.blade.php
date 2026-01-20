@extends('admin.layouts.master')
@section('title', 'Customer Statement')
@section('breadcrumb', 'Customer Statement')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h3>
                            View Customer Statement
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
                                    <input type="text" name="search_text" id="search_name" class="form-control" value="{{ request('search_text')??'' }}" placeholder="Enter Customer Name" />
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
                        {{--<h4 align="center">{{$customer_name}}</h4>--}}
                        {{--<h5 align="center">{{$customer_address}}</h5>--}}
                        {{--<h5>{{$date_info}}</h5>--}}
                    </div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th>Trx Id</th>
                            <th>Date</th>
                            <th>Cus Name</th>
                            <th>Description</th>
                            <th>Qty(CFT)</th>
                            <th>Rate</th>
                            <th>Payment Details</th>
                            <th>Debit</th>
                            <th>Adjustment</th>
                            <th>Credit</th>
                            <th>Balance</th>
                            @if($user->branchId == '')
                                <th>Branch</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; $bal = 0; $debit = 0; $total_adjustment =0; $credit = 0; $total_qty= 0; ?>
                        @foreach ($statements as $statement)
                            <?php
                            $bill_date = "";

                            $payment_details ="";
                            $ref_date = "";
                            $adjustment=0;

                            $pay_rowb = $statement->bill;
                            $pay_row = $statement->customer_payment;

                            if($pay_row != "")
                            {
                                $adjustment = round($pay_row->adjustment_amount,3);
                                $ref_date = $pay_row->ref_date;

                                if($pay_row->bank_id != ""){
                                    $payment_details = "{$pay_row->payment_mode}:{$pay_row->bank_info->short_name}, AC no: {$pay_row->bank_info->account_no}, Cheque/Receipt no: {$pay_row->cheque_no}";
                                }else{
                                    $ref_date = $pay_row->ref_date;
                                    $payment_details = "Cash";
                                }

                            }

                            $qty = 0;$rate = 0;
                            $bill_row = $statement->bill;
                            if($bill_row != "") {
                                $rate = round($bill_row->rate,3);
                            }
                            if($bill_row != "") {
                                $qty = round($bill_row->total_cft,3);
                            }

                            ?>
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $statement->transaction_id }}</td>

                                <td>
                                    <?php
                                    $trans_id = $statement->transaction_id;
                                    $eti= explode("-",$trans_id);
                                    $sep_trans_id=($eti[0]);
                                    if($sep_trans_id=='CBILL') echo date('d-M-y', strtotime($pay_rowb->bill_date));

                                    if($sep_trans_id=='CUSP') echo date('d-M-y', strtotime($ref_date));
                                    ?>
                                </td>
                                <td>{{ $statement->customer->name }}</td>
                                <td>{{ $statement->description }}</td>
                                <td>{{ $qty }}</td>
                                <td>{{ $rate }}</td>
                                <td>{{ $payment_details }}</td>
                                <td>{{ number_format($statement->debit  - $adjustment,2) }}</td>
                                <td>{{ number_format($adjustment,2) }}</td>
                                <td>{{ number_format($statement->credit,2) }}</td>
                                <td>
                                    {{ number_format($statement->balance,2) }}
                                </td>
                                @if($user->branchId == '')
                                    <td>{{ $statement->branch->name??'-' }}</td>
                                @endif
                            </tr>
                            <?php
                            $i++;
                            $total_qty += $qty;
                            $debit += $statement->debit - $adjustment;
                            $total_adjustment+=$adjustment;
                            $credit += $statement->credit - $adjustment;
                            ?>

                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr style="background-color:#999999; color: #fff;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total:</b></td>
                            <td><b>{{number_format($total_qty,2)}}</b></td>
                            <td></td>
                            <td></td>
                            <td><b>{{number_format($debit,2)}}</b></td>
                            <td><b>{{number_format($total_adjustment,2)}}</b></td>
                            <td><b>{{number_format($credit,2)}}</b></td>
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
                source : '{!!URL::route('autoComplete',['table_name' => 'customers'])!!}',
                minLenght:1,
                autoFocus:true,
            });
        } );
    </script>
@endsection
