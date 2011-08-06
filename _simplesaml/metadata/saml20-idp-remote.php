<?php
/**
 * SAML 2.0 remote IdP metadata for simpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://rnd.feide.no/content/idp-remote-metadata-reference
 */

$metadata['gepi-idp'] = array(
	'name' => array('fr' => 'Gepi'),
	'SingleSignOnService'  => 'https://localhost/gepi/simplesaml/saml2/idp/SSOService.php',
	'SingleLogoutService'  => 'https://localhost/gepi/simplesaml/saml2/idp/SingleLogoutService.php',
	'certFingerprint'      => 'AA:FD:FF:98:48:18:A8:56:73:32:73:8F:33:53:04:8C:36:9B:E6:B2'
);

//add a preference for the organizationId of the user session
//pour sacoche c'est dans la requete : id, f_base, ou le cookie
$organization = '';
if (isset($_REQUEST['id'])) {
	$organization= $_REQUEST['id'];
} else if (isset($_REQUEST['f_base'])) {
	$organization= $_REQUEST['f_base'];
} else {
	$path = dirname(dirname(dirname(__FILE__)));
	define('SACoche','index'); //inutile ici mais obligatoire pour l'include suivant
	require_once("$path/_inc/constantes.php");
	if (isset($_COOKIE[COOKIE_STRUCTURE])) {
		$organization= $_COOKIE[COOKIE_STRUCTURE];
	}
}
if ($organization != '0' && $organization != '') {
	$metadata['gepi-idp']['SingleSignOnService'] .= '?organization='.$organization;
}

?>
