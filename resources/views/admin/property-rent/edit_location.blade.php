@extends('admin.layouts.master')
@section('title', 'Edit Location')
@section('breadcrumb', 'Edit Location')
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Edit Location</h1>
                </div>

                <div class="block-fluid">

                    <form action="{{ route('owner.location.update')  }}" method="post" enctype="multipart/form-data" id="edit_form" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$location->id}}" />
                        <div class="row-form clearfix">
                            <div class="col-md-3">Name</div>
                            <div class="col-md-6"><input type="text" required value="{{$location->name}}" name="name" id="name"/></div>
                        </div>
                        <div class="row-form clearfix">
                            <div class="col-md-3">Location Details</div>
                            <div class="col-md-6"><textarea name="details" id="details">{{$location->location_details}}</textarea></div>
                        </div>
                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-default">Update</button>
                                <a role="button" href="{{ route('owner.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

@endsection





