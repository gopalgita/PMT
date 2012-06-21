<?php
/**
 * Generic Function access.
 *
 * The following are the generic functions which helps in reuse of so many files
 *
 *
 * @version    :$Id$
 * @link       :./incudes/functions.php
 * @since      :File available since Release 1.0
 * @author     :Gopal Krushna
 *
 */

/**
 * Gets the value resulting from an HTTP POST method.
 *
 * <b>Note:</b> The return value will be affected by the value of
 * <var>magic_quotes_gpc</var> in the php.ini file.
 *
 * @param string $name Name used in the HTML form
 *
 * @return string The value used in the HTML form
 *
 * @see getGetValue
 */
function getPostValue ( $name ) {
	global $HTTP_POST_VARS;

	if ( isset ( $_POST ) && is_array ( $_POST ) && ! empty ( $_POST[$name] ) ) {
		$HTTP_POST_VARS[$name] = $_POST[$name];
		return $_POST[$name];
	} else if ( ! isset ( $HTTP_POST_VARS ) ) {
		return null;
	} else if ( ! isset ( $HTTP_POST_VARS[$name] ) ) {
		return null;
	}
	return ( $HTTP_POST_VARS[$name] );
}

/**
 * Gets the value resulting from an HTTP GET method.
 *
 * <b>Note:</b> The return value will be affected by the value of
 * <var>magic_quotes_gpc</var> in the php.ini file.
 *
 * If you need to enforce a specific input format (such as numeric input), then
 * use the {@link getValue()} function.
 *
 * @param string $name Name used in the HTML form or found in the URL
 *
 * @return No Return of values
 *
 * @see getPostValue
 */
function getGetValue ( $name ) {
	global $HTTP_GET_VARS;

	if ( isset ( $_GET ) && is_array ( $_GET ) && ! empty ( $_GET[$name] ) ) {
		$HTTP_GET_VARS[$name] = $_GET[$name];
		return $_GET[$name];
	} else if ( ! isset ( $HTTP_GET_VARS ) )  {
		return null;
	} else if ( ! isset ( $HTTP_GET_VARS[$name] ) ) {
		return null;
	}
	return ( $HTTP_GET_VARS[$name] );
}
/**
 * The function returns the Billing cutoff dates
 *
 *
 * @param No paramameters
 * @returns The billing cutoff dates
 *
 *
 */
function billingcutoffdates ($yearSelect="" ) {

	if(empty($yearSelect))
	$sql="SELECT b.`BillingCutoffDates` FROM billing_calendar b";
	else
	$sql="SELECT b.`BillingCutoffDates` FROM billing_calendar b WHERE YEAR(b.`BillingCutoffDates`)= YEAR('$yearSelect')";

	$result = dbi_query($sql);
	$j=0;
	while($row = dbi_fetch_row($result))
	{
		$bcdt[$j]= $row[0];
		$j++;
	}
	dbi_free_result ( $result );

	return $bcdt;
}
/**
 * Gets the options resulting from the Database table.
 *
 *
 * If you need to enforce a specific input format (such as numeric input), then
 * use the {@link getValue()} function.
 *
 * @param string $ProjectName Project Name is as described in Level2WBS description in SAP
 * @param string $type HTML type as described
 *
 * @return string The value used in the HTML form (or URL)
 *
 * @see getPostValue
 */
function getOptions ($type) {
    $list = "";
	Switch ($type){
		case "Activity":
			$table = "pmdb_activity";
            $sql="Select * from $table ";
            $result = dbi_query($sql);
            $i=0;
            while($row = dbi_fetch_row($result)) {
                $list[$i]=$row[0];
                $i++;
            }
			break;
        case "Project":
            $table = "pmdb_projectuser";
            $sql="Select * from $table where UserId = '" . $_SESSION["Eid"] . "'";
            $result = dbi_query($sql);
            $i=0;
            while($row = dbi_fetch_row($result)) {
                $list[$i]=$row[0];
                $i++;
            }
            break;
        default:
            $list = "";  
	}	
	return $list;
}

/**
 * The function creates template for different kind of form fields
 * in the dashboard entry template.
 *
 * @param string $idHtml  Name used in HTML Form
 * @param string $labelHtml  Label used for the name $idHtml
 * @param string $type  The type defines which kind of HTML tag
 * @param string $eventFunction  the javscript function
 * @param string $projectName for a specific project
 *
 * @returns The function does not return any value
 *
 *
 */
function inputDashboardTemplate ($idHtml, $labelHtml, $type, $eventFunction, $projectName) {
	switch($type){
		case "INPUT":
			echo "<label for=\"$idHtml\">$labelHtml:</label>";
			echo "<input type=\"text\" name=\"$idHtml\" id=\"$idHtml\" />";
			echo"<br>";
			break;
		case "SELECT":
			echo "<label for=\"$idHtml\">$labelHtml :</label>";
			echo "<select name=\"$idHtml\" id=\"$idHtml\" onchange=\"$eventFunction\">";
			$idHtmltemp= getOptions($idHtml);

			echo "<option value =\"\">Please Select</option>";
			for($i=0;$i<Count($idHtmltemp);$i++){
				if(strstr($idHtmltemp[$i],":")){
					$idHtmltemp1=explode(":",$idHtmltemp[$i]);
					echo "<option value =\"$idHtmltemp[$i]\">$idHtmltemp1[0]</option>";
				}
				else
				echo "<option value =\"$idHtmltemp[$i]\">$idHtmltemp[$i]</option>";
			}
			echo "<option value =\"Rejected\">Rejected</option>";
			echo "</select><br>";
			break;
		case "SELECT_ARRAY":
			echo "<label for=\"$idHtml\">$labelHtml :</label>";
			echo "<select name=\"$idHtml\" id=\"$idHtml\" onchange=\"$eventFunction\">";
			echo "<option value =\"\">Please Select</option>";
			for($i=0;$i<Count($projectName);$i++){
				echo "<option value =\"$projectName[$i]\">$projectName[$i]</option>";
			}
			echo "</select><br>";
			break;
		case "DATE":
			echo"<br>";
            echo "<label for=\"$idHtml\">$labelHtml: (yyyy-mm-dd)</label>";
            echo "<input type=\"text\" name=\"$idHtml\" id=\"$idHtml\"";
            echo " value = \"$value\"";
            if ($disabled=="D")
            echo "Disabled=\"Disabled\"";
            echo " />";
            $idHtmlbtn="$idHtml"."btn";
            echo "<button id= \"$idHtmlbtn\"";
            if ($disabled=="D")
            echo "Disabled=\"Disabled\"";
            echo ">:::</button>";
            echo "<script type=\"text/javascript\">";
            echo "var cal = Calendar.setup({";
            echo "onSelect: function(cal) { cal.hide() }";
            echo "});";
            echo  "cal.manageFields( \"$idHtmlbtn\",\"$idHtml\", \"%Y-%m-%d\");";
            echo "cal.manageFields( \"$idHtmlbtn\", \"$idHtml\",\"%Y-%m-%d\");";
            echo "</script>";
            echo"<br>";
            break;
	}
}
/**
 * The function creates template for different kind of form fields
 * in the dashboard entry template.
 *
 * @param string $idHtml  Name used in HTML Form
 * @param string $labelHtml  Label used for the name $idHtml
 * @param string $type  The type defines which kind of HTML tag
 * @param string $eventFunction  the javscript function
 * @param string $projectName for a specific project
 * @param string $value for a HTML Inputs
 * @param string $Disabled for HTML input should be non editable mode
 *
 * @returns The function does not return any value
 *
 *
 */
function inputDataToTemplate ($idHtml, $labelHtml, $type, $eventFunction, $projectName,$value,$disabled) {

	switch($type){
		case "INPUT":
			echo "<label for=\"$idHtml\">$labelHtml:</label>";
			echo "<input type=\"text\" name=\"$idHtml\" id=\"$idHtml\"";
			echo "value = \"$value\"";
			if ($disabled=="D")
			echo "Disabled=\"Disabled\"";
			echo " />";
			echo"<br>";
			break;
		case "SELECT":
			$idHtmltemp = getOptions($idHtml);
			for($i=0;$i<count($idHtmltemp);$i++){
				if($idHtmltemp[$i]==$value){
					$j=$i;
				}
			}
			if($idHtml!="billinginfo")
			$j++;
			echo "<label for=\"$idHtml\">$labelHtml :</label>";
			echo "<select name=\"$idHtml\" id=\"$idHtml\" onchange=\"$eventFunction\">";
			if (count($idHtmltemp)!= 1){
				echo "<option value =\"\">Please Select</option>";
				for($i=0;$i<count($idHtmltemp);$i++){
					$idHtmltemp1=explode(":",$idHtmltemp[$i]);
					if($idHtmltemp1[0]==$value)
					echo "<option value =\"$idHtmltemp[$i]\" Selected>$idHtmltemp1[0]</option>";
					else
					echo "<option value =\"$idHtmltemp[$i]\">$idHtmltemp1[0]</option>";
				}
				echo "<option value =\"Rejected\">Rejected</option>";
			}
			else
			echo "<option value =\"$idHtmltemp[0]\">$idHtmltemp[0]</option>";
			echo "</select><br>";


			break;
		case "SELECT_ARRAY":
			echo "<label for=\"$idHtml\">$labelHtml :</label>";
			echo "<select name=\"$idHtml\" id=\"$idHtml\" onchange=\"$eventFunction\">";
			echo "<option value =\"\">Please Select</option>";
			for($i=0;$i<Count($projectName);$i++){
				if($projectName[$i]==$value)
				echo "<option value =\"$projectName[$i]\"Selected>$projectName[$i]</option>";
				else
				echo "<option value =\"$projectName[$i]\">$projectName[$i]</option>";
			}
			echo "</select><br>";
			break;

		case "DATE":
            echo"<br>";
			echo "<label for=\"$idHtml\">$labelHtml: (yyyy-mm-dd)</label>";
			echo "<input type=\"text\" name=\"$idHtml\" id=\"$idHtml\"";
			echo " value = \"$value\"";
			if ($disabled=="D")
			echo "Disabled=\"Disabled\"";
			echo " />";
			$idHtmlbtn="$idHtml"."btn";
			echo "<button id= \"$idHtmlbtn\"";
			if ($disabled=="D")
			echo "Disabled=\"Disabled\"";
			echo ">:::</button>";
			echo "<script type=\"text/javascript\">";
			echo "var cal = Calendar.setup({";
			echo "onSelect: function(cal) { cal.hide() }";
			echo "});";
			echo  "cal.manageFields( \"$idHtmlbtn\",\"$idHtml\", \"%Y-%m-%d\");";
			echo "cal.manageFields( \"$idHtmlbtn\", \"$idHtml\",\"%Y-%m-%d\");";
			echo "</script>";
			echo"<br>";
			break;
	}
}
/**
 * Returns the week number for specified date.
 *
 * Depends on week numbering settings.
 *
 * @param int $array the list
 *
 * @return int The dimenstion of the specified array.
 */
function countdim($array)
{
	if (is_array(reset($array)))
	$return = countdim(reset($array)) + 1;
	else
	$return = 1;

	return $return;
}

/**
 * Returns the email ID if Eid is entered
 *
 *
 * @param int Eid the list
 *
 * @return text
 */
function getEmailId($eid)
{
	$password="k4heNatH";
	$ds=ldap_connect("ldap://prodldap.honeywell.com");
	if ($ds) {
		$dn="cn=atlassian-prod,ou=ApplicationUsers,o=honeywell";
		$r=ldap_bind($ds,$dn,$password);
		$sr=ldap_search($ds, "o=honeywell","uid=E$eid");
		$info = ldap_get_entries($ds, $sr);
		for ($i=0; $i<$info["count"]; $i++) {
			$dn=$info[$i]["dn"];
			$name=$info[$i]["cn"][0] ;
			$email= $info[$i]["mail"][0];
		}
		ldap_close($ds);
	}
	else {
		echo "<h4>Unable to connect to LDAP server</h4>";
	}
	return $email;
}
/* triggers an email to whole team.
 *
 */
