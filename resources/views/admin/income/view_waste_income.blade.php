@extends('admin.layouts.master')
@section('title', 'View Waste Incomes')
@section('breadcrumb', 'View Waste Incomes')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole(['super-admin']) || $user->can('add-general-income'))
        <li><a href="{{ route('income.general.create') }}"><span class="glyphicon glyphicon-plus"></span> Add General Income</a></li>
    @endif
    @if($user->hasRole(['super-admin']) || $user->can('add-waste-income'))
        <li><a href="{{ route('income.waste.create') }}"><span class="glyphicon glyphicon-plus-sign"></span> Add Waste Income</a></li>
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
                            View Waste Incomes
                            <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 30 Days':'- '. request('date_range') }}</span>
                        </h1>
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
                                    <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}" class="form-control" placeholder="Enter Search Text" />
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"
                                               class="form-control" placeholder="Date Range" autocomplete="off"/>
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
                            <th>Trx Id</th>
                            <th>Income Name</th>
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
                        @foreach($incomes as $income)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $income->transaction_id }}</td>
                                <td>{{ $income->income_name }}</td>
                                <td>{{ $income->income_type->type_name }}</td>
                                <td>{{ date('d-M-y', strtotime($income->date)) }}</td>
                                <td>{{ number_format($income->amount,2) }}</td>
                                <td>{{ $income->description }}</td>
                                <td>{{ ($income->branch) ? $income->branch->branchName : ''}}</td>
                                <td>
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="badge badge-success dropdown-toggle" aria-expanded="false">Files <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <?php $file_text = $income->file; $files = explode(",", $file_text) ;?>
                                            @foreach ($files as $file)
                                                @if($file != "")
                                                    <li><a href="{{ URL::to('/img/files/income_files/waste/'.$file) }}" target="_blank" rel="tag">{{ $file }}</a></li>
                                               @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                                <td class="hidden-print">
                                @if($user->hasRole('super-admin') || $user->can('edit-income'))
                                    <a role="button" class="edit-btn"
                                       data-id="{{$income->id}}"
                                       data-income_name="{{$income->income_name}}"
                                       data-income_type_id="{{$income->income_type_id}}"
                                       data-date="{{$income->date}}"
                                       data-amount="{{$income->amount}}"
                                       data-description="{{$income->description}}"
                                       data-target="#editModal"
                                       data-toggle="modal">
                                        <span class="fa fa-edit"></span>
                                    </a>
                                @endif

                                @if($user->branchId =='' && ($user->hasRole('super-admin') || $user->can('delete-income')))
                                    <a href="{{ route('income.delete',$income->transaction_id) }}" onclick ="return confirm('Are you sure you want to delete?');" class="fa fa-trash"></a>
                                @endif
                                </td>
                            </tr>
                            <?php $ex_total += $income->amount;?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr style="background-color:#999999; color: #fff;">
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
    <!-- Bootrstrap edit modal form -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Income</h4>
                </div>
                <form action="{{ route('income.update') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        {{csrf_field()}}
                        <input type="hidden" id="id" name="id" value="">
                        <div class="row-form clearfix">
                            <label class="col-md-3">Income Name:* </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="income_name" id="income_name" required/>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Income Type:* </label>
                            <div class="col-md-6">
                                <select name="income_type_id" id="income_type_id" required>
                                    <option value="">choose income type</option>
                                    @foreach($income_types as $type)
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
                $('#income_name').val($(this).data('income_name'));
                $('#income_type_id').val($(this).data('income_type_id'));
                $('#amount').val($(this).data('amount'));
                $('#date').val($(this).data('date'));
                $('#description').val($(this).data('description'));
            });

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'income_types'])!!}',
                minLenght:1,
                autoFocus:true,

            });
        });
    </script>
@endsection

