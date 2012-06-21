<?php
/**
 * This file contains all the functions for getting information
 * about users.  So, if you want to use an authentication scheme
 * other than the pmdb_user table, you can just create a new
 * version of each function found below.
 *
 * Note: this application assumes that usernames (logins) are unique.(i.e Eids)
 *
 * Check to see if a given login/password is valid.  If invalid,
 * the error message will be placed in $error.
 * params:
 * $login - user login
 * $password - user password
 * returns: true or false
 *
 * @copyright  :2012 Gopal Krushna
  * @version    :$Id$
 * @link       :./incudes/user.php
 * @since      :File available since Release 1.0
 * @author     :Gopal Satpathy 
 *
 */

function user_valid_login ( $login, $password ) {
	global $error;
	$ret = false;
	$sql = "SELECT ID FROM pmdb_user WHERE " . "ID = '" . $login . "' AND Password = '" . md5($password) . "'";
	$res = dbi_query ( $sql );
	if ( $res ) {
		$row = dbi_fetch_row ( $res );
		if ( $row && $row[0] != "" )
        	$ret = true;
	    else
            $error = "Invalid login". ": " . "incorrect password";
		} else {
			$error = "Invalid login";
			// Could be no such user or bad password
			// Check if user exists, so we can tell.
			$res2 = dbi_query ( "SELECT Eid FROM pmdb_user " . "WHERE Eid = '$login'" );
			if ( $res2 ) {
				$row = dbi_fetch_row ( $res2 );
				if ( $row && ! empty ( $row[0] ) ) {
					// got a valid username, but wrong password
					$error = "Invalid login" . ": " . "incorrect password";
				} else {
					// No such user.
					$error = "Invalid login" . ": " . "no such user";
				}
				dbi_free_result ( $res2 );
			}
		}
		dbi_free_result ( $res );	
	return $ret;
}

// Load info about a user (first name, last name, admin) and set
// globally.
// params:
//   $user - user login
function user_load_variables ( $login ) {
	$sql = "SELECT * FROM pmdb_user WHERE " . "ID = '" . $login . "'";
	$res = mysql_query ( $sql );
	if ( $res ) {
		if ( $row = dbi_fetch_row( $res ) ) {

			$_SESSION["Eid"] = $login;
			$_SESSION["EmployeeName"] = $row[1];
			$_SESSION["EmployeeNumber"] = $row[0];
			$_SESSION["IsAdmin"] = $row[8];
			$_SESSION["email"] =  $row[9];
			$_SESSION["password"] = $row[10];
			$_SESSION["projectName"] = $row[17];
			$_SESSION["YearSelect"]=$row[18];
		}
		dbi_free_result ( $res );
	} else {
		echo $error = "Database error" . ": " . dbi_error ();
		return false;
	}
	return true;
}

// Add a new user.
// params:
//   $user - user login
//   $password - user password
//   $firstname - first name
//   $lastname - last name
//   $email - email address
//   $admin - is admin? ("Y" or "N")
function user_add_user ( $user, $password, $firstname, $lastname, $email,
$admin ) {
	global $error;

	if ( $user == "__public__" ) {
		$error = "Invalid user login";
		return false;
	}

	if ( strlen ( $email ) )
	$uemail = "'" . $email . "'";
	else
	$uemail = "NULL";
	if ( strlen ( $firstname ) )
	$ufirstname = "'" . $firstname . "'";
	else
	$ufirstname = "NULL";
	if ( strlen ( $lastname ) )
	$ulastname = "'" . $lastname . "'";
	else
	$ulastname = "NULL";
	if ( strlen ( $password ) )
	$upassword = "'" . md5($password) . "'";
	else
	$upassword = "NULL";
	if ( $admin != "Y" )
	$admin = "N";
	$sql = "INSERT INTO webcal_user " .
    "( cal_login, cal_lastname, cal_firstname, " .
    "cal_is_admin, cal_passwd, cal_email ) " .
    "VALUES ( '$user', $ulastname, $ufirstname, " .
    "'$admin', $upassword, $uemail )";
	if ( ! dbi_query ( $sql ) ) {
		$error = "Database error". ": " . dbi_error ();
		return false;
	}
	return true;
}

