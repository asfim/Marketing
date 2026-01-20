@extends('admin.layouts.master')
@section('title', 'Add Role')
@section('breadcrumb', 'Add Role')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Add Role</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('role.store') }}" method="post" id="role_form" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <div class="row-form clearfix">
                                    <div class="col-md-6">
                                        <label>Role Name*</label>
                                        <input type="text" value="" name="name" id="name" class="form-control" required/>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Display Name</label>
                                        <input type="text" value="" name="display_name" class="form-control" id="display_name"/>
                                    </div>
                                    <div class="col-md-12">
                                        <label>Description</label>
                                        <textarea name="description" id="description" rows="2" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4>Permission</h4>
                                @foreach($permissions as $permission)
                                    <input type="checkbox" class="form-control" name="permission[]" value="{{ $permission->id }}" id="{{ $permission->id }}"/>
                                    <label for="{{ $permission->id }}">{{ $permission->display_name }}</label>
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



