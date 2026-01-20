<!DOCTYPE html>
<html lang="en">
<head>

    <title>CCL || Login</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
        integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <!--Main CSS-->
    <style>
        *{box-sizing:border-box}body{background:#f6f5f7;font-family:Montserrat,sans-serif;height:100vh;padding:40px 0}span{font-size:12px}a{color:#333;font-size:14px;text-decoration:none;margin:15px 0}button{border-radius:20px;border:1px solid #ff4b2b;background-color:#ff4b2b;color:#fff;font-size:12px;font-weight:700;padding:12px 45px;letter-spacing:1px;text-transform:uppercase;transition:transform 80ms ease-in}button:focus{outline:0}input{background-color:#eee;border:none;padding:12px 15px;margin:8px 0;width:100%}.sign-in-wrapper{background-color:#fff;border-radius:10px;box-shadow:0 14px 28px rgba(0,0,0,.25),0 10px 10px rgba(0,0,0,.22);position:relative;margin:0 50px;min-height:450px;padding:30px 50px 0;text-align:center}.sign-in-wrapper form a{display:inline-block;margin-top:30px}.sign-in-wrapper button{cursor:pointer}.deelko-copyright{margin-top:60px;padding:10px 0;border-top:1px solid #ddd;display:block;text-align:center;font-weight:500;font-size:12px}.deelko-copyright a{font-size:12px}
    </style>

</head>

<body>

    <main>
        
        <section id="deelko-login">
            <div class="container text-center">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">
                        <div class="sign-in-wrapper">
                            <a href="{{ url('/') }}" target="_blank"><img src="{{ asset('assets/images/logo.png') }}" class="logo" alt="" title="Visit Website"></a>
                            <hr class="mb-5">

@yield('content')
 
                            <span class="deelko-copyright">
                                <a target="_blank" href="http://deelko.com"><img src="{{ asset('assets/images/deelko-logo.png') }}" width="120" class="deelko-logo mb-1" alt=""></a><br>
                                Made With <i class="fa fa-heart"></i> by
                                <a target="_blank" href="http://deelko.com">Deelko</a>
                                
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>


    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>

</body>

</html>