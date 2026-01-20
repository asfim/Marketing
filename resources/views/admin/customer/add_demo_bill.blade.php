@extends('admin.layouts.master')
@section('title', 'Create Bill')
@section('breadcrumb', 'Create Bill')
@section('content')
    <div class="workplace">
        <div class="row">

            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Create Bill for - <span class="src-info">{{ $customer->name }}</span></h1>
                </div>
                <div class="block-fluid table-sorting clearfix">

                    <form action="{{ route('customer.demo.bill.store')}}" method="post" id="bill_info_form" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" required name="customer_id" value="{{$customer->id}}" />
                        <input type="hidden" required name="total_cuM" value="{{$total_cuM}}" />
                        <input type="hidden" required name="total_cft" value="{{$total_cft}}" />
                        <input type="hidden" required name="total_amount" value="{{$total_amount}}" />
                        <input type="hidden" required name="ids" value="{{$txt_ids}}" />
                        <input type="hidden" required name="mix_design_id" value="{{$mix_design_id}}" />
                        <input type="hidden" required name="rate" value="{{$rate}}" />
                        <div class="row-form clearfix">
                            <div class="col-md-4">
                                <label>Bill Date</label>*
                                <input type="text" value="" name="bill_date" id="bill_date" required class="form-control datepicker"/>
                            </div>
                            <div class="col-md-4">
                                <label>Concrete Method</label>*
                                <select name="concrete_method" id="concrete_method" required class="form-control">
                                    <option value="">choose a option...</option>
                                    <option value="Pump">Pump</option>
                                    <option value="Non Pump">Non Pump</option>
                                </select>
                            </div>
                            <div class="col-md-4" id = "pump_div" style="display: none;">
                                <label>Pump Charge</label>
                                <input type="number" name="pump_charge" id="pump_charge" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label>AIT</label>
                                <input type="number" value="" name="ait" id="ait" class="form-control"/>
                            </div>
                            <div class="col-md-4">
                                <label>VAT</label>
                                <input type="number" value="" name="vat" id="vat" class="form-control"/>
                            </div>
                            <div class="col-md-4">
                                <label>Engineer Tips</label>
                                <input type="number" value="" name="eng_tips" id="eng_tips" class="form-control"/>
                            </div>
                            <div class="col-md-12">
                                <label>Remarks</label>
                                <textarea name="description" id="description" class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>SL No</th>
                            <th>Challan no</th>
                            <th>PSI</th>
                            <th>Qty(Cu.M)</th>
                            <th>Qty(Cft)</th>
                            <th>Sell Date</th>
                            <th>Rate(Per CFT)</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                        </tr>

                        </thead>

                        <tbody>
                        {!! $table_rows !!}
                        <tr>
                            <td></td>
                            <td><b>Total =</b></td>
                            <td></td>
                            <td><b>{{ number_format($total_cuM,2) }}</b></td>
                            <td><b>{{ number_format($total_cft,2) }}</b></td>
                            <td></td>
                            <td><b>Total Price:</b></td>
                            <td><b>{{'BDT '. number_format($total_amount,2) }}</b></td>
                            <td></td>
                        </tr>
                        </tbody>

                    </table>

                    <div class="col-md-12">
                        <div class="footer" style="text-align: center;">
                            <button type="submit" id="btn_submit" class="btn btn-primary">Save Bill</button>
                            {{--<a href="{{URL::to('/view-challan-list')}}" class="btn btn-default">Cancel</a>--}}
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>


@endsection

@section('page-script')
    <script>
        $(document).ready(function() {

            $("#btn_submit").on('click',function(){
                $("#bill_info_form").submit();
            });

            $("#concrete_method").change(function(){
                var con_method = $("#concrete_method").val();
                if(con_method === "Pump")
                {
                    $("#pump_div").show();
                }

                else{$("#pump_div").hide();}
            });


        });
    </script>
@endsection
