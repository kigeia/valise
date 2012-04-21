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
	'certFingerprint'      => 'aafdff984818a8567332738f3353048c369be6b2'
);

$path = dirname(dirname(dirname(dirname(__FILE__))));
require_once("$path/__private/config/constantes.php");
if (!defined('SACoche')) {
	define('SACoche','ssaml');
}
require_once($path.'/_inc/config_serveur.php');
$return_base = load_sacoche_mysql_config();
if ($return_base === false) {
	echo 'erreur : pas de base trouvée<br/>';
	var_dump(debug_backtrace());
	die;
}

$DB_TAB = DB_STRUCTURE_lister_parametres('"gepi_url","gepi_rne","gepi_certificat_empreinte"');
foreach($DB_TAB as $DB_ROW)
{
	${$DB_ROW['parametre_nom']} = $DB_ROW['parametre_valeur'];
}

$metadata['gepi-idp']['SingleSignOnService'] = $gepi_url.'/lib/simplesaml/www/saml2/idp/SSOService.php'.'?rne='.$gepi_rne; /* issu de SACoche */
$metadata['gepi-idp']['SingleLogoutService'] = 	$gepi_url.'/lib/simplesaml/www/saml2/idp/SingleLogoutService.php'.'?rne='.$gepi_rne; /* issu de SACoche */
$metadata['gepi-idp']['certFingerprint'] = $gepi_certificat_empreinte;

?>
