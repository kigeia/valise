<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2009
 * 
 * ****************************************************************************************************
 * SACoche [http://competences.sesamath.net] - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath [http://www.sesamath.net]
 * Distribution sous licence libre prévue pour l'été 2010.
 * ****************************************************************************************************
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
$TITRE = "Affecter les périodes aux classes &amp; groupes";
?>

<?php
// Fabrication des éléments select du formulaire
$select_periodes        = afficher_select(DB_OPT_periodes_etabl($_SESSION['STRUCTURE_ID'])        , $select_nom=false , $option_first='non' , $selection=false , $optgroup='non');
$select_classes_groupes = afficher_select(DB_OPT_classes_groupes_etabl($_SESSION['STRUCTURE_ID']) , $select_nom=false , $option_first='non' , $selection=false , $optgroup='oui');
?>

<p class="hc">
	<span class="manuel"><a class="pop_up" href="./aide.php?fichier=gestion_periodes">DOC : Gestion des périodes</a></span>
</p>

<hr />

<form action="">
	<table><tr>
		<td class="nu" style="width:25em">
			<b>Liste des périodes :</b><br />
			<select id="select_periodes" name="select_periodes[]" multiple="multiple" size="10" class="t8"><?php echo $select_periodes; ?></select>
		</td>
		<td class="nu" style="width:20em">
			<b>Liste des classes &amp; groupes :</b><br />
			<select id="select_classes_groupes" name="select_classes_groupes[]" multiple="multiple" size="10" class="t8"><?php echo $select_classes_groupes; ?></select>
		</td>
		<td class="nu" style="width:25em">
			<p><span class="astuce">Utiliser "<i>Shift + clic</i>" ou "<i>Ctrl + clic</i>"<br />pour une sélection multiple.</span></p>
			du <input id="f_date_debut" name="f_date_debut" size="9" type="text" value="<?php echo date("d/m/Y") ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q><br />
			au <input id="f_date_fin" name="f_date_fin" size="9" type="text" value="<?php echo date("d/m/Y") ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q><br />
			<input id="ajouter" type="button" value="Ajouter" /> ces associations.<p />
			<input id="retirer" type="button" value="Retirer" /> ces associations.
			<p><label id="ajax_msg">&nbsp;</label></p>
		</td>
	</tr></table>
</form>

<div id="bilan">
</div>
