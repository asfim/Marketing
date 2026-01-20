@extends('admin.layouts.master')
@section('title', 'View Product Details')
@section('breadcrumb', 'View Product Details')
@section('page-script')
<script>
    var t = 0;
	var new_item = 0;
        
	$(document).ready(function(e) {
            
           //update no and change color
           $("#btn_bill_update").on('click',function(){
               $("#error_div").empty();
               var bill_no = $("#bill_no_modal").val();
               var ad_qty = $("#ad_qty_modal").val();
               var ad_cost = $("#ad_cost_modal").val();
               $("#bill_no").val(bill_no);
               $("#adjustment_qty").val(ad_qty);
               $("#adjustment_cost").val(ad_cost);
               if(bill_no === "")
               {
                   $("#error_div").append("<p>Please enter bill no</p>");
                   
               }
               
               else {
                   $("#checked_bill_form").submit();
               }
           });
           
           //calculate mat cost
                $("#total_material_cost").bind('click',function(){
                        var trent = $("#truck_rent").val();
			var ubill = $("#unload_bill").val();
                        var unitp = $("#rate_per_unit").val();
			var pqty = $("#product_qty").val();
                        if(trent === "" )
                        {
                        alert('Truck rent is null..Enter a digit');
			return false;
                        }
                        if(ubill === "" )
                        {
                        alert('Unload bill is null..Enter a digit');
			return false;
                        }
                        if(unitp === "" )
                        {
                        alert('Unit price is null..Enter a digit');
			return false;
                        }
                        if(pqty === "" )
                        {
                        alert('Product quantity is null..Enter a digit');
			return false;
                        }
                        var mat_cost = parseFloat(unitp) * parseFloat(pqty);
                        parseFloat(mat_cost);
                        $("#material_cost").val(mat_cost);
                        var tmat_cost = mat_cost + parseFloat(trent) + parseFloat(ubill);
                        parseFloat(tmat_cost);
                        $("#total_material_cost").val(tmat_cost);
                        
		});
                
                $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'suppliers'])!!}',
                minLenght:1,
                autoFocus:true,
  
                });
                
               var table = $('#tSortable_2').DataTable({
                    dom: 'flBrtip',
                    "lengthMenu": [[100, 200, 500, 1000, -1], [100, 200, 500, 1000, "All"]],
                    buttons: [
                        {
                        extend: 'print',
                        text: 'Print page',
                        autoPrint: true,
                        exportOptions: {
                columns: '1,2,3,4,5,6,7,8,9,10',
            },
           customize: function ( win ) {
                    $(win.document.body).find('h1').css('text-align','center');
            },
                    }
                    ],

                } );
                
         //get selected rows data for total cost and quantity
//         $('#tSortable_2 tbody').on( 'click', 'tr', function () {
//        $(this).toggleClass('selected');
//    } );
        
        $("#tSortable_2 tbody").on( 'click', 'tr', function () {
            
        if($(this).find("input[type=checkbox]").is(':checked'))
        {
             $(this).addClass('selected');
        }
        else{$(this).removeClass('selected');}
        
    } );
    
    //for select all rows
    $("#tSortable_2 thead tr input[type=checkbox]").on( 'click', function () {

        if($(this).is(':checked'))
        {
             $("#tSortable_2 tbody tr").addClass('selected');
        }
        else{$("#tSortable_2 tbody tr").removeClass('selected');}
        
    } );
    
        var $checkbox = $("input[type=checkbox]");
        
               
           $checkbox.change(function(){
               
              var len = $("input[type=checkbox]:checked").length;
              var total_mat_cost = 0;
              var total_qty = 0;
              if(len>0)
              {
                  var rowData = table.rows('.selected').data();
                  $.each(rowData, function( index, value ) {
                   var str = rowData[index][7].split(" ");
                   total_qty = total_qty + parseFloat(str[0]);
                   total_mat_cost = total_mat_cost + parseFloat(rowData[index][9]);
                   
                    
                        });
                $("#total_pro_qty").html('<b>'+total_qty+'</b>');
                  $("#total_mat_cost").html('<b>'+total_mat_cost+'</b>'); 
                  $("#total_selected").html('<b>'+len+'</b>'); 
                        //alert(rowData[0][7]);
               $("#check_btn_div").css('display','block');   
              }
              else {$("#check_btn_div").css('display','none'); }
              len = 0;
           });
 

    
    });
    </script>
