@extends('admin.layouts.master')
@section('title', 'Product List')
@section('breadcrumb', 'Product List')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole(['super-admin']) || $user->can('product-create'))
        <li><a data-target="#createModal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add New Product</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Product List</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Conversion Rate (kg/cft)</th>
                            <!--<th>Unit Price</th>-->
                            <th>Description</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category }}</td>
                                <td>{{ $product->conversion_rate }}</td>
                                <!--<td>{{ $product->unit_price }}</td>-->
                                <td>{{ $product->description }}</td>
                                <td class="hidden-print">
                                    <a role="button" class="edit-btn"
                                       data-id="{{$product->id}}"
                                       data-name="{{$product->name}}"
                                       data-category="{{$product->category}}"
                                       data-conversion_rate="{{$product->conversion_rate}}"
                                       data-unit_price="{{$product->unit_price}}"
                                       data-description="{{$product->description}}"
                                       data-target="#editModal"
                                       data-toggle="modal">
                                        <span class="fa fa-edit"></span>
                                    </a>

                                <!-- <a href="{{URL::to('delete-product-name/'.$product['id'])}}" onclick='return confirm("Are you sure you want to delete?");' class="glyphicon glyphicon-remove"></a>-->
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

    <!-- Bootrstrap modal form -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Product Information</h4>
                </div>
                <form action="{{ route('product.update') }}" method="post" class="form-horizontal">
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                {{csrf_field()}}
                                <input type="hidden" name="id" id="id" value="" />
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Category</label>
                                    <div class="col-md-8">
                                        <select name="category" id="category" required>
                                            <option value="">choose a option...</option>
                                            <option value="Cement">Cement</option>
                                            <option value="Stone">Stone</option>
                                            <option value="Chemical">Chemical</option>
                                            <option value="Sand">Sand</option>
                                            <option value="Sand">Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Product Name</label>
                                    <div class="col-md-8"><input type="text" value="" name="name" required="" id="name"/></div>
                                </div>
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Conversion rate (kg/cft)</label>
                                    <div class="col-md-8"><input type="text" placeholder="enter data if applicable" value="" name="conversion_rate" id="conversion_rate"/></div>
                                </div>
                                <!--<div class="row-form clearfix">-->
                                <!--    <label class="col-md-3">Unit Price</label>-->
                                <!--    <div class="col-md-8"><input type="text" placeholder="for calculating stock value" value="" name="unit_price" id="unit_price"/></div>-->
                                <!--</div>-->
                                <div class="row-form clearfix">
                                    <label class="col-md-3">Description</label>
                                    <div class="col-md-8"><textarea name="description" id="description"></textarea></div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" aria-hidden="true">Update Data</button>
                        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Add New Product Information</h4>
                </div>
                <form action="{{ route('product.store') }}" method="post" id="category_form" class="form-horizontal">
                    <div class="modal-body">
                        {{csrf_field()}}
                        <div class="row-form clearfix">
                            <label class="col-md-3">Category</label>
                            <div class="col-md-8">
                                <select name="category" required>
                                    <option value="">choose a option...</option>
                                    <option value="Cement">Cement</option>
                                    <option value="Stone">Stone</option>
                                    <option value="Chemical">Chemical</option>
                                    <option value="Sand">Sand</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Product Name</label>
                            <div class="col-md-8"><input type="text" value="{{ old('name') }}" name="name" required/></div>
                        </div>

                        <!-- <div class="row-form clearfix">
                             <div class="col-md-3">Image</div>
                             <div class="col-md-6">
                                 <div class="uploader">
                                     <input type="file" name="photo[]" id="photo" multiple="">
                                 </div>
                             </div>
                        </div> -->
                        <div class="row-form clearfix">
                            <label class="col-md-3">Conversion rate (kg/cft)</label>
                            <div class="col-md-8"><input type="text" value="{{ old('conversion_rate') }}" name="conversion_rate" placeholder="enter data if applicable"/></div>
                        </div>

                        <!--<div class="row-form clearfix">-->
                        <!--    <label class="col-md-3">Unit Price</label>-->
                        <!--    <div class="col-md-8"><input type="text" value="{{ old('unit_price') }}" name="unit_price" placeholder="for calculating stock value"/></div>-->
                        <!--</div>-->
                        
                       
        
        <input type="hidden" value="{{ old('unit_price', 0) }}" name="unit_price" />




                        <div class="row-form clearfix">
                            <label class="col-md-3">Description</label>
                            <div class="col-md-8"><textarea name="description">{{ old('description') }}</textarea></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" aria-hidden="true">Submit</button>
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
                $('#conversion_rate').val($(this).data('conversion_rate'));
                $('#unit_price').val($(this).data('unit_price'));
                $('#description').val($(this).data('description'));
            });

        });
    </script>
@endsection
