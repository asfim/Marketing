@extends('admin.layouts.master')
@section('title', 'View Customer Projects')
@section('breadcrumb', 'View Customer Projects')
<?php $userauth = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>View Customer Projects:
                        <span class="src-info">{{ $customer_name }}</span></h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Address</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->address }}</td>
                                <td class="hidden-print">
                                    @if($userauth->hasRole('super-admin') || $userauth->can('customer-project-edit'))
                                        <a role="button" class="edit-btn"
                                           data-id="{{$project->id}}"
                                           data-name="{{$project->name}}"
                                           data-address="{{$project->address}}"
                                           data-target="#editModal"
                                           data-toggle="modal">
                                            <span class="fa fa-edit"></span>
                                        </a>
                                    @endif

                                    @if($userauth->hasRole('super-admin') || $userauth->can('customer-project-delete'))
                                        <a href="{{ route('customer.project.delete',$project->id) }}" onclick='return confirm("Are you sure you want to delete?");' class="fa fa-trash"></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <div class="footer" style="text-align: center;">
                        <a href="{{ route('customer.list') }}" class="btn btn-warning">Back</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
    <!-- Bootrstrap modal edit form -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('customer.project.update') }}" method="post" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Customer project</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        <div class="block-fluid">
                            {{csrf_field()}}
                            <input type="hidden" name="id" id="id" value="" />
                            <div class="row-form clearfix">
                                <label class="col-md-3">Name</label>
                                <div class="col-md-9"><input type="text" value="" name="name" id="name" required/></div>
                            </div>

                            <div class="row-form clearfix">
                                <label class="col-md-3">Address</label>
                                <div class="col-md-9"><textarea name="address" id="address"></textarea></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button class="btn btn-primary" type="submit" aria-hidden="true">Save Updates</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('page-script')
    <script type="text/javascript">
        jQuery(document).ready(function($){

            $(document).on('click','.edit-btn', function(){
                $('#id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('#address').val($(this).data('address'));
            });
        });
    </script>
@endsection
