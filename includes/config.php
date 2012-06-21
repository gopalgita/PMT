<?php
/**
 * This file loads configuration settings from the data file settings.php and
 * sets up some needed variables.
 *
 * The settings.php file is created during installation using the web-based db
 * setup page (install/index.php).
 *
 *
 * @copyright  :2010 Honeywell.com
 * @license    :http://www.honeywell.com
 * @version    :$Id$
 * @link       :./incudes/config.php
 * @since      :File available since Release 1.0
 * @author     :Gopal Satpathy <gopal.satpathy@honeywell.com>
 *
 */

if ( empty ( $PHP_SELF ) && ! empty ( $_SERVER ) &&
! empty ( $_SERVER['PHP_SELF'] ) ) {
	$PHP_SELF = $_SERVER['PHP_SELF'];
}
if ( ! empty ( $PHP_SELF ) && preg_match ( "/\/includes\//", $PHP_SELF ) ) {
	die ( "You can't access this file directly!" );
}

// Unset some variables in case the server has register_globals
// enabled.  This will prevent users from settings these values
// in a URL:
// http://localhost/calendar/month.php?includedir=http://xxx/bad-guy
unset ( $db_type );
unset ( $db_database );
unset ( $db_login );
unset ( $db_password );

// Open settings file to read
$settings = array ();
$settings_file = dirname(__FILE__) . "/settings.php";
//called from send_reminders.php
if ( ! empty ( $includedir ) )
$fd = @fopen ( "$includedir/settings.php", "rb", true );
else
$fd = @fopen ( "settings.php", "rb", true );
if ( ! $fd )
$fd = @fopen ( "includes/settings.php", "rb", true );
if ( ! $fd  && file_exists ( $settings_file ) )
$fd = @fopen ( $settings_file, "rb", true );
if ( empty ( $fd ) ) {
	// There is no settings.php file.
	// Redirect user to install page if it exists.
	if ( file_exists ( "install/index.php" ) ) {
		Header ( "Location: install/index.php" );
		exit;
	} else {
		die_miserable_death ( "Could not find settings.php file.<br />\n" .
      "Please copy settings.php.orig to settings.php and modify for your " .
      "site.\n" );
	}
}

// We don't use fgets() since it seems to have problems with Mac-formatted
// text files.  Instead, we read in the entire file, then split the lines
// manually.
$data = '';
while ( ! feof ( $fd ) ) {
	$data .= fgets ( $fd, 4096 );
}
fclose ( $fd );

// Replace any combination of carriage return (\r) and new line (\n)
// with a single new line.
$data = preg_replace ( "/[\r\n]+/", "\n", $data );

// Split the data into lines.
$configLines = explode ( "\n", $data );

for ( $n = 0; $n < count ( $configLines ); $n++ ) {
	$buffer = $configLines[$n];
	$buffer = trim ( $buffer, "\r\n " );
	if ( preg_match ( "/^#/", $buffer ) )
	continue;
	if ( preg_match ( "/^<\?/", $buffer ) ) // start php code
	continue;
	if ( preg_match ( "/^\?>/", $buffer ) ) // end php code
	continue;
	if ( preg_match ( "/(\S+):\s*(\S+)/", $buffer, $matches ) ) {
		$settings[$matches[1]] = $matches[2];
		//echo "settings $matches[1] => $matches[2] <br>";
	}
}
$configLines = $data = '';
global $db_type, $db_host, $db_login, $db_password, $db_database,$user_inc, $db_persistent;
// Extract db settings into global vars
$db_type = $settings['db_type'];
$db_host = $settings['db_host'];
$db_login = $settings['db_login'];
$db_password = $settings['db_password'];
$db_database = $settings['db_database'];
$db_persistent = preg_match ( "/(1|yes|true|on)/i",
$settings['db_persistent'] ) ? '1' : '0';

foreach ( array ( "db_type", "db_host", "db_login", "db_password" ) as $s ) {
	if ( empty ( $settings[$s] ) ) {
		die_miserable_death ( "Could not find <tt>$s</tt> defined in " .
      "your <tt>settings.php</tt> file.\n" );
	}
}
?>