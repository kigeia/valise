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

$path = dirname(dirname(dirname(dirname(__FILE__))));
require_once("$path/__private/config/constantes.php");
require_once("$path/__private/mysql/serveur_sacoche_structure.php");
require_once("$path/_inc/class.DB.config.sacoche_structure.php");
require_once("$path/_inc/fonction_requetes_structure.php");
require_once("$path/_lib/DB/DB.class.php");

$DB_TAB = DB_STRUCTURE_lister_parametres('"gepi_url","gepi_rne","gepi_certificat_empreinte"');
foreach($DB_TAB as $DB_ROW)
{
	${$DB_ROW['parametre_nom']} = $DB_ROW['parametre_valeur'];
}

$metadata['gepi-idp']['SingleSignOnService'] = $gepi_url.'/lib/simplesaml/www/saml2/idp/SSOService.php'.'?rne='.$gepi_rne; /* issu de SACoche */
$metadata['gepi-idp']['SingleLogoutService'] = 	$gepi_url.'/lib/simplesaml/www/saml2/idp/SingleLogoutService.php'.'?rne='.$gepi_rne; /* issu de SACoche */
$metadata['gepi-idp']['certFingerprint'] = $gepi_certificat_empreinte;

?>
