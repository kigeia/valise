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

// Fonctions pour gérer les sessions.
// La session est transmise via le cookie "$_COOKIE['SESSION_NOM']".

// Paramétrage de la session
define('SESSION_NOM','SACoche-session');
require_once(dirname(__FILE__).'/../__private/config/constantes.php');

//on précise un chemin de base pour le cookie, utile en cas de plusieurs applications sur le même serveur
$cookiePath = parse_url(HEBERGEUR_ADRESSE_SITE,PHP_URL_PATH);
if (substr($cookiePath,strlen($cookiePath)-1) != '/') {
	$cookiePath .= '/';
}
session_set_cookie_params(0,$cookiePath);
define('COOKIE_PATH',$cookiePath);

session_name(SESSION_NOM);
session_cache_limiter('nocache');
session_cache_expire(180);

// Ouvrir une session existante
function open_old_session()
{
	$ID = $_COOKIE[SESSION_NOM];
	session_id($ID);
	if(!isset($_SESSION)) {
		return session_start();
	} else {
		return true;
	}
}

// Créer une nouvelle session
function open_new_session()
{
	$ID = uniqid().md5('grain_de_sable'.mt_rand());	// Utiliser l'option préfixe ou entropie de uniqid() insère un '.' qui peut provoquer une erreur disant que les seuls caractères autorisés sont a-z, A-Z, 0-9 et -
	session_id($ID);
	return session_start();
}

// Initialiser une session existante
function init_session()
{
	$_SESSION = array();
	// Numéro de la base
	$_SESSION['BASE']             = 0;
	// Données associées à l'utilisateur.
	$_SESSION['USER_PROFIL']      = 'public';	// public / webmestre / administrateur / directeur / professeur / eleve
	$_SESSION['USER_ID']          = 0;
	$_SESSION['USER_NOM']         = '-';
	$_SESSION['USER_PRENOM']      = '-';
	// Données associées à l'établissement.
	$_SESSION['SESAMATH_ID']      = 0;
	$_SESSION['CONNEXION_MODE']   = 'normal';
	$_SESSION['DUREE_INACTIVITE'] = 30;
}

// Fermer une session existante
function close_session()
{
	include_once(dirname(__FILE__).'/../_simplesaml/lib/_autoload.php');
	$auth = new SimpleSAML_Auth_Simple(SIMPLESAML_AUTHSOURCE);
	if ($auth->isAuthenticated()) {
		$auth->logout();
	}
	$alerte_sso = (isset($_SESSION['ALERTE_SSO'])) ? '&amp;f_base='.$_SESSION['BASE'].'&amp;f_mode='.$_SESSION['CONNEXION_MODE'] : false ;
	$_SESSION = array();
	setcookie(session_name(),'',time()-42000,'/');
	session_destroy();
	return $alerte_sso;
}

