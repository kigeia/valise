<?php

/**
 * Classe pour l'authentification dans sacoche
 *
 * Provides the same interface as Auth_Simple.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Auth_SacocheSimple extends SimpleSAML_Auth_Simple {

	/**
	 * Initialise une authentification en utilisant les param�tre renseign�s dans sacoche
	 *
	 * @param string|NULL $auth  The authentication source. Si non pr�cis�, utilise la source configur�e dans gepi.
	 */
	public function __construct($auth = null) {
		if ($auth == null) {
			if (isset($_SESSION['utilisateur_saml_source'])) {
				//on prend la source pr�cis�e pr�cedemment en session.
				//Cela sert si le mode d'authentification a chang� au cours de la session de l'utilisateur
				$auth = $_SESSION['utilisateur_saml_source'];
			} else if (isset($_SESSION['AUTH_SIMPLESAML_SOURCE'])) {
			    $auth = $_SESSION['AUTH_SIMPLESAML_SOURCE'];
			}
		}
		
		$config = SimpleSAML_Configuration::getOptionalConfig('authsources.php');
		$sources = $config->getOptions();
		if (!count($sources)) {
			echo 'Erreur simplesaml : Aucune source configurée dans le fichier authsources.php';
			die;
		}
		if (!in_array($auth, $sources)) {
			//si la source pr�cis�e n'est pas trouv�e, utilisation par d�faut d'une source proposant tout les choix possible
			//(voir le fichier authsources.php)
			$auth = 'Authentification au choix entre toutes les sources configurees';
		}
		
		//on utilise une variable en session pour se souvenir quelle est la source utilis� pour cette session. Utile pour le logout, si entretemps l'admin a chang� la source d'authentification.
		$_SESSION['utilisateur_saml_source'] = $auth;
		
		parent::__construct($auth);
	}

	/**
	 * Ajouter pour gepi : utilisation des cookies et requetes organisation
	 * Start an authentication process.
	 *
	 * This function never returns.
	 *
	 * This function accepts an array $params, which controls some parts of
	 * the authentication. The accepted parameters depends on the authentication
	 * source being used. Some parameters are generic:
	 *  - 'ErrorURL': An URL that should receive errors from the authentication.
	 *  - 'KeepPost': If the current request is a POST request, keep the POST
	 *    data until after the authentication.
	 *  - 'ReturnTo': The URL the user should be returned to after authentication.
	 *  - 'ReturnCallback': The function we should call after the user has
	 *    finished authentication.
	 *
	 * @param array $params  Various options to the authentication request.
	 */
	public function login(array $params = array()) {
		
		if (!isset($params['multiauth:preselect'])) {
			if (isset($_REQUEST['source'])) {
				$params['multiauth:preselect'] = $_REQUEST['source'];
			} else if (isset($_COOKIE['source'])) {
				$params['multiauth:preselect'] = $_COOKIE['source'];
			}
		}

		if (!isset($params['core:organization'])) {//organization (rne) pour l'authentification
			$DB_TAB = DB_STRUCTURE_PUBLIC::DB_lister_parametres('"gepi_rne"');
			if ($DB_TAB) {
				$params['core:organization'] = $DB_TAB['parametre_valeur'];
			} elseif (isset($_REQUEST['organization'])) {
				$params['core:organization'] = $_REQUEST['organization'];
			} else if (isset($_COOKIE['organization'])) {
				$params['core:organization'] = $_COOKIE['organization'];
			} else if (isset($_REQUEST['rne'])) {
				$params['core:organization'] = $_REQUEST['rne'];
			} else if (isset($_COOKIE['RNE'])) {
				$params['core:organization'] = $_COOKIE['RNE'];
			}
		}
		
		if (isset($params['core:organization'])) {
			//on passe le paramètre en extension
			$ext = array();
			$dom = new DOMDocument();
			$ce = $dom->createElementNS('gepi_name_space', 'gepi_name_space:organization', $params['core:organization']);
			$ext[] = new SAML2_XML_Chunk($ce);
			$params = array('saml:Extensions' => $ext);
		}
		
		parent::login($params);
	}
	
	/**
	 * Efface la variable de la source d'authentification de la session
	 * Log the user out.
	 *
	 * This function logs the user out. It will never return. By default,
	 * it will cause a redirect to the current page after logging the user
	 * out, but a different URL can be given with the $params parameter.
	 *
	 * Generic parameters are:
	 *  - 'ReturnTo': The URL the user should be returned to after logout.
	 *  - 'ReturnCallback': The function that should be called after logout.
	 *  - 'ReturnStateParam': The parameter we should return the state in when redirecting.
	 *  - 'ReturnStateStage': The stage the state array should be saved with.
	 *
	 * @param string|array|NULL $params  Either the url the user should be redirected to after logging out,
	 *                                   or an array with parameters for the logout. If this parameter is
	 *                                   NULL, we will return to the current page.
	 */
	public function logout($params = NULL) {
		unset($_SESSION['utilisateur_saml_source']);
		parent::logout($params);
	}
	
}
