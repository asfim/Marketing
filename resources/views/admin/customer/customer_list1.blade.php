@extends('admin.layouts.master')
@section('title', 'Customer List')
@section('breadcrumb', 'Customer List')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole('super-admin') || $user->can('customer-create'))
        <li><a data-target="#addModal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add Customer</a></li>
    @endif
@endsection
@section('content')

    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>View Customers</h1>
                    </div>

                    <div class="col-md-4 text-center" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">

                            <div class="input-group">
                                <input type="text" name="search_text" value="{{ old('search_text') }}" id="search_name" class="form-control" placeholder="Enter Search Text" />
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-default search-btn">Search</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">


                    <form action="{{ route('customer.balance.update') }}" method="POST">
                        @csrf
                        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                @if($user->branchId == '')
                                    <th>Balance</th>
                                    <th>New Balance</th>
                                    <th>Action</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }} <br> {{ $customer->extra_phone_no }}</td>
                                    <td>{{ $customer->address }}</td>
                                    @if($user->branchId == '')
                                        <td>{{ $customer->balanceText() }}</td>
                                        <td>
                                            <input type="number" name="balances[{{ $customer->id }}]" class="form-control" step="0.01" >
                                        </td>

                                        <td>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </form>


                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>



@endsection


@section('page-script')
    <script type="text/javascript">
        jQuery(document).ready(function($){

            $(document).on('click','.edit-btn', function(){
                $('#id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('#email').val($(this).data('email'));
                $('#phone').val($(this).data('phone'));
                $('#extra_phone_no').val($(this).data('extra_phone_no'));
                $('#address').val($(this).data('address'));
            });
            $(document).on('click','.add-project-btn', function(){
                $('#customer_id').val($(this).data('customer_id'));
                $('#customer_name').val($(this).data('customer_name'));
            });

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'customers'])!!}',
                minLenght:1,
                autoFocus:true,
            });
        });
    </script>
@endsection
