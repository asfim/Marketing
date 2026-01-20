@extends('admin.layouts.master')
@section('title', 'General Settings')
@section('breadcrumb', 'Add User')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>General Settings</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('config.store') }}" method="post" enctype="multipart/form-data" id="user_form" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <label>Company Name*</label>
                                <input type="text" value="{{ $settings['company_name'] }}" name="company_name"  required/>
                            </div>
                            <div class="col-md-6">
                                <label>Email Address</label>
                                <input type="email" value="{{ $settings['email'] }}" name="email" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Phone Numbers</label>
                                <input type="text" value="{{ $settings['phone'] }}" name="phone" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Address</label>
                                <input type="text" value="{{ $settings['address'] }}" name="address" required/>
                            </div>
                            <div class="col-md-6">
                                <label>Invoice Prefix*</label>
                                <input type="text" value="{{ $settings['invoice_prefix'] }}" name="invoice_prefix" required/>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Company Logo</label><br>
                                        <input type="file" name="logo" id="file" class="form-control"/>
                                    </div>
                                    <div class="col-md-3">
                                        <br>
                                        @if(file_exists("assets/images/".$settings['logo']))
                                            <img src="{{ asset("assets/images/".$settings['logo']) }}" width="100">
                                        @else
                                            <img src="{{ asset('assets/images/deelko-logo.png') }}" width="100">
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label>Favicon</label><br>
                                        <input type="file" name="favicon" id="file" class="form-control"/>
                                    </div>
                                    <div class="col-md-3">
                                        <br>
                                        <img src="{{ asset("assets/images/".$settings['favicon']) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="footer" style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
@endsection



