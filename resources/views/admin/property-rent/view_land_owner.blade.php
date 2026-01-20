@extends('admin.layouts.master')
@section('title', 'View Land Owners')
@section('breadcrumb', 'View Land Owners')
<?php $userauth   = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>View Land Owners</h1>

                    <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                        <form action="{{ route('searchLandOwner') }}" method="post" enctype="multipart/form-data" id="search_all_form" class="form-horizontal">

                            <div class="" align="right">
                                {{csrf_field()}}
                                <input type="text" name="search_name" id="search_name" placeholder="Enter Search Text" />
                                {{--<input type="date" name="from_date" id="from_date" placeholder="From Date" />--}}
                                {{--<input type="date" name="to_date" id="to_date" placeholder="To Date" />--}}

                                <button type="submit" class="btn btn-default">Search</button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="tSortable_2">
                        <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th width="15%">Name</th>
                            <th width="8%">Phone</th>
                            <th width="15%">Total Payable Amount</th>
                            <th width="20%">Locations</th>
                            <th width="10%">Actions</th>
                            <th width="10%">Pay Rent</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1;$l = 1; $grandta=0; ?>
                        @foreach($land_owners as $owner)
                            <tr>
                                <td><input type="checkbox" name="checkbox"/></td>
                                <td>{{$owner->name}}</td>
                                <td>{{$owner->phone}}</td>
                                <td>{{'BDT ' .\App\OwnerPaymentLog::where('profile_id',$owner->id)->sum('paid_amount') }}</td>
                                <td>
                                <?php
                                $locations = \App\Location::where('profile_id',$owner->id)->get();

                                foreach($locations as $location)
                                {
                                    echo '<div id="location_name'.$l.'" style="float:left;">'.$location->name.'</div>&nbsp<a href="'.URL::to('/edit-location-form/'.$location->id).'" class="glyphicon glyphicon-pencil"></a><br>';
                                    $l++;
                                }
                                ?>
                                <!--<a href="#locationModal<?php //echo $i;?>" role="button" class="glyphicon glyphicon-pencil" data-toggle="modal"></a>-->

                                </td>
                                <td>
                                    @if($userauth->hasRole('super-admin') || $userauth->can('edit-land-owners'))
                                        <a href="#oModal<?php echo $i;?>" role="button" class="glyphicon glyphicon-pencil" data-toggle="modal"></a>&nbsp&nbsp
                                    @endif

                                    @if($userauth->hasRole('super-admin') || $userauth->can('delete-land-owners'))
                                        <a href="{{URL::to('/delete-owner/'.$owner->id)}}" class="glyphicon glyphicon-remove"></a>
                                    @endif

                                </td>
                                <td>
                                    @if($userauth->hasRole('super-admin') || $userauth->can('payment-rent'))
                                        <a href="{{URL::to('/pay-rent/'.$owner->id)}}" target="_blank" style="border-bottom: 1px solid;">Pay Rent</a><br>
                                    @endif

                                    @if($userauth->hasRole('super-admin') || $userauth->can('show-rent-payment'))
                                        <a href="{{URL::to('/show-rent-payment/'.$owner->id)}}" target="_blank" class="">Show Rent Payments</a>
                                    @endif

                                </td>
                            </tr>
                            <?php $i++; $grandta += \App\OwnerPaymentLog::where('profile_id',$owner->id)->sum('paid_amount'); ?>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Grand Total = </b></td>
                            <td><b>{{'BDT '.$grandta}}</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
    <!-- Bootrstrap modal form -->
    <?php $l = 1;?>
    @foreach($land_owners as $owner)
        <div class="modal fade" id="oModal<?php echo $l;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4>Edit Land owner profile</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                <form action="{{URL::to('/edit-owner')}}" method="post" enctype="multipart/form-data" id="land_owner_edit_form<?php echo $l;?>" class="form-horizontal">
                                    {{csrf_field()}}
                                    <input type="hidden" name="id" value="{{$owner->id}}" />
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name</div>
                                        <div class="col-md-6"><input type="text" required="" value="{{$owner->name}}" name="name" id="name"/></div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Phone</div>
                                        <div class="col-md-6"><input type="text" required="" value="{{$owner->phone}}" name="phone" id="phone"/></div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Email</div>
                                        <div class="col-md-6"><input type="email" value="{{$owner->email}}" name="email" id="email"/></div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning" type="submit" form="land_owner_edit_form<?php echo $l;?>" aria-hidden="true">Save updates</button>
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php $l++;?>
    @endforeach
@endsection

@section('page-script')
    <script>
        jQuery(document).ready(function($){
            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'land_owner_profiles'])!!}',
                minLenght:1,
                autoFocus:true,

            });

            $('#tSortable_2').DataTable({
                dom: 'flBrtip',
                buttons: [
                    {
                        extend: 'print',
                        text: 'Print page',
                        autoPrint: true,
                        exportOptions: {
                            columns: '1,2,3,4',
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


