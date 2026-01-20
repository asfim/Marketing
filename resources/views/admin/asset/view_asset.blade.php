@extends('admin.layouts.master')
@section('title', 'View Assets')
@section('breadcrumb', 'View Assets')
<?php $user = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->hasRole('super-admin') || $user->can('add-assets'))
        <li><a href="{{ route('asset.create') }}"><span class="glyphicon glyphicon-plus"></span> Add Asset</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h1>
                            View Assets
                            <span class="src-info">{{ (request('search_text') == '' && request('date_range') == '')?'- Last 1 Years':'- '. request('date_range') }}</span>
                        </h1>
                    </div>

                    <div class="col-md-7 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-6">
                                    <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}" class="form-control" placeholder="Enter Search Text" />
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"  class="form-control" placeholder="Date Range" />
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default search-btn">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-condensed table-hover" id="datatable">
                        <thead>
                        <tr>
                            <th>Asset Id</th>
                            <th>Asset Name</th>
                            <th>Type</th>
                            <th>Purchase Date</th>
                            <th>Purchase Amount</th>
                            <th>Salvage Value</th>
                            <th>Total Depreciation</th>
                            <th>Description</th>
                            <th>Remain Inst Amount</th>
                            <th>Inst Status</th>
                            @if($user->branchId =='')
                            <th>Branch</th>
                            @endif
                            <th class="hidden-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $ass_total = 0;$total_dep =0;
                        ?>
                        @foreach($assets as $asset)
                            <tr>
                                <td>{{ $asset->asset_id }}</td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->asset_type->name }}</td>
                                <td>{{ date('d-M-y', strtotime($asset->purchase_date)) }}</td>
                                <td>{{ number_format($asset->purchase_amount,2) }}</td>
                                <td>{{ number_format($asset->salvage_value,2) }}</td>
                                <td>{{ number_format($asset->depreciated_amount,2) }}</td>
                                <td>{{$asset->description}}</td>
                                <?php
                                    if($asset->installment_status == 1)
                                        $remain_installment_amnt = $asset->total_installment_amount - $asset->purchase_amount;
                                    else
                                        $remain_installment_amnt = 0;
                                ?>
                                <td>{{ number_format($remain_installment_amnt,2) }}</td>
                                <td>{{ ($asset->installment_status == 0)? 'No':'Yes' }}</td>
                                @if($user->branchId =='')
                                    <td>{{ $asset->branch->name??'-' }}</td>
                                @endif
                                <td class="hidden-print">
                                    @if($user->hasRole('super-admin') || $user->can('edit-assets'))
                                    <a role="button" class="edit_btn" title="Edit Asset"
                                       data-id="{{$asset->id}}"
                                       data-asset_type_id="{{$asset->asset_type_id}}"
                                       data-asset_id="{{$asset->asset_id}}"
                                       data-asset_name="{{$asset->name}}"
                                       data-purchase_date="{{$asset->purchase_date}}"
                                       data-installment_status="{{$asset->installment_status}}"
                                       data-purchase_amount="{{$asset->purchase_amount}}"
                                       data-salvage_value="{{$asset->salvage_value}}"
                                       data-asset_life_year="{{$asset->asset_life_year}}"
                                       data-description="{{$asset->description}}"
                                       data-toggle="modal"
                                       data-target="#edit_modal"
                                    ><span class="fa fa-edit"></span></a>
                                    @endif
                                    @if($user->hasRole('super-admin') || $user->can('delete-assets'))
                                    <a href="{{ route('asset.delete',$asset->id) }}" onclick="return confirm('Are you sure to delete this asset?')"
                                       class="fa fa-trash" role="button" title="Delete Asset"></a>
                                    @endif

                                    @if($asset->installment_status == 1)
                                        <a href="{{ route('asset.installment.show',$asset->id) }}" class="fa fa-eye" role="button" title="View Installment" target="_blank"></a>
                                        <a href="{{ route('asset.installment.create',$asset->id) }}" class="fa fa-plus" role="button" title="Add Installment" target="_blank"></a>
                                    @endif

                                </td>
                            </tr>
                            <?php $ass_total += $asset->purchase_amount; $total_dep += $asset->depreciated_amount;?>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr style="background-color:#999999; color: #fff;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total = </b></td>
                            <td><b>{{'BDT '.number_format($ass_total,2) }}</b></td>
                            <td></td>
                            <td><b>{{'BDT '.number_format($total_dep,2) }}</b></td>
                            <td></td>
                            <td></td>
                            @if($user->branchId =='')
                                <td></td>
                            @endif
                            <td></td>
                            <td class="hidden-print"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

    <!-- Bootrstrap edit modal form -->
    @if($user->hasRole('super-admin') || $user->can('edit-assets'))
    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form action="{{ route('asset.update') }}" id="edit_form" method="post" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Edit Assets</h4>
                </div>
                <div class="modal-body modal-body-np">
                    <div class="row">
                        @csrf
                        <input type="hidden" name="id" id="id" value="" />
                        <div class="col-md-6">
                            <label>Select a type</label>
                            <select class="form-control" name="asset_type_id" id="asset_type_id" required>
                                <option value="">choose a option...</option>
                                @foreach($asset_types as $type)
                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Asset Id</label>
                            <input class="form-control" type="text" value="" name="asset_id" id="asset_id"/>
                        </div>
                        <div class="col-md-12">
                            <label>Asset Name</label><br>
                            <input class="form-control" type="text" value="" name="name" id="asset_name" required/>
                        </div>
                        <div class="col-md-6">
                            <label>Purchase Date</label>
                            <input class="form-control datepicker" type="text" value="" name="purchase_date" id="purchase_date" required/>
                        </div>
                        <input type="hidden" name="installment_status" id="installment_status" value="">
                            {{--<div class="col-md-6">--}}
                                {{--<label>Installment</label>--}}
                                {{--<select class="form-control" name="installment_status" id="installment_status" required>--}}
                                    {{--<option value="">choose a option...</option>--}}
                                    {{--<option value="1">Yes</option>--}}
                                    {{--<option value="0">No</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        <!--
          <div class="row-form clearfix">
                            <div class="col-md-3">Payment Mode</div>
                            <div class="col-md-6">
                                <select name="payment_mode" id="payment_mode" required>
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
                                        @foreach($banks as $bank)
                            <option value="{{$bank->id}}">{{$bank->bank_name}}</option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">Cheque/Receipt no</div>
                            <div class="col-md-3"><input type="text" value="" name="cheque_no" id="cheque_no"/></div>
                            <div class="col-md-2">Check/Receipt Date</div>
                            <div class="col-md-2"><input type="date" value="" name="cheque_date" id="cheque_date"/></div>
                       </div>
                      -->
                        <div class="col-md-6">
                            <label>Purchase Amount</label>
                            <input class="form-control" type="text" value="" name="purchase_amount" id="purchase_amount" required/>
                        </div>
                        <div class="col-md-6">
                            <label>Salvage Value</label>
                            <input class="form-control" type="text" value="" name="salvage_value" id="salvage_value" required/>
                        </div>
                        <div class="col-md-6">
                            <label>Asset Life Years</label>
                            <input class="form-control" type="text" value="" name="asset_life_year" id="asset_life_year" required/>
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" aria-hidden="true">Save Updates</button>
                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>


                </form>
            </div>
        </div>
    </div>
    @endif
    @section('page-script')
    <script>
        $(document).ready(function(){

            $(document).on('click','.edit_btn', function(){
                $('#id').val($(this).data('id'));
                $('#asset_type_id').val($(this).data('asset_type_id'));
                $('#asset_id').val($(this).data('asset_id'));
                $('#asset_name').val($(this).data('asset_name'));
                $('#purchase_date').val($(this).data('purchase_date'));
                $('#installment_status').val($(this).data('installment_status'));
                $('#purchase_amount').val($(this).data('purchase_amount'));
                $('#salvage_value').val($(this).data('salvage_value'));
                $('#asset_life_year').val($(this).data('asset_life_year'));
                $('#description').val($(this).data('description'));

                if($(this).data('installment_status') === '0'){
                    $('#male').attr('checked',true);
                }else if($(this).data('sex') === 'F'){
                    $('#female').attr('checked',true);
                }else if($(this).data('sex') === 'O'){
                    $('#other').attr('checked',true);
                }
            });
        });

    </script>
    <script>
        jQuery(document).ready(function($) {

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'assets'])!!}',
                minLenght:1,
                autoFocus:true,

            });

        });
    </script>
@endsection
@endsection

