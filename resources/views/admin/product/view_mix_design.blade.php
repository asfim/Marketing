@extends('admin.layouts.master')
@section('title', 'View Customer\'s Mix Designs')
@section('breadcrumb', 'View Customer\'s Mix Designs')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>View {{$cust_name}} Mix Designs</h1>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>PSI</th>
                            <th>Stone</th>
                            <th>Sand</th>
                            <th>Cement</th>
                            <th>Additive</th>
                            <th>Water</th>
                            <th>Rate</th>
                            <th>Description</th>
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($designs as $design)
                            <tr>
                                <td>{{ $design->psi }}</td>
                                <td>
                                    <?php
                                    $a = 0;
                                    $stone_name_txt = $design->stone_id;
                                    $stone_id_array = array_filter(explode(',', $stone_name_txt));
                                    $stone_qty_txt = $design->stone_quantity;
                                    $stone_qty_array = array_filter(explode(',', $stone_qty_txt));
                                    foreach ($stone_id_array as $stone_name)
                                    {
                                        if(!isset($stone_qty_array[$a]))
                                        {
                                            $stone_qty_array[$a] = null;
                                        }
                                        echo $name_sto = \App\ProductName::where('id',$stone_name)->value('name') .' : '.$stone_qty_array[$a].'kg,<br> ';
                                        $a++;
                                    }?>
                                </td>
                                <td>
                                    <?php
                                    $sand_name_txt = $design->sand_id;
                                    $sand_id_array = array_filter(explode(',', $sand_name_txt));
                                    $sand_qty_txt = $design->sand_quantity;
                                    $sand_qty_array = array_filter(explode(',', $sand_qty_txt));
                                    $b = 0;
                                    foreach ($sand_id_array as $sand_name)
                                    {
                                        if(!isset($sand_qty_array[$b]))
                                        {
                                            $sand_qty_array[$b] = null;
                                        }
                                        echo $name_sa = \App\ProductName::where('id',$sand_name)->value('name') .' : '.$sand_qty_array[$b].'kg,<br> ';
                                        $b++;
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php

                                    $cement_name_txt = $design->cement_id;
                                    $cement_id_array = array_filter(explode(',', $cement_name_txt));
                                    $cement_qty_txt = $design->cement_quantity;
                                    $cement_qty_array = array_filter(explode(',', $cement_qty_txt));
                                    $d = 0;
                                    foreach ($cement_id_array as $cement_name)
                                    {
                                        if(!isset($cement_qty_array[$d]))
                                        {
                                            $cement_qty_array[$d] = null;
                                        }
                                        echo $name_ce = \App\ProductName::where('id',$cement_name)->value('name') .' : '.$cement_qty_array[$d].'kg,<br> ';
                                        $d++;
                                    }
                                    ?>
                                </td>
                                <td>

                                    <?php
                                    $chemical_name_txt = $design->chemical_id;
                                    $chemical_id_array = array_filter(explode(',', $chemical_name_txt), 'strlen');
                                    $chemical_qty_txt = $design->chemical_quantity;
                                    $chemical_qty_array = array_filter(explode(',', $chemical_qty_txt), 'strlen');
                                    $c = 0;
                                    foreach ($chemical_id_array as $chemical_name)
                                    {
                                        if(!isset($chemical_qty_array[$c]))
                                        {
                                            $chemical_qty_array[$c] = null;
                                        }
                                        echo $name_c = \App\ProductName::where('id',$chemical_name)->value('name') .' : '.$chemical_qty_array[$c].'kg,<br> ';
                                        $c++;
                                    }?>
                                </td>
                                <td>{{ $design->water .' : '.$design->water_quantity.'lt' }}</td>
                                <td>{{ $design->rate }}</td>
                                <td>{{ $design->description }}</td>
                                <td class="hidden-print">
                                    <a href="{{ route('mix.design.edit', $design->id) }}" target="_blank" class="fa fa-edit"></a>
{{--                                    <a href="{{URL::to('delete-mix-design/'.$design['id'])}}" onclick='return confirm("Are you sure you want to delete?");' class="glyphicon glyphicon-remove"></a>--}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <div class="footer" style="text-align: center;">
                            <a href="{{ route('customer.list') }}" class="btn btn-warning">Back</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
@endsection