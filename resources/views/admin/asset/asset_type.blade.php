@extends('admin.layouts.master')
@section('title', 'Asset Type')
@section('breadcrumb', 'Asset Type')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->can('add-assets'))
        <li><a href="{{ route('asset.create') }}"><span class="glyphicon glyphicon-plus"></span> Add Asset</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-8">

                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>View Asset Types</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($asset_types as $type)
                            <tr>
                                <td>{{$type->name}}</td>
                                <td>{{$type->description}}</td>
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('edit-asset-type'))
                                        <a role="button" class="edit_btn"
                                           data-id="{{$type->id}}"
                                           data-type_name="{{$type->name}}"
                                           data-description="{{$type->description}}"
                                           data-toggle="modal"
                                           data-target="#edit_modal">
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
            <div class="col-md-4">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Add Asset Type</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('asset.type.store') }}" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="row-form ">
                            <label>Type Name</label>
                            <input type="text" class="form-control" name="name" id="name" required value="{{ old('name') }}" placeholder="Furniture,Land,Buildings,Vehicles,Patents,Stock,Equipment etc"/>
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>

                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="dr"><span></span></div>
            </div>
        </div>

        <div class="dr"><span></span></div>

    </div>

    <!-- Bootrstrap modal eidt form -->
    @foreach($asset_types as $type)
        <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('asset.type.update') }}" method="post" class="form-horizontal">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4>Edit Asset Type</h4>
                        </div>
                        <div class="modal-body modal-body-np">
                            <div class="row">
                                <div class="block-fluid">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" id="id" />
                                        <div class="row-form clearfix">
                                            <label class="col-md-3">Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" value="" name="type_name" id="type_name"/>
                                            </div>
                                        </div>
                                        <div class="row-form clearfix">
                                            <label class="col-md-3">Description</label>
                                            <div class="col-md-9"><textarea class="form-control" name="a_description" id="a_description"></textarea></div>
                                        </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" aria-hidden="true">Save Updates</button>
                            <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection


@section('page-script')
    <script>
        $(document).ready(function(){

            $(document).on('click','.edit_btn', function(){
                $('#id').val($(this).data('id'));
                $('#type_name').val($(this).data('type_name'));
                $('#a_description').val($(this).data('description'));
            });
        });
    </script>
@endsection





