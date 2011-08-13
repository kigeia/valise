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

/*
 * Ouvrir une session existante
 * 
 * @param void
 * @return bool
 */
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

/*
 * Ouvrir une nouvelle session
 * 
 * @param void
 * @return bool
 */
function open_new_session()
{
	$ID = uniqid().md5('grain_de_sable'.mt_rand());	// Utiliser l'option préfixe ou entropie de uniqid() insère un '.' qui peut provoquer une erreur disant que les seuls caractères autorisés sont a-z, A-Z, 0-9 et -
	session_id($ID);
	return session_start();
}

/*
 * Initialiser une session ouverte
 * 
 * @param void
 * @return void
 */
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

/*
 * Fermer une session existante
 * 
 * @param void
 * @return void
 */
function close_session()
{
//	include_once(dirname(dirname(__FILE__)).'/_lib/SimpleSAMLphp/lib/_autoload.php');
//	$auth = new SimpleSAML_Auth_Simple(SIMPLESAML_AUTHSOURCE);
//	if ($auth->isAuthenticated()) {
//		$auth->logout();
//	}
	$_SESSION = array();
	setcookie(session_name(),'',time()-42000,'');
	session_destroy();
}

/*
 * Rechercher une session existante et gérer les différents cas possibles.
 * 
 * @param array $TAB_PROFILS_AUTORISES
 * @return void | exit ! (sur une string si ajax, une page html, ou modification $PAGE pour process SSO)
 */
