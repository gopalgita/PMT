<?php
/**
 * The file initializes certain variables which is common to different pages in the webpage
 *
 *
 * @copyright  :2012
 * @license    :
 * @version    :$Id$
 * @link       :./incudes/init.php
 * @since      :File available since Release 1.0
 * @author     :Gopal Satpathy
 *
 */

if (empty($PHP_SELF) && !empty($_SERVER) && !empty($_SERVER['PHP_SELF'])) {
    $PHP_SELF = $_SERVER['PHP_SELF'];
}
if (!empty($PHP_SELF) && preg_match("/\/includes\//", $PHP_SELF)) {
    die("You can't access this file directly!");
}

// db settings are in config.php

// Establish a database connection.
// This may have happened in validate.php, depending on settings.
// If not, do it now.
if (empty($c)) {
    $c = dbi_connect($db_host, $db_login, $db_password, $db_database);
}
$flag = 0;
$x = 1080;
$y = 450;
$currentYear = 2010;
//$currentYear=2011;
global $currentYear;
$dollarValue_2010 = 24;
//$dollarValue_2011=24;
$date = date("d");
$startWeekNum = 1;
// Due Forelough it is one else it is 0;
//$startWeekNum=0;// Due Forelough it is one else it is 0;
$baseFontSize = 12;
$toolName = "Project Management Tool";
$sql = "SELECT WEEKOFYEAR(NOW()) ";
$result = dbi_query($sql);
$row = dbi_fetch_row($result);

$defaultDashboardFieldName = array("PI", "Tech Gate", "Docket No.", "Project Name", "Program Name", "Document Type", "Document No", "Document Title", "Estimated Hrs", "Actual  Hrs", "Document Recd dt", "Project Start Dt.", "Customer Due Date", "Customer Due Date", "Billing Cutoff Date", "Remarks");
$additionalDasboardFieldName = array("16" => "Revision", "Scope", "ECR number", "ECO number", "No. of Pages", "Project Code", "Task Code", "Charge Code Validity Date", "MAT Code", "AEWA/PO/IWO No.", "Invoice No.", "Dash Numbers", "Technicians", "Review", "Comments", "GRMS", "Released in PDM", "GRMS ID", "ORI Number", "CMM", "Illustration_Hrs", "Tool_art", "Tool_Style", "status", "No_of_Arts", "planned_date", "Priority", "part_site", "Editor_name", "Dept_No", "minor_defects", "major_defects", "project_code_enddt", "Task_code_enddt");
$today = date("Y-m-d");

$FusionExportPath = "http://localhost/pmdb/pmdb/includes/PHP/FCExporter.php";

$sql = "Select * from pmdb_project p";
$result = dbi_query($sql);
$i = 0;
while ($row = dbi_fetch_row($result)) {
    $PROJECTNAMES[$i] = $row[0];
    $i++;
}
dbi_free_result ( $result );
$sql = "SELECT ProgramName FROM ";
$ChartRenderingType = "2D";
$chartValues = 1;
$chartAnimation = 1;
$chartRotateValues = 1;
$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
?>