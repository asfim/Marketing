@extends('admin.layouts.master')
@section('title', 'Search Result')
@section('breadcrumb', 'Search Result')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            @if($user->hasRole('super-admin') || $user->can('customer-list'))
            <div class="col-md-6">
                <div class="head clearfix">
                    <div class="isw-users"></div>
                    <h1>Search Result for - <span class="src-info">Customers</span></h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            {{--<th>Address</th>--}}
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }} <br> {{ $customer->extra_phone_no }}</td>
                                {{--<td>{{ $customer->address }}</td>--}}
                                <td>
                                    @if($user->hasRole('super-admin') || $user->can('customer-view-profile'))
                                        <a href="{{ route('customer.profile', $customer->id) }}" title="View Profile" target="_blank"><span class="fa fa-eye"></span></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            @endif
            @if($user->hasRole('super-admin') || $user->can('customer-list'))
            <div class="col-md-6">
                <div class="head clearfix">
                    <div class="isw-users"></div>
                    <h1>Search Result for - <span class="src-info">Suppliers</span></h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            {{--<th>Address</th>--}}
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->phone }} <br> {{ $supplier->extra_phone_no }}</td>
                                {{--<td>{{ $customer->address }}</td>--}}
                                <td>
                                    @if($user->hasRole('super-admin') || $user->can('customer-view-profile'))
                                        <a href="{{ route('supplier.profile', $supplier->id) }}" title="View Profile" target="_blank"><span class="fa fa-eye"></span></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            @endif
        </div>

        <div class="dr"><span></span></div>

    </div>
@endsection


@section('page-script')
@endsection