// Update a user
// params:
//   $user - user login
//   $firstname - first name
//   $lastname - last name
//   $email - email address
//   $admin - is admin?
function user_update_user ( $user, $firstname, $lastname, $email, $admin ) {
	global $error;

	if ( strlen ( $email ) )
	$uemail = "'" . $email . "'";
	else
	$uemail = "NULL";
	if ( strlen ( $firstname ) )
	$ufirstname = "'" . $firstname . "'";
	else
	$ufirstname = "NULL";
	if ( strlen ( $lastname ) )
	$ulastname = "'" . $lastname . "'";
	else
	$ulastname = "NULL";
	if ( $admin != "Y" )
	$admin = "N";

	$sql = "UPDATE webcal_user SET cal_lastname = $ulastname, " .
    "cal_firstname = $ufirstname, cal_email = $uemail," .
    "cal_is_admin = '$admin' WHERE cal_login = '$user'";
	if ( ! dbi_query ( $sql ) ) {
		$error = "Database error" . ": " . dbi_error ();
		return false;
	}
	return true;
}

// Update user password
// params:
//   $user - user login
//   $password - last name
function user_update_user_password ( $user, $password ) {
	global $error;

	$sql = "UPDATE webcal_user SET cal_passwd = '".md5($password)."' " .
    "WHERE cal_login = '$user'";
	if ( ! dbi_query ( $sql ) ) {
		$error = "Database error" . ": " . dbi_error ();
		return false;
	}
	return true;
}

// Delete a user from the system.
// We assume that we've already checked to make sure this user doesn't
// have events still in the database.
// params:
//   $user - user to delete
function user_delete_user ( $user ) {
	// Get event ids for all events this user is a participant
	$events = array ();
	$res = dbi_query ( "SELECT webcal_entry.cal_id " .
    "FROM webcal_entry, webcal_entry_user " .
    "WHERE webcal_entry.cal_id = webcal_entry_user.cal_id " .
    "AND webcal_entry_user.cal_login = '$user'" );
	if ( $res ) {
		while ( $row = dbi_fetch_row ( $res ) ) {
			$events[] = $row[0];
		}
	}

	// Now count number of participants in each event...
	// If just 1, then save id to be deleted
	$delete_em = array ();
	for ( $i = 0; $i < count ( $events ); $i++ ) {
		$res = dbi_query ( "SELECT COUNT(*) FROM webcal_entry_user " .
      "WHERE cal_id = " . $events[$i] );
		if ( $res ) {
			if ( $row = dbi_fetch_row ( $res ) ) {
				if ( $row[0] == 1 )
				$delete_em[] = $events[$i];
			}
			dbi_free_result ( $res );
		}
	}
	// Now delete events that were just for this user
	for ( $i = 0; $i < count ( $delete_em ); $i++ ) {
		dbi_query ( "DELETE FROM webcal_entry WHERE cal_id = " . $delete_em[$i] );
	}

	// Delete user participation from events
	dbi_query ( "DELETE FROM webcal_entry_user WHERE cal_login = '$user'" );

	// Delete preferences
	dbi_query ( "DELETE FROM webcal_user_pref WHERE cal_login = '$user'" );

	// Delete from groups
	dbi_query ( "DELETE FROM webcal_group_user WHERE cal_login = '$user'" );

	// Delete bosses & assistants
	dbi_query ( "DELETE FROM webcal_asst WHERE cal_boss = '$user'" );
	dbi_query ( "DELETE FROM webcal_asst WHERE cal_assistant = '$user'" );

	// Delete user's views
	$delete_em = array ();
	$res = dbi_query ( "SELECT cal_view_id FROM webcal_view " .
    "WHERE cal_owner = '$user'" );
	if ( $res ) {
		while ( $row = dbi_fetch_row ( $res ) ) {
			$delete_em[] = $row[0];
		}
		dbi_free_result ( $res );
	}
	for ( $i = 0; $i < count ( $delete_em ); $i++ ) {
		dbi_query ( "DELETE FROM webcal_view_user WHERE cal_view_id = " .
		$delete_em[$i] );
	}
	dbi_query ( "DELETE FROM webcal_view WHERE cal_owner = '$user'" );

	// Delete layers
	dbi_query ( "DELETE FROM webcal_user_layers WHERE cal_login = '$user'" );

	// Delete any layers other users may have that point to this user.
	dbi_query ( "DELETE FROM webcal_user_layers WHERE cal_layeruser = '$user'" );

	// Delete user
	dbi_query ( "DELETE FROM webcal_user WHERE cal_login = '$user'" );
}
?>
