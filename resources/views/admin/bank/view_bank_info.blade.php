@extends('admin.layouts.master')
@section('title', 'Bank Information')
@section('breadcrumb', 'Bank Information')
@section('content')
<?php $user   = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole('super-admin') || $user->can('add-bank-info'))
        <li><a data-target="#createModal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add Bank Info</a></li>
    @endif
@endsection
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Bank Information</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Bank Name</th>
                            <th>Account Name</th>
                            <th>Account No</th>
                            <th>Account Type</th>
                            <th>Branch Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($banks as $bank)
                            <tr>
                                <td>{{ $bank->bank_name }}</td>
                                <td>{{ $bank->account_name }}</td>
                                <td>{{ $bank->account_no }}</td>
                                <td>{{ $bank->account_type }}</td>
                                <td>{{ $bank->branch_name }}</td>
                                <td>{{ $bank->description }}</td>
                                <td><?php if($bank->status == 1) echo 'Active'; else echo 'Inactive';?></td>
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('edit-bank-info'))
                                        <a href="#bModal{{$i}}" role="button" class="fa fa-edit" data-toggle="modal"></a>
                                    @endif

                                    @if($user->hasRole('super-admin') || $user->can('delete-bank-info'))
                                        <a href="{{ route('bank.delete',$bank->id) }}" onclick='return confirm("Are you sure you want to delete?");' class="fa fa-trash"></a>
                                    @endif
                                </td>
                            </tr>
                            <?php $i++; ?>
                        @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="dr"><span></span></div>
    </div>
    <!-- Bootrstrap modal form -->
    <?php  $j = 1;?>
    @foreach ($banks as $bank)
        <div class="modal fade" id="bModal<?php echo $j;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4>Edit Bank Info</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                <form action="{{ route('bank.update') }}" method="post" id="bank_edit_form<?php echo $j;?>" class="form-horizontal">
                                    {{csrf_field()}}
                                    <input type="hidden" name="id" value="<?php echo $bank->id;?>" />
                                    <div class="row-form clearfix">
                                        <div class="col-md-6">
                                            <label>Account Type</label>
                                            <select name="account_type" id="account_type" required>
                                                <option value="">choose a option...</option>
                                                <option value="Current" <?php if($bank->account_type == 'Current' ) echo 'selected';?>>Current</option>
                                                <option value="Loan" <?php if($bank->account_type == 'Loan' ) echo 'selected';?>>Loan</option>
                                                <option value="Savings" <?php if($bank->account_type == 'Savings' ) echo 'selected';?>>Savings</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Bank Name</label>
                                            <input type="text" value="{{$bank->bank_name}}" name="bank_name" id="bank_name" required/>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <label>Account Name</label>
                                                <input type="text" value="{{$bank->account_name}}" name="account_name" id="account_name" required/>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Account Number</label>
                                                <input type="text" value="{{$bank->account_no}}" name="account_no" id="account_no" required/>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Branch Name</label>
                                            <input type="text" value="{{$bank->branch_name}}" name="branch_name" id="branch_name" required/>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Status</label>
                                            <select name="status" id="status" required>
                                                <option value="">choose a option...</option>
                                                <option <?php if($bank->status == 1 ) echo 'selected';?> value="1">Active</option>
                                                <option value="0" <?php if($bank->status == 0 ) echo 'selected';?>>Inactive</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <label>Description</label>
                                            <textarea name="description" id="description">{{$bank->description}}</textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: center;">
                        <button class="btn btn-primary" type="submit" form="bank_edit_form<?php echo $j;?>" aria-hidden="true">Save Updates</button>
                        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php $j++;?>
    @endforeach

    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Add Bank Info</h4>
                </div>
                <form action="{{ route('bank.store') }}" method="post" class="form-horizontal">
                    @csrf
                    <div class="modal-body">
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Account Type</label>
                                <select name="account_type" id="account_type" required="">
                                    <option value="">choose an option...</option>
                                    <option value="Current">Current</option>
                                    <option value="Loan">Loan</option>
                                    <option value="Savings">Savings</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Bank Name</label>
                                <input type="text" required value="" name="bank_name" id="bank_name"/>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Account Name</label>
                                <input type="text" value="" required name="account_name" id="account_name"/>
                            </div>
                            <div class="col-md-6">
                                <label>Account Number</label>
                                <input type="text"name="account_no" id="account_no" pattern= "[0-9.-]*" placeholder="only 0-9 digit and -,."  required />
                            </div>
                            <div class="col-md-6">
                                <label>Branch Name</label>
                                <input type="text" value="" required name="branch_name" id="branch_name"/>
                            </div>
                            <div class="col-md-12">
                                <label>Description</label>
                                <textarea name="description" id="description"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="text-align: center;">
                        <button class="btn btn-primary" type="submit" aria-hidden="true">Save Bank Info</button>
                        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
    </script>
@endsection
