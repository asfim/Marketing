@extends('admin.layouts.master')
@section('title', 'Add Land/House Owner')
@section('breadcrumb', 'Add Land/House Owner')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    <li><a  data-target="#createModal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add New Location</a></li>
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-8">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Create Land/House Owner Profile</h1>
                </div>

                <div class="block-fluid">
                    <form action="{{ route('owner.store') }}" method="post" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Select owner type*</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">choose a option...</option>
                                    <option value="Land Owner">Land Owner</option>
                                    <option value="House Owner">House Owner</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Name*</label>
                                <input type="text" value="" name="name" class="form-control" id="name" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Phone*</label>
                                <input type="text" value="" name="phone" class="form-control" id="phone" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" value="" name="email" class="form-control" id="email"/>
                            </div>
                            <div class="col-md-6">
                                <label>Location*</label>
                                <input type="text" value="" name="location_name" class="form-control" id="location_name"/>
                            </div>
                            <div class="col-md-6">
                                <label>Address/Location Details</label>
                                <textarea name="location_details" class="form-control" id="address"></textarea>
                            </div>

                            <div class="col-md-12">
                                <div class="footer" style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="dr"><span></span></div>
    </div>
    <!-- Bootrstrap modal form -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Add Extra Location</h4>
                </div>
                <form action="{{ route('owner.location.store')}}" method="post" class="form-horizontal">
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            {{csrf_field()}}
                            <div class="row-form clearfix">
                                <label class="col-md-3">Select owner type</label>
                                <div class="col-md-6">
                                    <select name="owner_type" id="owner_type" required="">
                                        <option value="">choose a option...</option>
                                        <option value="Land Owner">Land Owner</option>
                                        <option value="House Owner">House Owner</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row-form clearfix">
                                <!-- load data when choose a type -->
                                <label class="col-md-3">Select a owner</label>
                                <div class="col-md-6">
                                    <select name="profile_id" id="profile_id" required="">
                                        <option value="">choose a option...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row-form clearfix">
                                <label class="col-md-3">Location Name</label>
                                <div class="col-md-6"><input type="text" value="" name="name" id="name"/></div>
                            </div>

                            <div class="row-form clearfix">
                                    <label class="col-md-3">Location Details</label>
                                    <div class="col-md-6"><textarea name="details" id="details"></textarea></div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-primary" type="submit" aria-hidden="true">Save</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        jQuery(document).ready(function($) {
            //load owner according to type
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#owner_type").on('change', function(){
                //$(this).preventDefault();
                //$(this).off("click").attr('href', "javascript: void(0);");
                $('#profile_id').find('option').remove().end().append($('<option>',{value:'', text:'choose an option..'}));
                var type = $('#owner_type').val();
                $.ajax({
                    type:"POST",
                    url:"{{ route('owners.load') }}",
                    data:{
                        _token: CSRF_TOKEN,
                        type:type
                    },
                    dataType: 'JSON',
                    success: function(resp){
                        $.each(resp.owners, function (i, member) {
                            //alert( i + ": " + member["id"] );
                            $('#profile_id').append($('<option>', {value:member["id"], text:member["name"]}));

                        });

                    }
                });
            });
        });
    </script>
@endsection


