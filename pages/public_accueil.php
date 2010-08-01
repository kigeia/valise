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

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
$TITRE = ''; // Pas de titre pour que le logo s'affiche à la place
$VERSION_JS_FILE += 5;
?>

<?php
// Lecture d'un cookie sur le poste client servant à retenir le dernier établissement sélectionné si identification avec succès
$BASE = (isset($_COOKIE[COOKIE_STRUCTURE])) ? clean_entier($_COOKIE[COOKIE_STRUCTURE]) : 0 ;
// Test si id d'établissement transmis dans l'URL
$BASE = (isset($_GET['id'])) ? clean_entier($_GET['id']) : $BASE ;
// Test si affichage du formulaire spécial pour un administrateur d'une structure (pas de SSO) ou pour le webmestre
$profil = ( isset($_GET['admin']) || isset($_GET['administrateur']) ) ? 'administrateur' : ( (isset($_GET['webmestre'])) ? 'webmestre' : 'normal' ) ;
?>

<?php
// Fichiers temporaires à effacer ; il y a ausi le dossier './__tmp/cookie/' auquel on ne touche pas, et les sous-dossiers de './__tmp/badge/' traités ailleurs
effacer_fichiers_temporaires('./__tmp/login-mdp' ,     10); // Nettoyer ce dossier des fichiers antérieurs à 10 minutes
effacer_fichiers_temporaires('./__tmp/export'    ,     60); // Nettoyer ce dossier des fichiers antérieurs à 1 heure
effacer_fichiers_temporaires('./__tmp/dump-base' ,     60); // Nettoyer ce dossier des fichiers antérieurs à 1 heure
effacer_fichiers_temporaires('./__tmp/import'    ,  10080); // Nettoyer ce dossier des fichiers antérieurs à 1 semaine
effacer_fichiers_temporaires('./__tmp/rss'       ,  43800); // Nettoyer ce dossier des fichiers antérieurs à 1 mois
?>

<?php
// Alerte si navigateur trop ancien
require_once('./_inc/fonction_css_browser_selector.php');
$chaine_detection = css_browser_selector();
if( strpos($chaine_detection,'ie4') || strpos($chaine_detection,'ie5') || strpos($chaine_detection,'ie6') || strpos($chaine_detection,'ff0') || strpos($chaine_detection,'ff1') || strpos($chaine_detection,'ff2') || strpos($chaine_detection,'opera8') )
{
	echo'<hr />';
	echo'<div class="danger">Attention : votre navigateur semble trop ancien pour permettre d\'utiliser SACoche correctement !</div>';
	echo'<div class="astuce">Installez 
	<a class="lien_ext" href="http://www.mozilla-europe.org/fr/"><img src="./_img/navigateur/firefox18.gif" alt="Firefox" /> Mozilla Firefox 3 ou +</a>, 
	<a class="lien_ext" href="http://www.opera-fr.com/telechargements/"><img src="./_img/navigateur/opera18.gif" alt="Opéra" /> Opéra 9 ou 10</a>, 
	<a class="lien_ext" href="http://www.apple.com/fr/safari/"><img src="./_img/navigateur/safari18.gif" alt="Safari" /> Safari 3 ou 4</a>, 
	<a class="lien_ext" href="http://www.google.fr/chrome"><img src="./_img/navigateur/chrome18.gif" alt="Chrome" /> Google Chrome</a>, 
	<a class="lien_ext" href="http://www.windows.fr/ie8"><img src="./_img/navigateur/explorer18.gif" alt="Explorer" /> Internet Explorer 7 ou 8</a>...
	</div>';
}
?>

<?php
// Alerte non déconnexion de l'ENT si deconnexion de SACoche depuis un compte connecté via un ENT
if($ALERTE_SSO)
{
	echo'<hr />';
	echo'<div class="danger">Attention : vous n\'êtes pas déconnecté de l\'ENT ! On peut encore entrer dans <em>SACoche</em> sans s\'identifier ! Fermez votre navigateur ou <a class="lien_ext" href="index.php?page=public_logout_SSO&amp;'.$ALERTE_SSO.'">déconnectez-vous de l\'ENT</a>.</div>';
}
?>

<?php
// Liens vers les autres profils d'identification
$liens_autres_profils = '';
if($profil!='normal')
{
	$liens_autres_profils .= '<a class="anti_h2" href="index.php">normal</a>';
}
if($profil!='administrateur')
{
	$liens_autres_profils .= '<a class="anti_h2" href="index.php?administrateur">administrateur</a>';
}
if($profil!='webmestre')
{
	$liens_autres_profils .= '<a class="anti_h2" href="index.php?webmestre">webmestre</a>';
}
?>

<hr />

<h2><img src="./_img/login.gif" alt="Identification" /> Identification <?php echo($profil=='normal')?'normale':'<span style="color:#C00">'.$profil.'</span>'; ?><?php echo $liens_autres_profils ?></h2>
<form action=""><fieldset>
	<input id="f_base" name="f_base" type="hidden" value="<?php echo $BASE ?>" />
	<input id="f_profil" name="f_profil" type="hidden" value="<?php echo $profil ?>" />
	<label id="ajax_msg" class="loader">Chargement en cours...</label>
</fieldset></form>

<hr />

<h2><img src="./_img/serveur.png" alt="Hébergement" /> Hébergement</h2>
<ul class="puce">
	<li><em>SACoche</em> peut être téléchargé et installé sur différents serveurs.</li>
	<li>Cette installation a été effectuée par : <?php echo (HEBERGEUR_ADRESSE_SITE) ? '<a class="lien_ext" href="'.html(HEBERGEUR_ADRESSE_SITE).'">'.html(HEBERGEUR_DENOMINATION).'</a>' : html(HEBERGEUR_DENOMINATION); ?> (<?php echo mailto(WEBMESTRE_COURRIEL,'SACoche','contact'); ?>).</li>
	<li>Déclaration <a class="lien_ext" href="http://www.cnil.fr">CNIL</a> <?php echo intval(HEBERGEUR_CNIL) ? 'n°'.HEBERGEUR_CNIL : 'non renseignée' ; ?>.</li>
</ul>

<hr />

<h2><img src="./_img/puce_astuce.png" alt="Informations" /> Informations</h2>
<ul class="puce">
	<li><em>SACoche</em> est un logiciel gratuit, libre, développé avec le soutien de <a class="lien_ext" href="http://www.sesamath.net"><em>Sésamath</em></a>.</li>
	<li>Consulter <a href="<?php echo SERVEUR_PROJET ?>">le site officiel de <em>SACoche</em></a> pour tout renseignement.</li>
	<li>Version installée <em><?php echo VERSION_PROG ?></em>.<label id="ajax_version" for="version"></label></li>
</ul>

<script type="text/javascript">
	var VERSION_PROG = "<?php echo VERSION_PROG ?>";
</script>