function emailToList($subject,$message,$list,$suffMsg="")
{
	$password="k4heNatH";
	$ds=ldap_connect("ldap://prodldap.honeywell.com");
	$dn="cn=atlassian-prod,ou=ApplicationUsers,o=honeywell";
	$r=ldap_bind($ds,$dn,$password);
	for($j=0;$j<count($list);$j++){
		$sr=ldap_search($ds, "o=honeywell","uid=E$list[$j]");
		$info = ldap_get_entries($ds, $sr);
		$emaili=$info[0]["mail"][0];
		$email.="$emaili,";
	}
	//		    $email="gopal.satpathy@honeywell.com";
	$message="<html><body>Dear Colleagues,<br><br>\"$message\"<br>$suffMsg<br>";

	$message .= "Regards,";
	$message .= "$toolName<br>(HTS-CMADS@honeywell.com)";
	$message .="</body><html>";
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	mail($email,$subject,$message,$headers);
	ldap_close($ds);


}




/**
 * Returns the week number for specified date.
 *
 * Depends on week numbering settings.
 *
 * @param int $date Weeknumber
 *
 * @return string The week number of the specified date
 */
function getBillingInformation ( $weeknum, $type ) {

	$sql="SELECT $type from billing where WeekNumber = \"$weeknum\" ";
	$result = dbi_query($sql);
	$j=0;
	while($row = dbi_fetch_row($result))
	{
		$weekend=$row[0];
	}
	return $weekend;
}

/**
 * Converts a time format HHMMSS (like 130000 for 1PM) into number of minutes past midnight.
 *
 * @param string $time Input time in HHMMSS format
 *
 * @return int The number of minutes since midnight
 */
function time_to_minutes ( $time ) {
	$h = (int) ( $time / 10000 );
	$m = (int) ( $time / 100 ) % 100;
	$num = $h * 60 + $m;
	return $num;
}
/**
 * Gets the Number of days from two dats
 *
 * @param string MM/DD/YYYY

 *
 * @return int he no. of business days between two dates and it skips the holidays
 */

function date_difference($start_date, $end_date, $workdays_only = false, $skip_holidays = false){
	$start_date = strtotime($start_date);
	$end_date = strtotime($end_date);
	$seconds_in_a_day = 86400;
	$sunday_val = "0";
	$saturday_val = "6";
	$workday_counter = 0;
	$holiday_array = array();

	$ptr_year = intval(date("Y", $start_date));
	$holiday_array[$ptr_year] = get_holidays(date("Y", $start_date));

	for($day_val = $start_date; $day_val <= $end_date; $day_val+=$seconds_in_a_day){
		$pointer_day = date("w", $day_val);
		if($workdays_only == true){
			if(($pointer_day != $sunday_val) AND ($pointer_day != $saturday_val)){
				if($skip_holidays == true){
					if(intval(date("Y", $day_val))!=$ptr_year){
						$ptr_year = intval(date("Y", $day_val));
						$holiday_array[$ptr_year] = get_holidays(date("Y", $day_val));
					}
					if(!in_array($day_val, $holiday_array[date("Y", $day_val)])){
						$workday_counter++;
					}
				}else{
					$workday_counter++;
				}
			}
		}else{
			if($skip_holidays == true){
				if(intval(date("Y", $day_val))!=$ptr_year){
					$ptr_year = intval(date("Y", $day_val));
					$holiday_array[$ptr_year] = get_holidays(date("Y", $day_val));
				}
				if(!in_array($day_val, $holiday_array[date("Y", $day_val)])){
					$workday_counter++;
				}
			}else{
				$workday_counter++;
			}
		}
	}
	return $workday_counter;
}

/**
 * Takes a date in yyyy-mm-dd format and returns a PHP timestamp
 *
 * @param string $MySqlDate
 * @return unknown
 */
function get_timestamp($MySqlDate){

	$date_array = explode("-",$MySqlDate); // split the array

	$var_year = $date_array[0];
	$var_month = $date_array[1];
	$var_day = $date_array[2];

	$var_timestamp = mktime(0,0,0,$var_month,$var_day,$var_year);
	return($var_timestamp); // return it to the user
}

/**
 * Returns the date of the $ord $day of the $month.
 * For example ordinal_day(3, 'Sun', 5, 2001) returns the
 * date of the 3rd Sunday of May (ie. Mother's Day).
 *
 * @author  heymeadows@yahoo.com
 *
 * @param int $ord
 * @param string $day (must be 3 char abbrev, per date("D);)
 * @param int $month
 * @param int $year
 * @return unknown
 */
function ordinal_day($ord, $day, $month, $year) {

	$firstOfMonth = get_timestamp("$year-$month-01");
	$lastOfMonth  = $firstOfMonth + date("t", $firstOfMonth) * 86400;
	$dayOccurs = 0;

	for ($i = $firstOfMonth; $i < $lastOfMonth ; $i += 86400){
		if (date("D", $i) == $day){
			$dayOccurs++;
			if ($dayOccurs == $ord){
				$ordDay = $i;
			}
		}
	}
	return $ordDay;
}

function memorial_day($inc_year){
	for($date_stepper = intval(date("t", strtotime("$inc_year-05-01"))); $date_stepper >= 1; $date_stepper--){
		if(date("l", strtotime("$inc_year-05-$date_stepper"))=="Monday"){
			return strtotime("$inc_year-05-$date_stepper");
			break;
		}
	}
}


/**
 * Looks through a lists of defined holidays and tells you which
 * one is coming up next.
 *
 * @author heymeadows@yahoo.com
 *
 * @param int $inc_year The year we are looking for holidays in
 * @return array
 */
function get_holidays($inc_year){
	//$year = date("Y");
	$year = $inc_year;

	//    $holidays[] = new Holiday("New Year's Day", get_timestamp("$year-1-1"));
	$holidays[] = new Holiday("Republic Day", get_timestamp("$year-1-26"));
	//    $holidays[] = new Holiday("Labour Day", ordinal_day(1, 'Mon', 3, $year));
	//    $holidays[] = new Holiday("Anzac Day", get_timestamp("$year-4-25"));
	//$holidays[] = new Holiday("St. Patrick's Day", get_timestamp("$year-3-17"));
	// TODO: $holidays[] = new Holiday("Good Friday", easter_date($year));
	//    $holidays[] = new Holiday("Easter", easter_date($year));
	// TODO: $holidays[] = new Holiday("Easter Monday", easter_date($year));
	//    $holidays[] = new Holiday("Foundation Day", ordinal_day(1, 'Mon', 6, $year));
	//    $holidays[] = new Holiday("Queen's Birthday", ordinal_day(1, 'Mon', 10, $year));
	//$holidays[] = new Holiday("Memorial Day", memorial_day($year));
	//$holidays[] = new Holiday("Mother's Day", ordinal_day(2, 'Sun', 5, $year));
	//$holidays[] = new Holiday("Father's Day", ordinal_day(3, 'Sun', 6, $year));
	//$holidays[] = new Holiday("Independence Day", get_timestamp("$year-7-4"));
	//$holidays[] = new Holiday("Labor Day", ordinal_day(1, 'Mon', 9, $year));
	$holidays[] = new Holiday("Christmas", get_timestamp("$year-12-25"));
	$holidays[] = new Holiday("Dussera", get_timestamp("2010-10-15"));
	$holidays[] = new Holiday("Ugadi", get_timestamp("2010-3-16"));
	$holidays[] = new Holiday("kanada Rajyotshava", get_timestamp("$year-11-1"));
	$holidays[] = new Holiday("Christmas", get_timestamp("2010-3-16"));
	$holidays[] = new Holiday("Diwali", get_timestamp("2010-11-04"));
	$holidays[] = new Holiday("Diwali", get_timestamp("2010-11-05"));
	//    $holidays[] = new Holiday("Boxing Day", get_timestamp("$year-12-26"));

	$numHolidays = count($holidays) - 1;
	$out_array = array();

	for ($i = 0; $i < $numHolidays; $i++){
		$out_array[] = $holidays[$i]->date;
	}
	unset($holidays);
	return $out_array;
}

class Holiday{
	//var $name;
	//var $date;
	public $name;
	public $date;

	// Contructor to define the details of each holiday as it is created.
	function holiday($name, $date){
		$this->name   = $name;   // Official name of holiday
		$this->date   = $date;   // UNIX timestamp of date
	}
}

/**
 * Displays a time in either 12 or 24 hour format.
 *
 * The global variable $TZ_OFFSET is used to adjust the time.  Note that this
 * is somewhat of a kludge for timezone support.  If an event is set for 11PM
 * server time and the user is 2 hours ahead, it will show up as 1AM, but the
 * date will not be adjusted to the next day.
 *
 * @param string $time          Input time in HHMMSS format
 * @param bool   $ignore_offset If true, then do not use the timezone offset
 *
 * @return string The time in the user's timezone and preferred format
 *
 * @global int The user's timezone offset from the server
 */
function display_time ( $time, $ignore_offset=0 ) {
	global $TZ_OFFSET;
	$hour = (int) ( $time / 10000 );
	if ( ! $ignore_offset )
	$hour += $TZ_OFFSET;
	$min = abs( ( $time / 100 ) % 100 );
	//Prevent goofy times like 8:00 9:30 9:00 10:30 10:00
	if ( $time < 0 && $min > 0 ) $hour = $hour - 1;
	while ( $hour < 0 )
	$hour += 24;
	while ( $hour > 23 )
	$hour -= 24;
	if ( $GLOBALS["TIME_FORMAT"] == "12" ) {
		$ampm = ( $hour >= 12 ) ? "pm" : "am";
		$hour %= 12;
		if ( $hour == 0 )
		$hour = 12;
		$ret = sprintf ( "%d:%02d%s", $hour, $min, $ampm );
	} else {
		$ret = sprintf ( "%d:%02d", $hour, $min );
	}
	return $ret;
}

/**
 * Returns the full name of the specified month.
 *
 * Use {@link month_short_name()} to get the abbreviated name of the month.
 *
 * @param int $m Number of the month (0-11)
 *
 * @return string The full name of the specified month
 *
 * @see month_short_name
 */
function month_name ( $m ) {
	switch ( $m ) {
		case 0: return "January";
		case 1: return "February";
		case 2: return "March";
		case 3: return "April";
		case 4: return "May_"; // needs to be different than "May"
		case 5: return "June";
		case 6: return "July";
		case 7: return "August";
		case 8: return "September";
		case 9: return "October";
		case 10: return "November";
		case 11: return "December";
	}
	return "Dec-Phase 2";
}

/**
 * Returns the abbreviated name of the specified month (such as "Jan").
 *
 * Use {@link month_name()} to get the full name of the month.
 *
 * @param int $m Number of the month (0-11)
 *
 * @return string The abbreviated name of the specified month (example: "Jan")
 *
 * @see month_name
 */
function month_short_name ( $m ) {
	switch ( $m ) {
		case 0: return "Jan";
		case 1: return "Feb";
		case 2: return "Mar";
		case 3: return "Apr";
		case 4: return "May";
		case 5: return "Jun";
		case 6: return "Jul";
		case 7: return "Aug";
		case 8: return "Sep";
		case 9: return "Oct";
		case 10: return "Nov";
		case 11: return "Dec";
	}
	return "Dec-Phase 2";
}

/**
 * Returns the full weekday name.
 *
 * Use {@link weekday_short_name()} to get the abbreviated weekday name.
 *
 * @param int $w Number of the day in the week (0=Sunday,...,6=Saturday)
 *
 * @return string The full weekday name ("Sunday")
 *
 * @see weekday_short_name
 */
