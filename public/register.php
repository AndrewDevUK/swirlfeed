<?php
session_start();

$server = 'mysql';
$db = 'swirlfeed';
$db_username = 'andrew';
$db_password = 'password';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO("mysql:dbname=$db;host=$server", $db_username, $db_password, $options);

// Declaring variables to prevent errors.
$fname = "";
$lname = "";
$email = "";
$email2 = "";
$password = "";
$password2 = "";
$date = "";
$error_array = [];

if(isset($_POST['register_button'])){

    // First name
    $fname = strip_tags($_POST['reg_fname']);   // Remove HTML/PHP tags
    $fname = str_replace(' ', '', $fname);      // Remove spaces
    $fname = ucfirst(strtolower($fname));       // Make first letter uppercase
    $_SESSION['reg_fname'] = $fname;            // Stores first name in session variable

    // Last name
    $lname = strip_tags($_POST['reg_lname']);   // Remove HTML/PHP tags
    $lname = str_replace(' ', '', $lname);      // Remove spaces
    $lname = ucfirst(strtolower($lname));       // Make first letter uppercase
    $_SESSION['reg_lname'] = $lname;            // Stores last name in session variable

    // Email
    $email = strip_tags($_POST['reg_email']);   // Remove HTML/PHP tags
    $email = str_replace(' ', '', $email);      // Remove spaces
    $email = ucfirst(strtolower($email));       // Make first letter uppercase
    $_SESSION['reg_email'] = $email;            // Stores email in session variable

    // Email confirmation
    $email2 = strip_tags($_POST['reg_email2']);   // Remove HTML/PHP tags
    $email2 = str_replace(' ', '', $email2);      // Remove spaces
    $email2 = ucfirst(strtolower($email2));       // Make first letter uppercase
    $_SESSION['reg_email2'] = $email2;            // Stores email in session variable

    // Password
    $password = strip_tags($_POST['reg_password']);   // Remove HTML/PHP tags
    $password2 = strip_tags($_POST['reg_password2']);   // Remove HTML/PHP tags

    $date = date("Y-m-d");  // Gets current date.

    if($email == $email2){
        // Check email is valid format.
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);

            // Check if email exits.
            $statement = $pdo->query("SELECT email FROM users WHERE email='$email'");
            $email_check = $statement->fetchAll();

            // Counts num rows returned.
            $num_rows = count($email_check);

            if($num_rows > 0){
                array_push($error_array, "Email already in use!<br>");
            }
        } else{
            array_push($error_array, "Invalid email format!<br>");
        }
    } else{
        array_push($error_array, "Emails do not match!<br>");
    }

    if(strlen($fname) > 25 || strlen($fname) < 2){
        array_push($error_array, "Your first name must be between 2 and 25 characters!<br>");
    }

    if(strlen($lname) > 25 || strlen($lname) < 2){
        array_push($error_array, "Your last name must be between 2 and 25 characters!<br>");
    }

    if($password != $password2){
        array_push($error_array, "Your passwords do not match!<br>");
    } else{
        if(preg_match('/[^A-Za-z0-9]/', $password)){
            array_push($error_array, "Your password must contain only letters and numbers!<br>");
        }
    }

    if(strlen($password) > 30 || strlen($password) < 5){
        array_push($error_array, "Your password must be between 5 and 30 characters!<br>");
    }

    if(empty($error_array)){
        $password = password_hash($password, PASSWORD_DEFAULT);     // Encrypt password

        // Generate username.
        $username = strtolower($fname . "_" . $lname);

        $statement = $pdo->query("SELECT username FROM users WHERE username='$username'");
        $username_check = $statement->fetchAll();

        $i = 0;
        $temp_username = $username;
        while(count($username_check) != 0){
            $i++;
            $temp_username = $username . "_" . $i;

            $statement = $pdo->query("SELECT username FROM users WHERE username='$temp_username'");
            $username_check = $statement->fetchAll();
        }
        $username = $temp_username;

        // Profile picture assignment.
        $rand = rand(1, 16);
        $profile_pic = "assets/images/profile_pics/defaults/";

        switch($rand){
            case 1:
                $profile_pic = $profile_pic = $profile_pic . "head_deep_blue.png";
                break;
            
            case 2:
                $profile_pic = $profile_pic . "head_emerald.png";
                break;
            
            case 3:
                $profile_pic = $profile_pic . "head_alizarin.png";
                break;
            
            case 4:
                $profile_pic = $profile_pic . "head_amethyst.png";
                break;
            
            case 5:
                $profile_pic = $profile_pic . "head_belize_hole.png";
                break;
            
            case 6:
                $profile_pic = $profile_pic . "head_carrot.png";
                break;
            
            case 7:
                $profile_pic = $profile_pic . "head_green_sea.png";
                break;
            
            case 8:
                $profile_pic = $profile_pic . "head_nephritis.png";
                break;
            
            case 9:
                $profile_pic = $profile_pic . "head_pete_river.png";
                break;
            
            case 10:
                $profile_pic = $profile_pic . "head_pomegranate.png";
                break;
            
            case 11:
                $profile_pic = $profile_pic . "head_pumpkin.png";
                break;
            
            case 12:
                $profile_pic = $profile_pic . "head_red.png";
                break;
            
            case 13:
                $profile_pic = $profile_pic . "head_sun_flower.png";
                break;
            
            case 14:
                $profile_pic = $profile_pic . "head_turquoise.png";
                break;
            
            case 15:
                $profile_pic = $profile_pic . "head_wet_asphalt.png";
                break;
            
            case 16:
                $profile_pic = $profile_pic . "head_wisteria.png";
                break;
            default:
                $profile_pic = $profile_pic . "head_deep_blue.png";
                break;
        }

        $sql = "INSERT INTO users (first_name,
                                    last_name,
                                    username,
                                    email,
                                    password,
                                    signup_date,
                                    profile_pic,
                                    num_posts,
                                    num_likes,
                                    user_closed,
                                    friend_array)
                VALUES (:first_name,
                        :last_name,
                        :username,
                        :email,
                        :password,
                        :signup_date,
                        :profile_pic,
                        :num_posts,
                        :num_likes,
                        :user_closed,
                        :friend_array)";
        
        $new_user = [
            'first_name' => $fname,
            'last_name' => $lname,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'signup_date' => $date,
            'profile_pic' => $profile_pic,
            'num_posts' => 0,
            'num_likes' => 0,
            'user_closed' => 'no',
            'friend_array' => ','];


        $statement = $pdo->prepare($sql);
        $statement->execute($new_user);

        array_push($error_array, "<span style='color: #14C800'>You're all set! Go ahead and login!</span><br>");

        // Clear session variables.
        $_SESSION['reg_fname'] = "";
        $_SESSION['reg_lname'] = "";
        $_SESSION['reg_email'] = "";
        $_SESSION['reg_email2'] = "";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Swirlfeed</title>
