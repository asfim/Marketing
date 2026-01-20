@extends('admin.layouts.master')
@section('title', 'Income Types')
@section('breadcrumb', 'Income Types')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole('super-admin') || $user->can('add-general-income'))
        <li><a href="{{ route('income.general.create') }}"><span class="glyphicon glyphicon-plus"></span> Add General Income</a></li>
    @endif
    @if($user->hasRole('super-admin') || $user->can('add-waste-income'))
        <li><a href="{{ route('income.waste.create') }}"><span class="glyphicon glyphicon-plus-sign"></span> Add Waste Expense</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-8">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>View Income Types</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($types as $type)
                            <tr>
                                <td>{{ $type->type_name }}</td>
                                <td>{{ $type->description }}</td>
                                <td>{{ $type->category }}</td>
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('edit-general-income-type') || $user->can('edit-waste-income-type'))
                                        <a role="button" class="edit-btn"
                                           data-id="{{$type->id}}"
                                           data-name="{{$type->type_name}}"
                                           data-description="{{$type->description}}"
                                           data-category="{{$type->category}}"
                                           data-target="#editModal"
                                           data-toggle="modal">
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
                    <h1>Add Income Type</h1>
                </div>
                <div class="block-fluid clearfix">
                    <form action="{{ route('income.type.store') }}" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="col-md-12">
                            <label>Category</label>
                            <select name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                @if($user->can('add-general-income-type') || $user->hasRole('super-admin'))
                                    <option value="General Income" {{ old('category')=='General Income'?'selected':'' }}>General Income</option>
                                @endif
                                @if($user->can('add-waste-income-type') || $user->hasRole('super-admin'))
                                    <option value="Waste Income" {{ old('category')=='Waste Income'?'selected':'' }}>Waste Income</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label>Name</label>
                            <input type="text" value="{{ old('name') }}" name="name" class="form-control" required/>
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
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


    <!-- Bootrstrap modal edit form -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Income Type</h4>
                </div>
                <form action="{{ route('income.type.update') }}" method="post" class="form-horizontal">
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                {{csrf_field()}}
                                <input type="hidden" name="id" id="id" value="" />
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Category</label>
                                    <div class="col-md-9">
                                        <select name="category" id="category" required>
                                            <option value="">Select Category</option>
                                            @if($user->can('add-general-income-type') || $user->hasRole('super-admin'))
                                                <option value="General Income">General Income</option>
                                            @endif
                                            @if($user->can('add-waste-income-type') || $user->hasRole('super-admin'))
                                                <option value="Waste Income">waste Income</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Name</label>
                                    <div class="col-md-9"><input type="text" value="" name="name" id="name"/></div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Description</label>
                                    <div class="col-md-9"><textarea name="description" id="description"></textarea></div>
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
    <script>
        $(document).ready(function(){

            $(document).on('click','.edit-btn', function(){
                $('#id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('#category').val($(this).data('category'));
                $('#description').val($(this).data('description'));
            });
        });

    </script>
@endsection