function weekday_name ( $w ) {
	switch ( $w ) {
		case 0: return "Sunday";
		case 1: return "Monday";
		case 2: return "Tuesday";
		case 3: return "Wednesday";
		case 4: return "Thursday";
		case 5: return "Friday";
		case 6: return "Saturday";
	}
	return "unknown-weekday($w)";
}
/**
 * Returns the full weekday name.
 *
 * Use {@link weekday_short_name()} to get the abbreviated weekday name.
 *
 * @param int $w Number of the day in the week (0=Sunday,...,6=Saturday)
 *
 * @return string The full weekday name ("Sunday")
 *
 * @see weekday_short_name
 */
function exportDashboardToExcel ( $projectName ) {
	require_once './../Classes/PHPExcel/IOFactory.php';
	require_once './../Classes/PHPExcel/Calculation.php';
	require_once './../Classes/PHPExcel/Shared/Date.php';
	require_once './../Classes/PHPExcel.php';



	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel,'HTML');
	$objWriter->save("$projectName.xlsx");
	$objWriter->save(str_replace('.php', '.htm', __FILE__));



}
/**
 * Returns the abbreviated weekday name.
 *
 * Use {@link weekday_name()} to get the full weekday name.
 *
 * @param int $w Number of the day in the week (0=Sunday,...,6=Saturday)
 *
 * @return string The abbreviated weekday name ("Sun")
 */
function weekday_short_name ( $w ) {
	switch ( $w ) {
		case 0: return "Sun";
		case 1: return "Mon";
		case 2: return "Tue";
		case 3: return "Wed";
		case 4: return "Thu";
		case 5: return "Fri";
		case 6: return "Sat";
	}
	return "unknown-weekday($w)";
}

/**
 * Returns the Excel(standard)date format i.e MM/DD/YYYY
 *
 * @param string In MySQL date format
 *
 * @return string Excel(standard)date format
 */
function dateExcelFormat ( $w ) {
	$w=explode("-",$w);
	$y= $w[0];
	$m= $w[1];
	$d= $w[2];
	return "$m/$d/$y";
}
/**
 * Converts a date in YYYYMMDD format into "Friday, December 31, 1999",
 * "Friday, 12-31-1999" or whatever format the user prefers.
 *
 * @param string $indate       Date in YYYYMMDD format
 * @param string $format       Format to use for date (default is "__month__
 *                             __dd__, __yyyy__")
 * @param bool   $show_weekday Should the day of week also be included?
 * @param bool   $short_months Should the abbreviated month names be used
 *                             instead of the full month names?
 * @param int    $server_time ???
 *
 * @return string Date in the specified format
 *
 * @global string Preferred date format
 * @global int    User's timezone offset from the server
 */
function date_to_str ( $indate, $format="", $show_weekday=true, $short_months=false, $server_time="" ) {
	global $DATE_FORMAT, $TZ_OFFSET;

	if ( strlen ( $indate ) == 0 ) {
		$indate = date ( "Ymd" );
	}

	$newdate = $indate;
	if ( $server_time != "" && $server_time >= 0 ) {
		$y = substr ( $indate, 0, 4 );
		$m = substr ( $indate, 4, 2 );
		$d = substr ( $indate, 6, 2 );
		if ( $server_time + $TZ_OFFSET * 10000 > 240000 ) {
			$newdate = date ( "Ymd", mktime ( 3, 0, 0, $m, $d + 1, $y ) );
		} else if ( $server_time + $TZ_OFFSET * 10000 < 0 ) {
			$newdate = date ( "Ymd", mktime ( 3, 0, 0, $m, $d - 1, $y ) );
		}
	}

	// if they have not set a preference yet...
	if ( $DATE_FORMAT == "" )
	$DATE_FORMAT = "__month__ __dd__, __yyyy__";

	if ( empty ( $format ) )
	$format = $DATE_FORMAT;

	$y = (int) ( $newdate / 10000 );
	$m = (int) ( $newdate / 100 ) % 100;
	$d = $newdate % 100;
	$date = mktime ( 3, 0, 0, $m, $d, $y );
	$wday = strftime ( "%w", $date );

	if ( $short_months ) {
		$weekday = weekday_short_name ( $wday );
		$month = month_short_name ( $m - 1 );
	} else {
		$weekday = weekday_name ( $wday );
		$month = month_name ( $m - 1 );
	}
	$yyyy = $y;
	$yy = sprintf ( "%02d", $y %= 100 );

	$ret = $format;
	$ret = str_replace ( "__yyyy__", $yyyy, $ret );
	$ret = str_replace ( "__yy__", $yy, $ret );
	$ret = str_replace ( "__month__", $month, $ret );
	$ret = str_replace ( "__mon__", $month, $ret );
	$ret = str_replace ( "__dd__", $d, $ret );
	$ret = str_replace ( "__mm__", $m, $ret );

	if ( $show_weekday )
	return "$weekday, $ret";
	else
	return $ret;
}

/* Determine if date is a weekend
 *
 * @param int $date  Timestamp of subject date OR a weekday number 0-6
 *
 * @return bool  True = Date is weekend
 */
function is_weekend ( $date ) {
	global $WEEKEND_START;

	// We can't test for empty because $date may equal 0.
	if ( ! strlen ( $date ) )
	return false;

	if ( ! isset ( $WEEKEND_START ) )
	$WEEKEND_START = 6;

	// We may have been passed a weekday 0-6.
	if ( $date < 7 )
	return ( $date == $WEEKEND_START % 7 || $date == ( $WEEKEND_START + 1 ) % 7 );

	// We were passed a timestamp.
	$wday = date ( 'w', $date );
	return ( $wday == $WEEKEND_START % 7 || $wday == ( $WEEKEND_START + 1 ) % 7 );
}
/* Is this a leap year?
 *
 * @internal JGH Borrowed isLeapYear from PEAR Date_Calc Class
 *
 * @param int $year  Year
 *
 * @return bool  True for a leap year, else false.
 *
 * @ignore
 */
function isLeapYear ( $year = '' ) {
	if ( empty ( $year ) )
	$year = strftime ( '%Y', time () );

	if ( strlen ( $year ) != 4 || preg_match ( '/\D/', $year ) )
	return false;

	return ( ( $year % 4 == 0 && $year % 100 != 0 ) || $year % 400 == 0 );
}

/* email trigger is for triggering email
 *
 * @internal JGH Borrowed isLeapYear from PEAR Date_Calc Class
 *
 * @param int $year  Year
 *
 * @return bool  True for a leap year, else false.
 *
 * @ignore
 */
function emailTrigger ( $subject, $to ) {
	$to  = 'gopal.satpathy@Honeywell.com' ;
	$subject = "HTML email";

	$message = "
<html>
<head>
<title>HTML email</title>
</head>
<body>";
	$message .="</body>
</html>
";
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	mail($to,$subject,$message,$headers);
}
/** Sends a redirect to the specified page.
 *
 * The database connection is closed and execution terminates in this function.
 *
 * <b>Note:</b> MS IIS/PWS has a bug in which it does not allow us to send a
 * cookie and a redirect in the same HTTP header.  When we detect that the web
 * server is IIS, we accomplish the redirect using meta-refresh.  See the
 * following for more info on the IIS bug:
 *
 * {@link http://www.faqts.com/knowledge_base/view.phtml/aid/9316/fid/4}
 *
 * @param string $url The page to redirect to.  In theory, this should be an
 *                    absolute URL, but all browsers accept relative URLs (like
 *                    "month.php").
 *
 * @global string   Type of webserver
 * @global array    Server variables
 * @global resource Database connection
 */
function do_redirect ( $url ) {
	global $SERVER_SOFTWARE, $_SERVER, $c;

	// Replace any '&amp;' with '&' since we don't want that in the HTTP
	// header.
	$url = str_replace ( '&amp;', '&', $url );
	if ( ( substr ( $SERVER_SOFTWARE, 0, 5 ) == "Micro" ) ||
	( substr ( $SERVER_SOFTWARE, 0, 3 ) == "WN/" ) ) {
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE html
    PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head>\n<title>Redirect</title>\n" .
      "<meta http-equiv=\"refresh\" content=\"0; url=$url\" />\n</head>\n<body>\n" .
      "Redirecting to.. <a href=\"" . $url . "\">here</a>.</body>\n</html>";
	} else {
		Header ( "Location: $url" );
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE html
    PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
    \"DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head>\n<title>Redirect</title>\n</head>\n<body>\n" .
      "Redirecting to ... <a href=\"" . $url . "\">here</a>.</body>\n</html>";
	}
	dbi_close ( $c );
	exit;
}
/*
 * email Trigger for the date passed
 */
function emailTriggerforBA ($date){

	$sql = "";

}

/*
 * Tool Tip which gives idea regarding the particular item
 */
function redirect($url){
	echo "<script type=\"text/javascript\">window.location=\"http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/$url\";</script>";
	dbi_close ( $c );
	exit;
}
/*
 *  The funciton helps in the putting the menu in all the pages
 *  It also describes  the whole menu
 */
function menuHeader($css=""){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NBEC Login</title>
<?php
if(empty($css))
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./includes/css/style.css\" /></head><body> ";
else
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./includes/css/$css.css\" /></head><body> ";
}
/*
 * Get the Billing calendar for a particular Year
 */
function menuTrailer(){
	echo "</body></HTML>";
}

function getProgramItem()
{
    $s = "SELECT ProgramName FROM pmdb_program p";
    $result = dbi_query($s);
    $i = 0;
    while ($row = dbi_fetch_row($result)) {        
        $programs[$i] = $row[0];
        $i++;
    }
    dbi_free_result($result);
    for ($i = 0; $i < count($programs); $i++) {
        $projects = "";
        echo "<li><a href=\"#nogo3\" class=\"fly\">$programs[$i]</a>";
            echo "<ul>";
            $sql = "SELECT ProjectName FROM pmdb_project p where ProgramName = '" . $programs[$i] . "'";
            $res = dbi_query($sql);
            $j = 0;
            while ($row = dbi_fetch_row($res)) {
                $projects[$j] = $row[0];
                $j++;
            }
            for($j = 0; $j < count($projects); $j++) {
                echo "<li><a href=\"Project.php?projectname=$projects[$j]\">$projects[$j]</a></li>";
            }
            echo "</ul>";
        echo "</li>";
    }  
}

function etranslate($txt){
	return $txt;
}

/** Project Metrics is displayed with no of tasks and No of pages.
 *
 *
 *
 * @param string ProjectName is the ProjectName as in database
 *
 * @global Current Billing date in database format
 */

