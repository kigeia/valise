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

$tab_messages_erreur = array();

// Fichier appelé pour l'affichage de chaque page.
// Passage en GET des paramètres pour savoir quelle page charger.

// Atteste l'appel de cette page avant l'inclusion d'une autre
define('SACoche','index');

// Constantes / Fonctions de redirections / Configuration serveur / Session
require_once('./_inc/constantes.php');
require_once('./_inc/fonction_redirection.php');
require_once('./_inc/config_serveur.php');
require_once('./_inc/fonction_sessions.php');

// Page appelée
$PAGE    = (isset($_REQUEST['page']))    ? $_REQUEST['page']    : ( (isset($_GET['sso'])) ? 'compte_accueil' : 'public_accueil' ) ;
$SECTION = (isset($_REQUEST['section'])) ? $_REQUEST['section'] : '' ;

// Fichier d'informations sur l'hébergement (requis avant la gestion de la session).
$fichier_constantes = $CHEMIN_CONFIG.'constantes.php';
if( (!is_file($fichier_constantes)) && ($PAGE!='public_installation') )
{
	affich_message_exit($titre='Informations hébergement manquantes',$contenu='Informations concernant l\'hébergeur manquantes.<br /><a href="./index.php?page=public_installation">Procédure d\'installation du site SACoche.</a>');
}

// Ouverture de la session et gestion des droits d'accès
require_once('./_inc/tableau_droits.php');
if(!isset($tab_droits[$PAGE]))
{
	$tab_messages_erreur[] = 'Erreur : droits de la page "'.$PAGE.'" manquants.';
	$PAGE = (substr($PAGE,0,6)=='public') ? 'public_accueil' : 'compte_accueil' ;
}

if (isset($_REQUEST['f_action']) && $_REQUEST['f_action'] == 'logout') {
	open_old_session();
	if (isset($_SESSION['USER_PROFIL'])) {
		if ($_SESSION['CONNEXION_MODE'] == 'cas') {
			$url = $_SESSION['CAS_SERVEUR_HOST'];
			close_session();
			header("Location: ".$url);
			die;
		} else if ($_SESSION['CONNEXION_MODE'] == 'ssaml' && $_SESSION['CONNEXION_NOM'] == 'configured_source'
			&& false !== strpos($_SESSION['AUTH_SIMPLESAML_SOURCE'], 'distant') ){
			$url = $_SESSION['GEPI_URL'];
			close_session();
			header("Location: ".$url);
			die;
		} else if ($_SESSION['CONNEXION_MODE'] == 'ssaml' && $_SESSION['CONNEXION_NOM'] == 'configured_source') {
			include_once(dirname(__FILE__).'/_lib/SimpleSAMLphp/lib/_autoload.php');
			$auth = new SimpleSAML_Auth_SacocheSimple();
			if ($auth->isAuthenticated()) {
				$auth->logout();
			}
			close_session();
			header("Location: ./index.php");
			die;
		}
	}
	close_session();
	header("Location: ./index.php");
	die;
}
gestion_session($tab_droits[$PAGE],$PAGE);

// Blocage éventuel par le webmestre ou un administrateur (on ne peut pas le tester avant car il faut avoir récupéré les données de session)
tester_blocage_application($_SESSION['BASE'],$demande_connexion_profil=false);

// Autres fonctions à charger
require_once('./_inc/fonction_clean.php');
require_once('./_inc/fonction_divers.php');
require_once('./_inc/fonction_formulaires_select.php');
require_once('./_inc/fonction_requetes_structure.php');
require_once('./_inc/fonction_requetes_webmestre.php');
require_once('./_inc/fonction_affichage.php');

// Annuler un blocage par l'automate anormalement long
annuler_blocage_anormal();

