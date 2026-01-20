@extends('admin.layouts.master')
@section('title', 'View Users')
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
                    <h1>View Users</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>User Name</th>
                            <th>User Email</th>
                            <th>Role</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            @php
                                $i=1;
                                $user_role = $user->roles()->first();
                            @endphp
                            @if($user_role->name != 'super-admin')
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user_role->display_name }}</td>
                                <td>{{ ($user->branch ? $user->branch->name : '') }}</td>
                                <td>
                                    @if($auth_user->hasRole(['super-admin']) || $user->can('user-secret-login'))
                                    <a href="{{ route('secret.login',encrypt($user->id)) }}"
                                       role="button" class="fa fa-user-secret" title="Secret Login"></a>
                                    @endif
                                    @if($auth_user->hasRole(['super-admin']) || $user->can('user-edit'))
                                    {{-- <a href="#uModal{{ $i }}" role="button" class="fa fa-edit" data-toggle="modal"></a> --}}
                                    <a href="{{route('user.edit', $user->id)}}" class="fa fa-edit"></a>
                                    @endif

                                    @if($auth_user->hasRole(['super-admin']) || $user->can('user-delete'))
                                    <a href="{{ route('user.destroy', $user->id) }}"
                                       onclick="event.preventDefault();
                                                 document.getElementById('user_delete_form_{{ $i }}').submit();"
                                       class="fa fa-trash"></a>
                                    <form id="user_delete_form_{{ $i }}" action="{{ route('user.destroy', $user->id) }}"
                                          method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                    </form>
                                    @endif
                                </td>
                            </tr>
                                @php($i++)
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
    @if($auth_user->hasRole(['super-admin']) || $user->can('user-edit'))
    <!-- Bootrstrap modal form -->
    <?php
        if(count($users) > 0){
            $i=0;
            foreach ($users as $user_data){
                $i++;
                $userRoles = $user_data->roles->pluck('id')->toArray();
    ?>

    <div class="modal fade" id="uModal{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit User</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            <form action="{{ route('user.update', $user_data->id) }}" method="post" enctype="multipart/form-data" id="user_edit_form{{ $i }}" class="form-horizontal">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="PUT" />
                                <input type="hidden" name="branchId" value="{{ $user_data->id }}" />
                                <div class="row-form clearfix">
                                    <label class="col-md-3">User Name</label>
                                    <div class="col-md-9"><input type="text" required value="{{ $user_data->name }}" name="name" id="userName"/></div>
                                </div>

                                <div class="row-form clearfix">
                                    <label class="col-md-3">User Email</label>
                                    <div class="col-md-9"><input type="email" required value="{{ $user_data->email }}" name="email" id="userEmail"/></div>
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
                                            @foreach($roles as $k=>$v)
                                                <option value="{{ $k }}" {{ in_array($k, $userRoles) ? "selected" : "" }}>{{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row-form clearfix">
                                    <label class="col-md-3">Branch</label>
                                    <div class="col-md-9">
                                        <select name="branch" id="branch">
                                            <option value="">----- Select Branch -----</option>
                                            @foreach ($branches as $k=>$v){
                                            <option value="{{$k}}" {{ $user_data->branchId==$k?'selected':'' }}>{{$v}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                            </form>
                        </div>

                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-primary" type="submit" form="user_edit_form{{ $i }}" aria-hidden="true">Save Updates</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
            }
        }
    ?>
    @endif

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