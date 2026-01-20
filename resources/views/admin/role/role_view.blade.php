@extends('admin.layouts.master')
@section('title', 'View Roles')
@section('breadcrumb', 'View Roles')
<?php $auth_user = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>View Roles</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->display_name }}</td>
                            <td>{{ $role->description }}</td>
                            <td class="hidden-print">

                                @if($auth_user->hasRole(['super-admin']) || $user->can('role-view'))
                                <a href="{{ route('role.show', $role->id) }}" target="_blank" role="button" class="fa fa-eye"></a>
                                @endif
                                @if($auth_user->hasRole(['super-admin']) || $user->can('role-edit'))
                                <a href="{{ route('role.edit', $role->id) }}" role="button" class="fa fa-edit"></a>&nbsp&nbsp
                                @endif

                                {{--<a href="{{ route('role.destroy', $role->id) }}"--}}
                                   {{--onclick="event.preventDefault();--}}
                                   {{--document.getElementById('role_delete_form_{{ $i }}').submit();"--}}
                                   {{--class="fa fa-trash"></a>--}}

                                {{--<form id="role_delete_form_'.$i.'" action="'.route('role.destroy', $role->id).'" method="POST" style="display: none;">--}}
                                    {{--{{ csrf_field() }}--}}
                                    {{--{{ method_field('DELETE') }}--}}
                                {{--</form>--}}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>


@endsection