function projectMetrics($ProjectName,$currbilling,$nop="",$not=""){

	$projectName=$ProjectName;
	$cur =$currbilling;
	echo "<SCRIPT LANGUAGE=\"Javascript\" SRC=\"./includes/FusionCharts.js\"></SCRIPT>";
	echo "<br><br><br>";
	$i=0;
	$j=13;

	$sql=" SELECT Distinct (b.`BillingcutoffDate`), SUM(WorkingHours)as Billing_Hours, (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)as FTE,
				SUM(b.`Workinghours`)* (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)+.1*SUM(b.`Workinghours`)* (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)as Demand_hrs,
				(SELECT SUM(p.`Actual_Hrs`+ p.`Illustration_Hrs`) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as Actual_hrs,
				(SELECT COUNT(*) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as No_of_tasks,
				(SELECT SUM(p.`No_of_Pages`) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as No_of_pages,
				(SELECT ROUND((SUM(p.`Actual_Hrs`)*60/SUM(p.`No_of_Pages`)),2) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as Minperpage
				FROM billing b
				WHERE YEAR(b.`BillingcutoffDate`) = YEAR('$currbilling')
				group by b.`BillingcutoffDate`
				order by b.`BillingcutoffDate`;";
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$billingcutoffdates[$i]= $row[0];
		if($billingcutoffdates[$i]==$cur)
		$j=$i;
		$billingDt[$i]=date("d-M-Y", strtotime("$billingcutoffdates[$i]"));
		$billingDates[$i]=date("m:F(d-M-y)", strtotime("$billingcutoffdates[$i]"));
		$billingMonth[$i]=date("M-Y", strtotime("$billingcutoffdates[$i]"));
		$standardHours[$i]=$row[1];
		$goalFte[$i]=$row[2];
		if(!empty($row[3]))
		$demandHours[$i]=$row[3];
		else
		$demandHours[$i]=0;
		if(!empty($row[4]))
		$actualHours[$i]=$row[4];
		else
		$actualHours[$i]=0;
		if(!empty($row[5]))
		$noOfTasks[$i]= $row[5];
		else
		$noOfTasks[$i]= 0;
		if(!empty($row[6]))
		$noOfPages[$i]= $row[6];
		else
		$noOfPages[$i]= 0;
		$i++;
	}
	dbi_free_result ( $result );
	$j++;
	
	$firstDay=substr($billingcutoffdates[0],0,3);
	$firstDay.="-01-01";
	for($k=0;$k<$j;$k++){
		$s="Select SUM(`sapprojectwise`.hours)as Hours from `sapprojectwise`
    		inner join pmdb_project_mapping ON `sapprojectwise`.level2WBSdescription = `pmdb_project_mapping`.level2WBSdescription
    			where `pmdb_project_mapping`.projectname Like '$projectName' AND YEAR(YearofPrj) = YEAR('$currbilling') ";
			
		if($k==0)
		$s.="AND `sapprojectwise`.`date` BETWEEN '$firstDay' AND '$billingcutoffdates[0]'";
		else{
			$l=$k-1;
			$s.="AND `sapprojectwise`.`date` BETWEEN ADDDATE('$billingcutoffdates[$l]',1) AND ADDDATE('$billingcutoffdates[$k]',1)";
		}
			
		$res = dbi_query($s);
		while($row = dbi_fetch_row($res))	{
			if(!empty($row[0]))
			$actualHrs[$k]=$row[0];
			else
			$actualHrs[$k]=0;
		}


	}
	// getting the dollar vale from a variable in init.php

	for($k=0;$k<$t;$k++){
		$totaldemand+=$demandHours[$k];
		$totalact+=$actualHours[$k];
	}



	// XML For Project Metrics Chart

	$srtrXML  = "";
	$srtrXML .= "<chart labelDisplay='ROTATE' adjustDiv='1' plotGradientColor='000222' placeValuesInside='1' rotateValues='1' numberprtrendline = '50' zeroPlaneAlpha='5' slantLabels='1' SYAxisName='$nop' PYAxisName='$not' caption='$projectName Metrics' bgColor='d9e1e4' xAxisName='Billing cutoff Dates' showValues='$chartValues' animation='1' formatNumberScale='0' showBorder='1'";
	$srtrXML .= "exportEnabled='1' exportAtClient='0' exportAction='download' exportHandler='http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/includes/PHP/FCExporter.php' exportFileName='MyFileName'>";
	$srtrXML .= "<categories>";
	For($i=0;$i<$j;$i++)
	$srtrXML .= "<category label='$billingDt[$i]' />";
	$srtrXML .= "</categories>";

	$srtrXML .= "<dataset seriesName='$nop' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$srtrXML .= "<set value='$noOfPages[$i]' ";
		if(($noOfTasks[$i]=="0")&& ($noOfPages[$i]=="0"))
		$srtrXML .= " displayValue='No Work' ";
		//								else
		//									$srtrXML .= " displayValue=' ' ";
		$srtrXML .= " />";
	}
	$srtrXML .= "</dataset>";

	$srtrXML .= "<dataset seriesName='$not' >";
	For($i=0;$i<$j;$i++){
		$srtrXML .= "<set value='$noOfTasks[$i]' ";
		if(($noOfTasks[$i]=="0")&& ($noOfPages[$i]=="0"))
		$srtrXML .= " displayValue=' ' ";
		//								else
		//									$srtrXML .= " displayValue=' ' ";
		$srtrXML .= " />";
	}
	$srtrXML .= "</dataset>";
	$srtrXML .= "</chart>";



	// This is for BA chart
	$srtXML  = "";
	$srtXML .= "<chart labelDisplay='ROTATE' adjustDiv='1' plotGradientColor='000222' placeValuesInside='1' rotateValues='1' numberprtrendline = '50' zeroPlaneAlpha='5' slantLabels='1' SYAxisName='$nop' PYAxisName='$not' caption='$projectName Metrics' bgColor='d9e1e4' xAxisName='Billing cutoff Dates' showValues='$chartValues' formatNumberScale='0' showBorder='1'";
	$srtXML .= "exportEnabled='1' exportAtClient='0' exportAction='download' exportHandler='http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/includes/PHP/FCExporter.php' exportFileName='MyFileName'>";
	$srtXML .= "<categories>";
	For($i=0;$i<$j;$i++)
	$srtXML .= "<category label='$billingMonth[$i]' />";
	$srtXML .= "</categories>";

	$srtXML .= "<dataset seriesName='Demand Hours' >";
	For($i=0;$i<$j;$i++){
		$srtXML .= "<set value='$demandHours[$i]' />";
	}
	$srtXML .= "</dataset>";

	//							$srtXML .= "<dataset seriesName='Aewa Hours' renderAs='Line'>";
	//							For($i=0;$i<$j;$i++){
	//							$srtXML .= "<set value='$aewa[$i]' />";
	//							}
	//							$srtXML .= "</dataset>";

	$srtXML .= "<dataset seriesName='SAP Charged Hours'>";
	For($i=0;$i<$j;$i++){
		//
		$srtXML .= "<set value='$actualHrs[$i]' />";
		//
	}
	$srtXML .= "</dataset>";


	$srtXML .= "<dataset seriesName='Actual FTE' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$actualFte[$i]=($actualHrs[$i]*$goalFte[$i])/$demandHours[$i];
		$actualFte[$i]=ROUND($actualFte[$i],2);
		$srtXML .= "<set value='$actualFte[$i]' />";
	}
	$srtXML .= "</dataset>";

	$srtXML .= "<dataset seriesName='Goal FTE' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$srtXML .= "<set value='$goalFte[$i]' />";
	}
	$srtXML .= "</dataset>";

	$srtXML .= "</chart>";

	// Create the chart - Column 3D Chart with data from strXML variable using dataXML method
	echo "<div id=\"Multiple\" >";
	echo "<div id=\"Left\" >";
	if(($ProjectName!="AeroPDM_Activity")||($ProjectName!="AVEL_MRO_DOC"))
	echo  renderChart("./Charts/ScrollCombiDY2D.swf", "", $srtrXML, "myNext1",600, 400, 0, 0);
	echo "</div>";
	echo "<div id=\"Left\" >";

	echo  renderChart("./Charts/ScrollCombiDY2D.swf", "", $srtXML, "myNext",600, 400, 0, 0);
	echo "</div>";
	echo "</div>";
}

/** Project Metrics is displayed with no of tasks and No of pages.
 *
 *
 *
 * @param string ProjectName is the ProjectName as in database
 *
 * @global Current Billing date in database format
 */

function projectMetricsProgram($ProjectName,$currbilling,$nop="",$not=""){

	$projectName=$ProjectName;
	$currentBilling =$currbilling;
	echo "<SCRIPT LANGUAGE=\"Javascript\" SRC=\"./includes/FusionCharts.js\"></SCRIPT>";
	//	if(empty($nop))
	//	$nop="No of Pages";
	//	if(empty($not))
	//	$not="No of Tasks";
	echo "<br><br><br>";
	$sql=" SELECT Distinct (b.`BillingcutoffDate`), SUM(WorkingHours)as Billing_Hours, (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)as FTE,
							SUM(b.`Workinghours`)* (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)+.1*SUM(b.`Workinghours`)* (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)as Demand_hrs,
							(SELECT SUM(p.`Actual_Hrs`+ p.`Illustration_Hrs`) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as Actual_hrs
							FROM billing b
							group by b.`BillingcutoffDate`
							order by b.`BillingcutoffDate`;";
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$billingcutoffdates[$i]= $row[0];
		if($billingcutoffdates[$i]==$currentBilling)
		$j=$i;
		$billingDt[$i]=date("d-M-Y", strtotime("$billingcutoffdates[$i]"));
		$billingDates[$i]=date("m:F(d-M-y)", strtotime("$billingcutoffdates[$i]"));
		$billingMonth[$i]=date("M-Y", strtotime("$billingcutoffdates[$i]"));
		$standardHours[$i]=$row[1];
		$goalFte[$i]=$row[2];
		if(!empty($row[3]))
		$demandHours[$i]=$row[3];
		else
		$demandHours[$i]=0;
		if(!empty($row[4]))
		$actualHours[$i]=$row[4];
		else
		$actualHours[$i]=0;
		$i++;
	}
	dbi_free_result ( $result );
	$sql=" SELECT Program_name, COUNT(No_of_Pages)as Docs,SUM(No_of_Pages) as Pages
							 FROM `$projectName` p 
							 Where DATE_FORMAT(Billing_Cutoff_dt,'%Y') = '2010' AND `Tech_gate` not like \"Rejected\"
							 Group by Program_name
							 order by Program_name DESC";
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$programs[$i]=$row[0];
		$noOfPages[$i]=$row[2];
		$noOfTasks[$i]=$row[1];
		$i++;
	}
	for($k=0;$k<$j;$k++){
		$s="Select SUM(`sapprojectwise`.hours)as Hours from `sapprojectwise`
							    			inner join pmdb_project_mapping ON `sapprojectwise`.level2WBSdescription = `pmdb_project_mapping`.level2WBSdescription
							    				where `pmdb_project_mapping`.projectname Like '$projectName' ";
			
		if($k==0)
		$s.="AND `sapprojectwise`.`date` BETWEEN '2010-01-04' AND '2010-01-17'";
		else{
			$l=$k-1;
			$s.="AND `sapprojectwise`.`date` BETWEEN ADDDATE('$billingcutoffdates[$l]',1) AND ADDDATE('$billingcutoffdates[$k]',1)";
		}
			
		$res = dbi_query($s);
		while($row = dbi_fetch_row($res))	{
			if(!empty($row[0]))
			$actualHrs[$k]=$row[0];
			else
			$actualHrs[$k]=0;
		}


	}
	// getting the dollar vale from a variable in init.php

	// XML For Project Metrics Chart

	$srtrXML  = "";
	$srtrXML .= "<chart labelDisplay='ROTATE' adjustDiv='1' plotGradientColor='000222' placeValuesInside='1' rotateValues='1' numberprtrendline = '50' zeroPlaneAlpha='5' slantLabels='1' SYAxisName='$nop' PYAxisName='$not' caption='$projectName Metrics' bgColor='d9e1e4' xAxisName='Billing cutoff Dates' showValues='$chartValues' formatNumberScale='0' showBorder='1'";
	$srtrXML .= "exportEnabled='1' exportAtClient='0' exportAction='download' exportHandler='http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/includes/PHP/FCExporter.php' exportFileName='MyFileName'>";
	$srtrXML .= "<categories>";
	For($i=0;$i<Count($programs);$i++)
	$srtrXML .= "<category label='$programs[$i]' />";
	$srtrXML .= "</categories>";

	$srtrXML .= "<dataset seriesName='$nop' parentYAxis='S'>";
	For($i=0;$i<$j;$i++)
	$srtrXML .= "<set value='$noOfPages[$i]' />";
	$srtrXML .= "</dataset>";

	$srtrXML .= "<dataset seriesName='$not' >";
	For($i=0;$i<$j;$i++)
	$srtrXML .= "<set value='$noOfTasks[$i]' />";
	$srtrXML .= "</dataset>";
	$srtrXML .= "</chart>";



	// This is for BA chart
	$srtXML  = "";
	$srtXML .= "<chart labelDisplay='ROTATE' adjustDiv='1' plotGradientColor='000000'  placeValuesInside='1' rotateValues='1' numberprtrendline = '50' zeroPlaneAlpha='5' slantLabels='1' PYAxisName='Hours' SYAxisName='FTE' caption='$projectName Budget Vs Actual Chart' bgColor='d9e1e4' xAxisName='Billing cutoff Dates' showValues='$chartValues' formatNumberScale='0' showBorder='1'";
	$srtXML .= "exportEnabled='1' exportAtClient='0' exportAction='download' exportHandler='http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/includes/PHP/FCExporter.php' exportFileName='MyFileName'>";
	$srtXML .= "<categories>";
	For($i=0;$i<$j;$i++)
	$srtXML .= "<category label='$billingMonth[$i]' />";
	$srtXML .= "</categories>";

	$srtXML .= "<dataset seriesName='Demand Hours' >";
	For($i=0;$i<$j;$i++){
		$srtXML .= "<set value='$demandHours[$i]' />";
	}
	$srtXML .= "</dataset>";


	$srtXML .= "<dataset seriesName='SAP Charged Hours'>";
	For($i=0;$i<$j;$i++){
		//
		$srtXML .= "<set value='$actualHrs[$i]' />";
		//
	}
	$srtXML .= "</dataset>";


	$srtXML .= "<dataset seriesName='Actual FTE' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$actualFte[$i]=($actualHrs[$i]*$goalFte[$i])/$demandHours[$i];
		$actualFte[$i]=ROUND($actualFte[$i],2);
		$srtXML .= "<set value='$actualFte[$i]' />";
	}
	$srtXML .= "</dataset>";

	$srtXML .= "<dataset seriesName='Goal FTE' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$srtXML .= "<set value='$goalFte[$i]' />";
	}
	$srtXML .= "</dataset>";

	$srtXML .= "</chart>";

	// Create the chart - Column 3D Chart with data from strXML variable using dataXML method
	echo "<div id=\"Multiple\" >";
	echo "<div id=\"Left\" >";
	if(($ProjectName!="AeroPDM_Activity")||($ProjectName!="AVEL_MRO_DOC"))
	echo  renderChart("./Charts/MSCombiDY2D.swf", "", $srtrXML, "myNext1",600, 400, 0, 0);
	echo "</div>";
	echo "<div id=\"Left\" >";

	echo  renderChart("./Charts/MSCombiDY2D.swf", "", $srtXML, "myNext",600, 400, 0, 0);
	echo "</div>";
	echo "</div>";
}

