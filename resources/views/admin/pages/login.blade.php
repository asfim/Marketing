<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>        
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <!--[if gt IE 8]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <![endif]-->
     <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>CCL - Login panel</title>

    <link rel="icon" type="image/ico" href="favicon.ico"/>
    
    <link href="{{asset('css/stylesheets.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>

        body, html {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            display:table;
        }
        body {
            display:table-cell;
            vertical-align:middle;
            background-image: url("img/login/bg-repeat.png");
        }
        form {
            display:table;/* shrinks to fit content */
            margin:auto;
        }

        .padding_text
        {
            padding: 1px;
        }

        .long_button
        {
            background-image: url('<?php echo asset('img/login/button-bg.png')?>');
            background-repeat: repeat-x;
        }

        #frm-area {
            position: relative;
            width: 638px;
            height: 413px;
            top: auto;
            left: auto;
        }


    </style>


</head>
<body>
  <div class="parent-frm">
        <form method="post" class="LoginForm" action="" id="form1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div id="frm-area">
                <div style="width: 900; border: 0px solid white;">
                    <div style="width: 626px; height: 396px; border: 5px solid #28373D; background-image: url('<?php echo asset('img/login/bg-login.png');?> ');">
                        <div style="width: 235px; height: 192px; margin: 112px 5px 5px 314px;">
                            <table width="100%" cellpadding="2">
                                <tr align="left">
                                    <td>

                                    </td>
                                </tr>
                                <tr align="left">
                                    <td>
                                        <div class="msg">
                                            @if($errors->any())
                                                @foreach($errors->all() as $error)
                                                    <p style="color: red; font-weight: 600;">{{ $error }}</p>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr align="left">
                                    <td>
                                        <span id="lblUserName" style="color:#252525;font-size:Small;">Login Name</span>
                                    </td>
                                </tr>
                                <tr align="left">
                                    <td>
                                        <input name="email" type="email" id="inputEmail" value="{{ old('email') }}" class="padding_text" style="color:#669900;border-color:#99CC00;border-style:Solid;height:20px;width:100%;" /><div
                                                style="height: 10px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr align="left">
                                    <td>
                                        <span id="lblUserPassword" style="color:#252525;font-size:Small;">Password</span>
                                    </td>
                                </tr>
                                <tr align="left">
                                    <td>
                                        <input name="password" type="password" id="inputPassword" class="padding_text" style="color:#669900;border-color:#99CC00;border-style:Solid;height:20px;width:100%;" /><div
                                                style="height: 2px;">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="left">
                                        <!--<input type="submit" name="btnLogin" value="Login"  id="btnLogin" class="button long_button" style="border-style:None;height:23px;width:20%;" />-->
                                        <a href="{{URL::to('/dashboard')}}" class="button long_button" style="border-style:None;text-decoration: none;height:23px;width:20%;">&nbsp;&nbsp;&nbsp;Login</a>
                                    </td>
                                    <td align="right"></td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td style="color: Red;">
                                        <span id="lblException" class="labelException"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
  
</body>
</html>
    

    

