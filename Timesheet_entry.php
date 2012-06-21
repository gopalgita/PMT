<?php
/**
 * This file contains all the forms which gives an graphical user interface(GUI)
 * to enter the date to a particular project
 *
 *
 * @copyright  :2012
 * @license    :Gopal Krushna
 * @version    :1.0.0
 * @link       :./Data_entry.php
 * @since      :File available since Release 1.0
 * @author     :Gopal Krushna
 *
 */
include "includes/FusionCharts.php";
include "includes/config.php";
include "includes/functions.php";
include "includes/connect.php";
include "includes/user.php";
include "includes/init.php";
menuHeader();
session_start();
if (empty($_SESSION["Eid"]))
    do_redirect('login.php');
include_once ('menu.php');
echo "</br>";

$db_id = getGetValue("db_id");
$projectName = getGetValue("projectname");
$sql = "SELECT * FROM pmdb_activity p";
$result = dbi_query($sql);
$fieldName = dbi_fetch_row($result);
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/framework.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/menu.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/dashboard.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/style.css\" />";
echo "<br><br><br><br><br>";
echo "<div id=\"dashboard\">";
echo "<form name=\"form\" id=\"form\" class=\"form\" action=\"Timesheet_entry_handler.php?db_id=$db_id\" 
      onsubmit=\"return validate(this)\" method=\"post\">";
echo "<input type=\"Hidden\" name= \"projectName\" Value=\"$projectName\">";
if (empty($db_id)) {    
    inputDashboardTemplate("Task", "Task Description", "INPUT");
    inputDashboardTemplate("Project", "Project Name", "SELECT", "changeval()", $projectName);
    inputDashboardTemplate("Activity", "Activity", "SELECT", "", "");
    inputDashboardTemplate("startdate", "WorkingDate", "DATE");
    inputDashboardTemplate("Hours", "Hours", "INPUT");    
} else {
    // Update existing data from the database
    $sql = "SELECT * FROM pmdb_timesheet s where ID = $db_id ";
    $result = dbi_query($sql);
    $i = 0;
    while ($row = dbi_fetch_row($result)) {
        inputDataToTemplate("Project", "Project Name", "INPUT", "", $projectName, $row[1], "D");
        inputDataToTemplate("Task", "Task Description", "INPUT", "", $projectName, $row[3], "D");
        inputDataToTemplate("Activity", "Activity", "SELECT", "changeval()", $projectName, $row[4], "");
        inputDataToTemplate("startdate", "Work Date", "DATE", "", "", $row[7], "");
        inputDataToTemplate("Hours", "Hours", "INPUT", "", "", $row[2], "");
    }
}
//submit button is Here
echo "<input type=\"submit\" value=\"Submit\"/>
</form>";
echo "</div><p id=\"dashboard\"></p>";
menuTrailer();
?>