<?php
/**
 * This file contains all the functions for getting information
 * about vacations taken by the team members. It gives a clear information what
 * is the goal and how the team members are achieving the goal of training
 *
 * @copyright  :Gopal Krushna
 * @license    :
 * @version    :1.0.0
 * @link       :vacation.php
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
if(empty($_SESSION["Eid"]))
do_redirect('login.php');
include_once ('menu.php');
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/framework.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/menu.css\" />";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/dashboard.css\" />";
echo "</br></br>";

echo "<center><input type=\"Button\" value=\"ADD Line Item\" onClick=\"parent.location='TimeSheet_entry.php?project_name=$projectName'\"/></center>";
//Create an XML data document in a string variable
$login = $_SESSION["Eid"];
$sql="SELECT p.ID, p.ProjectName, p.Task, a.Description, p.Hours FROM pmdb_timesheet p JOIN pmdb_activity a 
     ON p.ActivityID = a.ID where p.Deleted IS NULL and EmpID = '" . $login . "' ORDER BY p.WorkingDate DESC";
$result = dbi_query($sql);
$j=0;
while($row = dbi_fetch_row($result)) {
    $db_id[$j] =  $row[0];
    $ProjectName[$j] = $row[1];
    $Task[$j] = $row[2];
    $Activity[$j] = $row[3];
    $Hours[$j] = $row[4];
    $j++;
}
dbi_free_result ( $result );
echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"table\" class=\"sortable\">";
echo "<thead><tr>"; 
echo "<th><h3>Project Name</h3></th>      
      <th><h3>Task Description</h3></th>
      <th><h3>Activity Type</h3></th>
      <th><h3>Hours</h3></th>";
echo "</tr></thead><tbody>";
for($i=0;$i<count($ProjectName);$i++){
    echo"<tr>";
    echo "<td>$ProjectName[$i]</td>";
    echo"<td><a href=\"Timesheet_entry.php?project_name=$projectName&db_id=$db_id[$i]\">$Task[$i]</td>";   
    echo"<td>$Activity[$i]</td>";
    echo"<td>$Hours[$i]</td>";   
    echo "</tr>";
}
echo "</tbody></table>";
?>
<div id="controls">
<div id="perpage"><select onchange="sorter.size(this.value)">
    <option value="5">5</option>
    <option value="10" selected="selected">10</option>
    <option value="20">20</option>
    <option value="50">50</option>
    <option value="100">100</option>
</select> <span>Entries Per Page</span></div>
<div id="navigation"><img src="images/first.gif" width="16" height="16"
    alt="First Page" onclick="sorter.move(-1,true)" /> <img
    src="images/previous.gif" width="16" height="16" alt="First Page"
    onclick="sorter.move(-1)" /> <img src="images/next.gif" width="16"
    height="16" alt="First Page" onclick="sorter.move(1)" /> <img
    src="images/last.gif" width="16" height="16" alt="Last Page"
    onclick="sorter.move(1,true)" /></div>
<div id="text">Displaying Page <span id="currentpage"></span> of <span
    id="pagelimit"></span></div>
</div>
<script type="text/javascript" src="./js/script.js"></script>
<script type="text/javascript">
  var sorter = new TINY.table.sorter("sorter");
    sorter.head = "head";
    sorter.asc = "asc";
    sorter.desc = "desc";
    sorter.even = "evenrow";
    sorter.odd = "oddrow";
    sorter.evensel = "evenselected";
    sorter.oddsel = "oddselected";
    sorter.paginate = true;
    sorter.currentid = "currentpage";
    sorter.limitid = "pagelimit";
    sorter.init("table",1);
  </script>
<?php

menuTrailer();
?>