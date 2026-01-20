@extends('admin.layouts.master')
@section('title', 'Edit Mix Design')
@section('breadcrumb', 'Edit Mix Design')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Edit Mix Design -
                        <span class="src-info">(Enter data in any field if needed, otherwise remain it blank)</span>
                    </h1>
                </div>
                <div class="block-fluid">
       
                    <form action="{{ route('mix.design.update') }}" method="post" class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $mix_design_row->id }}" />
                        <input type="hidden" name="cust_id" value="{{ $mix_design_row->customer_id }}" />

                        <div class="row-form clearfix">
                            <div class="col-md-4"><b>Customer Name: </b>{{ $customer_name }}</div>
                            <div class="col-md-4"><b>PSI: </b>{{ $mix_design_row->psi }}</div>
                        </div>

                        {{-- Stone --}}
                        @php
                            $stone_ids = explode(',', $mix_design_row->stone_id);
                            $stone_qtys = explode(',', $mix_design_row->stone_quantity);
                        @endphp
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Add Stone</label>
                                <a href="javascript:void(0);" class="add_stone badge badge-success" onclick="addRow('stoneTable')">
                                    <i class="glyphicon glyphicon-plus"></i>
                                </a>
                                <table id="stoneTable" border="0" width="100%" style="border-collapse: separate;border-spacing: 8px 10px;">
                                    <tbody>
                                    @foreach($stone_ids as $index => $id)
                                        <tr>
                                            <td>
                                                <select name="stone_id[]" class="form-control" required>
                                                    <option value="">--- Select Stone ---</option>
                                                    @foreach($stones as $stone)
                                                        <option value="{{ $stone->id }}" {{ $stone->id == $id ? 'selected' : '' }}>{{ $stone->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="any" name="stone_quantity[]" class="form-control" value="{{ $stone_qtys[$index] ?? '' }}" required/>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Sand --}}
                            @php
                                $sand_ids = explode(',', $mix_design_row->sand_id);
                                $sand_qtys = explode(',', $mix_design_row->sand_quantity);
                            @endphp
                            <div class="col-md-6">
                                <label>Add Sand</label>
                                <a href="javascript:void(0);" class="add_stone badge badge-success" onclick="addRow('sandTable');">
                                    <i class="glyphicon glyphicon-plus"></i>
                                </a>
                                <table id="sandTable" border="0" width="100%" style="border-collapse: separate;border-spacing: 8px 10px;">
                                    <tbody>
                                    @foreach($sand_ids as $index => $id)
                                        <tr>
                                            <td>
                                                <select name="sand_id[]" class="form-control" required>
                                                    <option value="">--- Select Sand ---</option>
                                                    @foreach($sands as $sand)
                                                        <option value="{{ $sand->id }}" {{ $sand->id == $id ? 'selected' : '' }}>{{ $sand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="any" name="sand_quantity[]" class="form-control" value="{{ $sand_qtys[$index] ?? '' }}" required/>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Cement --}}
                        @php
                            $cement_ids = explode(',', $mix_design_row->cement_id);
                            $cement_qtys = explode(',', $mix_design_row->cement_quantity);
                        @endphp
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Add Cement</label>
                                <a href="javascript:void(0);" class="add_stone badge badge-success" onclick="addRow('cementTable');">
                                    <i class="glyphicon glyphicon-plus"></i>
                                </a>
                                <table id="cementTable" border="0" width="100%" style="border-collapse: separate;border-spacing: 8px 10px;">
                                    <tbody>
                                    @foreach($cement_ids as $index => $id)
                                        <tr>
                                            <td>
                                                <select name="cement_id[]" class="form-control" required>
                                                    <option value="">--- Select Cement ---</option>
                                                    @foreach($cements as $cement)
                                                        <option value="{{ $cement->id }}" {{ $cement->id == $id ? 'selected' : '' }}>{{ $cement->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="any" name="cement_quantity[]" class="form-control" value="{{ $cement_qtys[$index] ?? '' }}" required/>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Chemical --}}
                            @php
                                $chemical_ids = explode(',', $mix_design_row->chemical_id);
                                $chemical_qtys = explode(',', $mix_design_row->chemical_quantity);
                            @endphp
                            <div class="col-md-6">
                                <label>Add Chemical</label>
                                <a href="javascript:void(0);" class="add_stone badge badge-success" onclick="addRow('chemicalTable');">
                                    <i class="glyphicon glyphicon-plus"></i>
                                </a>
                                <table id="chemicalTable" border="0" width="100%" style="border-collapse: separate;border-spacing: 8px 10px;">
                                    <tbody>
                                    @foreach($chemical_ids as $index => $id)
                                        <tr>
                                            <td>
                                                <select name="chemical_id[]" class="form-control" required>
                                                    <option value="">--- Select Chemical ---</option>
                                                    @foreach($chemicals as $chemical)
                                                        <option value="{{ $chemical->id }}" {{ $chemical->id == $id ? 'selected' : '' }}>{{ $chemical->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="any" name="chemical_quantity[]" class="form-control" value="{{ $chemical_qtys[$index] ?? '' }}" required/>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Water, Rate & Description --}}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Add Water</label>
                                <input type="hidden" name="water" value="Water" readonly />
                                <input type="number" step="any" name="water_quantity" id="water_quantity" value="{{ $mix_design_row->water_quantity }}" />
                            </div>
                            <div class="col-md-6">
                                <label>Rate per CFT</label>
                                <input type="number" step="any" name="rate" id="rate" value="{{ $mix_design_row->rate }}" required />
                            </div>
                            <div class="col-md-12">
                                <label>Description</label>
                                <textarea name="description" id="description">{{ $mix_design_row->description }}</textarea>
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

    <script>
        function removeRow(table) {
            $("body").on('click', '.remove', function () {
                $(this).closest("tbody tr").remove();
            });
        }

        function addRow(table) {
            $("#" + table + " tbody tr").first().clone().appendTo("#" + table + " tbody")
                .append('<button class="remove btn btn-danger" onclick="removeRow(table)">X</button>')
                .find("input").val('').end()
                .find("select").val('');
        }
    </script>
@endsection






