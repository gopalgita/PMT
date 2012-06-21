<?php
include "includes/FusionCharts.php";
include "includes/config.php";
include "includes/functions.php";
include "includes/connect.php";
include "includes/user.php";
include "includes/init.php";
menuHeader();
session_start();
if(empty($_SESSION["Eid"]))
do_redirect('login.php');
include_once ('menu.php');
echo "welcome " . $_SESSION["EmployeeName"];
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/framework.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/menu.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/dashboard.css\" />";
?>
<?php
//Body ends
menuTrailer();
?>