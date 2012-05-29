<?PHP
// File: $Id: config.php.tpl 649 2012-02-21 02:21:11Z bjoern $
// +----------------------------------------------------------------------+
// | Version: Sefrengo $Name:  $                                          
// +----------------------------------------------------------------------+
// | Copyright (c) 2005 - 2012 sefrengo.org <info@sefrengo.org>           |
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
// + Revision: $Revision: 649 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes: 
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+

// MYSQL
$cfg_cms['db_type'] = 'mysql';
$cfg_cms['db_host'] = '<!--{host}-->';
$cfg_cms['db_database'] = '<!--{db}-->';
$cfg_cms['db_user'] = '<!--{user}-->';
$cfg_cms['db_password'] = '<!--{pass}-->';
$cfg_cms['db_table_prefix'] = '<!--{prefix}-->';
$cfg_cms['db_utf8'] = true;
$cfg_cms['db_mysql_pconnect'] = false;

// Namen der SQL-Tabellen
$cms_db['backendmenu']	= $cfg_cms['db_table_prefix'].'backendmenu';
$cms_db['cat'] = $cfg_cms['db_table_prefix'].'cat';
$cms_db['cat_expand']	= $cfg_cms['db_table_prefix'].'cat_expand';
$cms_db['cat_side'] = $cfg_cms['db_table_prefix'].'cat_side';
$cms_db['cat_tree'] = $cfg_cms['db_table_prefix'].'cat_tree';
$cms_db['cat_lang'] = $cfg_cms['db_table_prefix'].'cat_lang';
$cms_db['clients'] = $cfg_cms['db_table_prefix'].'clients';
$cms_db['clients_lang']	= $cfg_cms['db_table_prefix'].'clients_lang';
$cms_db['code'] = $cfg_cms['db_table_prefix'].'code';
$cms_db['content'] = $cfg_cms['db_table_prefix'].'content';
$cms_db['content_external']	= $cfg_cms['db_table_prefix'].'content_external';
$cms_db['container'] = $cfg_cms['db_table_prefix'].'container';
$cms_db['container_conf']	= $cfg_cms['db_table_prefix'].'container_conf';
$cms_db['db_cache'] = $cfg_cms['db_table_prefix'].'db_cache';
$cms_db['directory'] = $cfg_cms['db_table_prefix'].'directory';
$cms_db['directory_lang'] = $cfg_cms['db_table_prefix'].'directory_lang';
$cms_db['event'] = $cfg_cms['db_table_prefix'].'event';
$cms_db['filetype'] = $cfg_cms['db_table_prefix'].'filetype';
$cms_db['groups'] = $cfg_cms['db_table_prefix'].'groups';
$cms_db['lang'] = $cfg_cms['db_table_prefix'].'lang';
$cms_db['lay'] = $cfg_cms['db_table_prefix'].'lay';
$cms_db['lay_upl'] = $cfg_cms['db_table_prefix'].'lay_upl';
$cms_db['logs'] = $cfg_cms['db_table_prefix'].'logs';
$cms_db['mod'] = $cfg_cms['db_table_prefix'].'mod';
$cms_db['sessions'] = $cfg_cms['db_table_prefix'].'sessions';
$cms_db['perms'] = $cfg_cms['db_table_prefix'].'perms';
$cms_db['side'] = $cfg_cms['db_table_prefix'].'side';
$cms_db['side_lang'] = $cfg_cms['db_table_prefix'].'side_lang';
$cms_db['tpl'] = $cfg_cms['db_table_prefix'].'tpl';
$cms_db['tpl_conf'] = $cfg_cms['db_table_prefix'].'tpl_conf';
$cms_db['tracker'] = $cfg_cms['db_table_prefix'].'tracker';
$cms_db['upl'] = $cfg_cms['db_table_prefix'].'upl';
$cms_db['upl_lang'] = $cfg_cms['db_table_prefix'].'upl_lang';
$cms_db['users'] = $cfg_cms['db_table_prefix'].'users';
$cms_db['users_groups']	= $cfg_cms['db_table_prefix'].'users_groups';
$cms_db['values'] = $cfg_cms['db_table_prefix'].'values';
$cms_db['plug'] = $cfg_cms['db_table_prefix'].'plug';


//todo: TO REMOVE - DEDI ACKWARD COMPATIBILITY
$dedi_db = $cms_db;
$cfg_dedi = $cfg_cms;

//log options for backend
$cfg_cms['log_path']	= 'logs/errorlog.txt';
$cfg_cms['log_size']	= 2097152; //2097152 = 2 MB
$cfg_cms['logfile_mailaddress']	= '';

// display errors
$cfg_cms['display_errors']	= 1;

//debug options
$cfg_cms['debug_sql'] = false;
$cfg_cms['debug_general'] = false;
$cfg_cms['debug_error'] = false;
//Signalisiert, das sich das System in irgendeinem Debug-Modus befindet
$cfg_cms['debug_active'] 	= ($cfg_cms['debug_sql'] || $cfg_cms['debug_general'] || $cfg_cms['debug_error']);

// HTML DEBUGGING
$cellpadding = '1';
$cellspacing = '2';
$border = '0';


// define that configfile is included
define ('CMS_CONFIGFILE_INCLUDED', true);

// overwrite value display_errors set ini_set for backend
if($cfg_cms['display_errors'] == 0 || $cfg_cms['display_errors'] == 1)
{
	ini_set('display_errors', $cfg_cms['display_errors']);
}
?>
