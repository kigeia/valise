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
$TITRE = "Évaluer une classe ou un groupe";
$VERSION_JS_FILE += 20;
//ajout du groupe en provenance de gepi
if (isset($_POST['gepi_current_group'])) {
	$gepi_current_group = json_decode($_POST['gepi_current_group'],true);
	$sacoche_groupe_id = importer_groupe_gepi($_POST['period_num'],json_decode($_POST['gepi_current_group'],true));
}
?>

<?php
// Élément de formulaire "f_aff_classe" pour le choix des élèves (liste des classes / groupes / besoins) du professeur, enregistré dans une variable javascript pour utilisation suivant le besoin, et utilisé pour un tri initial
// Fabrication de tableaux javascript "tab_niveau" et "tab_groupe" indiquant le niveau et le nom d'un groupe
$select_eleve  = '<option value=""></option>';
$tab_niveau_js = 'var tab_niveau = new Array();';
$tab_groupe_js = 'var tab_groupe = new Array();';
$tab_id_classe_groupe = array();
$DB_TAB = DB_STRUCTURE_lister_groupes_professeur($_SESSION['USER_ID']);
$tab_options = array('classe'=>'','groupe'=>'','besoin'=>'');
foreach($DB_TAB as $DB_ROW)
{
	$groupe = strtoupper($DB_ROW['groupe_type']{0}).$DB_ROW['groupe_id'];
	$tab_options[$DB_ROW['groupe_type']] .= '<option value="'.$groupe.'">'.html($DB_ROW['groupe_nom']).'</option>';
	$tab_niveau_js .= 'tab_niveau["'.$groupe.'"]="'.sprintf("%02u",$DB_ROW['niveau_ordre']).'";';
	$tab_groupe_js .= 'tab_groupe["'.$groupe.'"]="'.html($DB_ROW['groupe_nom']).'";';
	if($DB_ROW['groupe_type']!='besoin')
	{
		$tab_id_classe_groupe[] = $DB_ROW['groupe_id'];
	}
}
foreach($tab_options as $type => $contenu)
{
	if($contenu)
	{
		$select_eleve .= '<optgroup label="'.ucwords($type).'s">'.$contenu.'</optgroup>';
	}
}

// Élément de formulaire "f_aff_periode" pour le choix d'une période
$select_periode = afficher_select(DB_STRUCTURE_OPT_periodes_etabl() , $select_nom='f_aff_periode' , $option_first='val' , $selection=false , $optgroup='non');
// Dates par défaut de début et de fin
$annee_debut = (date('n')>8) ? date('Y') : date('Y')-1 ;
$date_debut  = '01/09/'.$annee_debut;
$date_fin    = date("d/m/Y");

// Fabrication du tableau javascript "tab_groupe_periode" pour les jointures groupes/périodes
$tab_groupe_periode_js = 'var tab_groupe_periode = new Array();';
if(count($tab_id_classe_groupe))
{
	$tab_memo_groupes = array();
	$DB_TAB = DB_STRUCTURE_lister_jointure_groupe_periode($listing_groupe_id = implode(',',$tab_id_classe_groupe));
	foreach($DB_TAB as $DB_ROW)
	{
		if(!isset($tab_memo_groupes[$DB_ROW['groupe_id']]))
		{
			$tab_memo_groupes[$DB_ROW['groupe_id']] = true;
			$tab_groupe_periode_js .= 'tab_groupe_periode['.$DB_ROW['groupe_id'].'] = new Array();';
		}
		$tab_groupe_periode_js .= 'tab_groupe_periode['.$DB_ROW['groupe_id'].']['.$DB_ROW['periode_id'].']="'.$DB_ROW['jointure_date_debut'].'_'.$DB_ROW['jointure_date_fin'].'";';
	}
}
?>

