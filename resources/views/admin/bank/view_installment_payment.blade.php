@extends('admin.layouts.master')
@section('title', 'View Installment Payments')
@section('breadcrumb', 'View Installment Payments')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('pay-installments')))
        <li><a href="{{ route('bank.installment.payment.create',$installment_infos->id) }}"><span class="glyphicon glyphicon-plus"></span> Add Loan Payment</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="wBlock clearfix">
                    <div class="dSpace">
                        <h4>{{ $installment_infos->bank_info->bank_name }}</h4>
                        <span class="number"> {{ $installment_infos->installment_name }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="wBlock red">
                    <div class="dSpace">
                        <h4>TOTAL LOAN</h4>
                        <span class="number">{{ number_format($installment_infos->total_loan,2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wBlock green">
                    <div class="dSpace">
                        <h4>TOTAL PAID</h4>
                        <span class="number">{{ number_format($installment_infos->total_loan_paid,2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wBlock yellow">
                    <div class="dSpace">
                        <h4>TOTAL LOAN DUE</h4>
                        <span class="number">{{ number_format($installment_infos->total_loan - $installment_infos->total_loan_paid,2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wBlock">
                    <div class="dSpace">
                        <h4>REMAIN INSTALLMENT</h4>
                        <span class="number">{{ $installment_infos->installment_number - $installment_infos->installment_paid }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>
                            Installment Payments:
                            <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- All':'- '. request('date_range') }}</span>
                        </h1>
                    </div>

                    <div class="col-md-6 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-6">
                                    <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}" class="form-control" placeholder="Enter Search Text" />
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"  class="form-control" placeholder="Date Range" />
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default search-btn">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Sl no</th>
                            <th>Payment Date</th>
                            <th>Payment Mode</th>
                            <th>Cheque/Receipt no</th>
                            <th>Cheque/Receipt Date</th>
                            <th>Paid Amount</th>
                            <th>Description</th>
                            <th>Download</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; $total_paid = 0;?>
                        @foreach($installment_logs as $log)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ date('d-M-y',strtotime($log->posting_date)) }}</td>
                                <td>{{ $log->payment_mode }}</td>
                                <td>{{ $log->cheque_no }}</td>
                                <td>{{ date('d-M-y',strtotime($log->cheque_date)) }}</td>
                                <td>{{ number_format($log->paid_amount,2) }}</td>
                                <td>{{ $log->description }}</td>
                                <td>
                                    <?php $files = explode(",", $log->file); ?>
                                    @if(!empty($log->file))
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle" aria-expanded="false">
                                            Download Files <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach ($files as $file)
                                            @if($file != "")
                                                <li><a href="{{ URL::to('/img/files/pay_installment_files/'.$file) }}" target="_blank" rel="tag">{{ $file }}</a></li>
                                            @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <!--<a href="#fModal" role="button" class="glyphicon glyphicon-pencil" data-toggle="modal"></a>&nbsp&nbsp-->
                                    @if($user->hasRole('super-admin') || $user->can('delete-installment-payments'))
                                        <a href="{{ route('bank.installment.payment.delete',$log->transaction_id) }}" onclick ="return confirm('Are you sure you want to delete?')" class="fa fa-trash"></a>
                                    @endif
                                </td>
                            </tr>
                            <?php $i++; $total_paid += $log->paid_amount;?>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><b>Total = </b></td>
                            <td><b>{{ number_format($total_paid,2) }}</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                 </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

@endsection

@section('page-script')
    <script>
        jQuery(document).ready(function($) {

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'bank_ins'])!!}',
                minLenght:1,
                autoFocus:true,

            });
        });
    </script>
@endsection





