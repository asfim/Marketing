@extends('admin.layouts.master')
@section('title', 'Add Asset')
@section('breadcrumb', 'Add Asset')
<?php $user = Auth::user(); ?>
@section('content')

    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Add Asset</h1>

                    @if($user->hasRole('super-admin') || $user->can('show-asstes'))
                        <ul class="buttons mini-nav">
                            <li class="tipb" data-original-title="Asset List" style="cursor: pointer;">
                                <a href="{{ route('asset.index') }}" class="isw-left_circle"></a>
                            </li>
                        </ul>
                    @endif
                </div>
                <div class="block-fluid clearfix">
                    <form action="{{ route('asset.store') }}" method="post" enctype="multipart/form-data" id="asset_form" class="form-horizontal">
                        @csrf
                        <div class="col-md-4">
                            <label>Branch</label>
                            @if($user->branchId == '')
                                <select class="form-control" name="branchId" id="branchId">
                                    <option value="" selected>*** MAIN BRANCH ***</option>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ collect(old('branchId'))->contains($branch->id) ? 'selected':'' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}" />
                                <input class="form-control" type="text" value="{{ $user->branch->name }}" readonly/>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label>Select asset type*</label>
                            <select class="form-control" name="asset_type_id" id="asset_type_id" required>
                                <option value="">choose a option...</option>
                                @foreach($asset_types as $type)
                                    <option value="{{ $type->id }}" {{ (collect(old('asset_type_id'))->contains($type->id)) ? 'selected':'' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Include Installment*</label>
                            <select class="form-control" name="installment_status" id="installment_status" required>
                                <option value="">choose a option...</option>
                                <option value="1" {{ (collect(old('installment_status'))->contains(1)) ? 'selected':'' }}>Yes</option>
                                <option value="0" {{ (collect(old('installment_status'))->contains(0)) ? 'selected':'' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="row" id="installment_info" style="display: none;">
                                <div class="col-md-6">
                                    <label>Monthly Installment Amount*</label>
                                    <input class="form-control" type="text" value="{{ old('monthly_amount') }}" name="monthly_amount" id="monthly_amount"/>
                                </div>
                                <div class="col-md-6">
                                    <label>Total Installment Amount*</label>
                                    <input class="form-control" type="text" value="{{ old('total_installment_amount') }}" name="total_installment_amount" id="total_installment_amount"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Asset Id</label>
                            <input class="form-control" type="text"  value="{{ old('asset_id') }}" name="asset_id" id="asset_id"/>
                        </div>
                        <div class="col-md-4">
                            <label>Asset Name*</label>
                            <input class="form-control" type="text"  value="{{ old('name') }}" name="name" id="name" required/>
                        </div>
                        <div class="col-md-4">
                            <label>Purchase Date*</label>
                            <input class="form-control datepicker" type="text" value="{{ old('purchase_date') }}" name="purchase_date" id="purchase_date" required/>
                        </div>
                        <div class="col-md-12">
                            @if($user->branchId == '')
                                <div class="col-md-4">
                                    <label>Payment Mode*</label>
                                    <select class="form-control" name="payment_mode" id="payment_mode" required>
                                        <option value="">choose a option...</option>
                                        <option value="Cash" {{ (collect(old('payment_mode'))->contains('Cash')) ? 'selected':'' }}>Cash</option>
                                        <option value="Bank" {{ (collect(old('payment_mode'))->contains('Bank')) ? 'selected':'' }}>Bank</option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="payment_mode" value="Cash"/>
                            @endif

                            <div class="col-md-4" id="bank_info" style="display: none;">
                                <label>Select Bank*</label>
                                <select class="form-control" name="bank_id" id="bank_id">
                                    <option value="">choose a option...</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ (collect(old('bank_id'))->contains($bank->id)) ? 'selected':'' }}>{{$bank->bank_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Cheque/Receipt no</label>
                                <input class="form-control" type="text" value="{{ old('cheque_no') }}" name="cheque_no" id="cheque_no"/>
                            </div>
                            <div class="col-md-2">
                                <label>Check/Receipt Date</label>
                                <input class="form-control datepicker" type="text" value="{{ old('cheque_date') }}" name="cheque_date" id="cheque_date"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Purchase/Paid Amount*</label>
                            <input class="form-control" type="text" value="{{ old('purchase_amount') }}" name="purchase_amount" id="purchase_amount" required/>
                        </div>
                        <div class="col-md-4">
                            <label>Salvage Value*</label>
                            <input class="form-control" type="text" value="{{ old('salvage_value') }}" name="salvage_value" id="salvage_value" required/>
                        </div>
                        <div class="col-md-4">
                            <label>Asset Life Years*</label>
                            <input class="form-control" type="text" value="{{ old('asset_life_year') }}" name="asset_life_year" id="asset_life_year" required/>
                        </div>

                        <div class="col-md-6">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="description">{{ old('description') }}</textarea>
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
        jQuery(document).ready(function($) {

            var $state = $('#payment_mode');
            var bank_info = document.getElementById("bank_info");
            $state.change(function () {
                ///alert('Chenged');
                if ($state.val() == 'Bank') {
                    bank_info.style.display = "block";
                    $("#bank_id").prop('disabled', false);
                }else {
                    bank_info.style.display = "none";
                }

            }).trigger('change');


            var $state1 = $('#installment_status');
            var installment_info = document.getElementById("installment_info");

            $state1.change(function ()
            {
                if ($state1.val() == 1)
                {
                    installment_info.style.display = "block";
                }
                else
                {
                    installment_info.style.display = "none";
                }

            }).trigger('change');
        });
    </script>
@endsection



