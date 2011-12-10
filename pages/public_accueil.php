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

// Lecture d'un cookie sur le poste client servant à retenir le dernier établissement sélectionné si identification avec succès
$BASE = (isset($_COOKIE[COOKIE_STRUCTURE])) ? clean_entier($_COOKIE[COOKIE_STRUCTURE]) : 0 ;
// Test si id d'établissement transmis dans l'URL
$BASE = (isset($_GET['id'])) ? clean_entier($_GET['id']) : $BASE ;
// Test si affichage du formulaire spécial pour le webmestre
$profil = (isset($_GET['webmestre'])) ? 'webmestre' : 'normal' ;
// Bascule profil webmestre / profils autres
$liens_autres_profils = ($profil=='normal') ? '<a class="anti_h2" href="index.php?webmestre">profil webmestre</a>' : '<a class="anti_h2" href="index.php">profils classiques</a>' ;

// Alerte si pas de javascript activé
echo'<noscript><hr /><div class="danger">Vous devez activer le javascript dans votre navigateur pour utiliser <em>SACoche</em>.</div></noscript>';

// Alerte non déconnexion de l'ENT si deconnexion de SACoche depuis un compte connecté via un ENT
if( (isset($_COOKIE[COOKIE_STRUCTURE])) && (isset($_COOKIE[COOKIE_AUTHMODE])) && ($_COOKIE[COOKIE_AUTHMODE]!='normal') )
{
	echo'<hr />';
	echo'<div class="danger">Attention : vous n\'êtes pas déconnecté du service d\'authentification externe, on peut revenir dans <em>SACoche</em> sans s\'identifier ! Fermez votre navigateur ou <a href="index.php?page=public_logout_SSO&amp;base='.$_COOKIE[COOKIE_STRUCTURE].'">déconnectez-vous de ce service</a>.</div>';
}

// Supprimer le cookie avec le mode d'identification, servant à une reconnexion SSO, devenu inutile puisque déconnecté à présent.
if(isset($_COOKIE[COOKIE_AUTHMODE]))
{
	setcookie(COOKIE_AUTHMODE,'',time()-42000,'');
}
?>

<hr />
<form action=""><fieldset>
	<input id="f_base" name="f_base" type="hidden" value="<?php echo $BASE ?>" />
	<input id="f_profil" name="f_profil" type="hidden" value="<?php echo $profil ?>" />
	<label class="tab" for="f_login">Nom d\'utilisateur :</label><input id="f_login" name="f_login" size="20" type="text" value="" tabindex="2" /><br />
	<label class="tab" for="f_password">Mot de passe :</label><input id="f_password" name="f_password" size="20" type="password" value="" tabindex="3" /><br />
	<span class="tab"></span>
	<input id="f_mode" name="f_mode" type="hidden" value="normal" />
	<input id="f_action" name="f_action" type="hidden" value="identifier" /><button id="f_submit" type="submit" tabindex="4"><img alt="" src="./_img/bouton/mdp_perso.png" /> Accéder à son espace.</button><label id="ajax_msg">&nbsp;</label><br />
</fieldset></form>

<script type="text/javascript">
	var VERSION_PROG = "<?php echo VERSION_PROG ?>";
</script>