// Patch fichier de config
if(is_file($fichier_constantes))
{
	// DEBUT PATCH CONFIG 1
	// A compter du 05/12/2010, ajout de paramètres dans le fichier de constantes pour paramétrer cURL. [à retirer dans quelques mois]
	if(!defined('SERVEUR_PROXY_USED') && function_exists('enregistrer_informations_session'))
	{
		fabriquer_fichier_hebergeur_info( array('SERVEUR_PROXY_USED'=>'','SERVEUR_PROXY_NAME'=>'','SERVEUR_PROXY_PORT'=>'','SERVEUR_PROXY_TYPE'=>'','SERVEUR_PROXY_AUTH_USED'=>'','SERVEUR_PROXY_AUTH_METHOD'=>'','SERVEUR_PROXY_AUTH_USER'=>'','SERVEUR_PROXY_AUTH_PASS'=>'') );
	}
	// FIN PATCH CONFIG 1
	// DEBUT PATCH CONFIG 2
	// A compter du 26/05/2011, ajout de paramètres dans le fichier de constantes pour les dates CNIL. [à retirer dans quelques mois]
	if(!defined('CNIL_NUMERO') && function_exists('enregistrer_informations_session'))
	{
		fabriquer_fichier_hebergeur_info( array('CNIL_NUMERO'=>HEBERGEUR_CNIL,'CNIL_DATE_ENGAGEMENT'=>'','CNIL_DATE_RECEPISSE'=>'') );
	}
	// FIN PATCH CONFIG 2
}

