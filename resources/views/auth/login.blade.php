

@extends('auth.auth_master')
@section('content')
    
    <form action="{{ route('login') }}" method="post" >
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus/>
        <input type="password" name="password" placeholder="Password" required/>
        
    @if($errors->any())
        <br><span class="invalid-feedback d-block"><strong>Invalid email or password</strong></span>
    @endif
        <button type="submit"  style="margin-top:30px">Sign In</button>
    </form>
@endsection
    

    

