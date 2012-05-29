<?PHP
// File: $Id: fnc.user.php 375 2011-06-03 09:14:04Z holger $
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

function user_set_active($iduser, $is_active) {
	global $cms_log;
	
	$sf_user = sf_factoryGetObject('ADMINISTRATION', 'User');
	$sf_user->loadByIduser($iduser);
	$sf_user->setIsOnline($is_active);
	$sf_user->save();
	
	//Fire events
	if($is_active == '0') {
		//Log
		$cms_log->info('user', 'user_deactivate', array('iduser' => $sf_user->getIduser(), 'username' => $sf_user->getUsername()));
		//Event
		fire_event( 'user_deactivate', $sf_user );
	} else {
		//Log
		$cms_log->info('user', 'user_activate', array('iduser' => $sf_user->getIduser(), 'username' => $sf_user->getUsername()));
		//Event
		fire_event( 'user_activate', $sf_user );
	}
}

function user_save() {
	global $auth, $db, $cms_db, $username, $password, $password_validate, $name, $surname, $email, $group, $iduser, $idgroup, $order, $ascdesc, $oldusername, $comment;
	global $salutation, $street, $street_alt, $zip, $location, $state, $country, $phone, $fax, $mobile, $pager, $homepage, $birthday, $firm, $position, $firm_street, $firm_street_alt, $firm_zip, $firm_location, $firm_state, $firm_country, $firm_email, $firm_phone, $firm_fax, $firm_mobile, $firm_pager, $firm_homepage, $title;

	global $iduser;
	
	//User Objekt anlegen
	$sf_user = sf_factoryGetObject('ADMINISTRATION', 'User');
	
	// Passwort vergleich
	$password = trim($password);
    $password_validate = trim($password_validate);
    $username = trim($username);
	if ((!empty($password) || (empty($password) && empty($iduser))) 
			&& ($password != $password_validate || strlen($password) < 3)) return 'incorrect';

	// keine Passwort
	if (empty($iduser) && empty($password)) return 'incorrect';

	// Kein Loginname
	if (empty($username)) return 'nologinname';
	
	// Username auf Existenz pr�fen
	if(is_int($sf_user->usernameExists($username))) {
		return 'existusername';
	}
	
	if (!is_array($group)) {
		$group['0'] = $idgroup;
	}

	// Wenn Sysadmin gew�hlt wurde, alle anderen kicken
	if (in_array ('2', $group)) {
			unset($group);
			$group['0'] = '2';
	}
    
	set_magic_quotes_gpc($username);
	set_magic_quotes_gpc($name);
	set_magic_quotes_gpc($surname);
	set_magic_quotes_gpc($email);
	set_magic_quotes_gpc($password);
	set_magic_quotes_gpc($salutation);
	set_magic_quotes_gpc($title);
	set_magic_quotes_gpc($street);
	set_magic_quotes_gpc($street_alt);
	set_magic_quotes_gpc($zip);
	set_magic_quotes_gpc($location);
	set_magic_quotes_gpc($state);
	set_magic_quotes_gpc($country);
	set_magic_quotes_gpc($phone);
	set_magic_quotes_gpc($fax);
	set_magic_quotes_gpc($mobile);
	set_magic_quotes_gpc($pager);
	set_magic_quotes_gpc($homepage);
	set_magic_quotes_gpc($birthday);
	set_magic_quotes_gpc($firm);
	set_magic_quotes_gpc($position);
	set_magic_quotes_gpc($firm_street);
	set_magic_quotes_gpc($firm_street_alt);
	set_magic_quotes_gpc($firm_zip);
	set_magic_quotes_gpc($firm_location);
	set_magic_quotes_gpc($firm_state);
	set_magic_quotes_gpc($firm_country);
	set_magic_quotes_gpc($firm_email);
	set_magic_quotes_gpc($firm_phone);
	set_magic_quotes_gpc($firm_fax);
	set_magic_quotes_gpc($firm_mobile);
	set_magic_quotes_gpc($firm_pager);
	set_magic_quotes_gpc($firm_homepage);
	set_magic_quotes_gpc($comment);
	
	// Besteht User bereits?
	if (!empty($iduser)) {
		$sf_user->loadByIduser($iduser);
		
	// sonst neuen Benutzer vorbereiten
	} else {
		$sf_user->setIsOnline(1);
		$sf_user->setIsDeletable(1);
	}
	
	// Variablen setzen
	$sf_user->setUsername($username);
	if(!empty($password)) {
		$sf_user->setPassword($password);
	}
	$sf_user->setTitle($title);
	$sf_user->setName($name);
	$sf_user->setSurname($surname);
	$sf_user->setEmail($email);
	$sf_user->setPosition($position);
	$sf_user->setSalutation($salutation);
	$sf_user->setStreet($street);
	$sf_user->setZip($zip);
	$sf_user->setLocation($location);
	$sf_user->setPhone($phone);
	$sf_user->setFax($fax);
	$sf_user->setComment($comment);
	$sf_user->setStreetAlt($street_alt);
	$sf_user->setState($state);
	$sf_user->setCountry($country);
	$sf_user->setMobile($mobile);
	$sf_user->setPager($pager);
	$sf_user->setHomepage($homepage);
	$sf_user->setBirthday($birthday);
	$sf_user->setFirm($firm);
	$sf_user->setFirmStreet($firm_street);
	$sf_user->setFirmStreetAlt($firm_street_alt);
	$sf_user->setFirmZip($firm_zip);
	$sf_user->setFirmLocation($firm_location);
	$sf_user->setFirmState($firm_state);
	$sf_user->setFirmCountry($firm_country);
	$sf_user->setFirmEmail($firm_email);
	$sf_user->setFirmPhone($firm_phone);
	$sf_user->setFirmFax($firm_fax);
	$sf_user->setFirmMobile($firm_mobile);
	$sf_user->setFirmPager($firm_pager);
	$sf_user->setFirmHomepage($firm_homepage);
	
	$sf_user->setIdgroups($group);
	
	//Anlegen oder Speichern
	$sf_user->save();
}

function user_delete() {
	global $iduser;
	$iduser = (int) $iduser;

	$sf_user = sf_factoryGetObject('ADMINISTRATION', 'User');
	$sf_user->loadByIduser($iduser);
	$sf_user->delete();
}
?>