/** Project Metrics is displayed with no of tasks and No of pages.
 *
 *
 *
 * @param string ProjectName is the ProjectName as in database
 *
 * @global Current Billing date in database format
 */

function projectMetricsData($ProjectName,$currbilling,$noOFTask,$noOFPages,$nop="",$not=""){

	$projectName=$ProjectName;
	$currentBilling =$currbilling;
	echo "<SCRIPT LANGUAGE=\"Javascript\" SRC=\"./includes/FusionCharts.js\"></SCRIPT>";
	if(empty($nop))
	$nop="No of Pages";
	if(empty($not))
	$not="No of Tasks";
	// Link concept
	echo "<br><br><br>";
	$sql=" SELECT Distinct (b.`BillingcutoffDate`), SUM(WorkingHours)as Billing_Hours, (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)as FTE,
			SUM(b.`Workinghours`)* (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)+.1*SUM(b.`Workinghours`)* (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)as Demand_hrs,
			(SELECT SUM(p.`Actual_Hrs`+ p.`Illustration_Hrs`) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as Actual_hrs,
			(SELECT COUNT(*) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as No_of_tasks,
			(SELECT SUM(p.`No_of_Pages`) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as No_of_pages,
			(SELECT ROUND((SUM(p.`Actual_Hrs`)*60/SUM(p.`No_of_Pages`)),2) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as Minperpage
			FROM billing b
			WHERE YEAR(b.`BillingcutoffDate`) = YEAR('$currbilling')
			group by b.`BillingcutoffDate`
			order by b.`BillingcutoffDate`;";
//	echo $sql;
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$billingcutoffdates[$i]= $row[0];
		if($billingcutoffdates[$i]==$currentBilling)
		$j=$i;
		$billingDt[$i]=date("d-M-Y", strtotime("$billingcutoffdates[$i]"));
		$billingDates[$i]=date("m:F(d-M-y)", strtotime("$billingcutoffdates[$i]"));
		$billingMonth[$i]=date("M-Y", strtotime("$billingcutoffdates[$i]"));
		$standardHours[$i]=$row[1];
		$goalFte[$i]=$row[2];
		if(!empty($row[3]))
		$demandHours[$i]=$row[3];
		else
		$demandHours[$i]=0;
		if(!empty($row[4]))
		$actualHours[$i]=$row[4];
		else
		$actualHours[$i]=0;
		if(!empty($row[5]))
		$noOfTasks[$i]= $row[5];
		else
		$noOfTasks[$i]= 0;
		if(!empty($row[6]))
		$noOfPages[$i]= $row[6];
		else
		$noOfPages[$i]= 0;
		$i++;
	}
	dbi_free_result ( $result );
	$j++;
	$firstDay=substr($billingcutoffdates[0],0,3);
	$firstDay.="-01-01";
	
	for($k=0;$k<$j;$k++){
		$s="Select SUM(`sapprojectwise`.hours)as Hours from `sapprojectwise`
			inner join pmdb_project_mapping ON `sapprojectwise`.level2WBSdescription = `pmdb_project_mapping`.level2WBSdescription
			where `pmdb_project_mapping`.projectname Like '$projectName' AND YEAR(yearofprj) = YEAR('$currbilling')";
			
		if($k==0)
		$s.="AND `sapprojectwise`.`date` BETWEEN '$firstDay' AND '$billingcutoffdates[0]'";
		else{
			$l=$k-1;
			$s.="AND `sapprojectwise`.`date` BETWEEN ADDDATE('$billingcutoffdates[$l]',1) AND '$billingcutoffdates[$k]'";
		}
			
		$res = dbi_query($s);
		while($row = dbi_fetch_row($res))	{
			if(!empty($row[0]))
			$actualHrs[$k]=$row[0];
			else
			$actualHrs[$k]=0;
		}


	}
	// getting the dollar vale from a variable in init.php

	// XML For Project Metrics Chart

	$srtrXML  = "";
	$srtrXML .= "<chart labelDisplay='ROTATE' adjustDiv='1' plotGradientColor='000222' placeValuesInside='1' rotateValues='1' numberprtrendline = '50' zeroPlaneAlpha='5' slantLabels='1' SYAxisName='$nop' PYAxisName='$not' caption='$projectName Metrics' bgColor='d9e1e4' xAxisName='Billing cutoff Dates' showValues='$chartValues' formatNumberScale='0' showBorder='1'";
	$srtrXML .= "exportEnabled='1' exportAtClient='0' exportAction='download' exportHandler='http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/includes/PHP/FCExporter.php' exportFileName='MyFileName'>";
	$srtrXML .= "<categories>";
	For($i=0;$i<$j;$i++)
	$srtrXML .= "<category label='$billingDt[$i]' />";
	$srtrXML .= "</categories>";

	$srtrXML .= "<dataset seriesName='$nop' parentYAxis='S'>";
	For($i=0;$i<$j;$i++)
	$srtrXML .= "<set value='$noOFPages[$i]' />";
	$srtrXML .= "</dataset>";

	$srtrXML .= "<dataset seriesName='$not' >";
	For($i=0;$i<$j;$i++)
	$srtrXML .= "<set value='$noOFTask[$i]' />";
	$srtrXML .= "</dataset>";
	$srtrXML .= "</chart>";



	// This is for BA chart
	$srtXML  = "";
	$srtXML .= "<chart labelDisplay='ROTATE' adjustDiv='1' plotGradientColor='000000'  placeValuesInside='1' rotateValues='1' numberprtrendline = '50' zeroPlaneAlpha='5' slantLabels='1' PYAxisName='Hours' SYAxisName='FTE' caption='$projectName Budget Vs Actual Chart' bgColor='d9e1e4' xAxisName='Billing cutoff Dates' showValues='$chartValues' formatNumberScale='0' showBorder='1'";
	$srtXML .= "exportEnabled='1' exportAtClient='0' exportAction='download' exportHandler='http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/includes/PHP/FCExporter.php' exportFileName='MyFileName'>";
	$srtXML .= "<categories>";
	For($i=0;$i<$j;$i++)
	$srtXML .= "<category label='$billingMonth[$i]' />";
	$srtXML .= "</categories>";

	$srtXML .= "<dataset seriesName='Demand Hours' >";
	For($i=0;$i<$j;$i++){
		$demandHours[$i]=ROUND($demandHours[$i],2);
		$srtXML .= "<set value='$demandHours[$i]' />";
	}
	$srtXML .= "</dataset>";


	$srtXML .= "<dataset seriesName='SAP Charged Hours'>";
	For($i=0;$i<$j;$i++){
		//
		$srtXML .= "<set value='$actualHrs[$i]' />";
		//
	}
	$srtXML .= "</dataset>";


	$srtXML .= "<dataset seriesName='Actual FTE' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$actualFte[$i]=($actualHrs[$i]*$goalFte[$i])/$demandHours[$i];
		$actualFte[$i]=ROUND($actualFte[$i],2);
		$srtXML .= "<set value='$actualFte[$i]' />";
	}
	$srtXML .= "</dataset>";

	$srtXML .= "<dataset seriesName='Goal FTE' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$srtXML .= "<set value='$goalFte[$i]' />";
	}
	$srtXML .= "</dataset>";

	$srtXML .= "</chart>";

	// Create the chart - Column 3D Chart with data from strXML variable using dataXML method
	echo "<div id=\"Multiple\" >";
	echo "<div id=\"Left\" >";
	if(($ProjectName!="AeroPDM_Activity")||($ProjectName!="AVEL_MRO_DOC"))
	echo  renderChart("./Charts/MSCombiDY2D.swf", "", $srtrXML, "myNext1",600, 400, 0, 0);
	echo "</div>";
	echo "<div id=\"Left\" >";

	echo  renderChart("./Charts/MSCombiDY2D.swf", "", $srtXML, "myNext",600, 400, 0, 0);
	echo "</div>";
	echo "</div>";
}

/** Project Metrics is displayed for Avel MRO project for different Sites
 *
 *  Yiled and fill rate for the different MR pages
 *
 * @param string ProjectName is the ProjectName as in database Used uin AVel MRO database
 *
 * @global Current Billing date in database format
 */
function projectMetircsSitewise(){

	$sql="SELECT Distinct `site` FROM `pmdb_pi` where `project_name` = 'avel_mro_doc'";
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$site[$i]=$row[0];
		$site[$i]=str_replace(" ","_",$site[$i]);
		$i++;
	}
	dbi_free_result ( $result );
	$sql="SELECT date_format (Billing_Cutoff_dt,'%M-%Y')as datee,";
	for($i=0;$i<count($site);$i++)
	$sql .="COUNT(IF(p.`site` = '$site[$i]',1,NULL))as $site[$i],";
	$sql.="COUNT(*)as Total FROM avel_mro_doc a
	JOIN pmdb_pi p
	ON p.pi=a.pi
	where DATE_FORMAT(Billing_Cutoff_dt,'%Y')= \"2010\"
	group by DATE_FORMAT(Billing_Cutoff_dt,'%m')
	order by DATE_FORMAT(Billing_Cutoff_dt ,'%m')";
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$Month[$i]=$row[0];
		$Phoenix[$i] = $row[1];
		$Wichita[$i] = $row[2];
		$Coon_Rapids[$i] = $row[3];
		$Shanghai[$i] = $row[4];
		$Strongsville[$i] = $row[5];
		$Minneapolis[$i] = $row[6];
		$Irving[$i] = $row[7];
		$Renton[$i] = $row[8];
		$Urbana[$i] = $row[9];
		$Olathe[$i] = $row[10];
		$Penang[$i] = $row[11];
		$Tucson[$i] = $row[12];
		$total[$i]=$row[13];

		$i++;
	}
	dbi_free_result ( $result );

	echo $data1_[0];
	$strXMLx  = "";
	$strXMLx = "<chart labelDisplay='ROTATE' bgColor='d9e1e4' showBorder='1' slantLabels='1' plotGradientColor='000222' placeValuesInside='1' caption='AvEl - MRO ORI Completion Status' yAxisMaxValue='5' rotateValues='1' xAxisName='Months' yAxisName='ORIs delevered' showValues='$chartValues' legendPosition='RIGHT'";
	$strXMLx .= "exportEnabled='1' exportAtClient='0' exportAction='download' exportHandler='http://localhost/WEB/FusionCharts/FusionCharts_Evaluation/ExportHandlers/PHP/FCExporter.php' exportFileName='MyFileName'>";
	$strXMLx .= "<categories>";
	For($i=0;$i<count($Month);$i++){
		$strXMLx .= "<category label='$Month[$i]' />";
	}
	$strXMLx .= "</categories>";
	$strXMLx .= "<dataset seriesName='Phoenix'>";
	For($i=0;$i<count($Month);$i++){
		$strXMLx .= "<set value='$Phoenix[$i]' />";
	}$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Wichita'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Wichita[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Coon_Rapids'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Coon_Rapids[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Shanghai'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Shanghai[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Strongsville'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Strongsville[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx.= "<dataset seriesName='Minneapolis'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Minneapolis[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Irving'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Irving[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Renton'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Renton[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Urbana'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Urbana[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Olathe'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Olathe[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Penang'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Penang[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "<dataset seriesName='Tusion'>";
	For($i=0;$i<count($Month);$i++)
	$strXMLx .= "<set value='$Tucson[$i]' />";
	$strXMLx .= "</dataset>";
	$strXMLx .= "</chart>";
	echo  renderChart("./Charts/StackedColumn2D.swf", "", $strXMLx, "myNext4",1200, 400, 0, 0);

}

function COQCOPQ($projectName,$currentWeekend,$startWeekNum){
	$sql="SELECT b.Weekend,b.Workinghours FROM billing b ORDER BY b.Weekend;";
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$weekend[$i]= $row[0];

		$WeekendDt[$i]=date("d-M-Y", strtotime("$weekend[$i]"));
		$WeekendDates[$i]=date("$i:d-M-Y", strtotime("$weekend[$i]"));
		if($weekend[$i]==$currentWeekend){
			$j=$i;
			break;
		}
		$i++;
	}
	dbi_free_result ( $result );
	$j++;
	for($k=0;$k<$j;$k++){
		$s="Select SUM(s.Hours)
			from `sapprojectwise` s
			inner join pmdb_project_mapping p
			ON s.level2WBSdescription = p.level2WBSdescription
			where p.level2WBSdescription = s.level2WBSdescription AND p.projectname Like '$projectName'";
		if($k==0)
		$s.=" AND s.`date` BETWEEN '2010-01-01' AND '$weekend[0]'";
		else{
			$l=$k-1;
			$w1=$weekend[$l];
			$w2=$weekend[$k];
			$s.=" AND s.`date` BETWEEN  ADDDATE('$w1',1)AND '$w2'";
		}
			
		//			echo "$k :".$s;
		//			echo "<br>";
		$res = dbi_query($s);
		$i=0;
		while($row = dbi_fetch_row($res))	{
			$sumHours[$k]=$row[0];
		}
	}
	for($k=0;$k<$j;$k++){
		$s="SELECT 	SUM(Hours),c.`Type`
									FROM sapprojectwise s
									Join pmdb_coqcopq_def c
									on s.`Phase` = c.`PhaseName` AND s.`SubPhase` = c.`SubPhaseName`
									inner join pmdb_project_mapping p
									ON s.level2WBSdescription = p.level2WBSdescription
									WHERE p.projectname LIKE '$projectName%'";
		if($k==0)
		$s.="AND s.`date` BETWEEN '2010-01-01' AND '$weekend[0]'";
		else{
			$l=$k-1;
			$w1=$weekend[$l];
			$w2=$weekend[$k];
			$s.="AND s.`date` BETWEEN  ADDDATE('$w1',1)AND '$w2'";
		}
		$s .= " AND c.`Type` LIKE 'COQ'";
		//				echo "$k :".$count." ".$s;
		//				echo "<br>";
		$res = dbi_query($s);
		while($row = dbi_fetch_row($res))	{
			$COQHours[$k]=$row[0];
			if($COQHours[$k]=="")
			$COQHours[$k]=0;
			$COQ[$k]=$COQHours[$k]*100/$sumHours[$k];
			$COQ[$k]=Round($COQ[$k],2);
		}
	}
	dbi_free_result ($res);
	for($k=0;$k<$j;$k++){
		$s="SELECT 	SUM(Hours),c.`Type`
									FROM sapprojectwise s
									Join pmdb_coqcopq_def c
									on s.`Phase` = c.`PhaseName` AND s.`SubPhase` = c.`SubPhaseName`
									inner join pmdb_project_mapping p
									ON s.level2WBSdescription = p.level2WBSdescription
									WHERE p.projectname LIKE '$projectName'";
		if($k==0)
		$s.="AND s.`date` BETWEEN '2010-01-01' AND '$weekend[0]'";
		else{
			$l=$k-1;
			$w1=$weekend[$l];
			$w2=$weekend[$k];
			$s.="AND s.`date` BETWEEN  ADDDATE('$w1',1)AND '$w2'";
		}
		$s .= " AND c.`Type` LIKE 'COPQ'";
		//		echo "$k :".$s;
		//		echo "<br>";
		$res = dbi_query($s);
		while($row = dbi_fetch_row($res))	{

			$COPQHours[$k]=$row[0];
			if($COPQHours[$k]=="")
			$COPQHours[$k]=0;
			$COPQ[$k]=$COPQHours[$k]*100/$sumHours[$k];
			$COPQ[$k]=Round($COPQ[$k],2);
		}
	}
	dbi_free_result ($res);

	$srtrXML  = "";
	$srtrXML .= "<chart labelDisplay='ROTATE' slantLabels='1' caption='$projectName COQ and COPQ' numberSuffix='%' bgColor='d9e1e4' xAxisName='Weekends' yAxisName='Percentage of COPQ and COQ' showValues='0' showBorder='1'>";

	$srtrXML .= "<categories>";
	For($i=$startWeekNum;$i<$j;$i++)
	$srtrXML .= "<category label='$WeekendDt[$i]' />";
	$srtrXML .= "</categories>";

	$srtrXML .= "<dataset seriesName='COQ Hours' color='00611C'>";
	For($i=$startWeekNum;$i<$j;$i++){
		$srtrXML .= "<set value='$COQ[$i]'  />";
	}
	$srtrXML .= "</dataset>";

	$srtrXML .= "<dataset seriesName='COPQ Hours' color='FF0000'>";
	For($i=$startWeekNum;$i<$j;$i++){

		$srtrXML .= "<set value='$COPQ[$i]' />";
	}
	$srtrXML .= "</dataset>";
	$srtrXML .= "</chart>";
	//Create the chart - Column 3D Chart with data from strXML variable using dataXML method

	echo  renderChart("./Charts/MSLine.swf", "", $srtrXML, "myNext2",600, 400, 0, 0);
}


