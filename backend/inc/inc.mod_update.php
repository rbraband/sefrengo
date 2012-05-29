<?PHP
// File: $Id: inc.mod_update.php 510 2011-10-23 21:38:16Z holger $
// +----------------------------------------------------------------------+
// | Version: Sefrengo $Name:  $                                          
// +----------------------------------------------------------------------+
// | Copyright (c) 2005 - 2006 sefrengo.org <info@sefrengo.org>           |
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
// + Revision: $Revision: 510 $
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

/**
 * 1. Benötigte Funktionen und Klassen includieren
 */
include_once('inc/fnc.mod.php');

// Browsern das cachen von Backendseiten verbieten
if ($cfg_cms['backend_cache'] == '1') {
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Datum aus Vergangenheit
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // immer geändert
	header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
}
// Debug options
$cfg_cms['debug_sql'] 		= false;
$cfg_cms['debug_general'] 	= false;
$cfg_cms['debug_error'] 	= false;
// read upload Modul
$modversion = $s_upload['modversion'];
$modname    = $s_upload['modname'];
$repid      = $s_upload['modrepid'];
$tmp['ENTRY_BGCOLOR'] = '#DBE3EF';
$tmp['OVERENTRY_BGCOLOR'] = '#C7D5EB';
// read Repository
$items = $rep->rep_local_count($idclient, 'mod');
if ($idclient != '0' && count($items['rep']["$idclient"]["$repid"]) >= 1) {
            foreach($items['rep']["$idclient"]["$repid"] as $modul) {
                if ((lib_floatval($modversion) > lib_floatval($modul['version']))) {
                    $umod[] = $modul;
                } elseif ((lib_floatval($modversion) == lib_floatval($modul['version']))) {
                    $rmod[] = $modul;
                }
            }
} elseif ($idclient == '0' && count($items['rep']["$repid"]) >= 1) {
            foreach($items['rep']["$repid"] as $modul) {
                if ((lib_floatval($modversion) > lib_floatval($modul['version'])) && $modul['idclient'] == '0') {
                    $umod[] = $modul;
                } elseif ((lib_floatval($modversion) == lib_floatval($modul['version'])) && $modul['idclient'] == '0') {
                    $rmod[] = $modul;
                }
            }
}
$ENTRY_SAFE    = '<label for="smodule['.$s_upload['modrepid'].']">' . $cms_lang['mod_confirm_new'] . '</label> <input type="checkbox" name="smodule['.$s_upload['modrepid'].']" value="true" id="smodule['.$s_upload['modrepid'].']" /> ';
// Start Output
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
echo "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"de\" lang=\"de\">\n";
echo "<head>\n";
echo "  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";
echo '  <title>Sefrengo '.$cfg_cms['version'].'</title>';
echo "\n  <link rel=\"stylesheet\" type=\"text/css\" href=\"tpl/standard/css/styles.css\" />\n";
echo "  <script src=\"tpl/standard/js/init.sefrengo.js\" type=\"text/javascript\"></script>\n";
echo '<script type="text/javascript">
<!--';
echo "
var query = '';\n
function submitUri(){\n
for(i=0; i<document.updatemod.elements.length; ++i) {\n
  if (document.updatemod.elements[i].name.search(/umodule.+/) != -1) {
    if (document.updatemod.elements[i].checked == true) query = query + '&' + document.updatemod.elements[i].name + '=' + document.updatemod.elements[i].value;\n
  }
  else if (document.updatemod.elements[i].name.search(/cmodule.+/) != -1) {
    if (document.updatemod.elements[i].checked == true) query = query + '&' + document.updatemod.elements[i].name + '=' + document.updatemod.elements[i].value;\n
  }
  else if (document.updatemod.elements[i].name.search(/rmodule.+/) != -1) {
    if (document.updatemod.elements[i].checked == true) query = query + '&' + document.updatemod.elements[i].name + '=' + document.updatemod.elements[i].value;\n
  }
  else if (document.updatemod.elements[i].name.search(/smodule.+/) != -1) {
    if (document.updatemod.elements[i].checked == true) query = query + '&' + document.updatemod.elements[i].name + '=' + document.updatemod.elements[i].value;\n
  }
  else query = query + '&' + document.updatemod.elements[i].name + '=' + document.updatemod.elements[i].value;\n
}\n
window.opener.location.href='" . $sess->urlRaw("main.php?area=mod") . "' + query;\n
self.close();\n
return false;\n
}";
echo '//-->
</script>';
echo '</head>';
echo '<body id="con-edit" style="overflow:hidden;background:fff;" onload="off();return true;">';
echo "\n<!-- inc.mod_update.php -->\n";
echo "<div>\n";
echo "  <div id=\"mod-up\">\n";
if ($errno == '0417') echo  $cms_lang['mod_confirm_update']; else echo $cms_lang['mod_confirm_reinstall'] . "\n";
echo "</div>\n";