@endsection
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
                            <h1>View Product Purchase Details</h1>

                            <div class="col-md-7 search_box" style="margin-top: 4px; float: right;">
                                <div class="col-md-2">
                                    <a href="#bill_status_modal" role="button" data-toggle="modal" class="btn btn-default" id="check_btn_div" onclick ='return confirm("Are you sure you want to Check all?");'  style="display: none;">Checked</a>
                               </div>
                                
                              <form action="{{ route('productView') }}" method="post" enctype="multipart/form-data" id="search_all_form" class="form-horizontal">

                                  <div class="" align="right">
                                      {{csrf_field()}}
                                      <input type="text" name="search_name" id="search_name" placeholder="Enter Search Text" />
                                      <input type="date" name="from_date" id="from_date" placeholder="From Date" />
                                      <input type="date" name="to_date" id="to_date" placeholder="To Date" />

                                      <button type="submit" class="btn btn-default">Search</button>
                                  </div>

                              </form>
                                
                            </div>

                      </div>
                       <div class="block-fluid table-sorting clearfix">
                           <form action="{{URL::to('/purchase-bill-check')}}" method="post" enctype="multipart/form-data" id="checked_bill_form" class="form-horizontal">
                               {{csrf_field()}}
                               <input type="hidden" name="bill_no" id="bill_no" value="" />
                               <input type="hidden" name="adjustment_qty" id="adjustment_qty" value="" />
                               <input type="hidden" name="adjustment_cost" id="adjustment_cost" value="" />
                            <table cellpadding="0" cellspacing="0" width="100%" class="table" id="tSortable_2">
                                <thead>
                                    <tr>
                                        <th width="3%"><input type="checkbox" name="checkall"/></th>
                                        <th width="7%">DMR No</th>
                                        <th width="7%">Chalan No</th>
                                        <th width="10%">Received date</th>
                                        <th width="12%">Supplier Name</th> 
                                        <th width="10%">Product Name</th>
                                        <th width="5%">Bill No</th>
                                        <th width="10%">Quantity</th>
                                        <th width="10%">Rate</th>
                                        <th width="10%">Mat Cost</th>
                                        <th width="10%">Total Mat Cost</th>
                                        <th width="5%">Actions</th>                                  
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $userauth   = Auth::user();
                                        $i = 1;$t_mat = 0; $tt_mat = 0;$total_qty =0;
                                    ?>
                                    @foreach($purchases as $purchase)
                                    <tr <?php if($purchase->check_status == 1) echo 'style="color:#09b509;"';?>>
                                        <td>
                                            <?php if($purchase->check_status == 0)
                                            {
                                            echo '<input type="checkbox" id="checkbox" value="'.$purchase->id.'" name="checkbox[]"/>';
                                            }
                                            else {echo '<span class="glyphicon glyphicon-warning-sign"></span>';}
                                            ?>
                                        </td>
                                        <td>
                                            @if($userauth->hasRole('super-admin') || $userauth->can('product-purchase-list-details'))
                                                <a href="#detailpurchase<?php echo $i;?>" role="button" data-toggle="modal">{{$purchase->dmr_no}}</a>
                                            @else
                                                {{$purchase->dmr_no}}
                                            @endif
                                        </td>
                                        <td>{{$purchase->chalan_no}}</td>
                                        <td><?php echo date('d-m-Y', strtotime($purchase->received_date)); ?></td>
                                        <td><?php echo $sup_name = DB::table('suppliers')->where('id', $purchase->supplier_id)->value('name');?></td>
                                        <td><?php echo $product_name = DB::table('product_names')->where('id', $purchase->product_name_id)->value('name');?></td>
                                        <td>{{$purchase->bill_no}}</td>
                                        <td>{{round($purchase->product_qty,3)." ".$purchase->unit_type}}</td>
                                        <td>{{round($purchase->rate_per_unit,3)}}</td>
                                        <td>{{round($purchase->material_cost,3)}}</td>
                                         <td>{{round($purchase->total_material_cost,3)}}</td>
                                        <td>
                                            @if($userauth->hasRole('super-admin') || $userauth->can('product-purchase-list-details'))
                                            <a href="#pModal<?php echo $i;?>" role="button" class="glyphicon glyphicon-pencil" data-toggle="modal"></a>&nbsp
                                                <a href="#detailpurchase<?php echo $i;?>" role="button" class="glyphicon glyphicon-eye-open" data-toggle="modal"></a>
                                                <a href="{{URL::to('delete-product/'.$purchase['transaction_id'])}}" onclick='return confirm("Are you sure you want to delete?");' class="glyphicon glyphicon-remove"></a>
                                            @endif
                                            
                                        </td>                                 
                                    </tr>
                                    <?php $i++; $t_mat += $purchase->material_cost; $tt_mat += $purchase->total_material_cost; $total_qty += $purchase->product_qty;?>
                                   @endforeach
                                   <tr>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td><b>Total =</b></td>
                                       <td></td>
                                       <td><b>{{round($total_qty,3)}}</b></td>
                                       <td></td>
                                       <td><b>{{'BDT '.round($t_mat,3)}}</b></td>
                                       <td><b>{{'BDT '.round($tt_mat,3)}}</b></td>
                                       <td></td>
                                   </tr>                               
                                </tbody>
                            </table>
                           </form>
                        </div>
                        
                    </div>
                </div>

                
                <div class="dr"><span></span></div>

            </div>
