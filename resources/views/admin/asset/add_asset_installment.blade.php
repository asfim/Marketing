@extends('admin.layouts.master')
@section('title', 'Add Asset Installment')
@section('breadcrumb', 'Add Asset Installment')
<?php $user  = Auth::user(); ?>
@section('shortcut_menu')
    @if($user->can('add-assets'))
        <li><a href="{{ route('asset.create') }}"><span class="glyphicon glyphicon-plus"></span> Add Asset</a></li>
    @endif
@endsection
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-9">
                        <div class="isw-documents"></div>
                        <h1>Add Asset Installment:
                            <span class="src-info">{{ $asset->name }} ( ID:{{ $asset->asset_id }} ) </span>
                        </h1>
                    </div>
                    <div class="col-md-3" style="text-align: right;">
                        <h1>Remaining Amount:
                            <span class="src-info"> {{ number_format($asset->total_installment_amount - $asset->purchase_amount,2) }} </span>
                        </h1>
                    </div>

                </div>
                <div class="block-fluid clearfix">
                    <form action="{{ route('asset.installment.store') }}" method="post" class="form-horizontal">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="{{$asset->id}}"/>
                        <div class="col-md-3">
                            <label>Branch</label>
                            @if($user->hasRole(['super-admin']) || $user->can('branch-list'))
                                <select class="form-control" name="branchId" id="branchId">
                                    <option value="">----- MAIN BRANCH -----</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ (collect(old('branchId'))->contains($branch->id)) ? 'selected':'' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="branchId" id="branchId" value="{{ $user->branchId }}" />
                                <input class="form-control" type="text" value="{{ $user->branches->branchName }}" readonly/>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <label>Installment Name</label>
                            <input class="form-control" type="text"  value="{{ old('name') }}" name="name" id="name" required/>
                        </div>
                        <div class="col-md-2">
                            <label>Payment Date</label>
                            <input class="form-control datepicker" type="text" value="{{ old('date') }}" name="date" id="date" required/>
                        </div>

                        <div class="col-md-3">
                            <label>Installment Amount</label>
                            <input class="form-control" type="text" value="{{ old('installment_amount') }}" name="installment_amount" id="installment_amount" required/>
                        </div>

                        <div class="col-md-12">
                            @if($user->branchId == '')
                                <div class="col-md-4">
                                    <label>Payment Mode</label>
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
                                <label>Select Bank</label>
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
                if ($state.val() == 'Bank') {
                    bank_info.style.display = "block";
                    $("#bank_id").prop('disabled', false);
                }else {
                    bank_info.style.display = "none";
                }

            }).trigger('change');
        });
    </script>
@endsection