//$tmp['BACK'] = "<a class=\"action\" href=\"javascript:window.opener.location.href='" . $sess->urlRaw("main.php?area=mod&action=upload&idclient=$idclient&override=false") . "';self.close();\" onmouseover=\"on('" . $cms_lang['gen_abort'] . "');return true;\" onmouseout=\"off();return true;\">" . $cms_lang['gen_abort'] . "</a>";
//echo "     <td align=\"right\">" . $tmp['BACK'] . "</td>";

// Formular zum updaten der Module
echo "    <form name=\"updatemod\" action=\"\" method=\"get\" onSubmit=\"return submitUri();\">\n";
echo "    <input type=\"hidden\" name=\"action\" value=\"upload\" />\n";
echo "    <input type=\"hidden\" name=\"idclient\" value=\"$idclient\" />\n";
echo "    <input type=\"hidden\" name=\"override\" value=\"true\" />\n";
echo " <div class=\"modscrolldiv\">\n";
echo " <table>\n";
echo "   <tr valign=\"top\">\n";
echo "     <td class=\"head\" width=\"100\">" . $cms_lang["mod_modulename"] . "</td>\n";
echo "     <td>" . htmlspecialchars($modname, ENT_COMPAT, 'UTF-8') . "</td>\n";
echo "   </tr>\n";
echo "   <tr valign=\"top\">\n";
echo "     <td class=\"head\" width=\"100\">" . $cms_lang["mod_version"] . "</td>\n";
echo "     <td>" . htmlspecialchars($modversion, ENT_COMPAT, 'UTF-8') . "</td>\n";
echo "   </tr>\n";
if (is_array($umod)) {
echo "   <tr valign=\"top\">\n";
echo "     <td class=\"head\" width=\"100\">" . $cms_lang["gen_overide"] . "</td>\n";
echo "     <td class=\"nopadd\">";
echo "<table>\n";
echo "  <tr valign=\"top\">
              <th align=\"center\">ID</th>
              <th align=\"center\">".$cms_lang['mod_version']."</th>
              <th align=\"left\">".$cms_lang['mod_verbosename']."</th>
              <th align=\"right\">".$cms_lang['mod_action']."</th>
            </tr>";
foreach($umod as $modul) {
$ENTRY_ID = $modul['idmod'];
$ENTRY_VERSION = $modul['version'];
$ENTRY_VERBOSE = $modul['verbose'];
$ENTRY_UPDATE  = '<label for="umodule['.$modul['idmod'].']">' . $cms_lang['mod_update_save'] . '</label> <input type="checkbox" name="umodule['.$modul['idmod'].']" value="true" id="umodule['.$modul['idmod'].']" /> ';
$ENTRY_CONFIG  = '<label for="cmodule['.$modul['idmod'].']">' . $cms_lang['mod_config_save'] . '</label> <input type="checkbox" name="cmodule['.$modul['idmod'].']" value="true" id="cmodule['.$modul['idmod'].']" /> ';
echo "      <tr valign=\"top\">
                 <td class=\"entry\" valign=\"middle\" align=\"center\">".$ENTRY_ID."</td>
                 <td class=\"entry\" valign=\"middle\" align=\"center\">".$ENTRY_VERSION."</td>
                 <td class=\"entry\" valign=\"middle\" align=\"left\">".$ENTRY_VERBOSE."</td>
                 <td class=\"entry nowrap\" valign=\"middle\" align=\"right\"></td>
                </tr>
                <tr valign=\"top\">
                 <td colspan=\"2\" class=\"entry\" valign=\"middle\" align=\"center\"></td>
                 <td colspan=\"2\" class=\"entry nowrap\" valign=\"middle\" align=\"right\">".$ENTRY_UPDATE."</td>
                </tr>
                <tr valign=\"top\">
                 <td colspan=\"2\" class=\"entry\" valign=\"middle\" align=\"center\"></td>
                 <td colspan=\"2\" class=\"entry nowrap\" valign=\"middle\" align=\"right\">".$ENTRY_CONFIG."</td>
                </tr>";
}
echo "         </table>";
echo "       </td>\n";
echo "      </tr>\n";
echo "      <tr>\n";
echo "	     <td class=\"head\" width=\"100\">" . $cms_lang['mod_defaultname'] . "</td>\n";
echo "	     <td valign=\"middle\" align=\"right\">".$ENTRY_SAFE."</td>\n";
echo "      </tr>\n";
}
if (is_array($rmod)) {
echo "	   <tr valign=\"top\">\n";
echo "	     <td class=\"head\" width=\"100\">" . $cms_lang["gen_reinstall"] . "</td>\n";
echo "	     <td class=\"nopadd\">";
echo "         <table>\n";
echo "          <tr valign=\"top\">
                     <th align=\"center\">ID</th>
                     <th align=\"center\">".$cms_lang['mod_version']."</th>
                     <th align=\"left\">".$cms_lang['mod_verbosename']."</th>
                     <th align=\"right\">".$cms_lang['mod_action']."</th>
                    </tr>";
foreach($rmod as $modul) {
$ENTRY_ID = $modul['idmod'];
$ENTRY_VERSION = $modul['version'];
$ENTRY_VERBOSE = $modul['verbose'];
$ENTRY_REINSTALL = '<label for="rmodule['.$modul['idmod'].']">' . $cms_lang['mod_reinstall_save'] .'</label> <input type="checkbox" name="rmodule['.$modul['idmod'].']" value="true" id="rmodule['.$modul['idmod'].']" /> ';
$ENTRY_CONFIG  = '<label for="cmodule['.$modul['idmod'].']">' . $cms_lang['mod_config_save'] .'</label> <input type="checkbox" name="cmodule['.$modul['idmod'].']" value="true" id="cmodule['.$modul['idmod'].']" /> ';
echo "      <tr valign=\"top\">
                 <td class=\"entry\" valign=\"middle\" align=\"center\">".$ENTRY_ID."</td>
                 <td class=\"entry\" valign=\"middle\" align=\"center\">".$ENTRY_VERSION."</td>
                 <td class=\"entry\" valign=\"middle\" align=\"left\">".$ENTRY_VERBOSE."</td>
                 <td class=\"entry\" valign=\"middle\" align=\"right\"></td>
                </tr>
                <tr valign=\"top\">
                 <td colspan=\"2\" class=\"entry\" valign=\"middle\" align=\"center\"></td>
                 <td colspan=\"2\" class=\"entry nowrap\" valign=\"middle\" align=\"right\">".$ENTRY_REINSTALL."</td>
                </tr>
                <tr valign=\"top\">
                 <td colspan=\"2\" class=\"entry\" valign=\"middle\" align=\"center\"></td>
                 <td colspan=\"2\" class=\"entry nowrap\" valign=\"middle\" align=\"right\">".$ENTRY_CONFIG."</td>
                </tr>";
}
echo "         </table>";
echo "       </td>\n";
echo "      </tr>\n";
echo "      <tr>\n";
echo "     <td class=\"head\" width=\"100\">" . $cms_lang['mod_defaultname'] . "</td>\n";
echo "     <td valign=\"middle\" align=\"right\">".$ENTRY_SAFE."</td>\n";
echo "      </tr>\n";
}
echo "</table>\n";
echo "</div>\n";
//echo "        <p><a href=\"javascript:window.opener.location.href='" . $sess->urlRaw("main.php?area=mod&action=upload&idclient=$idclient&override=false") . "';self.close();\">\n<img src=\"tpl/" . $cfg_cms['skin'] . "/img/but_cancel.gif\" /></a>\n<input type=\"image\" src=\"tpl/" . $cfg_cms['skin'] . "/img/but_ok.gif\" />\n</td>\n";
echo "    <p id=\"submitscan\">\n";
echo "        <input type='submit' name='sf_save' title='".$cms_lang['gen_save_titletext']."' value='".$cms_lang['gen_save']."' class=\"sf_buttonAction\" />\n";
echo "        <input type='button' name='sf_cancel' title='".$cms_lang['gen_cancel_titletext']."' value='".$cms_lang['gen_cancel']."' class=\"sf_buttonActionCancel\" onclick=\"window.opener.location.href='" . $sess->urlRaw("main.php?area=mod&action=upload&idclient=$idclient&override=false") . "';self.close();\" />\n";
echo "    </p>\n";


echo "    </form>\n";
echo "    </div>\n";

define('CMS_DISABLE_FOOTER_LICENSE', TRUE);
include('inc/inc.footer.php');
?>
