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
$TITRE = "Identité de l'installation";
?>

<?php
$exemple_denomination = (HEBERGEUR_INSTALLATION=='mono-structure') ? 'Collège de Trucville' : 'Rectorat du paradis' ;
$exemple_adresse_web  = (HEBERGEUR_INSTALLATION=='mono-structure') ? 'http://www.college-trucville.com' : 'http://www.ac-paradis.fr' ;
$uai_div_hide_avant   = (HEBERGEUR_INSTALLATION=='mono-structure') ? '' : '<div class="hide">' ;
$uai_div_hide_apres   = (HEBERGEUR_INSTALLATION=='mono-structure') ? '' : '</div>' ;
$cnil_check_oui       = intval(CNIL_NUMERO) ? ' checked' : '' ;
$cnil_check_non       = intval(CNIL_NUMERO) ? '' : ' checked' ;
$cnil_numero          = intval(CNIL_NUMERO) ? CNIL_NUMERO : '' ;
$cnil_dates_class     = intval(CNIL_NUMERO) ? 'show' : 'hide' ;
?>

<p><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_webmestre__identite_installation">DOC : Identité de l'installation (page d'accueil)</a></span></p>

<hr />

<form action="#" method="post" id="form1"><fieldset>
	<h2>Caractéristiques de l'hébergement</h2>
	<label class="tab" for="f_denomination">Dénomination <img alt="" src="./_img/bulle_aide.png" title="Exemple : <?php echo $exemple_denomination ?>" /> :</label><input id="f_denomination" name="f_denomination" size="55" type="text" value="<?php echo html(HEBERGEUR_DENOMINATION); ?>" /><br />
	<?php echo $uai_div_hide_avant ?>
	<label class="tab" for="f_uai">n° UAI (ex-RNE) <img alt="" src="./_img/bulle_aide.png" title="Ce champ est facultatif." /> :</label><input id="f_uai" name="f_uai" size="8" type="text" value="<?php echo html(HEBERGEUR_UAI); ?>" /><br />
	<?php echo $uai_div_hide_apres ?>
	<label class="tab" for="f_adresse_site">Adresse web <img alt="" src="./_img/bulle_aide.png" title="Exemple : <?php echo $exemple_adresse_web ?>" /> :</label><input id="f_adresse_site" name="f_adresse_site" size="60" type="text" value="<?php echo html(HEBERGEUR_ADRESSE_SITE); ?>" /><br />
	<label class="tab" for="f_logo">Logo :</label><select id="f_logo" name="f_logo"><option value=""></option></select><label id="ajax_logo"></label><br />
	<h2>Déclaration C.N.I.L. <img alt="" src="./_img/bulle_aide.png" title="Voir la documentation." /></h2>
	<label class="tab" for="f_logo">État :</label><label for="f_cnil_non"><input type="radio" id="f_cnil_non" name="f_cnil_etat" value="non"<?php echo $cnil_check_non ?> /> non renseignée</label>&nbsp;&nbsp;&nbsp;<label for="f_cnil_oui"><input type="radio" id="f_cnil_oui" name="f_cnil_etat" value="oui"<?php echo $cnil_check_oui ?> /> n°</label><input id="f_cnil_numero" name="f_cnil_numero" size="10" type="text" value="<?php echo $cnil_numero ?>" />
	<div id="cnil_dates" class="<?php echo $cnil_dates_class; ?>">
		<label class="tab" for="f_cnil_date_engagement">Date engagement <img alt="" src="./_img/bulle_aide.png" title="Date à laquelle la demande a été effectuée auprès de la CNIL." /> :</label><input id="f_cnil_date_engagement" name="f_cnil_date_engagement" size="9" type="text" value="<?php echo html(CNIL_DATE_ENGAGEMENT); ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q><br />
		<label class="tab" for="f_cnil_date_recepisse">Date récépissé <img alt="" src="./_img/bulle_aide.png" title="Date à laquelle la CNIL a retourné le récépissé de déclaration." /> :</label><input id="f_cnil_date_recepisse" name="f_cnil_date_recepisse" size="9" type="text" value="<?php echo html(CNIL_DATE_RECEPISSE); ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
	</div>
	<h2>Coordonnées du webmestre</h2>
	<label class="tab" for="f_nom">Nom :</label><input id="f_nom" name="f_nom" size="20" type="text" value="<?php echo html(WEBMESTRE_NOM); ?>" /><br />
	<label class="tab" for="f_prenom">Prénom :</label><input id="f_prenom" name="f_prenom" size="20" type="text" value="<?php echo html(WEBMESTRE_PRENOM); ?>" /><br />
	<label class="tab" for="f_courriel">Courriel :</label><input id="f_courriel" name="f_courriel" size="60" type="text" value="<?php echo html(WEBMESTRE_COURRIEL); ?>" />
	<p><span class="tab"></span><input id="f_action" name="f_action" type="hidden" value="enregistrer" /><button id="f_submit" type="submit" class="parametre">Valider ces réglages.</button><label id="ajax_msg">&nbsp;</label></p>
</fieldset></form>

<hr />

<h2>Logos disponibles</h2>
<form action="#" method="post" id="form2"><fieldset>
	<label class="tab" for="f_upload">Uploader un logo :</label><button id="f_upload" type="button" class="fichier_import">Parcourir...</button><label id="ajax_upload">&nbsp;</label>
	<p><label id="ajax_listing"></label></p>
	<ul class="puce" id="listing_logos">
		<li></li>
	</ul>
</fieldset></form>