// Recherche d'une session existante et gestion des cas possibles.
function gestion_session($TAB_PROFILS_AUTORISES)
{
	//récupération de l'organisation (appelé rne ou base)
	//pour sacoche c'est dans la requete : id, f_base, ou le cookie
	$BASE = 0;
	require_once(dirname(__FILE__).'/fonction_clean.php');
	if (isset($_REQUEST['id'])) {
		$BASE = clean_entier($_REQUEST['id']);
	} else if (isset($_REQUEST['f_base'])) {
		$BASE= clean_entier($_REQUEST['f_base']);
	} else {
		if (isset($_COOKIE[COOKIE_STRUCTURE])) {
			$BASE= clean_entier($_COOKIE['rne']);
		}
	}

	global $ALERTE_SSO;
	// Messages d'erreurs possibles
	$tab_msg_alerte = array();
	$tab_msg_alerte[ 'I.2.a']['index'] = 'Session non retrouvée.\nConfigurez votre navigateur pour qu\'il accepte les cookies !\nRedirection vers l\'accueil...';
	$tab_msg_alerte[ 'I.2.a']['ajax']  = 'Session non retrouvée. Vérifiez l\'acceptation des cookies ! Retournez à l\'accueil...';
	$tab_msg_alerte[ 'I.2.b']['index'] = 'Tentative d\'accès direct à une page réservée !\nRedirection vers l\'accueil...';
	$tab_msg_alerte[ 'I.2.b']['ajax']  = 'Session perdue. Déconnectez-vous et reconnectez-vous...';
	$tab_msg_alerte['II.1.a']['index'] = 'Votre session a expiré !\nRedirection vers l\'accueil...';
	$tab_msg_alerte['II.1.a']['ajax']  = 'Session expirée. Déconnectez-vous et reconnectez-vous...';
	$tab_msg_alerte['II.3.a']['index'] = 'Tentative d\'accès direct à une page réservée !\nRedirection vers l\'accueil...';
	$tab_msg_alerte['II.3.a']['ajax']  = 'Page réservée. Retournez à l\'accueil...';
	$tab_msg_alerte['II.4.c']['index'] = 'Tentative d\'accès direct à une page réservée !\nRedirection vers l\'accueil...';
	$tab_msg_alerte['II.4.c']['ajax']  = 'Page réservée. Déconnexion effectuée. Retournez à l\'accueil...';
	// Zyva !
	if (defined('SIMPLESAML_AUTHSOURCE') && SIMPLESAML_AUTHSOURCE != '') {
		include_once(dirname(__FILE__).'/../_simplesaml/lib/_autoload.php');
		$auth = new SimpleSAML_Auth_Simple(SIMPLESAML_AUTHSOURCE);
		if (!$auth->isAuthenticated()) {
			//purge des attributs de session sacoche
			unset($_SESSION['USER_PROFIL']);
			unset($_SESSION['USER_ID']);
			unset($_SESSION['USER_ID_ENT']);
			unset($_SESSION['USER_ID_GEPI']);
		}
		
		//on forge une extension saml pour tramsmettre l'établissement précisé dans sacoche
		$ext = array();
		if ($BASE != 0) {
			$dom = new DOMDocument();
			$ce = $dom->createElementNS('gepi_name_space', 'gepi_name_space:organization', $BASE);
			$ext[] = new SAML2_XML_Chunk($ce);
		}
		$auth_params = array('saml:Extensions' => $ext);
		if (isset($_REQUEST['source'])) {
			$auth_params['multiauth:preselect'] = $_REQUEST['source'];
		}
		$auth->requireAuth($auth_params);//authentification
					
		setcookie(COOKIE_STRUCTURE,$BASE,time()+60*60*24*365,'/');//l'utilisateur est bien authentifié pour cet établissement, on le met en cookie
		
		$attr = $auth->getAttributes();
		
		if (//si l'utilisateur est authentifié mais que il n'est pas chargé en session ou que c'est un autre utilisateur en session on charge le nouvel utilisateur
			!isset($_SESSION['USER_ID']) || 
			(
				isset($attr['USER_ID'][0]) && $attr['USER_ID'][0] != $_SESSION['USER_ID']
			) ||
			!isset($_SESSION['USER_ID_ENT']) || 
			(
				isset($attr['USER_ID_ENT'][0]) && $attr['USER_ID_ENT'][0] != $_SESSION['USER_ID_ENT']
			)
			
		) {
			//l'utilisateur est authentifié mais les attributs ne sont pas encore chargés en session
			require_once(dirname(__FILE__).'/fonction_divers.php');
			require_once(dirname(__FILE__).'/../_lib/DB/DB.class.php');
			require_once(dirname(__FILE__)."/fonction_requetes_structure.php");
			require_once(dirname(__FILE__)."/../__private/mysql/serveur_sacoche_structure.php");
			require_once(dirname(__FILE__).'/class.DB.config.sacoche_structure.php');
						
			if (isset($attr['USER_ID'][0])) {
				//si on a un attribut USER_ID c'est qu'on a une authentification locale
				if ($attr['USER_ID'][0] == 0) {
					enregistrer_informations_session_webmestre();
				} else {
					enregistrer_informations_session($BASE,'normal',$attr['USER_ID'][0]);
				}
			} else {
				//si on a pas d'attribut USER_ID c'est qu'on a une authentification externe. On va rechercher sur l'attribut USER_ID_ENT
				$DB_ROW = DB_STRUCTURE_recuperer_donnees_utilisateur_id('gepi',$attr['USER_ID_ENT'][0]);
				$user_id = -1;
				if(!count($DB_ROW)) {
					//l'utilisateur n'est pas dans la base on va l'importer
					$user_id = DB_STRUCTURE_ajouter_utilisateur(
						'', //sconet_id
						'', //sconet_num
						'', //reference
						$attr['USER_PROFIL'][0],
						$attr['USER_NOM'][0],
						$attr['USER_PRENOM'][0],
						$attr['USER_ID_GEPI'][0], //on met l'id gepi (qui correspond au login gepi) pour le user_login
						'', //pas de password pour une authentification externe
						'', //classe
						$attr['USER_ID_ENT'][0],
						$attr['USER_ID_GEPI'][0]
						);
				} else {
					$user_id = (int) $DB_ROW['user_id'];
				}
				
				//on va mettre à jours l'utilisateur avec les données transmises
				$DB_VAR = array();
				$DB_VAR[':profil'] = $attr['USER_PROFIL'][0];
				$DB_VAR[':nom'] = $attr['USER_NOM'][0];
				$DB_VAR[':prenom'] = $attr['USER_PRENOM'][0];
				$DB_VAR[':login'] = $attr['USER_ID_GEPI'][0];
				$DB_VAR[':id_ent'] = $attr['USER_ID_ENT'][0];
				$DB_VAR[':id_gepi'] = $attr['USER_ID_GEPI'][0];
				DB_STRUCTURE_modifier_utilisateur($user_id,$DB_VAR);
				//on met à jour les matières
				if (isset($attr['matieres'])) {
					// Récupérer la liste des matiere_id
					$DB_SQL = 'SELECT matiere_id FROM sacoche_matiere ';
					$DB_SQL.= 'WHERE matiere_ref=:matiere_ref limit 1;';
					foreach($attr['matieres'] as $matiere_ref) {
						$DB_VAR = array(':matiere_ref'=>$matiere_ref);
						$DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
						if(count($DB_ROW)) {
							DB_STRUCTURE_modifier_liaison_professeur_matiere($user_id,$DB_ROW['matiere_id'],true);
						}
					}
				}	
				
				$result = enregistrer_informations_session($BASE,'gepi',$attr['USER_ID_ENT'][0]);
				if ($result != null) {
					echo 'il y a une erreur car normalement on vient d enregistrer le profil mais il ne semble pas être dans la base : '.$result;
				}
			}
			require_once(dirname(__FILE__).'/fonction_maj_base.php');
		}
	}
	
	if(!isset($_COOKIE[SESSION_NOM]))
	{
		// I. Aucune session transmise
		open_new_session(); init_session();
		if(!$TAB_PROFILS_AUTORISES['public'])
		{
			if (defined('SIMPLESAML_AUTHSOURCE') && SIMPLESAML_AUTHSOURCE != '') {$auth->requireAuth();}
			// I.2. Redirection : demande d'accès à une page réservée donc identification avant accès direct
			if(isset($_GET['verif_cookie']))
			{
				// I.2.a. L'utilisateur vient de s'identifier : c'est donc anormal, le cookie de session n'a pas été trouvé car le navigateur client n'enregistre pas les cookies
				alert_redirection_exit($tab_msg_alerte['I.2.a'][SACoche]);
			}
			else
			{
				// I.2.b. Session perdue ou expirée pour une raison diverse (cookies effacés, plusieurs onglets, expiration, ...)
				alert_redirection_exit($tab_msg_alerte['I.2.b'][SACoche]);
			}
		}
	}
	else
	{
		// II. id de session transmis
		open_old_session();
		if(!isset($_SESSION['USER_PROFIL']))
		{
			if (defined('SIMPLESAML_AUTHSOURCE') && SIMPLESAML_AUTHSOURCE != '') {$auth->requireAuth();}
			// II.1. Pas de session retrouvée (sinon cette variable serait renseignée)
			if(!$TAB_PROFILS_AUTORISES['public'])
			{
				// II.1.a. Session perdue ou expirée et demande d'accès à une page réservée : redirection pour une nouvelle identification
				$ALERTE_SSO = close_session(); open_new_session(); init_session();
				alert_redirection_exit($tab_msg_alerte['II.1.a'][SACoche]);
			}
			else
			{
				// II.1.b. Session perdue ou expirée et page publique : création d'une nouvelle session (éventuellement un message d'alerte pour indiquer session perdue ?)
				$ALERTE_SSO = close_session();open_new_session();init_session();
			}
		}
		elseif($_SESSION['USER_PROFIL'] == 'public')
		{
			// II.3. Personne non identifiée
			if(!$TAB_PROFILS_AUTORISES['public'])
			{
				if (defined('SIMPLESAML_AUTHSOURCE') && SIMPLESAML_AUTHSOURCE != '') {$auth->requireAuth();}
				// II.3.a. Espace non identifié => Espace identifié : redirection pour identification
				init_session();
				alert_redirection_exit($tab_msg_alerte['II.3.a'][SACoche]);
			}
			else
			{
				// II.3.b. Espace non identifié => Espace non identifié : RAS
			}
		}
		else
		{
			// II.4. Personne identifiée
			if($TAB_PROFILS_AUTORISES[$_SESSION['USER_PROFIL']])
			{
				// II.4.a. Espace identifié => Espace identifié identique : RAS
			}
			elseif($TAB_PROFILS_AUTORISES['public'])
			{
				// II.4.b. Espace identifié => Espace non identifié : création d'une nouvelle session vierge (éventuellement un message d'alerte pour indiquer session perdue ?)
				if (SACoche!='ajax')
				{
					// Ne pas déconnecter si on appelle le calendrier de l'espace public
					$ALERTE_SSO = close_session();open_new_session();init_session();
				}
			}
			elseif(!$TAB_PROFILS_AUTORISES['public'])
			{
				// II.4.c. Espace identifié => Autre espace identifié incompatible : redirection pour une nouvelle identification
				init_session();
				alert_redirection_exit($tab_msg_alerte['II.4.c'][SACoche]);
			}
		}
	}
}

?>