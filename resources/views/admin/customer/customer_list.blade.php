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


                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th width="2%"><input type="checkbox" name="checkall"/></th>
                            <th>id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>                           
                            <th>Advance</th>
                            <th>Due</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                       <?php $i = 1; $total_advance = 0; $total_due = 0;?>
                        @foreach ($customers as $customer)                
                             @php
                                $adj_balance = $customer->adjustedBalance();
                                if ($adj_balance < 0) {
                                    $total_advance += abs($adj_balance);
                                } else {
                                    $total_due += abs($adj_balance);
                                }
                            @endphp
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td style="width: 70px;">{{ $customer->phone }} <br> {{ $customer->extra_phone_no }}</td>
                                <td>{{ $customer->address }}</td>
                                @if($user->hasRole('super-admin') || $user->can('customer-balance show'))
                                <td style="width: 100px; font-size: 15px !important;" >
                                    @if($adj_balance < 0)
                                        <span style="background:#007bff;color:#fff;padding:6px 12px;border-radius:6px;">{{ number_format(abs($adj_balance), 2) }} TK</span>
                                    @else
                                     <span style="background:#3a3a3a;color:#fff;padding:6px 12px;border-radius:6px;">0.00 TK</span>
                                    @endif
                                    
                                </td>
                                <td style="width: 100px; font-size: 15px !important;" >
                                    @if($adj_balance > 0)
                                        <span style="background:#dc3545;color:#fff;padding:6px 12px;border-radius:6px;">{{ number_format(abs($adj_balance), 2) }} TK</span>
                                    @else
                                        
                                            <span style="background:#3a3a3a;color:#fff;padding:6px 12px;border-radius:6px;">0 TK</span>
                                       
                                    @endif
                                </td>
                                @endif
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('customer-edit'))
                                        <a role="button" title="Edit Customer" class="edit-btn"
                                           data-id="{{$customer->id}}"
                                           data-name="{{$customer->name}}"
                                           data-email="{{$customer->email}}"
                                           data-phone="{{$customer->phone}}"
                                           data-extra_phone_no="{{$customer->extra_phone_no}}"
                                           data-address="{{$customer->address}}"
                                           data-target="#editModal"
                                           data-toggle="modal">
                                            <span class="fa fa-edit"></span>
                                        </a>
                                    @endif
                                    @if($user->branchId == '' && $user->hasRole('super-admin') || $user->can('customer-profile'))
                                    <a href="{{ route('customer.profile', $customer->id) }}" title="View Profile" target="_blank"><span class="fa fa-eye"></span></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                         <tfoot>
                            <tr>
                                <td colspan="6" class="text-right" style ="font-size: 15px"><strong>Total: </strong> </td>
                                <td class="text-left" style="font-size: 15px; font-weight: bold;">{{number_format($total_advance,2)}} TK</td>
                                <td class="text-left" style="font-size: 15px; font-weight: bold;">{{number_format($total_due,2)}} TK</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>


                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

    @if($user->hasRole('super-admin') || $user->can('customer-edit'))
    <!-- CUSTOMER EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Customer</h4>
                </div>
                <form action="{{ route('customer.update')}}" method="post" class="form-horizontal">
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            {{csrf_field()}}
                            <input type="hidden" name="id" id="id" value="" />
                            <div class="row-form clearfix">
                                <label class="col-md-3">Name*</label>
                                <div class="col-md-9"><input type="text" value="" name="name" id="name" required/></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Email</label>
                                <div class="col-md-9"><input type="email" value="" name="email" id="email"/></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Phone*</label>
                                <div class="col-md-9"><input type="text" value="" name="phone" id="phone" required/></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Extra Phone Numbers</label>
                                <div class="col-md-9"><input type="text" value="" name="extra_phone_no" id="extra_phone_no"/></div>
                            </div>
                            <div class="row-form clearfix">
                                <label class="col-md-3">Address</label>
                                <div class="col-md-9"><textarea name="address" id="address"></textarea></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-primary" type="submit" aria-hidden="true">Save Updates</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($user->hasRole('super-admin') || $user->can('customer-create'))
    {{-- ADD NEW CUSTOMER MODAL --}}
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Add New Customer</h4>
                </div>
                <form action="{{ route('customer.store') }}" method="post" class="form-horizontal">
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
