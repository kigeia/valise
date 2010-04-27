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
$TITRE = "Consulter des référentiels d'établissements";
?>

<?php
// Fabrication des éléments select du formulaire
$select_matiere = afficher_select(DB_OPT_matieres_communes($_SESSION['MATIERES'])                 , $select_nom='f_matiere' , $option_first='oui' , $selection=false , $optgroup='non');
$select_niveau  = afficher_select(DB_OPT_niveaux_etabl($_SESSION['NIVEAUX'],$_SESSION['PALIERS']) , $select_nom='f_niveau'  , $option_first='oui' , $selection=false , $optgroup='non');
?>

<script type="text/javascript">
	var id_matiere_transversale    = "<?php echo ID_MATIERE_TRANSVERSALE ?>";
	var listing_id_niveaux_paliers = "<?php echo LISTING_ID_NIVEAUX_PALIERS ?>";
</script>

<form id="form_select" action="">
	<fieldset>
		<label class="tab" for="f_matiere">Matière <img alt="" src="./_img/bulle_aide.png" title="Seules les matières cochées par l'administrateur apparaissent." /> :</label><?php echo $select_matiere ?><br />
		<label class="tab" for="f_niveau">Niveau <img alt="" src="./_img/bulle_aide.png" title="Seules les niveaux cochés par l'administrateur apparaissent." /> :</label><?php echo $select_niveau ?><br />
		<span class="tab"></span><input id="f_submit" type="button" value="Valider." /><label id="ajax_msg">&nbsp;</label>
	</fieldset>
</form>

<hr />

<div id="choisir_referentiel" class="hide">
	<h2>Liste des référentiels disponibles - <span id="mat_niv"></span></h2>
	<ul class="donneur link">
	</ul>
</div>

<div id="zone_compet" class="hide">
</div>


