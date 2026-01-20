@extends('admin.layouts.master')
@section('title', 'Add Rent Info')
@section('breadcrumb', 'Add Rent Info')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-8">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Add Rent Information</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('property.rent.store')}}" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Select a land owner</label>
                                <select name="profile_id" id="profile_id" required class="form-control">
                                    <option value="">choose a option...</option>
                                    @foreach($owners as $owner)
                                        <option value="{{$owner->id}}" {{ old('profile_id')==$owner->id?'selected':'' }}>{{$owner->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Select a Location</label>
                                <select name="location_id" id="location_id" required class="form-control">
                                    <option value="">choose an option...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Total Month</label>
                                <input type="text" value="" name="total_month" id="total_month" required class="form-control"/>
                            </div>
                            <div class="col-md-6">
                                <label>Monthly Rent</label>
                                <input type="text" value="" name="monthly_rent" id="monthly_rent" required class="form-control"/>
                            </div>
                            <div class="col-md-6">
                                <label>Payable Amount</label>
                                <input type="text" value="" name="payable_amount" id="payable_amount" class="form-control" required readonly/>
                            </div>
                            <div class="col-md-6">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="footer" style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
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
        jQuery(document).ready(function($) {
            //TODO:: onblur calculation
            $("#payable_amount").bind('click',function(){
                var month = $("#total_month").val();
                var rent = $("#monthly_rent").val();
                if(month === "" )
                {
                    alert('Total Month is empty..Enter a digit');
                    return false;
                }
                if(rent === "" )
                {
                    alert('Rent is null..Enter a digit');
                    return false;
                }
                var payable_amt = parseInt(month) * parseInt(rent);
                $("#payable_amount").val(payable_amt);

            });
            //load locations according to owner
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#profile_id").on('change', function(){
                $('#location_id').empty();
                $('#location_id').append('<option>choose an option ...</option>');
                var profile_id = $('#profile_id').val();
                $.ajax({
                    type:"POST",
                    url:"{{ route('locations.load') }}",
                    data:{
                        _token: CSRF_TOKEN,
                        profile_id:profile_id

                    },
                    dataType: 'JSON',
                    success: function(resp){
                        $.each(resp.locations, function (i, member) {
                            $('#location_id').append($('<option>', {value:member["id"], text:member["name"]}));

                        });

                    }
                });
            });
        });
    </script>
@endsection