<!-- Bootrstrap edit modal form -->
<?php $k = 1;?>
@foreach($purchases as $purchase)
        <div class="modal fade" id="pModal<?php echo $k;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>                        
                        <h4>Edit Purchase</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                            <form action="{{URL::to('/edit-product')}}" method="post" enctype="multipart/form-data" id="product_edit_form_<?php echo $k;?>" class="form-horizontal">
                            {{csrf_field()}}
                            <input type="hidden" name="id" value="{{$purchase->id}}"/>
                               <div class="row-form clearfix">
                                <div class="col-md-3">DMR No: </div>
                                <div class="col-md-6">
                                    <input type="number" value="{{$purchase->dmr_no}}" required="" readonly=""  name="dmr_no" id="dmr_no"/>
                                </div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Chalan No: </div>
                                <div class="col-md-6">
                                    <input type="text" value="{{$purchase->chalan_no}}" readonly="" required="" name="chalan_no" id="chalan_no"/>
                                </div>
                           </div>
                            
                            <div class="row-form clearfix">
                                <div class="col-md-3">Received Date : </div>
                                <div class="col-md-6">
                                    <input type="date" value="<?php echo $purchase->received_date; ?>" required="" name="received_date" id="received_date"/>
                                </div>
                           </div> 
                             <div class="row-form clearfix">
                                <div class="col-md-3">Product Name </div>
                                <div class="col-md-6">
                                   
                                    <select name="product_name_id" id="product_name_id" required="">
                                            <option value="">choose a option...</option>
                                             @foreach($product_names as $product_name)
                                            <option value="{{$product_name->id}}" <?php if($product_name->id == $purchase->product_name_id) echo 'selected';?>>{{$product_name->name}}</option>
                                              @endforeach
                                              
                                    </select>
                             
                                
                                </div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Supplier Name : </div>
                                <div class="col-md-6">
                                    <select name="supplier_id" id="supplier_name" required="">
                                            <option value="">choose a option...</option>
                                            @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}" <?php if($supplier->id == $purchase->supplier_id) echo 'selected';?>>{{$supplier->name}}</option>
                                              @endforeach
                                    </select>
                                
                                </div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Quantity :</div>
                                <div class="col-md-3">
                                    <input type="number" required="" value="{{$purchase->product_qty}}"  name="product_qty" id="product_qty" />
                                </div>
                                <div class="col-md-3">
                                    <select name="unit_type" id="unit_type" required="">
                                            <option value="">choose a option...</option>
                                            <option value="CFT" <?php if($purchase->unit_type == 'CFT') echo 'selected';?>>CFT</option>
                                            <option value="Ton" <?php if($purchase->unit_type == 'Ton') echo 'selected';?>>Ton</option>
                                            <option value="KG" <?php if($purchase->unit_type == 'KG') echo 'selected';?>>KG</option>
                                              
                                    </select> 
                                    
                                </div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Rate Per Unit :</div>
                                <div class="col-md-6"><input type="number" step="any" required=""  value="{{$purchase->rate_per_unit}}" name="rate_per_unit" id="rate_per_unit"/></div>
                           </div>
                             <div class="row-form clearfix">
                                <div class="col-md-3">Material Cost :</div>
                                <div class="col-md-6"><input type="number" step="any" required=""  value="{{$purchase->material_cost}}" name="material_cost" id="material_cost"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Truck Rent :</div>
                                <div class="col-md-6"><input type="number" step="any" value="{{$purchase->truck_rent}}"  name="truck_rent" id="truck_rent"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Unload Bill :</div>
                                <div class="col-md-6"><input type="number" step="any" value="{{$purchase->unload_bill}}"  name="unload_bill" id="unload_bill"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Total Material Cost :</div>
                                <div class="col-md-6"><input type="number" step="any"  required="" value="{{$purchase->total_material_cost}}" name="total_material_cost" id="total_material_cost"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Vehicle No</div>
                                <div class="col-md-6"><input type="text" value="{{$purchase->vehicle_no}}"  name="vehicle_no" id="vehicle_no"/></div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Description</div>
                                <div class="col-md-6"><textarea name="description"  id="description">{{$purchase->description}}</textarea></div>
                           </div>  
                            <div class="row-form clearfix">
                                <div class="col-md-3">Download/View File</div>
                                <div class="col-md-6">
                                    <?php

                                        $file_text = $purchase->file;
                                        $files = explode(",", $file_text);

                                        $created_date   = date("Y-m-d", strtotime($purchase->created_at));
                                        $create_date_separator  = explode("-", $created_date);
                                        $updated_date   = date("Y-m-d", strtotime($purchase->updated_at));
                                        $update_date_separator  = explode("-", $updated_date);

                                        $path   = public_path();
                                        if(is_dir($path)){
                                            $img_path   = public_path('img/files');
                                            $img_url    = asset('/img/files');
                                        }else{
                                            $img_path   = asset('/img/files');
                                            $img_url    = asset('/img/files');
                                        }
                                        foreach ($files as $file)
                                        {
                                            if($created_date == $updated_date) {

                                                $file_name_url  = $img_url.'/'.$create_date_separator[0].'/'.$create_date_separator[1].'/'.$create_date_separator[2].'/'.$file;
                                                $file_name  = $img_path.'/'.$create_date_separator[0].'/'.$create_date_separator[1].'/'.$create_date_separator[2].'/'.$file;
                                                //echo '<img src="'.asset('/img/files/'.$create_date_separator[0].'/'.$create_date_separator[1].'/'.$create_date_separator[2].'/'.$file.'').'">';
                                                if(file_exists($file_name)){
                                                    echo '<a href="'.URL::to($file_name_url).'" rel="tag">'.$file.'</a><br>';
                                                }
                                            }else{
                                                $file_name_url  = $img_url.'/'.$create_date_separator[0].'/'.$create_date_separator[1].'/'.$create_date_separator[2].'/'.$file;
                                                $file_name  = $img_path.'/'.$create_date_separator[0].'/'.$create_date_separator[1].'/'.$create_date_separator[2].'/'.$file;
                                                if(file_exists($file_name)){
                                                    echo '<a href="'.URL::to($file_name_url).'" rel="tag" target="_blank">'.$file.'</a><br>';
                                                }
                                            }
                                        }

                                    ?>
                                </div>
                           </div>
                         <div class="row-form clearfix">
                                <div class="col-md-3">Upload New Files</div>
                                <div class="col-md-6">
                                    <div class="uploader">
                                        <div class="uploader" id="uniform-photo">
                                            <input type="file" name="file[]" id="file" multiple />
                                        </div>
                                    </div>
                                </div>
                           </div>
                            
                            </form>
                        </div>                
                           
                        </div>
                    </div>   
                    <div class="modal-footer">
                        <button class="btn btn-warning" type="submit" form="product_edit_form_<?php echo $k;?>">Save updates</button> 
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>            
                    </div>
                </div>
            </div>
        </div>
