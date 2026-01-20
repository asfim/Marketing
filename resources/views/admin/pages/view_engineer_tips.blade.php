@extends('admin.layouts.master')
@section('title', 'Engineer Tips Details')
@section('breadcrumb', 'Engineer Tips Details')
@section('page-script')
<script>
    
    $(document).ready(function() {
    $('#btn_search').on('click',function(){
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    if(to_date < from_date)
    {
        alert('The To date is less then from date');
        return false;
    } 
    });
    
    $("#search_name").autocomplete({
  source : '{!!URL::route('autoComplete',['table_name' => 'customers'])!!}',
  minLenght:1,
  autoFocus:true,
  
});
    $('#tSortable_2').DataTable({
        dom: 'flBrtip',
        "lengthMenu": [[100, 200, 500, 1000, -1], [100, 200, 500, 1000, "All"]],
        buttons: [
            {
            extend: 'print',
            text: 'Print page',
            autoPrint: true,
            exportOptions: {
                columns: '1,2,3,4,5,6,7,8',
            },
           customize: function ( win ) {
                    $(win.document.body).find('h1')
                        .after(
                            $("#statement_info")
                        ).css('text-align','center');;
            },
        }
        ],
        
    } );
} );
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
                            <h3>View Engineer Tips Details</h3>
                            <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                              <form action="{{ route('searchEngineerTips') }}" method="post" enctype="multipart/form-data" id="search_all_form" class="form-horizontal">

                                  <div class="" align="right">
                                      {{csrf_field()}}
                                      <input type="text" name="search_name" id="search_name" placeholder="Enter search Name" />
                                      <input type="date" name="from_date" id="from_date" placeholder="From Date" />
                                      <input type="date" name="to_date" id="to_date" placeholder="To Date" />

                                      <button type="submit" id="btn_search" class="btn btn-default">Search</button>
                                  </div>

                              </form>
                            </div>
                      </div>
                       <div class="block-fluid table-sorting clearfix">
                           <div id="statement_info">
                           </div>
                            <table cellpadding="0" cellspacing="0" width="100%" class="table" id="tSortable_2">
                                <thead>
                                    <tr>
                                        <th width="3%"><input type="checkbox" name="checkall"/></th>
                                        <th width="5%">Trann Id</th>
                                        <th width="7%">Date</th>
                                        <th width="7%">Customer Name</th>
                                        <th width="18%">Description</th>               
                                        <th width="10%">Debit</th> 
                                        <th width="10%">Credit</th>
                                        <th width="10%">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;$bal_f = 0;$debit = 0; $credit = 0;$total_qty= 0; ?>
                                    @foreach($all_statements as $statement) 
                                    <tr>
                                        <td><input type="checkbox" name="checkbox"/></td>
                                        <td>{{$statement->transaction_id}}</td>
                                        <td><?php echo date('d-m-Y', strtotime($statement->posting_date)); ?></td>
                                        <td>{{$statement->customer_name}}</td>
                                        <td>{{$statement->description}}</td>
                                        <td>{{$statement->debit}}</td>
                                        <td>{{$statement->credit}}</td>
                                        <td>{{$statement->balance}}</td>
                                    </tr>
                                    <?php $i++; $debit += $statement->debit;  $credit += $statement->credit; ?>
                                    @endforeach
                                <tr>
                                <td></td>
                                <td></td>
                                <td></td> 
                                <td></td> 
                                <td><b>Total:</b></td>
                                <td><b>{{'BDT '.round($debit,3)}}</b></td>
                                <td><b>{{'BDT '.round($credit,3)}}</b></td>
                                <td><b>{{'BDT '.round($debit - $credit)}}</b></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>

                
                <div class="dr"><span></span></div>

            </div>

@endsection