// Interface de connexion à la base, chargement et config (test sur $fichier_constantes car à éviter si procédure d'installation non terminée).
if(is_file($fichier_constantes))
{
	require_once($fichier_constantes);
	// Classe de connexion aux BDD
	require_once('./_lib/DB/DB.class.php');
	// Choix des paramètres de connexion à la base de données adaptée...
	// ...multi-structure ; base sacoche_structure_***
	if( (in_array($_SESSION['USER_PROFIL'],array('administrateur','directeur','professeur','parent','eleve'))) && (HEBERGEUR_INSTALLATION=='multi-structures') )
	{
		$fichier_mysql_config = 'serveur_sacoche_structure_'.$_SESSION['BASE'];
		$fichier_class_config = 'class.DB.config.sacoche_structure';
		$PATCH = 'STRUCTURE' ; // A compter du 02/08/2010, déplacement du port dans le fichier créé à l'installation. [à retirer dans quelques mois]
	}
	// ...multi-structure ; base sacoche_webmestre
	elseif( (in_array($_SESSION['USER_PROFIL'],array('webmestre','public'))) && (HEBERGEUR_INSTALLATION=='multi-structures') )
	{
		$fichier_mysql_config = 'serveur_sacoche_webmestre';
		$fichier_class_config = 'class.DB.config.sacoche_webmestre';
		$PATCH = 'WEBMESTRE' ; // A compter du 02/08/2010, déplacement du port dans le fichier créé à l'installation. [à retirer dans quelques mois]
	}
	// ...mono-structure ; base sacoche_structure
	elseif(HEBERGEUR_INSTALLATION=='mono-structure')
	{
		$fichier_mysql_config = 'serveur_sacoche_structure';
		$fichier_class_config = 'class.DB.config.sacoche_structure';
		$PATCH = 'STRUCTURE' ; // A compter du 02/08/2010, déplacement du port dans le fichier créé à l'installation. [à retirer dans quelques mois]
	}
	else
	{
		affich_message_exit($titre='Configuration anormale',$contenu='Une anomalie dans les données d\'hébergement et/ou de session empêche l\'application de se poursuivre.');
	}
	// Ajout du chemin correspondant
	$fichier_mysql_config = $CHEMIN_MYSQL.$fichier_mysql_config.'.php';
	$fichier_class_config = './_inc/'.$fichier_class_config.'.php';
	// Chargement du fichier de connexion à la BDD
	if(is_file($fichier_mysql_config))
	{
		require_once($fichier_mysql_config);
		// DEBUT PATCH MYSQL 1
		// A compter du 02/08/2010, déplacement du port dans le fichier créé à l'installation. [à retirer dans quelques mois]
		if(!defined('SACOCHE_'.$PATCH.'_BD_PORT'))
		{
			$tab_fichier = Lister_Contenu_Dossier($CHEMIN_MYSQL);
			$bad = array( "define('SACOCHE_STRUCTURE_BD_NAME" , "define('SACOCHE_WEBMESTRE_BD_NAME" );
			$bon = array( "define('SACOCHE_STRUCTURE_BD_PORT','3306');	// Port de connexion\r\ndefine('SACOCHE_STRUCTURE_BD_NAME" , "define('SACOCHE_WEBMESTRE_BD_PORT','3306');	// Port de connexion\r\ndefine('SACOCHE_WEBMESTRE_BD_NAME" );
			foreach($tab_fichier as $fichier)
			{
				$fichier_contenu = file_get_contents($CHEMIN_MYSQL.'/'.$fichier);
				$fichier_contenu = str_replace($bad,$bon,$fichier_contenu);
				Ecrire_Fichier($CHEMIN_MYSQL.'/'.$fichier,$fichier_contenu);
			}
			define('SACOCHE_'.$PATCH.'_BD_PORT','3306');	// Port de connexion
		}
		// FIN PATCH MYSQL 1
		require_once($fichier_class_config);
	}
	elseif($PAGE!='public_installation')
	{
		affich_message_exit($titre='Paramètres BDD manquants',$contenu='Paramètres de connexion à la base de données manquants.<br /><a href="./index.php?page=public_installation">Procédure d\'installation du site SACoche.</a>');
	}
	// DEBUT PATCH MYSQL 2
	// A compter du 05/12/2010, 2 users MySQL sont créés par établissement (localhost & %) ; il faut créer les manquants antérieurs sinon erreur lors de la suppression. [à retirer dans quelques mois]
	if(defined('SACOCHE_WEBMESTRE_BD_HOST'))
	{
		$nb_structures = (int)DB_WEBMESTRE_compter_structure();
		if($nb_structures)
		{
			$BDlink = mysql_connect(SACOCHE_WEBMESTRE_BD_HOST.':'.SACOCHE_WEBMESTRE_BD_PORT,SACOCHE_WEBMESTRE_BD_USER,SACOCHE_WEBMESTRE_BD_PASS);
			$BDres  = mysql_query('SELECT host, user FROM mysql.user WHERE user LIKE "sac_user_%"');
			$nb_users = mysql_num_rows($BDres);
			if($nb_users < $nb_structures*2)
			{
				$tab_user_host = array();
				while($BDrow = mysql_fetch_array($BDres,MYSQL_ASSOC))
				{
					$tab_user_host[$BDrow['user']][] = $BDrow['host'];
				}
				foreach($tab_user_host as $user => $tab_host)
				{
					if(count($tab_host)==1)
					{
						$fichier_mdp = file_get_contents('./__private/mysql/'.str_replace('sac_user','serveur_sacoche_structure',$user).'.php');
						$nb_match = preg_match( '#'."SACOCHE_STRUCTURE_BD_PASS','".'(.*?)'."'".'#' , $fichier_mdp , $tab_matches );
						$host = ($tab_host[0]=='%') ? 'localhost' : '%';
						$base = str_replace('user','base',$user);
						$pass = $tab_matches[1];
						mysql_query('CREATE USER '.$user.'@"'.$host.'" IDENTIFIED BY "'.$pass.'"');
						mysql_query('GRANT ALTER, CREATE, DELETE, DROP, INDEX, INSERT, SELECT, UPDATE ON '.$base.'.* TO '.$user.'@"'.$host.'"');
					}
				}
			}
			mysql_close($BDlink);
		}
	}
	// FIN PATCH MYSQL 2
}

// Authentification requise par SSO
if(defined('LOGIN_SSO'))
{
	require('./pages/public_login_SSO.php');
}

