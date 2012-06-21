<?php
include "includes/config.php";
include "includes/functions.php";
include "includes/connect.php";
include "includes/user.php";
include "includes/init.php";
session_start();
$login = getPostValue ( 'Eid' );
$password = getPostValue ( 'Password' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NBEC login</title>
<link rel="stylesheet" type="text/css" href="./css/style.css" />
<script type="text/javascript" src="./includes/js/login.js"></script>
</head>
<body>
<?php
if (user_valid_login($login, $password)) {
    if (user_load_variables($login)) {
        echo "TEST";
        do_redirect("home.php");

    } else {
        session_unset();
    }
} else {    
    session_unset();
}
?>
<center>
<h1>Welcome to NBEC</h1>
</center>
<div id="wrapper">
<form name="form" id="form" class="form" action="login.php"
	onsubmit="return validate(this)" method="post"><label for="Eid">Emp ID:</label>
<input type="text" size="20" name="Eid" id="Eid" /> 
<label for="Password">Password:</label> <input type="password"
	name="Password" id="Password" /> <input type="submit" value="Login"
	class="submit" /></form>
</div>
</body>
</html>

