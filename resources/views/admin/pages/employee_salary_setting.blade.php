@extends('admin.layouts.master')
@section('title', 'Employee Salary Setting')
@section('breadcrumb', 'Employee Salary Setting')
@section('content')
<div class="workplace">

                <div class="row">
                    <div class="col-md-12">
                      <div class="head clearfix">
                            <div class="isw-documents"></div>
                            <h1>Employee Salary Setting</h1>
                        </div>
                        <div class="block-fluid">
                            <form action="{{ route('employee.salarySet') }}" method="post" enctype="multipart/form-data" id="employee_form" class="form-horizontal">
                            {{csrf_field()}}

                            <div class="row-form clearfix">
                                <div class="col-md-3">Employee</div>
                                <div class="col-md-6">
                                    <select name="employee_select" id="employee_select">
                                        <option value="">------ Select Employee ------</option>
                                        <?php
                                            if(count($employee_data_array) > 0)
                                            {
                                                for($i=0; $i<count($employee_data_array); $i++)
                                                {
                                                    echo html_entity_decode($employee_data_array[$i]);
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                                <input type="hidden" name="employeeId" id="employeeId" value="">
                            <div class="row-form clearfix">
                                <div class="col-md-3">Employee Salary</div>
                                <div class="col-md-6"><input type="text" value="" name="salary" id="salary" readonly/></div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Payment Mode</div>
                                <div class="col-md-6">
                                    <select name="payment_mode" id="payment_mode">
                                        <option value="">choose a option...</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row-form clearfix" id="bank_info" style="display: none;">
                                <div class="col-md-1">Select Bank</div>
                                <div class="col-md-3">
                                    <select name="bank_id" id="bank_id">
                                        <option value="">choose a option...</option>
                                        <?php
                                            if(count($bank_data_array) > 0){
                                                for($i=0; $i<count($bank_data_array); $i++){
                                                    echo html_entity_decode($bank_data_array[$i]);
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-1">Check no</div>
                                <div class="col-md-3"><input type="text" value="" name="check_no" id="check_no"/></div>
                                <div class="col-md-2">Check/Receipt Date</div>
                                <div class="col-md-2"><input type="date" value="" name="cheque_date" id="cheque_date"/></div>
                            </div>

                                <div class="row-form clearfix">
                                <div class="col-md-3">Bonus</div>
                                <div class="col-md-6"><input type="text" value="" name="employeeBonus" id="employeeBonus"/></div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Total Salary</div>
                                <div class="col-md-6"><input type="text" value="" name="totalSalary" id="totalSalary" readonly/></div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Description</div>
                                <div class="col-md-6">
                                    <textarea name="description" id="description" cols="30" rows="3"></textarea>
                                </div>
                           </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Salary Month</div>
                                <div class="col-md-6">
                                    <select name="sMonth" id="sMonth">
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
                                </div>
                            </div>

                            <div class="row-form clearfix">
                                <div class="col-md-3">Payment Date</div>
                                <div class="col-md-6"><input type="date" value="" name="paymentDate" id="paymentDate"/></div>
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
        $(document).ready(function($){

            $("#employee_select").on("change", function(){

                var att     = $(this).val();
                var separator = att.split("___");
                if(att != ''){
                    $("#employeeId").val(separator[0]);
                    $("#salary").val(Math.round(separator[1]));
                    $("#totalSalary").val(Math.round(separator[1]));
                }else{
                    $("#employeeId").val("");
                    $("#salary").val("");
                    $("#totalSalary").val("");
                }
            });

            var $state = $('#payment_mode');
            var bank_info = document.getElementById("bank_info");
            $state.change(function () {
                //alert('Chenged');
                if ($state.val() == 'Bank') {
                    bank_info.style.display = "block";
                } else {
                    bank_info.style.display = "none";
                }
            }).trigger('change');

            $("#employeeBonus").on("keyup", function(){
                var bonus   = $(this).val();
                var salary  = $("#salary").val();
                var t_salary = '';
                if(bonus != '' && bonus != 0 && salary != ''){
                    t_salary    = parseInt(salary) + parseInt(bonus);
                    $("#totalSalary").val(t_salary);
                }else{
                    $("#totalSalary").val(salary);
                }
            });
        })(window.jQuery);
    </script>
@endsection




