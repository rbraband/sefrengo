<?PHP
// File: $Id: index.php 635 2012-02-13 15:13:17Z bjoern $
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



$cms_path = '';
$cfg_cms = array();
$cfg_client = array();

// Projektkonfiguration laden
include('cms/inc/config.php');
include($cms_path.'inc/config.php');

// Inkludiert die Definition f�r die Fehlerbehandlung
require_once ($cms_path.'inc/inc.error_handling.php');

//Load API
require_once ($cms_path.'API/inc.apiLoader.php');

////////////////////////////////////////////////////
// basecontroller
////////////////////////////////////////////////////
$sf_controller = 'FrontendPage';
$sf_area = 'index';
$output = '';
if ($sf_factory->classExists('CONTROLLER', $sf_controller))
{
	$controller = sf_api('CONTROLLER', $sf_controller);
	if (method_exists($controller, $sf_area) && substr($sf_area,0,1) != '_')
	{
		//Set Controller Vars
		call_user_method('setInitArea', $controller, $sf_controller, trim($pairs['1']));
		//Call init all Method
		call_user_method('initControllerCall', $controller);
		// Call Controller
		call_user_method($sf_area, $controller);
	}
	else
	{
		echo  'Controller area not found or forbidden';
	}
}
else
{
	echo  'Controller '.$sf_controller.' not found';
}

$sf_factory->unloadAll();
?>
