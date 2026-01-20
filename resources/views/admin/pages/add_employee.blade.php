@extends('admin.layouts.master')
@section('title', 'Add Employee')
@section('breadcrumb', 'Add Employee')
@section('content')
<div class="workplace">

                <div class="row">
                    <div class="col-md-12">
                      <div class="head clearfix">
                            <div class="isw-documents"></div>
                            <h1>Add a Employee</h1>
                        </div>
                        <div class="block-fluid">
                            <form action="{{ route('employee.store') }}" method="post" enctype="multipart/form-data" id="employee_form" class="form-horizontal">
                            {{csrf_field()}}
                            <div class="row-form clearfix">
                                <div class="col-md-3">Employee ID</div>
                                <div class="col-md-6"><input type="text" value="" name="employeeId" id="employeeId"/></div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Name</div>
                                <div class="col-md-6"><input type="text" value="" name="employeeName" id="employeeName"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Email</div>
                                <div class="col-md-6"><input type="email" value="" name="employeeEmail" id="employeeEmail"/></div>
                           </div> 
                            <div class="row-form clearfix">
                                <div class="col-md-3">Phone</div>
                                <div class="col-md-6"><input type="text" value="" name="employeePhone" id="employeePhone"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Address</div>
                                <div class="col-md-6"><textarea name="employeeAddress" id="employeeAddress"></textarea></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Joining Date</div>
                                <div class="col-md-6"><input type="date" value="" name="joiningDate" id="joiningDate"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">NID</div>
                                <div class="col-md-6"><input type="text" value="" name="nationalId" id="nationalId"/></div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Salary</div>
                                <div class="col-md-6"><input type="number" value="" name="salary" id="salary"/></div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Photo (image file)</div>
                                <div class="col-md-6">
                                    <div class="uploader">
                                        <div class="uploader" id="uniform-photo">
                                            <input type="file" name="photo" id="photo">
                                            <span class="filename" style="user-select: none;">No file selected</span>
                                            <span class="action" style="user-select: none;">Choose File</span>
                                        </div>
                                    </div>
                                </div>
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




