<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://competences.sesamath.net> - Suivi d'Acquisitions de Compétences
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
$VERSION_JS = '3';
?>

<?php
// Lecture d'un cookie sur le poste client servant à retenir le dernier établissement sélectionné si identification avec succès
$select_id = (isset($_COOKIE['competences-etablissement'])) ? $_COOKIE['competences-etablissement'] : 0;
// Test si id d'établissement transmis dans l'URL
$select_id = (isset($_GET['id'])) ? $_GET['id'] : $select_id;
// formulaire de choix d'établissements
$options_structures = afficher_select(DB_OPT_structures_sacoche() , $select_nom=false , $option_first='val' , $selection=$select_id , $optgroup='oui');
?>

<?php
// Fichiers temporaires à effacer ; il y a ausi le dossier './__tmp/cookie/' auquel on ne touche pas.
vide_dossier('./__tmp/login-mdp/' ,     10); // Nettoyer ce dossier des fichiers antérieurs à 10 minutes
vide_dossier('./__tmp/export/'    ,     60); // Nettoyer ce dossier des fichiers antérieurs à 1 heure
vide_dossier('./__tmp/import/'    ,   1440); // Nettoyer ce dossier des fichiers antérieurs à 1 jour (24h)
vide_dossier('./__tmp/dump-base/' ,  10080); // Nettoyer ce dossier des fichiers antérieurs à 1 semaine
vide_dossier('./__tmp/rss/'       ,  43800); // Nettoyer ce dossier des fichiers antérieurs à 1 mois
vide_dossier('./__tmp/badge/'     , 525600); // Nettoyer ce dossier des fichiers antérieurs à 1 an
?>

<?php
// Dump de la base SACoche du serveur Sésamath avec backupDB http://www.silisoftware.com/scripts/
// On peut tester aussi http://www.clubic.com/forum/programmation/php-mysql-produire-un-dump-de-quelques-tables-un-backup-a-la-myadmin-mais-plus-simple-id176796-page1.html
// On peut tester aussi http://www.journaldunet.com/developpeur/tutoriel/php/050304-php-dump-mysql.shtml
/*
Bien mieux : MySQLDUmper http://www.mysqldumper.net mais necessite de placer un script perl dans un dossier cgi pour être automatisé
A voir aussi : http://www.ozerov.de/bigdump.php
*/
// La base étant trop grosse, ça provoque systématiquement une erreur 500, alors je laisse tomber en attendant la décentralisation.
/*
if(is_dir('./__pages_webmestre'))
{
	$chemin_fichier = './__tmp/dump-base/db_backup.'.SACOCHE_BD_NAME.'.'.date("Y-m-d").'.sql.gz';
	if(!is_file($chemin_fichier))
	{
		// fichier fantôme pour éviter un autre appel avant la fin du dump
		file_put_contents($chemin_fichier,'');
		// appel du dump via la source d'une image
		echo'<img alt="" src="./__DBsvg/backupDB.php?nohtml=1&amp;onlyDB='.SACOCHE_BD_NAME.'&amp;StartBackup=standard&amp;mailto=1" />';
	}
}
*/
?>

<?php
// Alerte si navigateur trop ancien
require_once('./_inc/fonction_css_browser_selector.php');
$chaine_detection = css_browser_selector();
if( strpos($chaine_detection,'ie4') || strpos($chaine_detection,'ie5') || strpos($chaine_detection,'ie6') || strpos($chaine_detection,'ff0') || strpos($chaine_detection,'ff1') || strpos($chaine_detection,'ff2') || strpos($chaine_detection,'opera8') )
{
	echo'<hr />';
	echo'<div class="danger">Attention : votre navigateur semble trop ancien, il ne permet pas d\'utiliser SACoche dans de bonne conditions !</div>';
	echo'<div class="astuce">Installez 
	<a class="lien_ext" href="http://www.mozilla-europe.org/fr/"><img src="./_img/navigateur/firefox18.gif" alt="Firefox" /> Mozilla Firefox 3 ou +</a>, 
	<a class="lien_ext" href="http://www.opera-fr.com/telechargements/"><img src="./_img/navigateur/opera18.gif" alt="Opéra" /> Opéra 9 ou 10</a>, 
	<a class="lien_ext" href="http://www.apple.com/fr/safari/"><img src="./_img/navigateur/safari18.gif" alt="Safari" /> Safari 3 ou 4</a>, 
	<a class="lien_ext" href="http://www.google.fr/chrome"><img src="./_img/navigateur/chrome18.gif" alt="Chrome" /> Google Chrome</a>, 
	<a class="lien_ext" href="http://www.windows.fr/ie8"><img src="./_img/navigateur/explorer18.gif" alt="Explorer" /> Internet Explorer 7 ou 8</a>...
	</div>';
	echo'<hr />';
}
?>

