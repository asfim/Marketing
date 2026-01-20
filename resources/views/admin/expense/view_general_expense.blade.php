@extends('admin.layouts.master')
@section('title', 'View General Expenses')
@section('breadcrumb', 'View General Expenses')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole(['super-admin']) || $user->can('add-general-expense'))
        <li><a href="{{ route('expense.general.create') }}"><span class="glyphicon glyphicon-plus"></span> Add General Expense</a></li>
    @endif
    @if($user->hasRole(['super-admin']) || $user->can('add-production-expense'))
        <li><a href="{{ route('expense.production.create') }}"><span class="glyphicon glyphicon-plus-sign"></span> Add Production Expense</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>
                            View General Expenses
                            <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}</span>
                        </h1>
                    </div>

                    <div class="col-md-12 search_box" style="margin-top: 4px">
                        <form action="" class="form-horizontal">
                            <div class="row justify-content-end">

                                {{-- Branch Dropdown --}}
                                <div class="col-md-3">
                                    @if($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('branch-list')))
                                        <select name="branchId" id="branchId" class="form-control">
                                            <option value="">All Branch</option>
                                            <option value="head_office" {{ request('branchId')=='head_office'?'selected':'' }}>
                                                ** Head Office Only **
                                            </option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ request('branchId')==$branch->id?'selected':'' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}" />
                                    @endif
                                </div>

                                {{-- Customer Dropdown --}}
                                <div class="col-md-3">
                                    <select name="customer_id" id="customer_id" class="form-control">
                                        <option value="">Customer Name</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request('customer_id')==$customer->id?'selected':'' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Search Text --}}
                                <div class="col-md-2">
                                    <input type="text"
                                           name="search_text"
                                           id="search_name"
                                           value="{{ request('search_text')??'' }}"
                                           class="form-control"
                                           placeholder="Enter Search Text" />
                                </div>

                                {{-- Date Range + Button --}}
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text"
                                               name="date_range"
                                               id="date_range"
                                               value="{{ request('date_range')??'' }}"
                                               class="form-control"
                                               placeholder="Date Range"
                                               autocomplete="off"/>
                                        <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </span>
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
                            <th>Trx Id</th>
                            <th>Expense Name</th>
                            <th>Customer Name</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Branch</th>
                            <th>Download</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $ex_total = 0;?>
                        @foreach($expenses as $expense)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $expense->transaction_id }}</td>
                                <td>{{ $expense->expense_name }}</td>
                                <td>{{ optional($expense->engineer_tips_statements)->customer_name ?? '-' }}</td>
                                <td>{{ $expense->expense_type->type_name }}</td>
                                <td>{{ date('d-M-y', strtotime($expense->date)) }}</td>
                                <td>{{ number_format($expense->amount,2) }}</td>
                                <td>{{ $expense->description }}</td>
                                <td>{{ $expense->branch->name??'-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="badge badge-success dropdown-toggle" aria-expanded="false">Files <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <?php $file_text = $expense->file; $files = explode(",", $file_text) ;?>
                                            @foreach ($files as $file)
                                                @if($file != "")
                                                    <li><a href="{{ URL::to('/img/files/expense_files/general/'.$file) }}" target="_blank" rel="tag">{{ $file }}</a></li>
                                               @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                                <td class="hidden-print">
                                    @if(!$expense->expense_type->is_system)
                                        @if($user->hasRole('super-admin') || $user->can('edit-expense'))
                                            <a role="button" class="edit-btn"
                                               data-id="{{$expense->id}}"
                                               data-expense_name="{{$expense->expense_name}}"
                                               data-expense_type_id="{{$expense->expense_type_id}}"
                                               data-customer_id="{{ optional($expense->engineer_tips_statements)->customer_id }}"
                                               data-date="{{$expense->date}}"
                                               data-amount="{{$expense->amount}}"
                                               data-description="{{$expense->description}}"
                                               data-target="#editModal"
                                               data-toggle="modal">
                                                <span class="fa fa-edit"></span>
                                            </a>
                                        @endif
                                        @if($user->branchId =='' && ($user->hasRole('super-admin') || $user->can('delete-expense')))
                                            <a href="{{ route('expense.delete',$expense->transaction_id) }}" onclick ="return confirm('Are you sure you want to delete?');" class="fa fa-trash"></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <?php $ex_total += $expense->amount;?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr style="background-color:#999999; color: #fff;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total:</b></td>
                            <td><b>{{ number_format($ex_total,2) }}</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="hidden-print"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

    <!-- EDIT expense MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Expense</h4>
                </div>
                <form action="{{ route('expense.update') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        {{csrf_field()}}
                        <input type="hidden" id="id" name="id" value="">

                       <div class="row-form clearfix">
                            <label class="col-md-3">Expense Name:* </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="expense_name" id="expense_name" required/>
                            </div>
                        </div>

                        <div class="row-form clearfix" id="customer-row">
                            <label class="col-md-3">Customer Name: </label>
                            <div class="col-md-6">
                                <select name="customer_id" id="customer_id">
                                    <option value="">choose customer name</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="row-form clearfix">
                            <label class="col-md-3">Expense Type:* </label>
                            <div class="col-md-6">
                                <select name="expense_type_id" id="expense_type_id" required>
                                    <option value="">choose expense type</option>
                                    @foreach($expense_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Date:* </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="date" class="datepicker" id="date" required/>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3"> Amount:* </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="amount" id="amount" required/>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Description: </label>
                            <div class="col-md-6">
                                <textarea type="text" name="description" id="description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: center;">
                        <button class="btn btn-primary" type="submit">Save Updates</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
 @endsection

@section('page-script')
    <script>
        $(document).ready(function(e) {

            $(document).on('click','.edit-btn', function(){
                $('#id').val($(this).data('id'));
                $('#expense_name').val($(this).data('expense_name'));
                // Only show customer if expense_type_id == 113

                if ($(this).data('expense_type_id') == 113) {
                    $('#customer-row').show(); // show dropdown
                    $('#customer_id').val($(this).data('customer_id')).trigger('change');
                } else {
                    $('#customer-row').hide(); // hide dropdown
                    $('#customer_id').val('').trigger('change'); // reset
                }

                $('#expense_type_id').val($(this).data('expense_type_id'));
                $('#amount').val($(this).data('amount'));
                $('#date').val($(this).data('date'));
                $('#description').html($(this).data('description'));
            });


            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'expense_types'])!!}',
                minLenght:1,
                autoFocus:true,

            });
        });
    </script>
@endsection