<?php $k++;?>
                    @endforeach
                    
<!-- details modal form -->
<?php $j = 1;?>
@foreach($purchases as $purchase)
        <div class="modal fade" id="detailpurchase<?php echo $j;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" id="close_modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>                        
                        <h4>Product Purchase Details</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                            {{csrf_field()}}
                            <div class="row-form clearfix">
                                <div class="col-md-3">DMR No: </div>
                                <div class="col-md-6">
                                    {{$purchase->dmr_no}}
                                </div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Chalan No: </div>
                                <div class="col-md-6">
                                   {{$purchase->chalan_no}}
                                </div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Purchase Date : </div>
                                <div class="col-md-6">
                                <?php echo date('d-m-Y', strtotime($purchase->purchase_date)); ?>
                                </div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Received Date : </div>
                                <div class="col-md-6">
                                   <?php echo date('d-m-Y', strtotime($purchase->received_date)); ?>
                                </div>
                           </div> 
                             <div class="row-form clearfix">
                                <div class="col-md-3">Product Name </div>
                                <div class="col-md-6">

                                 <?php echo $product_name = DB::table('product_names')->where('id', $purchase->product_name_id)->value('name');?>
                                </div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Supplier Name : </div>
                                <div class="col-md-6">
                                   
                                 <?php echo $sup_name = DB::table('suppliers')->where('id', $purchase->supplier_id)->value('name');?>
                                </div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Quantity :</div>
                                <div class="col-md-6">
                                    {{$purchase->product_qty.$purchase->unit_type}}
                                </div>
                                
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Rate Per Unit :</div>
                                <div class="col-md-6">{{$purchase->rate_per_unit}}</div>
                           </div>
                             <div class="row-form clearfix">
                                <div class="col-md-3">Material Cost :</div>
                                <div class="col-md-6">{{$purchase->material_cost}}</div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Truck Rent :</div>
                                <div class="col-md-6">{{$purchase->truck_rent}}</div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Unload Bill :</div>
                                <div class="col-md-6">{{$purchase->unload_bill}}</div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Total Material Cost :</div>
                                <div class="col-md-6">{{$purchase->total_material_cost}}</div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Vehicle No</div>
                                <div class="col-md-6">{{$purchase->vehicle_no}}</div>
                           </div>
                            <div class="row-form clearfix">
                                <div class="col-md-3">Description</div>
                                <div class="col-md-6"><p>{{$purchase->description}}</p></div>
                           </div>  
                            <div class="row-form clearfix">
                                <div class="col-md-3">Download/View File</div>
                                <div class="col-md-6">
                                    <?php $file_text = $purchase->file;
                                    $files = explode(",", $file_text);
                                    foreach ($files as $file)
                                    {
                                    ?>
                                    <a href="{{URL::to('/img/files/'.$file)}}" target="_blank" rel="tag"><?php echo $file;?></a><br>
                                  <?php } ?>  
                                </div>
                           </div>
                        </div>                
                           
                        </div>
                    </div>   
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal" id="close_modal1" aria-hidden="true">Close</button>            
                    </div>
                </div>
            </div>
        </div>
