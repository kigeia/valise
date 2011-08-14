<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Générale Publique GNU pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

require_once(dirname(__FILE__).'/constantes.php');

// Vérifier la version de PHP
$version_php_mini = '5.1';
if(version_compare(PHP_VERSION,$version_php_mini,'<'))
{
	affich_message_exit($titre='PHP trop ancien',$contenu='Version de PHP utilisée sur ce serveur : '.PHP_VERSION.'<br />Version de PHP requise au minimum : '.$version_php_mini);
}
$version_mysql_mini = '5.0'; // Pour l'installation

// Vérifier la présence des modules nécessaires
$extensions_chargees = get_loaded_extensions();
$extensions_requises = array('curl','dom','gd','mbstring','mysql','PDO','pdo_mysql','session','zip','zlib');
$extensions_manquantes = array_diff($extensions_requises,$extensions_chargees);
if(count($extensions_manquantes))
{
	affich_message_exit($titre='PHP incomplet',$contenu='Les modules PHP suivants sont manquants : '.implode($extensions_manquantes,' '));
}

// La fonction array_fill_keys() n'est disponible que depuis PHP 5.2 ; SACoche exigeant PHP 5.1, la définir si besoin.
if(!function_exists('array_fill_keys'))
{
	function array_fill_keys($tab_clefs,$valeur)
	{
		return array_combine( $tab_clefs , array_fill(0,count($tab_clefs),$valeur) );
	}
}

// Fixer le niveau de rapport d'erreurs PHP
if(SERVEUR_TYPE == 'PROD')
{
	// Rapporter toutes les erreurs à part les E_NOTICE ; c'est la configuration par défaut de php.ini.
	ini_set('error_reporting',E_ALL ^ E_NOTICE);
}
else
{
	// Rapporter toutes les erreurs PHP sur le serveur local
	ini_set('error_reporting',E_ALL);
}

// Définir le décalage horaire par défaut de toutes les fonctions date/heure 
@date_default_timezone_set('Europe/Paris');

// Ne pas échapper les apostrophes pour Get/Post/Cookie
ini_set('magic_quotes_gpc',0);
ini_set('magic_quotes_sybase',0);

// Ne pas enregistrer les variables Environment/GET/POST/Cookie/Server comme des variables globales.
// register_globals ne peut pas être définit durant le traitement avec "ini_set"...
// ini_set(register_globals,0);

// Durée de vie des données (session...) sur le serveur, en nombre de secondes.
ini_set('session.gc_maxlifetime',3000);
// Le module doit utiliser seulement les cookies pour stocker les identifiants de sessions du côté du navigateur.
// Protection contre les attaques qui utilisent des identifiants de sessions dans les URL.
if (session_id() == '') {
	ini_set('session.use_trans_sid', 0); 
	ini_set('session.use_only_cookies',1);
}

// Ne pas autoriser les balises courtes d'ouverture de PHP (et possibilité d'utiliser XML sans passer par echo).
ini_set('short_open_tag',0);

// Désactiver le mode de compatibilité avec le Zend Engine 1 (PHP 4).
// Sinon l'utilisation de "simplexml_load_string()" ou "DOMDocument" (par exemples) provoquent des erreurs fatales, + incompatibilité avec classe PDO.
ini_set('zend.ze1_compatibility_mode',0);

// Modifier l'encodage interne pour les fonctions mb_* (manipulation de chaînes de caractères multi-octets)
mb_internal_encoding(CHARSET);

/**
 * load_sacoche_mysql_configD
 * Charge la base de donnée avec le bon numéro de base. Retourne faux si non chargé.
 *
 * @param int $BASE
 * @return false | int | string false si la base n'est pas chargée, le numéro de la base si elle est chargée
 */

function load_sacoche_mysql_config($BASE = null) {
	$path = dirname(dirname(__FILE__));
	//récupération de l'organisation (appelé rne ou base)
	//pour sacoche c'est dans la requete : id, f_base, ou le cookie, ou dans la session
	require_once($path.'/_inc/constantes.php');
	if (isset($_REQUEST['id'])) {
		$BASE = $_REQUEST['id'];
	} else if (isset($_REQUEST['f_base'])) {
		$BASE = $_REQUEST['f_base'];
	} else if (isset($_REQUEST['base'])) {
		$BASE = $_REQUEST['base'];
	} else if (isset($_COOKIE) && isset($_COOKIE[COOKIE_STRUCTURE])) {
		$BASE = $_COOKIE[COOKIE_STRUCTURE];
	} else if (isset($_SESSION) && isset($_SESSION['BASE'])) {
		$BASE = $_SESSION['BASE'];
	}
	if (isset($BASE) && $BASE != 0) {
		//on le met dans la session, ça peut toujours servir
		$_SESSION['BASE'] = $BASE;
		//on regarde si le fichier de configuration existe
		if (is_file($path.'/__private/mysql/serveur_sacoche_structure_'.$BASE.'.php')) {
			require_once($path.'/__private/mysql/serveur_sacoche_structure_'.$BASE.'.php');
		} else {
			return false;
		}
	} else {
		//on regarde si le fichier de configuration existe
		if (is_file($path.'/__private/mysql/serveur_sacoche_structure.php')) {
			require_once($path.'/__private/mysql/serveur_sacoche_structure.php');
			$BASE = 0;
		} else {
			return false;
		}
	}
	
	require_once($path.'/_inc/class.DB.config.sacoche_structure.php');
	require_once($path.'/_inc/fonction_requetes_structure.php');
	require_once($path.'/_lib/DB/DB.class.php');
	return $BASE;
}

?>