</head>
<body>
    <form action="register.php" method="POST">
        <input type="text" name="reg_fname" placeholder="First Name" value="<?php
            if(isset($_SESSION['reg_fname'])){
                echo $_SESSION['reg_fname'];
            } ?>" required>
        <br>
        <?php if(in_array("Your first name must be between 2 and 25 characters!<br>", $error_array)) echo "Your first name must be between 2 and 25 characters!<br>";?>

        <input type="text" name="reg_lname" placeholder="Last Name" value="<?php
            if(isset($_SESSION['reg_lname'])){
                echo $_SESSION['reg_lname'];
            } ?>" required>
        <br>
        <?php if(in_array("Your last name must be between 2 and 25 characters!<br>", $error_array)) echo "Your last name must be between 2 and 25 characters!<br>";?>

        <input type="email" name="reg_email" placeholder="Email" value="<?php
            if(isset($_SESSION['reg_email'])){
                echo $_SESSION['reg_email'];
            } ?>" required>
        <br>

        <input type="email" name="reg_email2" placeholder="Confirm Email" value="<?php
            if(isset($_SESSION['reg_email2'])){
                echo $_SESSION['reg_email2'];
            } ?>" required>
        <br>
        <?php if(in_array("Email already in use!<br>", $error_array)) echo "Email already in use!<br>";
                else if(in_array("Invalid email format!<br>", $error_array)) echo "Invalid email format!<br>";
                else if(in_array("Emails do not match!<br>", $error_array)) echo "Emails do not match!<br>";?>

        <input type="password" name="reg_password" placeholder="Password" required>
        <br>
        <input type="password" name="reg_password2" placeholder="Confirm Password" required>
        <br>
        <?php if(in_array("Your passwords do not match!<br>", $error_array)) echo "Your passwords do not match!<br>";
                else if(in_array("Your password must contain only letters and numbers!<br>", $error_array)) echo "Your password must contain only letters and numbers!<br>";
                else if(in_array("Your password must be between 5 and 30 characters!<br>", $error_array)) echo "Your password must be between 5 and 30 characters!<br>";?>

        <input type="submit" name="register_button" value="Register">
        <br>

        <?php if(in_array("<span style='color: #14C800'>You're all set! Go ahead and login!</span><br>", $error_array)) echo "<span style='color: #14C800'>You're all set! Go ahead and login!</span><br>";?>
    </form>
</body>
</html>