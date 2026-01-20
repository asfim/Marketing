@extends('admin.layouts.master')
@section('title', 'Add Mix Design')
@section('breadcrumb', 'Add Mix Design')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Mix Design Per Cu.m</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('mix.design.store') }}" method="post" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Select a Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control select2"
                                        {{ request('customer')!=''?'disabled':'' }}>
                                    <option value="">choose a option...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{$customer->id}}"
                                                {{ request('customer')==$customer->id?'selected':'' }}>{{$customer->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Select PSI</label>
                                <select name="psi" id="psi" class="form-control">
                                    <option value="">choose a option...</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="1500">1500</option>
                                    <option value="2000">2000</option>
                                    <option value="2500">2500</option>
                                    <option value="3000">3000</option>
                                    <option value="3500">3500</option>
                                    <option value="4000">4000</option>
                                    <option value="4500">4500</option>
                                    <option value="5000">5000</option>
                                    <option value="5500">5500</option>
                                    <option value="6000">6000</option>
                                    <option value="6500">6500</option>
                                    <option value="7000">7000</option>
                                </select>
                            </div>
                        </div>

                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Add Stone</label>
                                @if(count($stones)>1)
                                <a href="javascript:void(0);" class="add_stone badge badge-success" onclick="addRow('stoneTable')"><i class="glyphicon glyphicon-plus"></i></a>
                                @endif
                                <table id="stoneTable" border="0" width="100%"
                                       style="border-collapse: separate;border-spacing: 8px 10px;">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <select name="stone_id[]" id="stone_id" required class="form-control">
                                                <option value="">--- Select Stone ---</option>
                                                @foreach($stones as $stone)
                                                    <option value="{{$stone->id}}">{{$stone->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="any" placeholder="input quantity in kg per cu.m" name="stone_quantity[]" class="form-control" id="stone_quantity[]" required/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                {{--<div class="col-md-1">--}}
                                    {{--<label for=""></label><br><br>--}}
                                    {{--<button class="add_stone badge btn-sm badge-primary" onclick="return addStoneRow();"><i class="fa fa-plus"></i></button>--}}
                                {{--</div>--}}
                            </div>
                            <div class="col-md-6">
                                <label>Add Sand</label>
                                @if(count($sands)>1)
                                <a href="javascript:void(0);" class="add_stone badge badge-success" onclick="addRow('sandTable');"><i class="glyphicon glyphicon-plus"></i> </a>
                                @endif
                                <table id="sandTable" border="0" width="100%"
                                       style="border-collapse: separate;border-spacing: 8px 10px;">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <select name="sand_id[]" id="sand_id" required class="form-control">
                                                <option value="">--- Select Sand ---</option>
                                                @foreach($sands as $sand)
                                                    <option value="{{$sand->id}}">{{$sand->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" step="any" placeholder="input quantity in kg per cu.m" name="sand_quantity[]" class="form-control" id="sand_quantity[]" required/></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Add Cement</label>
                                @if(count($cements)>1)
                                <a href="javascript:void(0);" class="add_stone badge badge-success" onclick="addRow('cementTable');"><i class="glyphicon glyphicon-plus"></i></a>
                                @endif
                                <table id="cementTable" border="0" width="100%"
                                       style="border-collapse: separate;border-spacing: 8px 10px;">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <select name="cement_id[]" id="cement_id" required class="form-control">
                                                <option value="">--- Select Cement ---</option>
                                                @foreach($cements as $cement)
                                                    <option value="{{$cement->id}}">{{$cement->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" step="any" placeholder="input quantity in kg per cu.m" name="cement_quantity[]" id="cement_quantity[]" required class="form-control"/></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <label>Add Chemical</label>
                                @if(count($cements)>1)
                                <a href="javascript:void(0);" class="add_stone badge badge-success" onclick="addRow('chemicalTable');"><i class="glyphicon glyphicon-plus"></i></a>
                                @endif
                                <table id="chemicalTable" border="0" width="100%"
                                       style="border-collapse: separate;border-spacing: 8px 10px;">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <select name="chemical_id[]" id="chemical_id" required class="form-control">
                                                <option value="">--- Select Chemical ---</option>
                                                @foreach($chemicals as $chemical)
                                                    <option value="{{$chemical->id}}">{{$chemical->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" step="any" placeholder="input quantity in kg per cu.m" name="chemical_quantity[]" id="chemical_quantity[]" required class="form-control"/></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Add Water</label>
                                <input type="hidden" value="Water" name="water" id="water" readonly/>
                                <input type="number" step="any" placeholder="input quantity in kg per cu.m" name="water_quantity" id="water_quantity" />
                            </div>
                            <div class="col-md-6">
                                <label>Rate per CFT</label>
                                <input type="number" step="any" value="" name="rate" id="rate" required/>
                            </div>
                            <div class="col-md-12">
                                <label>Description</label>
                                <textarea name="description" id="description"></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-------------------
              <form id="form1" runat="server">
                 <div>

                     <button onclick="return addRow();">click me</button>
                     <table id="myTable" border="1">
                         <tr>
                         <th>Product Name</th>
                         <th>Unit Type</th>
                         <th>Rate Per unit</th>
                         <th>Quantity</th>
                         <th>Material Cost</th>
                         </tr>
                         <tr>
                             <td><input type="text" name="product_name[]" id="product_name" /></td>
                             <td>
                                 <select name="unit_type[]" id="unit_type">
                                         <option value="cft">cft</option>
                                         <option value=ton>ton</option>
                                         <option value="kg">kg</option>

                                 </select>
                             </td>
                             <td><input type="number" step="any" name="rate_per_unit[]" id="rate_per_unit[]" /></td>
                              <td><input type="number" step="any" name="quantity[]" id="quantity[]" /></td>
                              <td><input type="number" step="any" name="material_cost[]" id="material_cost[]" /></td>
                         </tr>

                     </table>
                 </div>
                 </form>
                    ------------------->
            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
@endsection

@section('page-script')
    <script>
        function removeRow(table)
        {
            $( "body" ).on('click', '.remove', function(){
                $(this).closest("tbody tr").remove();
            });
        }
        function addRow(table)
        {
            $( "#"+table+" tbody tr" ).first().clone().appendTo( "#"+table+" tbody" ).append('<button class="remove btn btn-danger" onclick="removeRow(table)">X</button>');
        }
    </script>
@endsection