<script type="text/javascript">
	// <![CDATA[
	var select_groupe="<?php echo str_replace('"','\"',$select_eleve); ?>";
	// ]]>
	var input_date="<?php echo date("d/m/Y") ?>";
	var date_mysql="<?php echo date("Y-m-d") ?>";
	<?php echo $tab_niveau_js ?> 
	<?php echo $tab_groupe_js ?> 
	<?php echo $tab_groupe_periode_js ?> 
</script>

<ul class="puce">
	<li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_professeur__evaluations_gestion">DOC : Gestion des évaluations.</a></span></li>
	<li><span class="astuce">Choisir des evaluations existantes à afficher, ou cliquer sur le "<span style="background:transparent url(./_img/sprite10.png) 0 0 no-repeat;background-position:-20px 0;width:16px;height:16px;display:inline-block;vertical-align:middle"></span>" pour créer une nouvelle évaluation.</span></li>
</ul>

<hr />

<form action="" id="form0"><fieldset>
	<input type="hidden" name="f_devoir_id" id="f_devoir_id" value=""/>
	<?php if (!isset($_POST['gepi_cn_devoirs_row'])) { ?>
	<label class="tab" for="f_aff_classe">Classe / groupe :</label><select id="f_aff_classe" name="f_aff_classe"><?php echo $select_eleve ?></select>
	<div id="zone_periodes" class="hide">
		<label class="tab" for="f_aff_periode">Période :</label><?php echo $select_periode ?>
		<span id="dates_perso" class="show">
			du <input id="f_date_debut" name="f_date_debut" size="9" type="text" value="<?php echo $date_debut ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
			au <input id="f_date_fin" name="f_date_fin" size="9" type="text" value="<?php echo $date_fin ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
		</span><br />
		<span class="tab"></span><input type="hidden" name="f_action" value="Afficher_evaluations" /><button id="actualiser" type="submit"><img alt="" src="./_img/bouton/actualiser.png" /> Actualiser l'affichage.</button><label id="ajax_msg0">&nbsp;</label>
	</div>
	<?php } else { ?>
		<!-- ne pas afficher le formulaire -->
	<?php } ?>
</fieldset></form>

