<?PHP
// File: $Id: inc.header.php 585 2011-12-17 01:24:43Z bjoern $
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
// + Revision: $Revision: 585 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes: 
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+
if(! defined('CMS_CONFIGFILE_INCLUDED')){
	die('NO CONFIGFILE FOUND');
}

$header = sf_api('VIEW', 'Header');
$header->sendHttpHeader();

// Backward compatibility to set onload function to body tag
if(is_array($tpl_in) == TRUE && array_key_exists('ONLOAD_FUNCTION', $tpl_in) == TRUE)
{
	$header->setBodyOnLoadFunction($tpl_in['ONLOAD_FUNCTION']);
}

// on display a plugin, move the default JS files from the footer to the header
if($area == 'plugin') 
{
	$defjs = sf_api('VIEW', 'DefaultJsFiles');
	// automatically prevents the js files in the footer
	$header->setDefaultJsFiles($defjs);
}

echo $header->get();

unset($header);
?>