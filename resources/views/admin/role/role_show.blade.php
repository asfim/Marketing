@extends('admin.layouts.master')
@section('title', 'View Roles Details')
@section('breadcrumb', 'View Roles Details')
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>View Roles Details</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table class="table table-responsive table-bordered">
                        <tr>
                            <td style="font-weight: bold;">Role Name</td>
                            <td>{{ $role_data->name }}</td>
                        </tr>

                        <tr>
                            <td style="font-weight: bold;">Display Name</td>
                            <td>{{ $role_data->display_name }}</td>
                        </tr>

                        <tr>
                            <td style="font-weight: bold;">Description</td>
                            <td>{{ $role_data->description }}</td>
                        </tr>

                        <tr>
                            <td style="font-weight: bold;">Permissions</td>
                            <td style="font-weight: bold; color: red;">
                                <?php
                                $permission_array = ", ";
                                $count = count($permissions);
                                $i=0;
                                ?>
                                @foreach($permissions as $permission)
                                    <?php $i++; ?>
                                    @if($i == $count)
                                        <?php $permission_array =""?>
                                    @endif
                                    {{ $permission->display_name.$permission_array }}
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>


@endsection