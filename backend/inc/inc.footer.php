<?PHP
// File: $Id: inc.header.php 393 2011-06-07 12:25:38Z holger $
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
// + Revision: $Revision: 393 $
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

$footer = sf_api('VIEW', 'Footer');

if(! defined('CMS_DISABLE_FOOTER_LICENSE') && CMS_DISABLE_FOOTER_LICENSE !== TRUE)
{
	$footer->addFooterLicense();
}

if(isset($tpl) && strlen($tpl->lastTemplatefile) > 0)
{
  $tpl->setCurrentBlock('__global__');
	$tpl->setVariable('FOOTER', $footer->get());
	$tpl->parseCurrentBlock();
}
else
{
  echo $footer->get();
}


unset($footer);
?>