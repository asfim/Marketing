@extends('admin.layouts.master')
@section('title', '403')
@section('content')
    <div class="workplace">
        <?php
        $msg = Session::get('message');
        if($msg)
        {
            echo '<div class="col-md-12">
                                <div class="alert alert-info">'
                .$msg.
                '</div>
                              </div>';
            Session::put('message',null);
        }
        ?>

        <div class="row">
            <div class="col-md-12">
                <h1 style="color: red;">You don't have permission.</h1>
            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>


@endsection