<form action="" id="form1" name="form1">
	<hr />
	<p id="p_alerte" class="danger hide">Une évaluation dont la saisie a commencé ne devrait pas voir ses items modifiés.<br />En particulier, retirer des items d'une évaluation efface les scores correspondants qui sont saisis !</p>
	<table class="form">
		<thead>
			<tr>
				<th>Date devoir</th>
				<th>Date visible</th>
				<th>Classe / Groupe</th>
				<th>Description</th>
				<th>Items</th>
				<th class="nu"><q class="ajouter" title="Ajouter une évaluation."></q></th>
			</tr>
		</thead>
		<tbody>
			<?php if (!isset($_POST['gepi_cn_devoirs_array'])) { ?>
				<tr><td class="nu" colspan="6"></td></tr>
			<?php } else { 
				$gepi_cn_devoirs_array = json_decode($_POST['gepi_cn_devoirs_array'], true);
				//on va rechercher si le devoir existe déjà
				$DB_ROW_DEVOIR = DB_STRUCTURE_recuperer_devoir_gepi($gepi_cn_devoirs_array['id']);
				if ($DB_ROW_DEVOIR) {		
					// Formater la date et la référence de l'évaluation
					$date_affich = convert_date_mysql_to_french($DB_ROW_DEVOIR['devoir_date']);
					$date_visible = ($DB_ROW_DEVOIR['devoir_date']==$DB_ROW_DEVOIR['devoir_visible_date']) ? 'identique' : convert_date_mysql_to_french($DB_ROW_DEVOIR['devoir_visible_date']);
					$ref = $DB_ROW_DEVOIR['devoir_id'].'_'.strtoupper($DB_ROW_DEVOIR['groupe_type']{0}).$DB_ROW_DEVOIR['groupe_id'];
					$s = ($DB_ROW_DEVOIR['items_nombre']>1) ? 's' : '';
					// Afficher une ligne du tableau
					// Afficher une ligne du tableau
					echo'<tr id='.$DB_ROW_DEVOIR['devoir_id'].'>';
					echo	'<td><i>'.html($DB_ROW_DEVOIR['devoir_date']).'</i>'.html($date_affich).'</td>';
					echo	'<td>'.html($date_visible).'</td>';
					echo	'<td>'.html($DB_ROW_DEVOIR['groupe_nom']).'</td>';
					echo	'<td>'.html($DB_ROW_DEVOIR['devoir_info']).'</td>';
					echo	'<td lang="'.html($DB_ROW_DEVOIR['items_listing']).'">'.html($DB_ROW_DEVOIR['items_nombre']).' item'.$s.'</td>';
					echo	'<td class="nu" lang="'.$ref.'">';
					echo		'<q class="modifier" title="Modifier cette évaluation (date, description, ...)."></q>';
					echo		'<q class="ordonner" title="Réordonner les items de cette évaluation."></q>';
					echo		'<q class="dupliquer" title="Dupliquer cette évaluation."></q>';
					echo		'<q class="supprimer" title="Supprimer cette évaluation."></q>';
					echo		'<q class="imprimer" title="Imprimer un cartouche pour cette évaluation."></q>';
					echo		'<q class="saisir" title="Saisir les acquisitions des élèves à cette évaluation."></q>';
					echo		'<q class="voir" title="Voir les acquisitions des élèves à cette évaluation."></q>';
					echo		'<q class="voir_repart" title="Voir les répartitions des élèves à cette évaluation."></q>';
					if ($DB_ROW_DEVOIR['gepi_cn_devoirs_id'] != 0) {
						$DB_TAB = DB_STRUCTURE_lister_parametres('"gepi_url","gepi_rne","integration_gepi"');
						foreach($DB_TAB as $DB_ROW)
						{
							${$DB_ROW['parametre_nom']} = $DB_ROW['parametre_valeur'];
						}
						if ($integration_gepi == 'yes' && $gepi_url != '' && $gepi_url != 'http://') {
							echo '<input type="hidden" name="gepi_devoir_url" value="'.$gepi_url.'/cahier_notes/index.php?id_devoir='.$DB_ROW_DEVOIR['gepi_cn_devoirs_id'].'&rne='.$gepi_rne.'"/>'; 
							echo '<q class="retourner" title="Retourner sur gepi."></q>';
			
							//on va construire un tableau des résultats de l'évaluation en pourcentage de réussite
							// on passe en revue les évaluations disponibles, et on retient les notes exploitables
							$output_array = array();
							$tab_modele_bon = array('RR','R','V','VV');	// les notes prises en compte dans le calcul du score
							//print_r($_SESSION['CALCUL_VALEUR']);die;
							$DB_TAB = DB_STRUCTURE_lister_saisies_devoir($DB_ROW_DEVOIR['devoir_id'],$with_REQ=true);
							foreach($DB_TAB as $DB_ROW_SASIE)
							{
								$id_gepi = $DB_ROW_SASIE['user_id_gepi'];
								if (!isset($output_array[$id_gepi])) {
									$output_array[$id_gepi] = 0;
								}
								if(in_array($DB_ROW_SASIE['saisie_note'],$tab_modele_bon)) {
									$output_array[$id_gepi] += ($_SESSION['CALCUL_VALEUR'][$DB_ROW_SASIE['saisie_note']]/$DB_ROW_DEVOIR['items_nombre']);
								}
							}
							echo '<q class="envoyer" title="Importer les notes sur gepi."></q>';
			
							echo '<input type="hidden" name="gepi_retour_note_url" id="gepi_retour_note_url" value="'.$gepi_url.'/cahier_notes/saisie_notes.php">';
							echo '<input type="hidden" name="import_sacoche" value="yes"/>';
							echo '<input type="hidden" name="is_posted" value="yes"/>';
							echo '<input type="hidden" name="rne" value="'.$gepi_rne.'"/>';
							echo '<input type="hidden" name="id_devoir" value="'.$DB_ROW_DEVOIR['gepi_cn_devoirs_id'].'"/>';
							$i=0;
							foreach($output_array as $id => $pourcent) {
								echo '<input name="log_eleve['.$i.']" type="hidden" value="'.$id.'"/>';
								echo '<input name="note_eleve['.$i.']" type="hidden" value="'.$pourcent.'"/>';
								echo '<input name="comment_eleve['.$i.']" type="hidden" value=""/>';
								$i = $i+1;
							}
							echo '<input type="hidden" name="indice_max_log_eleve" value="'.count($output_array).'"/>';
							echo '</form>';
						}
					}
					echo	'</td>';
					echo'</tr>';
				} else {
					echo'<tr>';
					//on va afficher un nouveau devoir
					echo '<input id="f_gepi_cn_devoirs_id" name="f_gepi_cn_devoirs_id" type="hidden" value="'.$gepi_cn_devoirs_array['id'].'" />';
					$date = new DateTime($gepi_cn_devoirs_array['date']);
					echo  '<td><input id="f_date" name="f_date" size="9" type="text" value="'.$date->format('d/m/Y').'" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q></td>';
					$date_visible = new DateTime($gepi_cn_devoirs_array['date_ele_resp']);
					echo  '<td><input id="box_date" type="checkbox" checked style="vertical-align:-3px" /> <span style="vertical-align:-2px">identique</span><span class="hide"><input id="f_date_visible" name="f_date_visible" size="9" type="text" value="'.$date_visible->format('d/m/Y').'" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q></span></td>';
					echo '<input id="f_groupe" name="f_groupe" type="hidden" value="G'.$sacoche_groupe_id.'" />';
					echo  '<td>'.$gepi_current_group['classlist_string'].' '.$gepi_current_group['name'].'</td>';
					echo  '<td><input id="f_info" name="f_info" size="20" type="text" value="'.$gepi_cn_devoirs_array['nom_court'].'" /></td>';
					echo  '<td><input id="f_compet_nombre" name="f_compet_nombre" size="10" type="text" value="0 item" readonly /><input id="f_compet_liste" name="f_compet_liste" type="hidden" value="" /><q class="choisir_compet" title="Voir ou choisir les items."></q></td>';
					echo  '<td class="nu"><input id="f_action" name="f_action" type="hidden" value="ajouter" /><q class="valider" title="Valider l\'ajout de cette évaluation."></q><q class="annuler" title="Annuler l\'ajout de cette évaluation."></q> <label id="ajax_msg">&nbsp;</label></td>';
					echo '</tr>';
				}
			 } ?>
		</tbody>
	</table>
