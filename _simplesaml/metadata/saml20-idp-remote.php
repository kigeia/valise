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
	'certFingerprint'      => 'AF:E7:1C:28:EF:74:0B:C8:74:25:BE:13:A2:26:3D:37:97:1D:A1:F9'
);
?>