function gestion_session($TAB_PROFILS_AUTORISES,$PAGE = null)
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
			$BASE= clean_entier($_COOKIE[COOKIE_STRUCTURE]);
		}
	}
	setcookie(COOKIE_STRUCTURE,$BASE,time()+60*60*24*365,'');
	

	$path = dirname(dirname(__FILE__));
	require_once("$path/__private/config/constantes.php");
	require_once("$path/__private/mysql/serveur_sacoche_structure.php");
	require_once("$path/_inc/class.DB.config.sacoche_structure.php");
	require_once("$path/_inc/fonction_requetes_structure.php");
	require_once("$path/_lib/DB/DB.class.php");
	$DB_TAB = DB_STRUCTURE_lister_parametres('"connexion_mode","connexion_nom"');
	foreach($DB_TAB as $DB_ROW)
	{
		${$DB_ROW['parametre_nom']} = $DB_ROW['parametre_valeur'];
	}
		
	if (isset($connexion_mode) && $connexion_mode = 'ssaml' && isset($connexion_nom) && $connexion_nom == 'configured_source') {
		//on saute la page d'acceuil
		if ($PAGE == 'public_accueil') {
			header("Location: ./index.php?page=compte_accueil");
			die();
		}
		include_once(dirname(dirname(__FILE__)).'/_lib/SimpleSAMLphp/lib/_autoload.php');
		$auth = new SimpleSAML_Auth_SacocheSimple();
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
					$DB_ROW = DB_STRUCTURE_recuperer_donnees_utilisateur_id('normal',$attr['USER_ID'][0]);
					enregistrer_session_user($BASE,$DB_ROW);
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
				if (isset($attr['USER_PROFIL'])) $DB_VAR[':profil'] = $attr['USER_PROFIL'][0];
				if (isset($attr['USER_NOM'])) $DB_VAR[':nom'] = $attr['USER_NOM'][0];
				if (isset($attr['USER_PRENOM'])) $DB_VAR[':prenom'] = $attr['USER_PRENOM'][0];
				if (isset($attr['USER_ID_GEPI'])) $DB_VAR[':login'] = $attr['USER_ID_GEPI'][0];
				if (isset($attr['USER_ID_GEPI'])) $DB_VAR[':id_gepi'] = $attr['USER_ID_GEPI'][0];
				if (!empty($DB_VAR)) DB_STRUCTURE_modifier_utilisateur($user_id,$DB_VAR);
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
				$DB_ROW = DB_STRUCTURE_recuperer_donnees_utilisateur('gepi',$attr['USER_ID_GEPI'][0]);
				DB_STRUCTURE_modifier_date('connexion',$DB_ROW['user_id']);
				$result = enregistrer_session_user($BASE,$DB_ROW);
				if ($result != null) {
					echo 'il y a une erreur car normalement on vient d enregistrer le profil mais il ne semble pas être dans la base : '.$result;
				}
			}
			
			require_once(dirname(__FILE__).'/fonction_divers.php');
			maj_base_si_besoin($BASE);
		}
	}
	
	if(!isset($_COOKIE[SESSION_NOM]))
	{
		// 1. Aucune session transmise
		open_new_session(); init_session();
		if(!$TAB_PROFILS_AUTORISES['public'])
		{
			if (isset($connexion_mode) && $connexion_mode = 'ssaml' && isset($connexion_nom) && $connexion_nom == 'configured_source') {$auth->requireAuth();}
			// 1.1. Demande d'accès à une page réservée, donc besoin d'identification
			if(isset($_GET['verif_cookie']))
			{
				// 1.1.1. En fait l'utilisateur vient déjà de s'identifier : c'est donc anormal, le cookie de session n'a pas été trouvé car le navigateur client n'enregistre pas les cookies
				affich_message_exit($titre='Problème de cookies',$contenu='Session non retrouvée !<br />Configurez votre navigateur pour qu\'il accepte les cookies.');
			}
			else
			{
				// 1.1.2. Session perdue ou expirée, ou demande d'accès direct (lien profond) : redirection pour une nouvelle identification
				redirection_SSO_ou_message_exit(); // Si SSO au prochain coup on ne passera plus par là.
			}
		}
		else
		{
			// 1.2 Accès à une page publique : RAS
		}
	}
	else
	{
		// 2. id de session transmis
		open_old_session();
		if(!isset($_SESSION['USER_PROFIL']))
		{
			if (isset($connexion_mode) && $connexion_mode = 'ssaml' && isset($connexion_nom) && $connexion_nom == 'configured_source') {$auth->requireAuth();}
			// 2.1. Pas de session retrouvée (sinon cette variable serait renseignée)
			if(!$TAB_PROFILS_AUTORISES['public'])
			{
				// 2.1.1. Session perdue ou expirée et demande d'accès à une page réservée : redirection pour une nouvelle identification
				close_session(); open_new_session(); init_session();
				redirection_SSO_ou_message_exit(); // On peut initialiser la session avant car si SSO au prochain coup on ne passera plus par là.
			}
			else
			{
				// 2.1.2. Session perdue ou expirée et page publique : création d'une nouvelle session, pas de message d'alerte pour indiquer que la session perdue
				close_session();open_new_session();init_session();
			}
		}
		elseif($_SESSION['USER_PROFIL'] == 'public')
		{
			// 2.2. Session retrouvée, utilisateur non identifié
			if(!$TAB_PROFILS_AUTORISES['public'])
			{
				if (isset($connexion_mode) && $connexion_mode = 'ssaml' && isset($connexion_nom) && $connexion_nom == 'configured_source') {$auth->requireAuth();}
				// 2.2.1. Espace non identifié => Espace identifié : redirection pour identification
				redirection_SSO_ou_message_exit(); // Pas d'initialisation de session sinon la redirection avec le SSO tourne en boucle.
 			}
			else
			{
				// 2.2.2. Espace non identifié => Espace non identifié : RAS
			}
		}
		else
		{
			// 2.3. Session retrouvée, utilisateur identifié
			if($TAB_PROFILS_AUTORISES[$_SESSION['USER_PROFIL']])
			{
				// 2.3.1. Espace identifié => Espace identifié identique : RAS
			}
			elseif($TAB_PROFILS_AUTORISES['public'])
			{
				// 2.3.2. Espace identifié => Espace non identifié : création d'une nouvelle session vierge, pas de message d'alerte pour indiquer que la session perdue
				// A un moment il fallait tester que ce n'était pas un appel ajax,pour éviter une déconnexion si appel au calendrier qui était dans l'espace public, mais ce n'est plus le cas...
				// Par contre il faut conserver la session de SimpleSAMLphp pour laisser à l'utilisateur la choix de se déconnecter ou non de son SSO.
				$SimpleSAMLphp_SESSION = ($_SESSION['CONNEXION_MODE']=='gepi') ? $_SESSION['SimpleSAMLphp_SESSION'] : FALSE ;
				close_session();open_new_session();init_session();
				if($SimpleSAMLphp_SESSION) { $_SESSION['SimpleSAMLphp_SESSION'] = $SimpleSAMLphp_SESSION; }
			}
			elseif(!$TAB_PROFILS_AUTORISES['public']) // (forcément)
			{
				// 2.3.3. Espace identifié => Autre espace identifié incompatible : redirection pour une nouvelle identification
				// Pas de redirection SSO sinon on tourne en boucle (il faudrait faire une déconnexion SSO préalable).
				affich_message_exit($titre='Page interdite avec votre profil',$contenu='Vous avez appelé une page inaccessible avec votre identification actuelle !<br />Déconnectez-vous ou retournez à la page précédente.');
			}
		}
	}
}

?>