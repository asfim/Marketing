@extends('admin.layouts.master')
@section('title', 'View Branches')
@section('breadcrumb', 'View Branches')
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-8">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>View Branches</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>Branch Name</th>
                            <th>Address</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($i=1)
                        @foreach ($branches as $branch)
                            <tr>
                                <td> {{ $i++ }}</td>
                                <td> {{ $branch->name }} @if($branch->is_main_branch) <span class="text-success">(Main Branch)</span> @endif </td>
                                <td> {{ $branch->address }} </td>
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('branch-edit'))
                                        <a role="button" class="edit-btn"
                                           data-branch_id="{{$branch->id}}"
                                           data-branch_name="{{$branch->name}}"
                                           data-branch_address="{{$branch->address}}"
                                           data-is_main_branch="{{$branch->is_main_branch}}"

                                           data-action="{{ route('branches.update', $branch->id) }}"
{{--                                           data-target="#editModal"--}}
{{--                                           data-toggle="modal"--}}
                                        >
                                            <span class="fa fa-edit"></span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            @if($user->hasRole('super-admin') || $user->can('branch-create'))
                <div class="col-md-4">
                    <div class="head clearfix">
                        <div class="isw-documents"></div>
                        <h1>Add Branch</h1>
                    </div>
                    <div class="block-fluid clearfix">
                        <form action="{{ route('branches.store') }}" method="post" enctype="multipart/form-data"
                              id="branch_form" class="form-horizontal">
                            @csrf
                            <div class="col-md-12">
                                <label>Branch Name*</label>
                                <input type="text" required value="{{ old('name') }}" name="name" class="form-control"/>
                            </div>
                            <div class="col-md-12">
                                <label>Address</label>
                                <textarea name="address" class="form-control">{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="as_main_branch">Select as Main Branch</label>
                                <input type="checkbox" id="as_main_branch" class="form-control" name="as_main_branch"
                                       value="1"/>
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
            @endif
        </div>

        <div class="dr"><span></span></div>

    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Branch</h4>
                </div>
                <form action="" method="post" id="action" class="form-horizontal">
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                @csrf
                                <input type="hidden" name="_method" value="PUT"/>
                                <input type="hidden" name="branchId" id="branchId"/>
                                <div class="row-form clearfix">
                                    <div class="col-md-3">Branch Name</div>
                                    <div class="col-md-9"><input type="text" required name="name" id="branchName"/>
                                    </div>
                                </div>

                                <div class="row-form clearfix">
                                    <div class="col-md-3">Address</div>
                                    <div class="col-md-9"><textarea name="address" id="branchAddress"></textarea></div>
                                </div>

                                <div class="row-form clearfix">
                                    <div class="col-md-3">

                                        <label for="as_main_branch">Select as Main Branch</label>
                                    </div>
                                    <div class="col-md-9">


                                        <input type="checkbox" id="asMainBranch" class="form-control"
                                               name="as_main_branch" value="1"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" aria-hidden="true">Save updates</button>
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('page-script')

    <script>
        $(document).ready(function () {

            $(document).on('click', '.edit-btn', function () {

                console.log()
                $('#branchId').val($(this).data('branch_id'));
                $('#branchName').val($(this).data('branch_name'));
                $('#branchAddress').val($(this).data('branch_address'));


                if ($(this).data('is_main_branch')){

                $('#uniform-asMainBranch span').addClass('checked')
                    $('#asMainBranch').attr('checked',true)

                }else{
                    $('#uniform-asMainBranch span').removeClass('checked')
                    $('#asMainBranch').attr('checked',false)

                }

                $('#action').attr('action', $(this).data('action'));
                $('#editModal').modal('show');
            });
        });

    </script>
@endsection
