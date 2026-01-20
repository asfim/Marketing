@extends('admin.layouts.master')
@section('title', 'View Land/House Owners')
@section('breadcrumb', 'View Land/House Owners')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>View Land/House Owners</h1>
                    </div>

                    <div class="col-md-8 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-3"></div>
                                <div class="col-md-4">
                                    <select name="type" id="type" class="form-control">
                                        <option value="">Select Type</option>
                                        <option value="Land Owner "{{ request('type')=='Land Owner'?'selected':'' }}>Land Owner</option>
                                        <option value="House Owner" {{ request('type')=='House Owner'?'selected':'' }}>House Owner</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}" class="form-control" placeholder="Enter Search Text" />
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
                            <th>Name</th>
                            <th>Type</th>
                            <th>Phone</th>
                            <th>Total Payable Amount</th>
                            <th>paaid Amount</th> <!-- New Column -->
                            <th>Locations</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1;$l = 1; $grandta=0; ?>
                        @foreach($owners as $owner)
                            <tr>
                                @php($owner_payable = $owner->rentInfos->sum('payable_amount') - $owner->paidAmount())
                                <td>{{ $owner->name }}</td>
                                <td>{{ $owner->type }}</td>
                                <td>{{ $owner->phone }}</td>
                                {{-- <td>{{'BDT '. number_format($owner_payable,2) }}</td> --}}
                                   <td>{{ 'BDT ' . number_format(max(0, $owner_payable), 2) }}</td>

                                <td>{{ 'BDT ' . number_format(max(0, -$owner_payable), 2) }}</td>
                                <td>
                                    @foreach($owner->locations as $location)
                                        <div>
                                            {{ $location->name }}
                                            <a role="button" title="Edit Location"
                                               data-location_id="{{$location->id}}"
                                               data-location_name="{{$location->name}}"
                                               data-location_details="{{$location->location_details}}"
                                               data-target="#editLocationModal"
                                               data-toggle="modal" class="edit-btn glyphicon glyphicon-pencil"></a><br>
                                        </div>
                                    @endforeach

                                </td>
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('edit-house-owners'))
                                        <a href="#oModal<?php echo $i;?>" role="button" class="fa fa-edit" data-toggle="modal"></a>
                                    @endif

                                    {{--@if($user->hasRole('super-admin') || $user->can('delete-house-owners'))--}}
                                        {{--<a href="{{ route('owner.delete',$owner->id) }}" onclick="return confirm('Are you sure you want to delete?');" class="fa fa-trash"></a>--}}
                                    {{--@endif--}}
                                    @if($user->hasRole('super-admin') || $user->can('show-rent-payment'))
                                        <a href="{{ route('property.rent.pay.index',$owner->id) }}" target="_blank" class="fa fa-eye" title="View Rent Details"></a>
                                    @endif
                                    @if($user->hasRole('super-admin') || $user->can('payment-rent'))
                                        <a href="{{ route('property.rent.pay.create',$owner->id) }}" target="_blank" class="fab fa-product-hunt" title="Rent Payment" style="font-size: 18px;"></a>
                                    @endif
                                </td>
                            </tr>
                            <?php $i++; $grandta += $owner_payable;?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Grand Total = </b></td>
                            <td><b>{{'BDT '. number_format($grandta,2) }}</b></td>
                            <td><b>Total Paid = {{ 'BDT ' . number_format($totalDue, 2) }}</b></td>
                            <td><b>
                            <td class="hidden-print"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
    <!-- Bootrstrap modal form -->
    <?php $l = 1;?>
    @foreach($owners as $owner)
        <div class="modal fade" id="oModal<?php echo $l;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4>Edit House owner profile</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                <form action="{{ route('owner.update') }}" method="post" enctype="multipart/form-data" id="house_owner_edit_form<?php echo $l;?>" class="form-horizontal">
                                    {{csrf_field()}}
                                    <input type="hidden" name="id" value="{{$owner->id}}" />
                                    <div class="row-form clearfix">
                                        <label class="col-md-3">Name*</label>
                                        <div class="col-md-6"><input type="text" required="" value="{{$owner->name}}" name="name" id="name"/></div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <label class="col-md-3">Phone*</label>
                                        <div class="col-md-6"><input type="text" required="" value="{{$owner->phone}}" name="phone" id="phone"/></div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <label class="col-md-3">Email</label>
                                        <div class="col-md-6"><input type="email" value="{{$owner->email}}" name="email" id="email"/></div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: center;">
                        <button class="btn btn-primary" type="submit" form="house_owner_edit_form<?php echo $l;?>" aria-hidden="true">Save updates</button>
                        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php $l++;?>
    @endforeach


    <div class="modal fade" id="editLocationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Location</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            <form action="{{ route('owner.location.update') }}" method="post" class="form-horizontal">
                                {{csrf_field()}}
                                <input type="hidden" name="location_id" id="location_id" value=""/>
                                <div class="row-form clearfix">
                                    <div class="col-md-12">
                                        <label>Name*</label>
                                        <input type="text" name="location_name" id="location_name" required value=""/>
                                    </div>
                                </div>
                                <div class="row-form clearfix">
                                    <div class="col-md-12">
                                        <label>Location Details</label>
                                        <textarea name="location_details" id="location_details"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="footer" style="text-align: center;">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        jQuery(document).ready(function($){

            $(document).on('click','.edit-btn', function(){
                $('#location_id').val($(this).data('location_id'));
                $('#location_name').val($(this).data('location_name'));
                $('#location_details').html($(this).data('location_details'));
            });

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'house_owner_profiles'])!!}',
                minLenght:1,
                autoFocus:true,
            });
        });
    </script>
@endsection



