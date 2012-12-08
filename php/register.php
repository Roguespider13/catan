<?php
    session_start();
    require_once 'User.php';
    require_once 'UserManager.php';

    $url = "/catan2/index.php";
    if (isset($_POST['register'])) {
        try {
            $user = new User();
            $user->setUsername($_POST['register_username']);
            $user->comparePasswords($_POST['password1'], $_POST['password2']);

            $um = new UserManager();
            $um->createUser($_POST['register_username'], $_POST['password1']);

            $url = "/catan2/php/create_or_join.php";
            $_SESSION['username'] = $_POST['register_username'];
            header("Location: $url");
            exit;
        }
        catch (Exception $e) {
            $_SESSION['ERROR'] = $e->getMessage();
            //echo $url;
            header("Location: $url");
            exit;
        }
    }
    else {
        $_SESSION['ERROR'] = "INVALID SCRIPT ENTRY";
        header("Location: $url");
        exit;
    }
?>