<?php $j++;?>
@endforeach

<div class="modal fade" id="bill_status_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>                        
                        <h4>Add Bill No</h4>
                    </div>
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                            <div class="row-form clearfix">
                                <div class="col-md-3">Total Selected: </div>
                                <div class="col-md-6">
                                    <span id="total_selected"></span>
                                </div>
                           </div>
                           <div class="row-form clearfix">
                                <div class="col-md-3">Total Quantity: </div>
                                <div class="col-md-6">
                                    <span id="total_pro_qty"></span>
                                </div>
                           </div>
                           <div class="row-form clearfix">
                                <div class="col-md-3">Total Mat Cost: </div>
                                <div class="col-md-6">
                                    <span id="total_mat_cost"></span>
                                </div>
                           </div>
                           <div class="row-form clearfix">
                                <div class="col-md-3">Bill No: </div>
                                <div class="col-md-6">
                                    <input type="text" value="" required="" name="bill_no_modal" id="bill_no_modal"/>
                                </div>
                           </div>
                                <div class="row-form clearfix">
                                <div class="col-md-3">Adjustment Qty: </div>
                                <div class="col-md-6">
                                    <input type="number" step="any" value="" required="" name="ad_qty_modal" id="ad_qty_modal"/>
                                </div>
                           </div>
                                <div class="row-form clearfix">
                                <div class="col-md-3">Adjustment Cost: </div>
                                <div class="col-md-6">
                                    <input type="number" step="any" value="" required="" name="ad_cost_modal" id="ad_cost_modal"/>
                                </div>
                           </div>
                                <div class="col-md-12" id="error_div" style="">
                                    
                                </div>
                            
                    <div class="modal-footer">
                        <button class="btn btn-warning" type="button" id="btn_bill_update">Save updates</button> 
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>            
                    </div>
                </div>
            </div>
        </div>
                </div>
            </div>
</div>

@endsection