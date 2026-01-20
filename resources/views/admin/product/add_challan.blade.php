@extends('admin.layouts.master')
@section('title', 'Add Challan')
@section('breadcrumb', 'Add Challan')
<?php $user_data = Auth::user(); ?>
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Create Challan</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('customer.challan.store') }}" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="row-form clearfix">
                            <label class="col-md-3">Branch</label>
                            <div class="col-md-6">
                                <select name="branchId" class="form-control" required>
                                    <option value="">-- Select Branch --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row-form clearfix">
                            <label class="col-md-3">Challan No</label>
                            <div class="col-md-6"><input type="text" required="" value="" name="challan_no"
                                                         id="challan_no"/></div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Select a customer</label>
                            <div class="col-md-6">
                                <select name="customer_id" id="customer_id" required="">
                                    <option value="">choose a option...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{$customer->id}}">{{$customer->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Select a Project</label>
                            <div class="col-md-6">
                                <select name="project_id" id="project_id" required="">
                                    <option value="">choose a option...</option>
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Select PSI</label>
                            <div class="col-md-6">
                                <select name="mix_design_id" id="psi" required="">
                                    <option value="">choose a option...</option>

                                </select>
                            </div>
                        </div>

                        <div class="row-form clearfix">
                            <label class="col-md-3">Cubic Meter</label>
                            <div class="col-md-6"><input type="text" required="" value="" name="cuM" id="cuM"/></div>
                        </div>

                        <div class="row-form clearfix">
                            <label class="col-md-3">Date</label>
                            <div class="col-md-6"><input type="text" required="" value="" name="sell_date"
                                                         id="sell_date" class="datepicker"/></div>
                        </div>

                        <div class="row-form clearfix">
                            <label class="col-md-3">Description</label>
                            <div class="col-md-6"><textarea name="description" id="description"></textarea></div>
                        </div>

                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-default">Submit</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
@endsection

@section('page-script')
    <script>
        jQuery(document).ready(function ($) {

//load rent according to location
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#customer_id").on('change', function () {
                //$(this).preventDefault();
                //$(this).off("click").attr('href', "javascript: void(0);");
                $('#psi').empty();
                $('#project_id').empty();
                $('#project_id').append($('<option>', {value: '', text: 'choose project'}));
                $('#psi').append($('<option>', {value: '', text: 'choose psi'}));
                var customer_id = $('#customer_id').val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo URL::to('/load-sell-product-info'); ?>",
                    data: {
                        _token: CSRF_TOKEN,
                        customer_id: customer_id

                    },
                    dataType: 'JSON',
                    success: function (resp) {
                        $.each(resp.projects, function (i, member) {

                            //alert( i + ": " + member["id"] );
                            $('#project_id').append($('<option>', {value: member["id"], text: member["name"]}));

                        });

                        $.each(resp.psi, function (i, member) {
                            $('#psi').append($('<option>', {
                                value: member["id"],   // use the ID here
                                text: member["psi"]
                            }));
                        });

                    }
                });
            });
        });
    </script>
@endsection