@extends('admin.layouts.master')
@section('title', 'View Employees')
@section('breadcrumb', 'View Employees')
@section('content')
<div class="workplace">

                <div class="row">
                    <div class="col-md-12">
                      <div class="head clearfix">
                            <div class="isw-documents"></div>
                            <h1>View Employees</h1>

                            <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                              <form action="{{ route('employee.search') }}" method="post" enctype="multipart/form-data" id="search_all_form" class="form-horizontal">

                                  <div class="" align="right">
                                      {{csrf_field()}}
                                      <input type="text" name="search_name" id="search_name" placeholder="Enter Search Text" />
                                      {{--<input type="date" name="from_date" id="from_date" placeholder="From Date" />--}}
                                      {{--<input type="date" name="to_date" id="to_date" placeholder="To Date" />--}}

                                      <button type="submit" class="btn btn-default">Search</button>
                                  </div>

                              </form>
                            </div>
                      </div>
                       <div class="block-fluid table-sorting clearfix">
                            <table cellpadding="0" cellspacing="0" width="100%" class="table" id="tSortable_2">
                                <thead>
                                    <tr>
                                        <th width="5%"><input type="checkbox" name="checkall"/></th>
                                        <th width="15%">Name</th>
                                        <th width="10%">Email</th>
                                        <th width="12%">Phone</th>
                                        <th width="15%">Address</th>
                                        <th width="10%">NID</th>
                                         <th width="10%">Join Date</th>
                                        <th width="8%">Salary</th>
                                        <th width="8%">Photo</th>
                                        <th width="8%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                  if(count($employee_data_array) > 0){
                                      for($i=0; $i<count($employee_data_array); $i++){
                                          echo html_entity_decode($employee_data_array[$i]);
                                      }
                                  }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>

                
                <div class="dr"><span></span></div>

            </div>
<!-- Bootrstrap modal form -->
<?php
  if(count($employees_data) > 0){
      $i = 0;
      foreach ($employees_data as $employee){
          $i++;
?>


        <div class="modal fade" id="eModal{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>                        
                        <h4>Edit Employee</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                            <form action="{{ route('employee.update', $employee->id) }}" method="post" enctype="multipart/form-data" id="employee_edit_form{{ $i }}" class="form-horizontal">
                            {{ csrf_field() }}
                            {{--<input type="hidden" name="_method" value="PUT" />--}}
                            <input type="hidden" name="id" value="{{ $employee->id }}" />
                            <div class="row-form clearfix">
                                <div class="col-md-3">Employee ID</div>
                                <div class="col-md-9"><input type="text" value="{{ $employee->employeeId }}" name="employeeId" id="employeeId" readonly/></div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Name</div>
                                <div class="col-md-9"><input type="text" value="{{ $employee->employeeName }}" name="employeeName" id="employeeName"/></div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Email</div>
                                <div class="col-md-9"><input type="email" value="{{ $employee->employeeEmail }}" name="employeeEmail" id="employeeEmail"/></div>
                           </div> 
                            <div class="row-form clearfix">
                                <div class="col-md-3">Phone</div>
                                <div class="col-md-9"><input type="text" value="{{ $employee->employeePhone }}" name="employeePhone" id="employeePhone"/></div>
                           </div>
                             <div class="row-form clearfix">
                                <div class="col-md-3">Joining Date</div>
                                <div class="col-md-6"><input type="date" value="{{ $employee->joiningDate }}" name="joiningDate" id="joiningDate"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">NID</div>
                                <div class="col-md-6"><input type="text" value="{{ $employee->nationalId }}" name="nationalId" id="nationalId"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Address</div>
                                <div class="col-md-9"><textarea name="employeeAddress" id="employeeAddress">{{ $employee->employeeAddress }}</textarea></div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Salary</div>
                                <div class="col-md-6"><input type="number" value="{{ $employee->salary }}" name="salary" id="salary"/></div>
                            </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Change Photo</div>
                                <div class="col-md-9"><input type="file" value="" name="photo" id="photo"/></div>
                           </div>
                            </form>
                        </div>                
                           
                        </div>
                    </div>   
                    <div class="modal-footer">
                        <button class="btn btn-warning" type="submit" form="employee_edit_form{{ $i }}" aria-hidden="true">Save updates</button>
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>            
                    </div>
                </div>
            </div>
        </div>

<?php
        }
    }
?>
@endsection