</form>

<form action="" id="zone_compet" class="hide">
	<p>
		<span class="tab"></span><button id="valider_compet" type="button"><img alt="" src="./_img/bouton/valider.png" /> Valider ce choix</button>&nbsp;&nbsp;&nbsp;<button id="annuler_compet" type="button"><img alt="" src="./_img/bouton/annuler.png" /> Annuler / Retour</button>
	</p>
	<?php
	// Affichage de la liste des items pour toutes les matières d'un professeur, sur tous les niveaux
	$DB_TAB = DB_STRUCTURE_recuperer_arborescence($_SESSION['USER_ID'],$matiere_id=0,$niveau_id=0,$only_socle=false,$only_item=false,$socle_nom=false);
	echo afficher_arborescence_matiere_from_SQL($DB_TAB,$dynamique=true,$reference=true,$aff_coef=false,$aff_cart=false,$aff_socle='texte',$aff_lien=false,$aff_input=true);
	?>
</form>

<form action="" id="zone_ordonner" class="hide">
	<p class="hc"><b id="titre_ordonner"></b><br /><label id="msg_ordonner"></label></p>
	<div id="div_ordonner">
	</div>
</form>

<!-- Sans "javascript:return false" une soumission incontrôlée s'effectue quand on presse "entrée" dans le cas d'un seul élève évalué sur un seul item. -->
<form action="javascript:return false" id="zone_saisir" class="hide">
	<p class="hc"><b id="titre_saisir"></b><br /><label id="msg_saisir"></label></p>
	<table id="table_saisir" class="scor_eval">
		<tbody><tr><td></td></tr></tbody>
	</table>
	<div id="td_souris_container"><div class="td_souris">
		<img alt="RR" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/RR.gif" /><img alt="ABS" src="./_img/note/commun/h/ABS.gif" /><br />
		<img alt="R" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/R.gif" /><img alt="NN" src="./_img/note/commun/h/NN.gif" /><br />
		<img alt="V" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/V.gif" /><img alt="DISP" src="./_img/note/commun/h/DISP.gif" /><br />
		<img alt="VV" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/VV.gif" /><img alt="X" src="./_img/note/commun/h/X.gif" />
	</div></div>
	<p class="ti" id="aide_en_ligne"><button id="report_note" type="button">Reporter</button> le code 
		<label for="f_defaut_VV"><input type="radio" id="f_defaut_VV" name="f_defaut" value="VV" checked /><img alt="VV" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/VV.gif" /></label>
		<label for="f_defaut_V"><input type="radio" id="f_defaut_V" name="f_defaut" value="V" /><img alt="V" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/V.gif" /></label>
		<label for="f_defaut_R"><input type="radio" id="f_defaut_R" name="f_defaut" value="R" /><img alt="R" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/R.gif" /></label>
		<label for="f_defaut_RR"><input type="radio" id="f_defaut_RR" name="f_defaut" value="RR" /><img alt="RR" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/RR.gif" /></label>
		<label for="f_defaut_ABS"><input type="radio" id="f_defaut_ABS" name="f_defaut" value="ABS" /><img alt="ABS" src="./_img/note/commun/h/ABS.gif" /></label>
		<label for="f_defaut_NN"><input type="radio" id="f_defaut_NN" name="f_defaut" value="NN" /><img alt="NN" src="./_img/note/commun/h/NN.gif" /></label>
		<label for="f_defaut_DISP"><input type="radio" id="f_defaut_DISP" name="f_defaut" value="DISP" /><img alt="DISP" src="./_img/note/commun/h/DISP.gif" /></label>
	dans toutes les cellules vides.<label id="msg_report">&nbsp;</label></p>
	<div>
		<a lang="zone_saisir_deport" href="#"><img src="./_img/toggle_plus.gif" alt="" title="Voir / masquer la saisie déportée." class="toggle" /></a> Saisie déportée
		<div id="zone_saisir_deport" class="hide">
			<input type="hidden" name="filename" id="filename" value="<?php echo './__tmp/export/saisie_'.$_SESSION['BASE'].'_'.$_SESSION['USER_ID'].'_'; ?>" />
			<span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_professeur__evaluations_saisie_deportee">DOC : Saisie déportée.</a></span>
			<ul class="puce">
				<li><a id="export_file1" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Récupérer un fichier vierge pour une saisie déportée (format <em>csv</em>).</a></li>
				<li><a id="export_file4" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Imprimer un tableau vierge utilisable pour un report manuel des notes (format <em>pdf</em>).</a></li>
				<li><button id="import_file" type="button"><img alt="" src="./_img/bouton/fichier_import.png" /> Envoyer un fichier de notes complété (format <em>csv</em>).</button><label id="msg_import">&nbsp;</label></li>
			</ul>
		</div>
	</div>
