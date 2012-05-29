<?php
// File: $Id: main.php 202 2009-07-23 10:39:02Z holger $
// +----------------------------------------------------------------------+
// | Version: Sefrengo $Name:  $                                          
// +----------------------------------------------------------------------+
// | Copyright (c) 2005 - 2007 sefrengo.org <info@sefrengo.org>           |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License                 |
// |                                                                      |
// | This program is subject to the GPL license, that is bundled with     |
// | this package in the file LICENSE.TXT.                                |
// | If you did not receive a copy of the GNU General Public License      |
// | along with this program write to the Free Software Foundation, Inc., |
// | 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA               |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// +----------------------------------------------------------------------+
// + Autor: $Author: holger $
// +----------------------------------------------------------------------+
// + Revision: $Revision: 202 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes: 
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+


// define the new constants since PHP version 5.3.0 for older PHP installations
if(version_compare(PHP_VERSION, '5.3.0', '<') === TRUE)
{
	define('E_DEPRECATED', 8192);
	define('E_USER_DEPRECATED', 16384);
}

// set a default error reporting, if nothing is set before (e.g. clients config.php)
if(isset($error_reporting) === FALSE)
{
	// Error Reporting komplett abschalten
	$error_reporting = 0;
	
	// Zeige alle Fehlermeldungen, aber keine Warnhinweise und Deprecated-Meldungen
	$error_reporting = E_ALL & ~E_NOTICE & ~E_DEPRECATED;
	
	// Meldet alle PHP Fehler
	//$error_reporting = E_ALL;
}

/**
 * Behandelt die Fehlermeldung die von PHP kommen
 * 
 * @return bool Gibt true zurück, damit die PHP-interne Fehlerbehandlung nicht ausgeführt wird
 * @param object $errno
 * @param object $errstr
 * @param object $errfile
 * @param object $errline
 */
function phpErrorHandler($errno, $errstr, $errfile, $errline)
{
	global $cms_log, $error_reporting;
	
	// get language string for errno
	switch ($errno)
	{
		case E_NOTICE:
		case E_USER_NOTICE:
			$errors = "Notice";
			break;
			
		case E_WARNING:
		case E_USER_WARNING:
			$errors = "Warning";
			break;
			
		case E_ERROR:
		case E_USER_ERROR:
			$errors = "Fatal Error";
			break;
			
		case E_DEPRECATED:
		case E_USER_DEPRECATED:
			$errors = "Deprecated";
			break;
			
		default:
			$errors = "Unknown";
			break;
	}
	
	// is the object $cms_log alread instanciated? Then send to logger.
	if(isset($cms_log) === TRUE)
	{
		$msg = sprintf("%s: %s in %s on line %d", $errors, $errstr, $errfile, $errline);
		
		// use different log priorities
		switch ($errno)
		{
			case E_NOTICE:
			case E_USER_NOTICE:
				$cms_log->info('php', $msg);
				break;
			
			case E_WARNING:
			case E_USER_WARNING:
				$cms_log->warning('php', $msg);
				break;
			
			case E_ERROR:
			case E_USER_ERROR:
				$cms_log->fatal('php', $msg);
				break;
			
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$cms_log->info('php', $msg);
				break;
				
			default:
				$cms_log->info('php', $msg);
				break;
		}
	
	}
	// display the error if reporting is enabled
	if($error_reporting > 0)
	{
		if (ini_get("display_errors"))
		{
			printf ("<br />\n<b>%s</b>: %s in <b>%s</b> on line <b>%d</b><br /><br />\n", $errors, $errstr, $errfile, $errline);
		}
		
		if (ini_get('log_errors'))
		{
			error_log(sprintf("PHP %s:  %s in %s on line %d", $errors, $errstr, $errfile, $errline));
		}
	}
	
	// do not call the internal php error function
	return TRUE;
}

// set error reporting setting
error_reporting ($error_reporting);

// define an error handler to php
// different error reporting: show errors, but no warnings and deprecated
set_error_handler("phpErrorHandler", E_ALL & ~E_NOTICE & ~E_DEPRECATED);

?>