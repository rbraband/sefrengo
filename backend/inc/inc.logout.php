<?PHP
// File: $Id: inc.logout.php 375 2011-06-03 09:14:04Z holger $
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
// + Revision: $Revision: 375 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes: 
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+

// Event
$_sf_userobj = sf_factoryGetObject('ADMINISTRATION', 'User');
$_sf_userobj->loadByIduser($auth->auth['uid']);
$cms_log->info('user', 'logout_success');
fire_event('logout_success', $_sf_userobj);

$auth -> logout();
$tpl->loadTemplatefile('logout.tpl');

$tpl_vals['MESSAGE_THANKS'] = $cms_lang['logout_thanksforusingcms'];
$tpl_vals['MESSAGE_LOGGED_OUT'] = $cms_lang['logout_youareloggedout'];
$tpl_vals['MESSAGE_LOGIN_AGAIN'] = $cms_lang['logout_backtologin1'] . ' ' . $cms_lang['logout_backtologin2'];
$tpl_vals['MESSAGE_LOGIN_LICENCE'] = $cms_lang['login_licence'];

$tpl->setVariable($tpl_vals);
?>