ob_start();
// Chargement de la page concernée
$filename_php = './pages/'.$PAGE.'.php';
if(!is_file($filename_php))
{
	$tab_messages_erreur[] = 'Erreur : page "'.$filename_php.'" manquante (supprimée, déplacée, non créée...).';
	$PAGE = ($_SESSION['USER_PROFIL']=='public') ? 'public_accueil' :'compte_accueil' ;
	$filename_php = './pages/'.$PAGE.'.php';
}
require($filename_php);
// Affichage dans une variable
$CONTENU_PAGE = ob_get_contents();
ob_end_clean();

// Chargement du js associé de la page
$filename_js_normal = './pages/'.$PAGE.'.js';
$SCRIPT = (is_file($filename_js_normal)) ? '<script type="text/javascript" charset="utf-8" src="'.compacter($filename_js_normal,$VERSION_JS_FILE,'pack').'"></script>' : '' ;

// Titre du navigateur
$TITRE_NAVIGATEUR = 'SACoche » Espace '.$_SESSION['USER_PROFIL'].' » ';
$TITRE_NAVIGATEUR.= ($TITRE) ? $TITRE : 'Evaluer par comptétences et valider le socle commun' ;

// Affichage de l'en-tête
entete();
?>
<head>
	<meta name="Description" content="SACoche - Suivi d'Acquisition de Compétences - Evaluer par compétences - Valider le socle commun" />
	<meta name="Keywords" content="SACoche Sésamath évaluer évaluation compétences compétence validation valider socle commun collège points Lomer" />
	<meta name="Author-Personal" content="Thomas Crespin pour Sésamath" />
	<meta name="Robots" content="index,follow" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" type="images/x-icon" href="./favicon.ico" />
	<link rel="icon" type="image/png" href="./favicon.png" />
	<link rel="stylesheet" type="text/css" href="<?php echo compacter('./_css/style.css',VERSION_CSS_SCREEN,'mini') ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo compacter('./_css/style_print.css',VERSION_CSS_SCREEN,'mini') ?>" media="print" />
	<?php if(isset($_SESSION['CSS'])){echo'<style type="text/css">'.$_SESSION['CSS'].'</style>';} ?>
	<script type="text/javascript" charset="utf-8" src="<?php echo compacter('./_js/jquery-librairies.js',VERSION_JS_BIBLIO,'mini') ?>"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo compacter('./_js/script.js',VERSION_JS_GLOBAL,'mini') ?>"></script>
	<title><?php echo $TITRE_NAVIGATEUR ?></title>
