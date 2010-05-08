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
$TITRE = "Mentions légales";
?>

<p class="hc">
	<span class="manuel"><a class="pop_up" href="./aide.php?fichier=webmestre_mentions_legales">DOC : Mentions légales (page d'accueil)</a></span></p>
</p>

<hr />

<form id="form1" action=""><fieldset>
	<h2>Caractéristiques de l'hébergement</h2>
	<label class="tab" for="f_denomination">Dénomination <img alt="" src="./_img/bulle_aide.png" title="Exemples :<br />Collège de Trucville<br />Rectorat du paradis" /> :</label><input id="f_denomination" name="f_denomination" size="55" type="text" value="<?php echo html(HEBERGEUR_DENOMINATION); ?>" /><br />
	<label class="tab" for="f_adresse_site">Adresse web <img alt="" src="./_img/bulle_aide.png" title="Exemples :<br />http://www.college-trucville.com<br />http://www.ac-paradis.fr<br />Ce champ est facultatif." /> :</label><input id="f_adresse_site" name="f_adresse_site" size="60" type="text" value="<?php echo html(HEBERGEUR_ADRESSE_SITE); ?>" /><br />
	<label class="tab" for="f_logo">Logo :</label><select id="f_logo" name="f_logo"><option value=""></option></select><label id="ajax_logo"></label><br />
	<label class="tab" for="f_cnil">C.N.I.L. <img alt="" src="./_img/bulle_aide.png" title="Voir la documentation.<br />Mettre 'non renseignée' ou 'n°12345678'." /> :</label><input id="f_cnil" name="f_cnil" size="15" type="text" value="<?php echo html(HEBERGEUR_CNIL); ?>" /><br />
	<h2>Coordonnées du webmestre</h2>
	<label class="tab" for="f_nom">Nom :</label><input id="f_nom" name="f_nom" size="20" type="text" value="<?php echo html(WEBMESTRE_NOM); ?>" /><br />
	<label class="tab" for="f_prenom">Prénom :</label><input id="f_prenom" name="f_prenom" size="20" type="text" value="<?php echo html(WEBMESTRE_PRENOM); ?>" /><br />
	<label class="tab" for="f_courriel">Courriel :</label><input id="f_courriel" name="f_courriel" size="60" type="text" value="<?php echo html(WEBMESTRE_COURRIEL); ?>" /><p />
	<span class="tab"></span><input id="f_action" name="f_action" type="hidden" value="enregistrer" /><input id="f_submit" type="submit" value="Valider." /><label id="ajax_msg">&nbsp;</label><br />
</fieldset></form>

<hr />

<h2>Logos disponibles</h2>
<form id="form2" action=""><fieldset>
	<label class="tab" for="f_upload">Uploader un logo :</label><input id="f_upload" type="button" value="Parcourir..." /><label id="ajax_upload">&nbsp;</label>
	<p><label id="ajax_listing"></label></p>
	<ul class="puce" id="listing_logos">
		<li></li>
	</ul>
</fieldset></form>

