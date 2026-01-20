@extends('admin.layouts.master')
@section('title', 'Supplier List')
@section('breadcrumb', 'Supplier List')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole(['super-admin']) || $user->can('supplier-create'))
        <li><a data-target="#addModal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add Supplier</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>View Suppliers</h1>
                    </div>

                    <div class="col-md-4 text-center" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">

                            <div class="input-group">
                                <input type="text" name="search_text" id="search_name" class="form-control" placeholder="Enter Search Text" />
                                {{--<input id="appendedInputButton" class="form-control" type="text" autocomplete="off">--}}
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-default search-btn">Search</button>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
                <div class="block-fluid table-sorting clearfix">

                    <form action="{{ route('supplier.updateBalance') }}" method="POST">
                        @csrf
                        <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped" id="datatable">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Extra Phone</th>
                                <th>Address</th>
                                @if($user->branchId == '')
                                    <th>Balance</th>
                                @endif
                                <!--<th>New Balance</th>-->
                                <th class="hidden-print">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; ?>
                            @foreach ($suppliers as $supplier)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ $supplier->extra_phone_no }}</td>
                                    <td>{{ $supplier->address }}</td>
                                    @if($user->branchId == '')
                                        <td>{{ $supplier->balanceText() }}</td>
                                    @endif
                                    @if($user->branchId == '')
                                        <td>
                                            <input type="number" step="0.01" name="balances[{{ $supplier->id }}]"
                                                   class="form-control" value="{{ old('balances.'.$supplier->id, $supplier->balanceText()) }}">
                                        </td>
                                    @endif

                                    <td class="hidden-print">
                                        <button type="submit" name="update_single[{{ $supplier->id }}]" class="btn btn-primary">
                                            Update
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3 text-center">
                            <button type="submit" class="btn btn-primary">Update All Balances</button>
                        </div>
                    </form>



                </div>

            </div>
        </div>

        <div class="dr"><span></span></div>

    </div>

    @if($user->hasRole('super-admin') || $user->can('supplier-edit'))
    <!-- EDIT SUPPLIER MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Supplier</h4>
                </div>
                <form action="{{ route('supplier.update') }}" method="post" class="form-horizontal">
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                @csrf
                                <input type="hidden" name="id" id="id" value="" class="form-control" />
                                <div class="col-md-6">
                                    <label>Name*</label>
                                    <input type="text" required value="" name="name" id="name" class="form-control"/>
                                </div>
                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" value="" name="email" id="email"class="form-control"/>
                                </div>
                                <div class="col-md-6">
                                    <label>Phone*</label>
                                    <input type="text" required value="" name="phone" id="phone" class="form-control"/>
                                </div>
                                <div class="col-md-6">
                                    <label>Extra Phone Numbers</label>
                                    <input type="text" value="" name="extra_phone_no" id="extra_phone_no" class="form-control"/>
                                </div>
                                <div class="col-md-12">
                                    <label>Address</label>
                                    <textarea name="address" id="address" class="form-control" rows="4"> </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" aria-hidden="true">Save updates</button>
                        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($user->hasRole(['super-admin']) || $user->can('supplier-create'))
    {{-- ADD NEW SUPPLIER MODAL --}}
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Add New Supplier</h4>
                </div>
                <form action="{{ route('supplier.store') }}" method="post" class="form-horizontal">
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                {{csrf_field()}}
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Name*</label>
                                    <div class="col-md-7"><input type="text" value="" name="name" required/></div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Email</label>
                                    <div class="col-md-7"><input type="email" value="" name="email"/></div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Phone*</label>
                                    <div class="col-md-7"><input type="text" value="" name="phone" required/></div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Extra Phone Numbers</label>
                                    <div class="col-md-7"><input type="text" value="" name="extra_phone_no"/></div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Address</label>
                                    <div class="col-md-7"><textarea name="address"></textarea></div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: center;">
                        <button class="btn btn-primary" type="submit" aria-hidden="true">Submit</button>
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
        $(document).ready(function(){

            $(document).on('click','.edit-btn', function(){
                $('#id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('#email').val($(this).data('email'));
                $('#phone').val($(this).data('phone'));
                $('#extra_phone_no').val($(this).data('extra_phone_no'));
                $('#address').val($(this).data('address'));
            });


            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'suppliers'])!!}',
                minLenght:1,
                autoFocus:true,

            });
        });

    </script>
@endsection