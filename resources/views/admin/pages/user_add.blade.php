@extends('admin.layouts.master')
@section('title', 'Add User')
@section('breadcrumb', 'Add User')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-8">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Add User</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('user.store') }}" method="post" enctype="multipart/form-data" id="user_form" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>User Name</label>
                                <input type="text" value="" name="name" id="userName" required/>
                            </div>
                            <div class="col-md-6">
                                <label>User Email</label>
                                <input type="email" value="" name="email" id="userEmail" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Password</label>
                                <input type="password" value="" name="password" id="userPassword" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Confirm Password</label>
                                <input type="password" value="" name="password_confirmation" id="userPassword_confirmation" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Role</label>
                                <select name="role" id="role" required>
                                    <option value="">----- Select Role -----</option>
                                    @foreach($roles as $id=>$role)
                                        <option value="{{ $id }}" {{ old('role')==$id?'selected':'' }}>{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Branch</label>
                                <select name="branch" id="branch">
                                    <option value="">----- Select Branch -----</option>
                                    @foreach($branches as $id=>$branch)
                                        <option value="{{ $id }}" {{ old('branch')==$id?'selected':'' }}>{{ $branch }}</option>
                                    @endforeach
                                </select>
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
@endsection



