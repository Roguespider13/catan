<!DOCTYPE html>
<head>
    <title>eCatan</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel = "stylesheet" type = "text/css" href = "css/bootstrap.css" media = "all">
    <link href='http://fonts.googleapis.com/css?family=Carrois+Gothic'
            rel='stylesheet' type='text/css'>


    <style type="text/css">
        body {
            font-family:'Carrois Gothic', sans-serif;
            padding-top: 40px;
            padding-bottom: 40px;
            background:url("images/bg.png");
        }

        .alert-error {
            font-size: 2.3em;
            margin-bottom: 10px;
             -webkit-border-radius: 5px;
               -moz-border-radius: 5px;
                    border-radius: 5px;
            width: 500px;
            margin: auto;
            margin-bottom: 25px;
            height: 30px;
            padding-top: 10px;
            padding-left: 15px;
        }

        .form-signin {
            max-width: 300px;
            padding: 19px 29px 29px;
            margin: 0 auto 20px;
            background-color: #fff;
            border: 1px solid #e5e5e5;
            -webkit-border-radius: 5px;
               -moz-border-radius: 5px;
                    border-radius: 5px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
               -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                    box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }

        .form-signin .form-signin-heading,
        .form-signin .checkbox {
            margin-bottom: 10px;
        }

        .form-signin input[type="text"],
        .form-signin input[type="password"] {
            font-size: 16px;
            height: auto;
            margin-bottom: 15px;
            padding: 7px 9px;
        }

    </style>
</head>

<body>


    <div id="container-fluid">

        <div class="row-fluid">

            <div class ="span3 offset2">

                <form class="form-signin" method="post" action="php/login.php">
                    <h2 class="form-signin-heading">Login</h2>

                    <input type="text" class="input-block-level" name="login_username" maxlength="30" placeholder="Username" /><br />
                    <input type="password" class="input-block-level" name="password" maxlength="30" placeholder="Password"/><br />
                    <button class="btn btn-large btn-primary" value="Login" name="login" type="submit">Login</button>
                </form>
            </div>
            <div class="span5">
                <form class="form-signin" method="post" action="php/register.php">
                    <h2 class="form-signin-heading">Register to Play</h2>

                    <input type="text" class="input-block-level" name="register_username" maxlength="30" placeholder="Username" /><br />
                    <input type="password" class="input-block-level" name="password1" maxlength="30" placeholder="Password"/><br />
                    <input type="password" class="input-block-level" name="password2" maxlength="30" placeholder="Confirm Password" /><br />
                    <button class="btn btn-large btn-primary" value="Register" name="register" type="submit">Register</button>
                </form>
            </div>
        </div>
        <?php
                   session_start();
                    if (isset($_SESSION['ERROR'])) {
                        echo '<div class ="alert-error">', htmlentities($_SESSION['ERROR']), '</div>';
                        unset($_SESSION['ERROR']);
                    }
                ?>
    </div>

</body>