</form>

<div id="zone_voir" class="hide">
	<p class="hc"><b id="titre_voir"></b><br /><label id="msg_voir"></label></p>
	<table id="table_voir" class="scor_eval">
		<tbody><tr><td></td></tr></tbody>
	</table>
	<p>
		<a lang="zone_voir_deport" href="#"><img src="./_img/toggle_plus.gif" alt="" title="Voir / masquer la saisie déportée." class="toggle" /></a> Saisie déportée &amp; archivage
		<div id="zone_voir_deport" class="hide">
			<span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_professeur__evaluations_saisie_deportee">DOC : Saisie déportée.</a></span>
			<ul class="puce">
				<li><a id="export_file2" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Récupérer un fichier des scores pour une saisie déportée (format <em>csv</em>).</a></li>
				<li><a id="export_file3" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Imprimer un tableau vierge utilisable pour un report manuel des notes (format <em>pdf</em>).</a></li>
				<li><a id="export_file5" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Archiver / Imprimer le tableau avec les scores (format <em>pdf</em>).</a></li>
			</ul>
		</div>
	</p>
</div>

<div id="zone_voir_repart" class="hide">
	<p class="hc"><b id="titre_voir_repart"></b><br /><label id="msg_voir_repart"></label></p>
	<table id="table_voir_repart1" class="scor_eval">
		<tbody><tr><td></td></tr></tbody>
	</table>
	<p />
	<ul class="puce">
		<li><a id="export_file6" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Archiver / Imprimer le tableau avec la répartition quantitative des scores (format <em>pdf</em>).</a></li>
	</ul>
	<p />
	<table id="table_voir_repart2" class="scor_eval">
		<tbody><tr><td></td></tr></tbody>
	</table>
	<p />
	<ul class="puce">
		<li><a id="export_file7" class="lien_ext" href=""><img alt="" src="./_img/bouton/fichier_export.png" /> Archiver / Imprimer le tableau avec la répartition nominative des scores (format <em>pdf</em>).</a></li>
	</ul>
	<p />
