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

// Élément de formulaire "f_geo" pour le choix d'une zone géographique
$options_geo = '';
$DB_TAB = DB_WEBMESTRE_WEBMESTRE::DB_lister_zones();
foreach($DB_TAB as $DB_ROW)
{
	$options_geo .= '<option value="'.$DB_ROW['geo_id'].'">'.html($DB_ROW['geo_nom']).'</option>';
}
?>

<p><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_webmestre__gestion_multi_etablissements">DOC : Gestion des établissements (multi-structures)</a></span></p>

<script type="text/javascript">
	// <![CDATA[
	var options_geo="<?php echo str_replace('"','\"',$options_geo); ?>";
	// ]]>
</script>

<form action="#" method="post" id="structures">
	<table class="form bilan_synthese vm_nug hsort">
		<thead>
			<tr>
				<th class="nu"></th>
				<th class="nu"><input id="all_check" type="image" alt="Tout cocher." src="./_img/all_check.gif" title="Tout cocher." /><br /><input id="all_uncheck" type="image" alt="Tout décocher." src="./_img/all_uncheck.gif" title="Tout décocher." /></th>
				<th>Id</th>
				<th>Zone géo</th>
				<th>Localisation<br />Dénomination</th>
				<th>UAI</th>
				<th>Nom<br />Prénom</th>
				<th>Courriel</th>
				<th class="nu"><q class="ajouter" title="Ajouter un établissement."></q></th>
			</tr>
		</thead>
		<tbody>
			<?php
			// Lister les structures
			$DB_TAB = DB_WEBMESTRE_WEBMESTRE::DB_lister_structures();
			foreach($DB_TAB as $DB_ROW)
			{
				// Afficher une ligne du tableau
				$img = (!is_file(CHEMIN_CONFIG.'blocage_webmestre_'.$DB_ROW['sacoche_base'].'.txt')) ? '<img class="bloquer" src="./_img/etat/acces_oui.png" title="Bloquer cet établissement." />' : '<img class="debloquer" src="./_img/etat/acces_non.png" title="Débloquer cet établissement." />' ;
				echo'<tr id="id_'.$DB_ROW['sacoche_base'].'">';
				echo	'<td class="nu"><a href="#id_0">'.$img.'</a></td>';
				echo	'<td class="nu"><input type="checkbox" name="f_ids" value="'.$DB_ROW['sacoche_base'].'" /></td>';
				echo	'<td class="label">'.$DB_ROW['sacoche_base'].'</td>';
				echo	'<td class="label"><i>'.sprintf("%02u",$DB_ROW['geo_ordre']).'</i>'.html($DB_ROW['geo_nom']).'</td>';
				echo	'<td class="label">'.html($DB_ROW['structure_localisation']).'<br />'.html($DB_ROW['structure_denomination']).'</td>';
				echo	'<td class="label">'.html($DB_ROW['structure_uai']).'</td>';
				echo	'<td class="label">'.html($DB_ROW['structure_contact_nom']).'<br />'.html($DB_ROW['structure_contact_prenom']).'</td>';
				echo	'<td class="label">'.html($DB_ROW['structure_contact_courriel']).'</td>';
				echo	'<td class="nu">';
				echo		'<q class="modifier" title="Modifier cet établissement."></q>';
				echo		'<q class="initialiser_mdp" title="Générer un nouveau mdp d\'un admin."></q>';
				echo		'<q class="supprimer" title="Supprimer cet établissement."></q>';
				echo	'</td>';
				echo'</tr>';
			}
			?>
		</tbody>
	</table>
	<div id="zone_actions" class="p">
		Pour les structures cochées : <input id="listing_ids" name="listing_ids" type="hidden" value="" />
		<button id="bouton_newsletter" type="button" class="mail_ecrire">Écrire un courriel.</button>
		<button id="bouton_stats" type="button" class="stats">Calculer les statistiques.</button>
		<button id="bouton_transfert" type="button" class="fichier_export">Exporter données &amp; bases.</button>
		<button id="bouton_supprimer" type="button" class="supprimer">Supprimer.</button>
		<label id="ajax_supprimer">&nbsp;</label>
	</div>
</form>
