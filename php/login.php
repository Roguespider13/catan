<?php
    session_start();
    require_once 'User.php';
    require_once 'UserManager.php';

    $url = "/catan2/index.php";
    if (isset($_POST['login'])) {
        try {
            $user = new User();
            $user->setUsername($_POST['login_username']);
            $user->checkPassword($_POST['password']);
            
            $um = new UserManager();
            
            if ($um->authenticateUser($_POST['login_username'], $_POST['password']) === FALSE) {
                throw new Exception("Invalid username or password");
            }

            $url = "/catan2/php/create_or_join.php";
            $_SESSION['username'] = $_POST['login_username'];
            unset($_SESSION['GAMEID']);
            header("Location: $url");
            exit;
        }
        catch (Exception $e) {
            $_SESSION['ERROR'] = $e->getMessage();
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