</head>
<body>
	<?php 
	if($_SESSION['USER_PROFIL']!='public')
	{
		// Espace identifié : cadre_haut (avec le menu) et cadre_bas (avec le contenu).
		echo'<div id="cadre_haut">'."\r\n";
		echo'	<div id="info">'."\r\n";
		echo'		<span class="button"><img alt="site officiel" src="./_img/favicon.gif" /> <a class="lien_ext" href="'.SERVEUR_PROJET.'">Site officiel</a></span>'."\r\n";
		if (isset($_SESSION['INTEGRATION_GEPI']) && $_SESSION['INTEGRATION_GEPI'] == 'yes') {
			echo'		<span class="button"> <a class="lien_ext" href="'.$_SESSION['GEPI_URL'].'?rne='.$_SESSION['GEPI_RNE'].'">Gepi</a></span>'."\r\n";
		}
		echo'		<span class="button"><img alt="structure" src="./_img/home.png" /> '.html($_SESSION['DENOMINATION']).'</span>'."\r\n";
		echo'		<span class="button"><img alt="'.$_SESSION['USER_PROFIL'].'" src="./_img/menu/profil_'.$_SESSION['USER_PROFIL'].'.png" /> '.html($_SESSION['USER_PRENOM'].' '.$_SESSION['USER_NOM']).' ('.$_SESSION['USER_PROFIL'].')</span>'."\r\n";
		echo'		<span class="button"><span id="clock"><img alt="" src="./_img/clock_fixe.png" /> '.$_SESSION['DUREE_INACTIVITE'].' min</span><img alt="" src="./_img/point.gif" /></span>'."\r\n";
		echo'		<button id="deconnecter"><img alt="" src="./_img/bouton/deconnecter.png" />';
		if ($_SESSION['CONNEXION_MODE'] == 'cas' || ($_SESSION['CONNEXION_MODE'] == 'ssaml' && $_SESSION['CONNEXION_NOM'] == 'configured_source' && false !== strpos($_SESSION['AUTH_SIMPLESAML_SOURCE'], 'distant')) ) {
			echo 'Retour au portail';
		} else {
			echo 'Déconnexion';
		}
		echo '</button>'."\r\n";
		echo'	</div>'."\r\n";
		echo'	<img id="logo" alt="SACoche" src="./_img/logo_petit2.png" />'."\r\n";
		$fichier_menu = ($_SESSION['USER_PROFIL']!='webmestre') ? '__menu_'.$_SESSION['USER_PROFIL'] : '__menu_'.$_SESSION['USER_PROFIL'].'_'.HEBERGEUR_INSTALLATION ;
		require_once('./pages/'.$fichier_menu.'.html'); // Le menu '<ul id="menu">...</ul>
		echo'</div>'."\r\n";
		echo'<div id="cadre_bas">'."\r\n";
		echo'	<h1>» '.$TITRE.'</h1>';
		if(count($tab_messages_erreur))
		{
			echo'<hr /><div class="danger o">'.implode('</div><div class="danger o">',$tab_messages_erreur).'</div><hr />';
		}
		echo 	$CONTENU_PAGE;
		// echo'<pre>';var_dump($_SESSION);echo'</pre>';
		echo'</div>'."\r\n";
	}
	else
	{
		// Accueil (identification ou procédure d'installation) : cadre unique (avec image SACoche & image hébergeur).
		echo'<div id="cadre_milieu">'."\r\n";
		$hebergeur_img  = ( (defined('HEBERGEUR_LOGO')) && (is_file('./__tmp/logo/'.HEBERGEUR_LOGO)) ) ? '<img alt="Hébergeur" src="./__tmp/logo/'.HEBERGEUR_LOGO.'" />' : '' ;
		$hebergeur_lien = ( (defined('HEBERGEUR_ADRESSE_SITE')) && HEBERGEUR_ADRESSE_SITE && ($hebergeur_img) ) ? '<a href="'.html(HEBERGEUR_ADRESSE_SITE).'">'.$hebergeur_img.'</a>' : $hebergeur_img ;
		$SACoche_lien   = '<a href="'.SERVEUR_PROJET.'"><img alt="Suivi d\'Acquisition de Compétences" src="./_img/logo_grand.gif" /></a>' ;
		echo ($PAGE=='public_accueil') ? '<h1 class="logo">'.$SACoche_lien.$hebergeur_lien.'</h1>' : '<h1>» '.$TITRE.'</h1>' ;
		echo 	$CONTENU_PAGE;
		// echo'<pre>';var_dump($_SESSION);echo'</pre>';
		echo'</div>'."\r\n";
	}
	?>
	<script type="text/javascript">
		var PAGE='<?php echo $PAGE ?>';
		var DUREE_AUTORISEE='<?php echo $_SESSION['DUREE_INACTIVITE'] ?>';
		var DUREE_AFFICHEE='<?php echo $_SESSION['DUREE_INACTIVITE'] ?>';
		var CONNEXION_USED='<?php echo (isset($_COOKIE[COOKIE_AUTHMODE])) ? $_COOKIE[COOKIE_AUTHMODE] : 'normal' ; ?>';
	</script>
	<?php echo $SCRIPT; ?>
	<!-- Objet flash pour lire un fichier audio grace au génial lecteur de neolao http://flash-mp3-player.net/ -->
	<h6><object class="playerpreview" id="myFlash" type="application/x-shockwave-flash" data="./_mp3/player_mp3_js.swf" height="1" width="1">
		<param name="movie" value="./_mp3/player_mp3_js.swf" />
		<param name="AllowScriptAccess" value="always" />
		<param name="FlashVars" value="listener=myListener&amp;interval=500" />
	</object></h6>
</body>
</html>
