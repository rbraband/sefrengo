<?PHP
// File: $Id: config.php 635 2012-02-13 15:13:17Z bjoern $
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
// + Autor: $Author: bjoern $
// +----------------------------------------------------------------------+
// + Revision: $Revision: 635 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes: 
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+

$cms_path = 'backend/';
$client    = '1';

// options for logger in frontend
$cfg_client['log_path']			= 'logs/errorlog.txt';
$cfg_client['log_size']			= 2097152; //2097152 = 2 MB
$cfg_client['logfile_mailaddress']	= '';

// display errors if no logger available
$cfg_client['display_errors']	= 1;

// overwrite the default error_reporting for client
//$error_reporting = E_ALL & ~E_NOTICE & ~E_DEPRECATED;


// overwrite value display_errors set ini_set for frontend
if($cfg_client['display_errors'] == 0 || $cfg_client['display_errors'] == 1)
{
	ini_set('display_errors', $cfg_client['display_errors']);
}
?>