/** Sends Yield and fill rate for the different MR pages
 *
 * 
 * @global
 * @global
 * @global
 */
function YieldFillrate($projectName,$currentWeekend,$startWeekNum){
	$sql="SELECT b.Weekend,b.Workinghours,m.fte, m.projectname,(b.Workinghours*m.fte*1.1) as Demand
					FROM billing b
					JOIN pmdb_fte_metrics m
					ON b.BillingcutoffDate = m. BillingcutoffDate
					WHERE m.projectname = \"$projectName\"  
					ORDER BY m.projectname,b.Weekend;";
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$weekend[$i]= $row[0];
		$week=$weekend[$i];
		$WeekendDt[$i]=date("d-M-Y", strtotime("$week"));
		$WeekendDates[$i]=date("$i:d-M-Y", strtotime("$week"));
		$standardHours[$i]=$row[1];
		$demandHours[$i]=$row[4];
		if($weekend[$i]==$currentWeekend){
			$j=$i;
			break;
		}
		$estHrs[$i]=$demandHours[$i]/5;

		$i++;
	}
	dbi_free_result ( $result );
	$j++;
	for($c=0;$c<$j;$c++){
		$sq="SELECT projectname,SUM(Hours)*fte
			FROM pmdb_resource_to_project p
			INNER JOIN pmdb_vacations v
			ON p.eid = v.eid and v.weekend = p.weekend
			where p.Weekend = '$weekend[$c]' and projectname Like \"$projectName\"
			group by projectname
			order by v.eid,v.weekend";
		$res = dbi_query($sq);
		//				echo $sq;
		//				echo "<br>";
		while($row = dbi_fetch_row($res))	{
			$vacHrsProject[$c]= $row[1];
		}
	}


	for($k=0;$k<$j;$k++){
		$s="Select SUM(`sapprojectwise`.hours)as Hours from `sapprojectwise`
							    			inner join pmdb_project_mapping ON `sapprojectwise`.level2WBSdescription = `pmdb_project_mapping`.level2WBSdescription
							    				where `pmdb_project_mapping`.projectname Like '$projectName' ";
		if($k==0)
		$s.="AND `sapprojectwise`.`date` BETWEEN '2010-01-01' AND '$weekend[0]'";
		else{
			$l=$k-1;
			$w1=$weekend[$l];
			$w2=$weekend[$k];
			$s.="AND `sapprojectwise`.`date` BETWEEN  ADDDATE('$w1',1)AND '$w2'";
		}
		//						echo "$k :".$count." ".$s;
		//						echo "<br>";
		$res = dbi_query($s);
		while($row = dbi_fetch_row($res))	{
			$actualHours[$k]=$row[0];
			$act[$k]=$actualHours[$k]/5;
		}
	}
	$srtXML2  = "";
	$srtXML2 .= "<chart labelDisplay='ROTATE' numberSuffix='%' adjustDiv='1' decimals = '2'  numberprtrendline = '50' slantLabels='1' YAxisName='Percentage of Yield and Fill Rate' caption='$projectName Yield Vs Fill Rate' bgColor='d9e1e4' xAxisName='Weekends' showValues='0' showBorder='1'";
	$srtXML2 .= ">";
	$srtXML2 .= "<categories>";
	For($i=$startWeekNum;$i<$j;$i++){
		$Weekend=$WeekendDt[$i];
		$srtXML2 .= "<category label='$Weekend' />";
	}
	$srtXML2 .= "</categories>";

	$srtXML2 .= "<dataset seriesName='Fill Rate' color='00611C'>";
	For($i=$startWeekNum;$i<$j;$i++){
		$fillRate[$i]=$actualHours[$i]*100/$demandHours[$i];
		$fillRate[$i]=Round($fillRate[$i],2);
		$srtXML2 .= "<set value='$fillRate[$i]' />";
	}
	$srtXML2 .= "</dataset>";

	$srtXML2 .= "<dataset seriesName='Yield Rate' color='FF0000'>";
	For($i=$startWeekNum;$i<$j;$i++){
		$yieldRate[$i]=$actualHours[$i]*100/($demandHours[$i]-$vacHrsProject[$i]);
		$yieldRate[$i]=Round($yieldRate[$i],2);
		$cpi[$i]=$estHrs[$i]/$act[$i];
		$cpi[$i]=Round($cpi[$i],2);
		//
		$srtXML2 .= "<set value='$yieldRate[$i]'   />";
	}
	$srtXML2 .= "</dataset>";

	$srtXML2 .= "</chart>";

	//Create the chart - Column 3D Chart with data from strXML variable using dataXML method

	echo  renderChart("./Charts/MSLine.swf ", "", $srtXML2, "myNext3",600, 400, 0, 0);

}


/** Sends a redirect to the specified page.
 *
 *
 * @global
 * @global
 * @global
 */
