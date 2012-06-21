<?php
/**
 * This file contains all the function which helps in entering the data from
 * the form to the Database.
 *
 * @copyright  :2012 Gopal Krushna
 * @license    :
 * @version    :$Id$
 * @link       :
 * @since      :File available since Release 1.0
 * @author     :Gopal Krushna
 */

include "includes/FusionCharts.php";
include "includes/config.php";
include "includes/functions.php";
include "includes/connect.php";
include "includes/user.php";
include "includes/init.php";
menuHeader();
session_start();
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/framework.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/menu.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/dashboard.css\" />";
global $eid;
$eid=$_SESSION["Eid"];
if(empty($_SESSION["Eid"]))
do_redirect('login.php');
include_once ('menu.php');
$db_id=$_GET["db_id"];
$Project = getPostValue("Project");
$Task = getPostValue("Task");
$Activity = getPostValue("Activity");
$Hours = getPostValue("Hours"); 
//Body starts
        
if(empty($db_id)){  
$sql="INSERT INTO pmdb_timesheet (";
if ($Project)
$sql .= "ProjectName, ";
if ($Task)
$sql .= "Task, ";
if ($Activity)
$sql .= "ActivityID, ";
if ($Hours)
$sql .= "Hours, ";
$sql .= "EmpID, ";
$sql .= "WorkingDate ";
$sql.= ") VALUES(";
if ($Project)
$sql.="\"$Project\",";
if ($Task)
$sql.="\"$Task\",";
if ($Activity)
$sql.="\"$Activity\",";
if ($Hours)
$sql.="\"$Hours\",";
$sql.="\"$eid\",";
$sql.="\"$today\" ";
$sql.=") ";
$result = dbi_query($sql);
}
else{
$sql="UPDATE pmdb_timesheet SET ";
if($Project)
$sql.="ProjectName= \"$Project\", ";
if($Task)
$sql.="Task= \"$Task\", ";
if($Hours)
$sql.="Hours= \"$Hours\", ";
if($Activity)
$sql.="ActivityID= \"$Activity\" ";
$sql.="WHERE ID = '$db_id'";
$result = dbi_query($sql);  
}
if($result){
    echo $url;    
echo "<script type=\"text/javascript\">alert(\"The Line items has sucessfully added/updated\");";
echo "window.location=\"http://". $_SERVER['SERVER_NAME'] . "/pmdb/pmt/Timesheet.php\"</script>";
}
//Body ends
menuTrailer();
?>