@extends('admin.layouts.master')
@section('title', 'Add Customer Project')
@section('breadcrumb', 'Add Customer Project')
@section('content')
<div class="workplace">
        <?php
    $msg = Session::get('message');
    $alert = Session::get('alert');
    if($msg)
    {
        echo '<div class="col-md-12">
                <div class="alert '.$alert.'">'
                    .$msg. 
                 '</div>
              </div>';
        Session::put('message',null);
    }
    ?>
                <div class="row">
                    <div class="col-md-12">
                      <div class="head clearfix">
                            <div class="isw-documents"></div>
                            <h1>Add Customer Project</h1>
                        </div>
                        <div class="block-fluid">
                            <form action="{{URL::to('/save-customer-project')}}" method="post" enctype="multipart/form-data" id="customer__project_form" class="form-horizontal">
                            {{csrf_field()}}
                            <div class="row-form clearfix">
                                <div class="col-md-3">Customer Name</div>
                                <div class="col-md-6">
                                    <input type="text" value="{{$customer->name}}" id="c_name" name="name" readonly="" />
                                    <input type="hidden" value="{{$customer->id}}" id="customer_id" name="customer_id" />
                                </div>
                           </div> 
                            <div class="row-form clearfix">
                                <div class="col-md-3">Project Name</div>
                                <div class="col-md-6"><input type="text" required="" value="" name="name" id="name"/></div>
                           </div> 
                            <div class="row-form clearfix">
                                <div class="col-md-3">Address</div>
                                <div class="col-md-6"><textarea name="address" id="address" required=""></textarea></div>
                           </div>
                            <div class="col-md-12">
                                <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-default">Submit</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                                <a href="{{URL::to('/view-customer')}}" class="btn btn-default">Back</a>
                                </div>
                            </div>
                            </form>
                        </div>
                        
                    </div>
                </div>

                
                <div class="dr"><span></span></div>

            </div>
@endsection





