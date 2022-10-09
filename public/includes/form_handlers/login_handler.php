<?php

if(isset($_POST['login_button'])){
    $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL);
    $_SESSION['log_email'] = $email;

    $user_check = pdo($pdo, "SELECT * FROM users WHERE email='$email'")->fetch();

    if(count($user_check) > 0){
        $pass_okay = password_verify($_POST['log_password'], $user_check['password']);

        if($pass_okay){
            // login sucess
            $username = $user_check['username'];
            $_SESSION['username'] = $username;

            // Reactivate account if closed.
            $user_check = pdo($pdo, "SELECT * FROM users WHERE email='$email' AND user_closed='yes'")->fetchAll();

            if(count($user_check) > 0){
                $arguments = [
                    'email' => $email,
                    'user_closed' => 'no'];
                $sql = "UPDATE users SET user_closed=:user_closed WHERE email=:email";
                pdo($pdo, $sql, $arguments);
            }

            header("Location: index.php");
            exit;
        } else{
            // Incorrect password
            array_push($error_array, "Email or password was incorrect!<br>");
        }
    } else{
        // User not found
        array_push($error_array, "Email or password was incorrect!<br>");
    }
}

?>