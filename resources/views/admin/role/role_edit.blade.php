@extends('admin.layouts.master')
@section('title', 'Edit Role')
@section('breadcrumb', 'Edit Role')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Edit Role</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('role.update', $role_data->id) }}" method="post" id="role_form" class="form-horizontal">
                        {{csrf_field()}}
                        {{ method_field("PUT") }}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <div class="row-form clearfix">
                                    <div class="col-md-6">
                                        <label>Role Name</label>
                                        <input type="text" value="{{ $role_data->name }}" name="name" id="name" class="form-control" required/>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Display Name</label>
                                        <input type="text" value="{{ $role_data->display_name }}" name="display_name" id="display_name" class="form-control"/>
                                    </div>
                                    <div class="col-md-12">
                                        <label>Description</label>
                                        <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ $role_data->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4>Permission</h4>
                                @foreach($permissions as $permission)
                                    <label for="{{ $permission->id }}"><input type="checkbox" {{ in_array($permission->id, $role_permissions) ? "checked" : "" }} class="form-control" name="permission[]" value="{{ $permission->id }}" id="{{ $permission->id }}"/> {{ $permission->display_name }}</label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
@endsection



