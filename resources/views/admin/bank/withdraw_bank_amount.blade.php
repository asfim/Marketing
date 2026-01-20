@extends('admin.layouts.master')
@section('title', 'Withdraw Bank Amount')
@section('breadcrumb', 'Withdraw Bank Amount')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole('super-admin') || $user->can('withdraw-bank-amount'))
        <li><a data-target="#createModal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Withdraw Bank Balance</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-5">
                        <div class="isw-documents"></div>
                        <h1>Total Bank Balance:
                            <span class="src-info">{{'BDT '. number_format($total_balance,2)}}</span> -
                            <span class="src-info">
                                @if(request()->filled('date_range'))
                                    ({{request()->get('date_range')}})
                                @else
                                    (Last 30 days)
                                @endif
                            </span>
                        </h1>
                    </div>
                    <div class="col-md-7 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-6">
                                    <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}"
                                           class="form-control" placeholder="Enter Search Text" autocomplete="off"/>
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
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th>Ref Date</th>
                            <th>Transaction Id</th>
                            <th>Bank Name</th>
                            <th>Description</th>
                            <th>Withdraw Cash</th>
                            <th>Branch</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $total = 0;?>
                        @foreach ($with_banks as $wbank)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ date('d-M-y',strtotime($wbank->ref_date)) }}</td>
                                <td>{{ $wbank->transaction_id }}</td>
                                <td>{{ $wbank->bank_info->bank_name }} Cheque No - {{ $wbank->cheque_no }}</td>
                                <td>{{ $wbank->description }}</td>
                                <td>{{ $wbank->debit }}</td>
                                <td>{{ $wbank->branch->name??'-' }}</td>
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('withdraw-bank-amount-delete'))
                                        <a href="{{ route('bank.investment.delete',$wbank->transaction_id) }}"
                                           onclick='return confirm("Are you sure you want to delete?");'
                                           class="fa fa-trash"></a>
                                    @endif

                                </td>
                            </tr>
                            <?php $total += $wbank->debit; ?>
                        @endforeach

                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total = </b></td>
                            <td><b>{{'BDT '. number_format($total,2)}}</b></td>
                            <td></td>
                            <td class="hidden-print"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="dr"><span></span></div>


    @if($user->hasRole('super-admin') || $user->can('withdraw-bank-amount'))
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Withdraw Bank Balance</h4>
                </div>
                <form action="{{ route('bank.withdraw.store')}}" method="post" id="bank_withdraw_form" class="form-horizontal">
                    <div class="modal-body">
                        <div class="row-form clearfix">
                            <div class="col-md-12">
                                <h5 class="color-h5 bold">Balance: <b id="balance"></b></h5>
                            </div>
                        </div>

                        {{csrf_field()}}
                        <div class="row-form clearfix" id="bank_info" style="display: block;">
                            <div class="col-md-6">
                                <label>Select Bank</label>
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="">----- choose a option -----</option>
                                    @foreach($banks as $bank)
                                        <option value="{{$bank->id}}" {{ (collect(old('bank_id'))->contains($bank->id)) ? 'selected':'' }}>{{$bank->bank_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Withdraw Balance For</label>
                                @if($user->hasRole(['super-admin']) || $user_data->can('branch-list'))
                                    <select name="branchId" id="branchId" class="form-control">
                                        <option value="">----- Select Branch -----</option>
                                        @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ (collect(old('branchId'))->contains($branch->id)) ? 'selected':'' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="branchId" id="branchId" value="{{ $user_data->branchId }}" />
                                    <input type="text" value="{{ $user_data->branch->name }}" readonly/>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label>Cheque no</label>
                                <input type="text" value="{{ old('cheque_no') }}" name="cheque_no" required class="form-control" id="cheque_no"/>
                            </div>

                            <div class="col-md-6">
                                <label>Check/Receipt Date</label>
                                <input type="text" value="{{ old('cheque_date') }}" name="cheque_date" id="cheque_date" class="datepicker"/>
                            </div>

                            <div class="col-md-6">
                                <label>Amount</label>
                                <input type="text" value="{{ old('debit') }}" name="debit" id="debit" class="form-control" required/>
                            </div>

                            <div class="col-md-6">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control" required>{{ old('description') }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer" style="text-align: center;">
                        <button class="btn btn-primary" type="submit" aria-hidden="true">Withdraw Balance</button>
                        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('page-script')
    <script>
        jQuery(document).ready(function($) {

//load bank amount according to bank
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#bank_id").on('change', function(){
                //$(this).preventDefault();
                //$(this).off("click").attr('href', "javascript: void(0);");
                var bank_id = $('#bank_id').val();
                $.ajax({
                    type:"POST",
                    url:"{{ route('bank.balance') }}",
                    data:{
                        _token: CSRF_TOKEN,
                        bank_id:bank_id

                    },
                    dataType: 'JSON',
                    success: function(resp){
                        $('#balance').html(resp.balance);
                    }
                });
            });

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'bank_infos'])!!}',
                minLenght:1,
                autoFocus:true,

            });

            $('#tSortable_2').DataTable({
                dom: 'lBrtip',
                "lengthMenu": [[100, 200, 500, 1000, -1], [100, 200, 500, 1000, "All"]],
                buttons: [
                    {
                        extend: 'print',
                        text: 'Print page',
                        autoPrint: true,
                        exportOptions: {
                            columns: '1,2,3,4,5,6',
                        },
                        customize: function ( win ) {
                            $(win.document.body).find('h1').css('text-align','center');
                        },
                    }
                ],

            } );
        });
    </script>
@endsection



