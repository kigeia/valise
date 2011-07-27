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
?>
