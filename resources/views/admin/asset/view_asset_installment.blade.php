@extends('admin.layouts.master')
@section('title', 'Asset Installment')
@section('breadcrumb', 'Asset Installment')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h3>View Asset Installment</h3>
                    @if($user->can('show-asstes'))
                        <ul class="buttons mini-nav">
                            <li class="tipb" data-original-title="Go Back" style="cursor: pointer;">
                                <a href="{{ route('asset.index') }}" class="isw-left_circle"></a>
                            </li>
                        </ul>
                    @endif
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Trx Id</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Payment Mode</th>
                            <th>Installment Amount</th>
                            <th>Branch</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($assetInstallments as $installment)
                            <tr>
                                <td>{{ $installment->transaction_id }}</td>
                                <td>{{ date('d-m-Y', strtotime($installment->date)) }}</td>
                                <td>{{ $installment->name }}</td>
                                <td>{{ $installment->description }}</td>
                                <td>{{ $installment->payment_mode }}</td>
                                <td>{{ number_format($installment->installment_amount,2) }}</td>
                                <td>
                                    @if($installment->branch)
                                        {{ $installment->branch->name }}
                                    @else
                                        MAIN BRANCH
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                        <tr style="background-color:#999999; color: #fff;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b></b></td>
                            <td><b>Total: </b></td>
                            <td><b>{{ 'BDT '.number_format($total_amount,2) }}</b></td>
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

{{--@section('page-script')--}}
    {{--<script>--}}

        {{--$(document).ready(function() {--}}
            {{--$("#search_name").autocomplete({--}}
                {{--source : '{!!URL::route('autoComplete',['table_name' => 'asset_installment'])!!}',--}}
                {{--minLenght:1,--}}
                {{--autoFocus:true,--}}

            {{--});--}}
        {{--} );--}}
    {{--</script>--}}
{{--@endsection--}}

