@extends('admin.layouts.master')
@section('title', 'View Salary Report')
@section('breadcrumb', 'View Salary Report')
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
                            <h1>View Salary Report</h1>

                            <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                              <form action="{{ route('employee.salaryReport') }}" method="GET" enctype="multipart/form-data" id="search_all_form" class="form-horizontal">

                                  <div class="" align="right">
                                      {{csrf_field()}}
                                      <select name="month" id="month" style="height: 28px;">
                                          <option value="">----- Select Month -----</option>
                                          <option value="january">January</option>
                                          <option value="february">February</option>
                                          <option value="march">March</option>
                                          <option value="april">April</option>
                                          <option value="may">May</option>
                                          <option value="jun">Jun</option>
                                          <option value="july">July</option>
                                          <option value="august">August</option>
                                          <option value="september">September</option>
                                          <option value="october">October</option>
                                          <option value="november">November</option>
                                          <option value="december">December</option>
                                      </select>
                                      <input type="date" name="from_date" id="from_date" placeholder="From Date" />
                                      <input type="date" name="to_date" id="to_date" placeholder="To Date" />

                                      <button type="submit" class="btn btn-default">Search</button>
                                  </div>
                              </form>

                            </div>

                      </div>

                       <div class="block-fluid table-sorting clearfix">
                            <table cellpadding="0" cellspacing="0" width="100%" class="table" id="tSortable_2">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" name="checkall"/></th>
                                        <th>Transaction ID</th>
                                        <th>Employee Name</th>
                                        <th>Salary</th>
                                        <th>Bonus</th>
                                        <th>Total Salary</th>
                                        <th>Payment Mode</th>
                                        <th>Month</th>
                                        <th>Payment Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                   if(count($emp_salary_report_array) > 0) {
                                       for($i=0; $i<count($emp_salary_report_array); $i++) {
                                           echo html_entity_decode($emp_salary_report_array[$i]);
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
@endsection

@section('page-script')
    <script>
        $(document).ready(function($){

//            $(".edit_esdata").on("click", function () {
//
//                var battr   = $(this).attr("href");
//                $(battr+" form")[0].reset();
//                var ptype   = $(battr+" form #payment_mode").val();
//                if(ptype == "Cash"){
//                    $(battr+" .report_bank_info").css("display", "none");
//                }else{
//                    $(battr+" .report_bank_info").css("display", "block");
//                }
//                $(battr+" .emp_bonus").on("keyup", function(){
//                    var bonus   = $(this).val();
//                    var salary  = $(battr+" .emp_salary").val();
//                    var t_salary = '';
//                    if(bonus != '' && bonus != 0 && salary != ''){
//                        t_salary    = parseInt(salary) + parseInt(bonus);
//                        $(battr+" .emp_tsalary").val(t_salary);
//                    }else{
//                        $(battr+" .emp_tsalary").val(salary);
//                    }
//                });
//
//            });
//
//
//            $(".payment_type").on("change", function() {
//
//                var ptype   = $(this).val();
//                var parent_id = $(this).closest(".modal" ).attr("id");
//                //alert(parent_id);
//                if(ptype == 'Bank')
//                {
//                    $("#"+parent_id+" .report_bank_info").css("display", "block");
//                }else{
//                    $("#"+parent_id+" .report_bank_info").css("display", "none");
//                }
//            });


        })(window.jQuery);
    </script>
@endsection