<?php
// Alerte non déconnexion de l'ENT si deconnexion de SACoche depuis un compte connecté via un ENT
if($ALERTE_SSO)
{
	echo'<div class="danger">Attention : vous n\'êtes pas déconnecté de l\'ENT ! On peut encore entrer dans <em>SACoche</em> sans s\'identifier ! Fermez votre navigateur ou <a class="lien_ext" href="./pages_public/logout_SSO.php?'.$ALERTE_SSO.'">déconnectez-vous de l\'ENT</a>.</div>';
}
?>

<h2>Identification</h2>
<form action=""><fieldset>
	<label class="tab" for="f_structure">Établissement :</label><select id="f_structure" name="f_structure" tabindex="1" ><?php echo $options_structures ?></select><br />
	<label class="tab" for="f_login">Nom d'utilisateur :</label><input id="f_login" name="f_login" size="20" type="text" value="" tabindex="3" /><i id="f_or">&nbsp;&nbsp;&nbsp;ou&nbsp;&nbsp;&nbsp;</i><label for="f_administrateur"><input id="f_administrateur" name="f_administrateur" type="checkbox" value="administrateur" tabindex="2" /> Administrateur</label><br />
	<label class="tab" for="f_password">Mot de passe :</label><input id="f_password" name="f_password" size="20" type="password" value="" tabindex="4" /><input id="f_password2" name="f_password2" size="20" type="text" value="connexion ENT" disabled="disabled" class="hide" /><br />
	<span class="tab"></span><input id="f_submit" type="submit" value="Accéder à son espace." tabindex="5" /><label id="ajax_msg">&nbsp;</label><br />
</fieldset></form>

<h2>Statistiques</h2>
<div>
	<?php echo affichage_stats(); ?>
</div>

<h2>Présentation</h2>
<ul class="puce">
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=intro-pourquoi-competences">DOC : Pourquoi évaluer par compétences ?</a></span></li>
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=intro-origine">DOC : Origine du projet.</a></span></li>
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=intro-fonctionnalites">DOC : Fonctionnalités de <em>SACoche</em>.</a></span></li>
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=intro-copies-ecran">DOC : Copies d'écran.</a></span></li>
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=intro-documentations">DOC : Documentations spécifiques.</a></span></li>
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=intro-avenir">DOC : Avenir de <em>SACoche</em>.</a></span></li>
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=intro-pourquoi-nom">DOC : Pourquoi le nom <em>SACoche</em> ?</a></span></li>
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=intro-demonstration">DOC : Établissement de démonstration.</a></span></li>
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=intro-inscription">DOC : Inscrire son établissement.</a></span></li>
</ul>

<h2>Actualités</h2>
<div>
<ul class="puce">
	<?php
	if(is_dir('./__pages_webmestre'))
	{
		$news_id = (isset($_GET['news'])) ? clean_entier($_GET['news']) : 0;
		$DB_SQL = ($news_id) ? 'SELECT * FROM livret_rss WHERE livret_rss_id='.$news_id.' LIMIT 1' : 'SELECT * FROM livret_rss ORDER BY livret_rss_date DESC LIMIT 1';
		$DB_ROW = DB::queryRow(SACOCHE_BD_NAME , $DB_SQL);
		if(count($DB_ROW))
		{
			echo'<li><b>'.html($DB_ROW['livret_rss_titre']).'</b> ['.convert_date_mysql_to_french($DB_ROW['livret_rss_date']).']<br />'.str_replace(array("\r\n","\r","\n"),'<br />',html($DB_ROW['livret_rss_contenu'])).'</li>';
		}
	}
	?>
	<li>Soyez au courant des nouveautés du projet, avec <a  href="http://competences.sesamath.net/_rss/rss.xml"><img src="./_img/rss.gif" alt="RSS" /> le flux RSS</a> !</li>
</ul>
</div>

<hr />
<div>
	<em>SACoche</em> est un projet de <a class="lien_ext" href="http://www.sesamath.net"><img alt="Sésamath_logo" src="./_img/logo_sesamath.png" /></a> .<br />
	Site conforme aux normes <img src="./_img/valid_xhtml1.gif" alt="XHTML 1.0 Strict" /> et <img src="./_img/valid_css2.gif" alt="CSS 2.0" /> .<br />
	Utilisez un navigateur respectant les standards, comme <a class="lien_ext" href="http://www.mozilla-europe.org/fr/"><img src="./_img/navigateur/firefox16.png" alt="Firefox" /> Firefox</a> !<br />
	Déclaration <a class="lien_ext" href="http://www.cnil.fr">CNIL</a> n°1390450 (catégorie "Espace numérique de travail").<br />
	Pour un <a class="lien_ext" href="http://fr.wikipedia.org/wiki/Easter_egg">easter egg</a>, essayez le <a class="lien_ext" href="http://fr.wikipedia.org/wiki/Code_Konami">code Konami</a> <img src="./_img/smiley.gif" alt="sourire" />
</div>