function pviOfProject($projectName,$month){
	$sql="SELECT p.`PorjectName`, ROUND(AVG (p.`Qty`),2), ROUND(AVG (p.`Sp`),2), ROUND(AVG (p.`Cp`),2), ROUND(AVG (p.`Pty`),2),ROUND(AVG (p.`RCI`),2),ROUND(AVG (p.`PM`),2), ROUND(AVG (p.`CE`),2)
				FROM pmdb_pvi p
				inner join pmdb_project_mapping m
				on p.`PorjectName` = m.`ProjectName`
        		where ProjectName = '$projectName' And DATE_FORMAT(`MONTH`,'%m') = DATE_FORMAT('$month','%m')-1
				group by p.`PorjectName`;";
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$Qty[$i]= $row[1];
		$Sp[$i]= $row[2];
		$Cp[$i]= $row[3];
		$Pty[$i]= $row[4];
		$RCI[$i]= $row[5];
		$PM[$i]= $row[6];
		$CE[$i]= $row[7];
		$i++;
	}
	dbi_free_result ( $result );
	$j=substr($month,5,2);
	$monthename=month_name($j-1);
	$strXML6  = "";
	$strXML6 .= "<chart labelDisplay='ROTATE' plotGradientColor='000222' placeValuesInside='1' rotateValues='1' strendline = '50' zeroPlaneAlpha='5' slantLabels='1' caption='PVI for the Month of $monthename ' bgColor='d9e1e4' xAxisName='Elements' yAxisName='Values' showValues='1'   formatNumberScale='0' showBorder='1' ";
	$strXML6 .= "exportEnabled='1' exportAtClient='0' exportAction='save' exportHandler='http://localhost/WEB/FusionCharts/FusionCharts_Evaluation/ExportHandlers/PHP/FCExporter.php'>";

	$strXML6 .= "<set label='Quality' value='$Qty[0]'";
	if($Qty[0]==9)
	$strXML6 .= "color= '003300'";
	elseif($Qty[0]>=7)
	$strXML6 .= "color= 'FFFF99'";
	else
	$strXML6 .= "color= 'E00000'";
	$strXML6 .= " />";
	$strXML6 .= "<set label='Schedule Performance' value='$Sp[0]'";
	if($Sp[0]==9)
	$strXML6 .= "color= '003300'";
	elseif($Sp[0]>=7)
	$strXML6 .= "color= 'FFFF99'";
	else
	$strXML6 .= "color= 'E00000'";
	$strXML6 .= " />";
	$strXML6 .= "<set label='Cost Performance' value='$Cp[0]' ";
	if($Cp[0]==9)
	$strXML6 .= "color= '003300'";
	elseif($Cp[0]>=7)
	$strXML6 .= "color= 'FFFF99'";
	else
	$strXML6 .= "color= 'E00000'";
	$strXML6 .= " />";
	$strXML6 .= "<set label='Productivity' value='$Pty[0]' ";
	if($Pty[0]==9)
	$strXML6 .= "color= '003300'";
	elseif($Pty[0]>=7)
	$strXML6 .= "color= 'FFFF99'";
	else
	$strXML6 .= "color= 'E00000'";
	$strXML6 .= " />";
	$strXML6 .= "<set label='Resouce Capability Index' value='$RCI[0]' ";
	if($RCI[0]==9)
	$strXML6 .= "color= '003300'";
	elseif($RCI[0]>=7)
	$strXML6 .= "color= 'FFFF99'";
	else
	$strXML6 .= "color= 'E00000'";
	$strXML6 .= " />";
	$strXML6 .= "<set label='Project Management' value='$PM[0]' ";
	if($PM[0]==9)
	$strXML6 .= "color= '003300'";
	elseif($PM[0]>=7)
	$strXML6 .= "color= 'FFFF99'";
	else
	$strXML6 .= "color= 'E00000'";
	$strXML6 .= " />";
	$strXML6 .= "<set label='Collaboration Effectiveness' value='$CE[0]' ";
	if($CE[0]==9)
	$strXML6 .= "color= '003300'";
	elseif($CE[0]>=7)
	$strXML6 .= "color= 'FFFF99'";
	else
	$strXML6 .= "color= 'E00000'";
	$strXML6 .= " />";

	$strXML6 .= "<trendlines><line startValue='7' color='00000' displayValue='Goal' showOnTop ='1' thickness ='2'/></trendlines>";
	$strXML6 .= "</chart>";
	//Create the chart - Column 3D Chart with data from strXML variable using dataXML method
	echo  renderChart("./Charts/Column2D.swf", "", $strXML6, "myNext6",1200, 400, 0, 0);



}
/** Color codes in Excel each cell when is exported
 *	It Needs the API PHPEXCEL to be included in the code before accessing the
 * function.
 *
 *
 * @global
 * @global
 * @global
 */
function cellExcelColor ($objPHPExcel,$color,$cellDef){
	$objPHPExcel->getActiveSheet()->getStyle($cellDef)->applyFromArray(
	array('fill' 	=> array(
								'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
								'color'		=> array('argb' => $color)
	)
	)
	);

}

/** Color codes in Excel each cell when is exported
 *	It Needs the API PHPEXCEL to be included in the code before accessing the
 * function.
 *
 *
 * @global
 * @global
 * @global
 */
function cellExcelBorder ($objPHPExcel,$cellDef){

	$objPHPExcel->getActiveSheet()->getStyle($cellDef)->applyFromArray(
	array('borders' => array(
								'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
								'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
								'left'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
								'top'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)		
	)
	)
	);

}

/** Color codes in Excel export
 *	It Needs the API PHPEXCEL to be included in the code before accessing the
 * function.
 *
 *
 * @global
 * @global
 * @global
 */
function dataDashboardExcel ($objPHPExcel,$startCell,$endCell,$techGate){


	if(($techGate=="1-YTS"))
	$colorCode='33CCCC';
	else if(($techGate=="2-WIP")||($techGate=="4-WIP2"))
	$colorCode='FFCCFFCC';
	else if(($techGate=="3-PIR")||($techGate=="5-PIR2"))
	$colorCode='FFFF00';
	else if(($techGate=="6-Final"))
	$colorCode='C0C0C0';
	else if(($techGate=="8-On Hold"))
	$colorCode='CC99FF';
	else
	$colorCode='FFFFFF';
	$objPHPExcel->getActiveSheet()->getStyle($startCell.$i.':'.$endCell.$i)->applyFromArray(
	array('fill' 	=> array(
								'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
								'color'		=> array('argb' => $colorCode)
	),
		  'borders' => array(	
								'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
								'right'		=> array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
	)
	)
	);



}
Function DashboardHeader($objPHPExcel){
	$objPHPExcel->getActiveSheet()->freezePane('A14');
	cellExcelColor($objPHPExcel,'33CCCC','A3:A3');
	cellExcelColor($objPHPExcel,'CCFFCC','A4:A4');
	cellExcelColor($objPHPExcel,'FFFF00','A5:A5');
	cellExcelColor($objPHPExcel,'C0C0C0','A6:A6');
	cellExcelColor($objPHPExcel,'FF00FF','A7:A7');
	cellExcelColor($objPHPExcel,'CC99FF','A8:A8');
	cellExcelColor($objPHPExcel,'CCCCFF','F5:H11');
	cellExcelColor($objPHPExcel,'C0C0C0','F4:H4');
	cellExcelColor($objPHPExcel,'99CCFF','F3:H3');
	cellExcelBorder($objPHPExcel,'F3:H11');
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "HTSL, India - Data Services & Management - Project Dash Board - Status as of");
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "Data is presented Site-wise, PI wise and then Tech Gate-wise");
	$objPHPExcel->getActiveSheet()->setCellValue('A9', "NOTE 1 : Freeze Pane command is used at Cell No. F13 for convenience.");
	$objPHPExcel->getActiveSheet()->setCellValue('A10', "NOTE 2:  * All Change Order Documents requires only the changed page count.");
	$objPHPExcel->getActiveSheet()->setCellValue('B3', "1-YTS");
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "2-WIP;  4-WIP2");
	$objPHPExcel->getActiveSheet()->setCellValue('B5', "3-PIR;  5-PIR2");
	$objPHPExcel->getActiveSheet()->setCellValue('B6', "6-Final");
	$objPHPExcel->getActiveSheet()->setCellValue('D3', "Yet to start");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "WIP at HTSL");
	$objPHPExcel->getActiveSheet()->setCellValue('D5', "PI Review");
	$objPHPExcel->getActiveSheet()->setCellValue('D6', "Completed");
	$objPHPExcel->getActiveSheet()->setCellValue('D7', "On Hold");
	$objPHPExcel->getActiveSheet()->setCellValue('D8', "Took Back");
	$objPHPExcel->getActiveSheet()->setCellValue('F1', '=NOW()');
	//$objPHPExcel->getActiveSheet()->setCellValue('F1', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
	//$objPHPExcel->getActiveSheet()->getStyle('F1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
	$objPHPExcel->getActiveSheet()->getStyle('F1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
	$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('F3', "SUMMARY");
	$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "Status Gate");
	$objPHPExcel->getActiveSheet()->setCellValue('G4', "# of Documents");
	$objPHPExcel->getActiveSheet()->setCellValue('H4', "# Pages");
	$objPHPExcel->getActiveSheet()->setCellValue('F5', "1-YTS");
	$objPHPExcel->getActiveSheet()->setCellValue('F6', "2-WIP");
	$objPHPExcel->getActiveSheet()->setCellValue('F7', "3-PIR");
	$objPHPExcel->getActiveSheet()->setCellValue('F8', "4-WIP2");
	$objPHPExcel->getActiveSheet()->setCellValue('F9', "5-PIR2");
	$objPHPExcel->getActiveSheet()->setCellValue('F10', "6-Final");
	$objPHPExcel->getActiveSheet()->setCellValue('F11', "TOTAL >>>");
	$objPHPExcel->getActiveSheet()->getStyle('F11')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getRowDimension('13')->setRowHeight(30);

	$objPHPExcel->getActiveSheet()->getStyle('F1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
	$objPHPExcel->getActiveSheet()->setCellValue('G5', '=COUNTIF(C:C,F5)');
	$objPHPExcel->getActiveSheet()->setCellValue('G6', '=COUNTIF(C:C,F6)');
	$objPHPExcel->getActiveSheet()->setCellValue('G7', '=COUNTIF(C:C,F7)');
	$objPHPExcel->getActiveSheet()->setCellValue('G8', '=COUNTIF(C:C,F8)');
	$objPHPExcel->getActiveSheet()->setCellValue('G9', '=COUNTIF(C:C,F9)');
	$objPHPExcel->getActiveSheet()->setCellValue('G10', '=COUNTIF(C:C,F10)');
	$objPHPExcel->getActiveSheet()->setCellValue('G11', '=SUM(G5:G10)');
	$objPHPExcel->getActiveSheet()->getStyle('G11')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('H5', '=SUMIF(C:C,F5,M:M)');
	$objPHPExcel->getActiveSheet()->setCellValue('H6', '=SUMIF(C:C,F6,M:M)');
	$objPHPExcel->getActiveSheet()->setCellValue('H7', '=SUMIF(C:C,F7,M:M)');
	$objPHPExcel->getActiveSheet()->setCellValue('H8', '=SUMIF(C:C,F8,M:M)');
	$objPHPExcel->getActiveSheet()->setCellValue('H9', '=SUMIF(C:C,F9,M:M)');
	$objPHPExcel->getActiveSheet()->setCellValue('H10', '=SUMIF(C:C,F10,M:M)');
	$objPHPExcel->getActiveSheet()->setCellValue('H11', '=SUM(H5:H10)');
	$objPHPExcel->getActiveSheet()->getStyle('H11')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setAutoFilter('A13:Z13');
}

function num2alpha($n) {
	$r = '';
	for ($i = 1; $n >= 0 && $i < 10; $i++) {
		$r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i - 1))) . $r;
		$n -= pow(26, $i);
	}
	return $r;
}
function HealthLogo($colorValue){
	echo "<Center><div id=\"progress\" style=\"width: 10px;border: 1px solid black\">";
	echo "<div id=\"progress_bar\" style=\"height: 30px; width:";
	echo "30"."px;background: $colorValue;\"></div></div><Center>";

}

