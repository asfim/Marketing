@extends('admin.layouts.master')
@section('title', 'Edit Users')
@section('page-style')
    <style>
        .password_div
        {
            display: none;
        }
    </style>
@endsection
@section('breadcrumb', 'View Users')
<?php $auth_user = Auth::user(); ?>
@section('shortcut_menu')
    @if($auth_user->can('user-create'))
        <li><a href="{{ route('user.create') }}"><span class="glyphicon glyphicon-plus"></span> Add User</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Edit User [{{$user->name}}]</h1>
                </div>
               

                <form action="{{ route('user.update', $user->id) }}" method="post" enctype="multipart/form-data" id="user_edit_form" class="form-horizontal">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" name="branchId" value="{{ $user->id }}" />
                    <div class="row-form clearfix">
                        <label class="col-md-3">User Name</label>
                        <div class="col-md-9"><input type="text" required value="{{ $user->name }}" name="name" id="userName"/></div>
                    </div>

                    <div class="row-form clearfix">
                        <label class="col-md-3">User Email</label>
                        <div class="col-md-9"><input type="email" required value="{{ $user->email }}" name="email" id="userEmail" readonly disabled/></div>
                    </div>

                    <div class="row-form clearfix">
                        <div class="col-md-12"><label><input type="checkbox" name="update_pass" class="update_pass"/>You want to change password please checked</label></div>
                    </div>

                    <div class="password_div">
                        <div class="row-form clearfix">
                            <label class="col-md-3">New Password</label>
                            <div class="col-md-9"><input type="password" value="" name="password" id="password" disabled="disabled"/></div>
                        </div>

                        <div class="row-form clearfix">
                            <label class="col-md-3">Confirm Password</label>
                            <div class="col-md-9"><input type="password" value="" name="password_confirmation" id="password_confirmation"/></div>
                        </div>
                    </div>

                    <div class="row-form clearfix">
                        <label class="col-md-3">Role</label>
                        <div class="col-md-9">
                            <select name="role" id="role" required>
                                <option value="">----- Select User Role -----</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{$user->role_id == $role->id ? "selected" : ""}}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row-form clearfix">
                        <label class="col-md-3">Branch</label>
                        <div class="col-md-9">
                            <select name="branch" id="branch">
                                <option value="">----- Select Branch -----</option>
                                @foreach ($branches as $branch)
                                <option value="{{$branch->id}}" {{ $user->branchId == $branch->id ?'selected':'' }}>{{$branch->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row-form clearfix text-right">
                        <input type="submit" value="Update User" class="btn btn-primary">
                    </div>
                </form>
                

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
@endsection

@section('page-script')
<script>
    $(document).ready(function ($) {
        $(".update_pass").on("click", function () {

            if($(this).is(':checked')){
                $(".password_div").fadeIn(300);
                $("input[name='password']").removeAttr("disabled");
            }else{
                $("input[name='password']").attr("disabled", "disabled");
                $(".password_div").fadeOut(300);
            }
        });
    })(window.jQuery);
</script>
@endsection