</div>

<?php
// Fabrication des éléments select du formulaire
$tab_cookie = load_cookie_select('cartouche');
$select_cart_contenu = afficher_select($tab_select_cart_contenu , $select_nom='f_contenu'     , $option_first='non' , $selection=$tab_cookie['cart_contenu'] , $optgroup='non');
$select_cart_detail  = afficher_select($tab_select_cart_detail  , $select_nom='f_detail'      , $option_first='non' , $selection=$tab_cookie['cart_detail']  , $optgroup='non');
$select_orientation  = afficher_select($tab_select_orientation  , $select_nom='f_orientation' , $option_first='non' , $selection=$tab_cookie['orientation']  , $optgroup='non');
$select_couleur      = afficher_select($tab_select_couleur      , $select_nom='f_couleur'     , $option_first='non' , $selection=$tab_cookie['couleur']      , $optgroup='non');
$select_marge_min    = afficher_select($tab_select_marge_min    , $select_nom='f_marge_min'   , $option_first='non' , $selection=$tab_cookie['marge_min']    , $optgroup='non');
?>

<form action="" id="zone_imprimer" class="hide"><fieldset>
	<p class="hc"><b id="titre_imprimer"></b><br /><button id="fermer_zone_imprimer" type="button"><img alt="" src="./_img/bouton/retourner.png" /> Retour</button></p>
	<label class="tab" for="f_contenu">Remplissage :</label><?php echo $select_cart_contenu ?><br />
	<label class="tab" for="f_detail">Détail :</label><?php echo $select_cart_detail ?><br />
	<div class="toggle">
		<span class="tab"></span><a href="#" class="puce_plus toggle">Afficher plus d'options</a>
	</div>
	<div class="toggle hide">
		<span class="tab"></span><a href="#" class="puce_moins toggle">Afficher moins d'options</a><br />
		<label class="tab">Orientation :</label><?php echo $select_orientation ?> <?php echo $select_couleur ?> <?php echo $select_marge_min ?><br />
		<label class="tab">Restriction :</label><input type="checkbox" id="f_restriction_req" name="f_restriction_req" value="1" /> <label for="f_restriction_req">Uniquement les items ayant fait l'objet d'une demande d'évaluation (ou dont une note est saisie).</label>
	</div>
	<span class="tab"></span><button id="f_submit_imprimer" type="button" value="'.$ref.'"><img alt="" src="./_img/bouton/valider.png" /> Générer le cartouche</button><label id="msg_imprimer">&nbsp;</label>
	<p id="zone_imprimer_retour"></p>
</fieldset></form>
