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
$TITRE = "Personnels de direction";
?>

<p><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_administrateur__gestion_directeurs">DOC : Gestion des directeurs</a></span></p>

<form action="#" method="post">
	<table class="form t9 hsort">
		<thead>
			<tr>
				<th>Id. ENT</th>
				<th>Id. GEPI</th>
				<th>Id Sconet</th>
				<th>Référence</th>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Login</th>
				<th>Mot de passe</th>
				<th class="nu"><q class="ajouter" title="Ajouter un directeur."></q></th>
			</tr>
		</thead>
		<tbody>
			<?php
			// Lister les directeurs
			$DB_TAB = DB_STRUCTURE_ADMINISTRATEUR::DB_lister_users('directeur',$only_actifs=true,$with_classe=false);
			foreach($DB_TAB as $DB_ROW)
			{
				// Afficher une ligne du tableau
				echo'<tr id="id_'.$DB_ROW['user_id'].'">';
				echo	'<td>'.html($DB_ROW['user_id_ent']).'</td>';
				echo	'<td>'.html($DB_ROW['user_id_gepi']).'</td>';
				echo	'<td>'.html($DB_ROW['user_sconet_id']).'</td>';
				echo	'<td>'.html($DB_ROW['user_reference']).'</td>';
				echo	'<td>'.html($DB_ROW['user_nom']).'</td>';
				echo	'<td>'.html($DB_ROW['user_prenom']).'</td>';
				echo	'<td>'.html($DB_ROW['user_login']).'</td>';
				echo	'<td class="i">champ crypté</td>';
				echo	'<td class="nu">';
				echo		'<q class="modifier" title="Modifier ce directeur."></q>';
				echo		'<q class="supprimer" title="Enlever ce directeur."></q>';
				echo	'</td>';
				echo'</tr>';
			}
			?>
		</tbody>
	</table>
</form>

<script type="text/javascript">
	var select_login="<?php echo $_SESSION['MODELE_DIRECTEUR']; ?>";
	var mdp_longueur_mini=<?php echo $_SESSION['MDP_LONGUEUR_MINI'] ?>;
</script>