/** Project Metrics is displayed with no of tasks and No of pages.
 *
 *
 *
 * @param string ProjectName is the ProjectName as in database
 *
 * @global Current Billing date in database format
 */

function projectMetricsDocprep($ProjectName,$currbilling,$nop="",$not="",$nOT, $nOP){

	$projectName=$ProjectName;
	$cur =$currbilling;
	echo "<SCRIPT LANGUAGE=\"Javascript\" SRC=\"./includes/FusionCharts.js\"></SCRIPT>";
	echo "<br><br><br>";
	$i=0;
	$j=13;
	$actualHrs="";

	$sql=" SELECT Distinct (b.`BillingcutoffDate`), SUM(WorkingHours)as Billing_Hours, (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)as FTE,
			SUM(b.`Workinghours`)* (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)+.1*SUM(b.`Workinghours`)* (SELECT fte FROM pmdb_fte_metrics where ProjectName Like \"$projectName\" AND b.`BillingcutoffDate`= pmdb_fte_metrics.BillingcutoffDate)as Demand_hrs,
			(SELECT SUM(p.`Actual_Hrs`+ p.`Illustration_Hrs`) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as Actual_hrs,
			(SELECT COUNT(*) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as No_of_tasks,
			(SELECT SUM(p.`No_of_Pages`) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as No_of_pages,
			(SELECT ROUND((SUM(p.`Actual_Hrs`)*60/SUM(p.`No_of_Pages`)),2) from `$projectName` p where b.`BillingcutoffDate`= p.Billing_Cutoff_dt AND tech_gate NOT Like \"Rejected\")as Minperpage
			FROM billing b
			WHERE YEAR(b.`BillingcutoffDate`)=YEAR('$currbilling')
			group by b.`BillingcutoffDate`
			order by b.`BillingcutoffDate`;";

	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result))
	{
		$billingcutoffdates[$i]= $row[0];
		if($billingcutoffdates[$i]==$cur)
		$j=$i;
		$billingDt[$i]=date("d-M-Y", strtotime("$billingcutoffdates[$i]"));
		$billingDates[$i]=date("m:F(d-M-y)", strtotime("$billingcutoffdates[$i]"));
		$billingMonth[$i]=date("M-Y", strtotime("$billingcutoffdates[$i]"));
		$standardHours[$i]=$row[1];
		$goalFte[$i]=$row[2];
		if(!empty($row[3]))
		$demandHours[$i]=$row[3];
		else
		$demandHours[$i]=0;
		if(!empty($row[4]))
		$actualHours[$i]=$row[4];
		else
		$actualHours[$i]=0;
		if(!empty($row[5]))
		$noOfTasks[$i]= $row[5];
		else
		$noOfTasks[$i]= 0;
		if(!empty($row[6]))
		$noOfPages[$i]= $row[6];
		else
		$noOfPages[$i]= 0;
		$i++;
	}
	dbi_free_result ( $result );
	$j++;

	for($k=0;$k<$j;$k++){
		$noOfTasks[$k]+=$nOT[$k];
		$noOfPages[$k]+=$nOP[$k];
	}

	$firstDay=substr($billingcutoffdates[0],0,3);
	$firstDay.="-01-01";
	for($k=0;$k<$j;$k++){
		$s="Select SUM(`sapprojectwise`.hours)as Hours from `sapprojectwise`
			inner join pmdb_project_mapping
			ON `sapprojectwise`.level2WBSdescription = `pmdb_project_mapping`.level2WBSdescription
			where `pmdb_project_mapping`.projectname Like '$projectName' AND YEAR(Yearofprj)=YEAR('$currbilling') ";
			
		if($k==0)
		$s.="AND `sapprojectwise`.`date` BETWEEN '$firstDay' AND '$billingcutoffdates[0]'";
		else{
			$l=$k-1;
			$s.="AND `sapprojectwise`.`date` BETWEEN ADDDATE('$billingcutoffdates[$l]',1) AND ADDDATE('$billingcutoffdates[$k]',1)";
		}
			
		$res = dbi_query($s);
		while($row = dbi_fetch_row($res))	{
			if(!empty($row[0]))
			$actualHrs[$k]=$row[0];
			else
			$actualHrs[$k]=0;
		}


	}
	// getting the dollar vale from a variable in init.php
	



	for($k=0;$k<$t;$k++){
		$totaldemand+=$demandHours[$k];
		$totalact+=$actualHours[$k];
	}

	// XML For Project Metrics Chart

	$srtrXML  = "";
	$srtrXML .= "<chart labelDisplay='ROTATE' adjustDiv='1' plotGradientColor='000222' placeValuesInside='1' rotateValues='1' numberprtrendline = '50' zeroPlaneAlpha='5' slantLabels='1' SYAxisName='$nop' PYAxisName='$not' caption='$projectName Metrics' bgColor='d9e1e4' xAxisName='Billing cutoff Dates'  showValues='$chartValues' animation='0' formatNumberScale='0' showBorder='1'";
	$srtrXML .= "exportEnabled='1' exportAtClient='0' exportAction='download' exportHandler='http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/includes/PHP/FCExporter.php' exportFileName='MyFileName'>";
	$srtrXML .= "<categories>";
	For($i=0;$i<$j;$i++)
	$srtrXML .= "<category label='$billingDt[$i]' />";
	$srtrXML .= "</categories>";

	$srtrXML .= "<dataset seriesName='$nop' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$srtrXML .= "<set value='$noOfPages[$i]' ";
		if(($noOfTasks[$i]=="0")&& ($noOfPages[$i]=="0"))
		$srtrXML .= " displayValue='No Work' ";
		//								else
		//									$srtrXML .= " displayValue=' ' ";
		$srtrXML .= " />";
	}
	$srtrXML .= "</dataset>";

	$srtrXML .= "<dataset seriesName='$not' >";
	For($i=0;$i<$j;$i++){
		$srtrXML .= "<set value='$noOfTasks[$i]' ";
		if(($noOfTasks[$i]=="0")&& ($noOfPages[$i]=="0"))
		$srtrXML .= " displayValue=' ' ";
		//								else
		//									$srtrXML .= " displayValue=' ' ";
		$srtrXML .= " />";
	}
	$srtrXML .= "</dataset>";
	$srtrXML .= "</chart>";



	// This is for BA chart
	$srtXML  = "";
	$srtXML .= "<chart labelDisplay='ROTATE' adjustDiv='1' plotGradientColor='000000'  placeValuesInside='1' rotateValues='1' numberprtrendline = '50' zeroPlaneAlpha='5' slantLabels='1' PYAxisName='Hours' SYAxisName='FTE' caption='$projectName Budget Vs Actual Chart' bgColor='d9e1e4' xAxisName='Billing cutoff Dates' showValues='$chartValues' formatNumberScale='0' showBorder='1'";
	$srtXML .= "exportEnabled='1' exportAtClient='0' exportAction='download' exportHandler='http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/includes/PHP/FCExporter.php' exportFileName='MyFileName'>";
	$srtXML .= "<categories>";
	For($i=0;$i<$j;$i++)
	$srtXML .= "<category label='$billingMonth[$i]' />";
	$srtXML .= "</categories>";

	$srtXML .= "<dataset seriesName='Demand Hours' >";
	For($i=0;$i<$j;$i++){
		$srtXML .= "<set value='$demandHours[$i]' />";
	}
	$srtXML .= "</dataset>";

//	$srtXML .= "<dataset seriesName='Aewa Hours' renderAs='Line'>";
//	For($i=0;$i<$j;$i++){
//	$srtXML .= "<set value='$aewa[$i]' />";
//	}
//	$srtXML .= "</dataset>";

	$srtXML .= "<dataset seriesName='SAP Charged Hours'>";
	For($i=0;$i<$j;$i++){
		//
		$srtXML .= "<set value='$actualHrs[$i]' />";
		//
	}
	$srtXML .= "</dataset>";


	$srtXML .= "<dataset seriesName='Actual FTE' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$actualFte[$i]=($actualHrs[$i]*$goalFte[$i])/$demandHours[$i];
		$actualFte[$i]=ROUND($actualFte[$i],2);
		$srtXML .= "<set value='$actualFte[$i]' />";
	}
	$srtXML .= "</dataset>";

	$srtXML .= "<dataset seriesName='Goal FTE' parentYAxis='S'>";
	For($i=0;$i<$j;$i++){
		$srtXML .= "<set value='$goalFte[$i]' />";
	}
	$srtXML .= "</dataset>";

	$srtXML .= "</chart>";

	// Create the chart - Column 3D Chart with data from strXML variable using dataXML method
	echo "<div id=\"Multiple\" >";
	echo "<div id=\"Left\" >";
	if(($ProjectName!="AeroPDM_Activity")||($ProjectName!="AVEL_MRO_DOC"))
	echo  renderChart("./Charts/ScrollCombiDY2D.swf", "", $srtrXML, "myNext1",600, 400, 0, 0);
	echo "</div>";
	echo "<div id=\"Left\" >";

	echo  renderChart("./Charts/ScrollCombiDY2D.swf", "", $srtXML, "myNext",600, 400, 0, 0);
	echo "</div>";
	echo "</div>";
}

function listOfProjectOvershooted(){

	$sql="SELECT p.projectName, total_HRS as AvailableAewa , SUM(tot_HRS)as Charged,(total_HRS*0.7)as percent
			FROM pmdb_vw_prjhrs_details p
			join pmdb_vw_prjsaphrs  s
			on s.projectname=p.projectname
			group by s.projectname
			having Charged > percent AND AvailableAewa != 0
			order by s.projectname;";
	$result = dbi_query($sql);
	$i=0;
	while($row = dbi_fetch_row($result)){
		$projects[$i]=$row[0];
		$availableAewa[$i]=$row[1];
		$Billed[$i]=$row[2];
		$Target[$i]=$row[3];
		$i++;
	}

	if(!empty($projects)){

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		$msg ="";
		$msg .= "Dear Colleagues,<br>The followinga are the list";
		$msg .= "<HTML><BODY bgcolor=\"d9e1e4\"><img src=\"http://ie11dt7twqr1s.global.ds.honeywell.com/pmdb/includes/php/images/Export On $weekend.jpg\"  border=\"0\" alt=\"\">";
		$msg .= "<table cellpadding=\"4\" cellspacing=\"3\" border=\"1\" id=\"table\" class=\"sortable\">";
		$msg .= "<thead><tr>";
		$msg  .= "<th>Project Names</th>
				<th>Available Aewa hours</th>
				<th>Billed Hours</th>
				<th>Target 70% of Aewa</th>	
				<th>Delta Hours</th>";
		$msg  .="</tr></thead><tbody>";

		For($k=0;$k<count($projects);$k++){
			$msg  .="<tr><td>";
			$msg  .=$projects[$k];
			$msg  .="</td>";
			$msg .= "<td>$availableAewa</td>";
			$msg .= "<td>$Billed</td>";
			$msg .= "<td>$Target</td>";
			$deltaHours=$availableAewa-$Billed;
			$msg .= "<td>$deltaHours</td>";
			$msg .="</tr>";
		}
		$msg .= "</tbody></table></div>";
		$msg .= "Regards,<br>$toolName<br>(HTS-CMADS@honeywell.com)";
		//$cc=getEmailId("414328");
		//$emailTo = "Saravana.Shanmugam@honeywell.com,Dinesh.Muniyappa@honeywell.com,KanakaDurga.Thangavelu@honeywell.com";
		//$headers .= 'Cc: '.$cc . "\r\n";
		//$bcc = getEmailId("449186");
		$bcc .="gopal.satpathy@honeywell.com";
		$headers .= 'Bcc: '.$bcc . "\r\n";
		mail("$emailTo,","Aewa Hours Overshooted",$msg,$headers);
			
	}



}
?>