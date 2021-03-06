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
$TITRE = "Bilan d'items sélectionnés";
?>

<?php
Formulaire::load_choix_memo();
$check_type_individuel = (Formulaire::$tab_choix['type_individuel'])   ? ' checked' : '' ;
$class_form_individuel = (Formulaire::$tab_choix['type_individuel'])   ? 'show'     : 'hide' ;
$check_type_synthese   = (Formulaire::$tab_choix['type_synthese'])     ? ' checked' : '' ;
$class_form_synthese   = (Formulaire::$tab_choix['type_synthese'])     ? 'show'     : 'hide' ;
$check_bilan_MS        = (Formulaire::$tab_choix['aff_bilan_MS'])      ? ' checked' : '' ;
$check_bilan_PA        = (Formulaire::$tab_choix['aff_bilan_PA'])      ? ' checked' : '' ;
$check_conv_sur20      = (Formulaire::$tab_choix['aff_conv_sur20'])    ? ' checked' : '' ;
if(in_array($_SESSION['USER_PROFIL'],array('parent','eleve')))
{
	// Une éventuelle restriction d'accès doit surcharger toute mémorisation antérieure de formulaire
	$check_bilan_MS   = (mb_substr_count($_SESSION['DROIT_BILAN_MOYENNE_SCORE']     ,$_SESSION['USER_PROFIL'])) ? ' checked' : '' ;
	$check_bilan_PA   = (mb_substr_count($_SESSION['DROIT_BILAN_POURCENTAGE_ACQUIS'],$_SESSION['USER_PROFIL'])) ? ' checked' : '' ;
	$check_conv_sur20 = (mb_substr_count($_SESSION['DROIT_BILAN_NOTE_SUR_VINGT']    ,$_SESSION['USER_PROFIL'])) ? ' checked' : '' ;
}
$check_retro_oui       = (Formulaire::$tab_choix['retroactif']=='oui') ? ' checked' : '' ;
$check_retro_non       = (Formulaire::$tab_choix['retroactif']=='non') ? ' checked' : '' ;
$check_aff_coef        = (Formulaire::$tab_choix['aff_coef'])          ? ' checked' : '' ;
$check_aff_socle       = (Formulaire::$tab_choix['aff_socle'])         ? ' checked' : '' ;
$check_aff_lien        = (Formulaire::$tab_choix['aff_lien'])          ? ' checked' : '' ;
$check_aff_domaine     = (Formulaire::$tab_choix['aff_domaine'])       ? ' checked' : '' ;
$check_aff_theme       = (Formulaire::$tab_choix['aff_theme'])         ? ' checked' : '' ;
$tab_groupes  = DB_STRUCTURE_COMMUN::DB_OPT_groupes_professeur($_SESSION['USER_ID']);
$tab_periodes = DB_STRUCTURE_COMMUN::DB_OPT_periodes_etabl();

$select_tri_objet   = Formulaire::afficher_select(Formulaire::$tab_select_tri_objet   , $select_nom='f_tri_objet'   , $option_first='non' , $selection=Formulaire::$tab_choix['tableau_tri_objet'] , $optgroup='non');
$select_tri_mode    = Formulaire::afficher_select(Formulaire::$tab_select_tri_mode    , $select_nom='f_tri_mode'    , $option_first='non' , $selection=Formulaire::$tab_choix['tableau_tri_mode']  , $optgroup='non');
$select_groupe      = Formulaire::afficher_select($tab_groupes                        , $select_nom='f_groupe'      , $option_first='oui' , $selection=false                                       , $optgroup='oui');
$select_periode     = Formulaire::afficher_select($tab_periodes                       , $select_nom='f_periode'     , $option_first='val' , $selection=false                                       , $optgroup='non');
$select_orientation = Formulaire::afficher_select(Formulaire::$tab_select_orientation , $select_nom='f_orientation' , $option_first='non' , $selection=Formulaire::$tab_choix['orientation']       , $optgroup='non');
$select_marge_min   = Formulaire::afficher_select(Formulaire::$tab_select_marge_min   , $select_nom='f_marge_min'   , $option_first='non' , $selection=Formulaire::$tab_choix['marge_min']         , $optgroup='non');
$select_pages_nb    = Formulaire::afficher_select(Formulaire::$tab_select_pages_nb    , $select_nom='f_pages_nb'    , $option_first='non' , $selection=Formulaire::$tab_choix['pages_nb']          , $optgroup='non');
$select_couleur     = Formulaire::afficher_select(Formulaire::$tab_select_couleur     , $select_nom='f_couleur'     , $option_first='non' , $selection=Formulaire::$tab_choix['couleur']           , $optgroup='non');
$select_legende     = Formulaire::afficher_select(Formulaire::$tab_select_legende     , $select_nom='f_legende'     , $option_first='non' , $selection=Formulaire::$tab_choix['legende']           , $optgroup='non');
$select_cases_nb    = Formulaire::afficher_select(Formulaire::$tab_select_cases_nb    , $select_nom='f_cases_nb'    , $option_first='non' , $selection=Formulaire::$tab_choix['cases_nb']          , $optgroup='non');
$select_cases_larg  = Formulaire::afficher_select(Formulaire::$tab_select_cases_size  , $select_nom='f_cases_larg'  , $option_first='non' , $selection=Formulaire::$tab_choix['cases_largeur']     , $optgroup='non');

// Dates par défaut de début et de fin
$annee_debut = (date('n')>8) ? date('Y') : date('Y')-1 ;
$date_debut = '01/09/'.$annee_debut;
$date_fin   = date("d/m/Y");

// Fabrication du tableau javascript "tab_groupe_periode" pour les jointures groupes/périodes
$tab_groupe_periode_js = 'var tab_groupe_periode = new Array();';
if(is_array($tab_groupes))
{
	$tab_id_classe_groupe = array();
	foreach($tab_groupes as $tab_groupe_infos)
	{
		if($tab_groupe_infos['optgroup']!='besoin')
		{
			$tab_id_classe_groupe[] = $tab_groupe_infos['valeur'];
		}
	}
	if(count($tab_id_classe_groupe))
	{
		$tab_memo_groupes = array();
		$DB_TAB = DB_STRUCTURE_COMMUN::DB_lister_jointure_groupe_periode($listing_groupe_id = implode(',',$tab_id_classe_groupe));
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
}
?>

<script type="text/javascript">
	var date_mysql="<?php echo date("Y-m-d") ?>";
	<?php echo $tab_groupe_periode_js ?> 
</script>

<div><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=releves_bilans__releve_items_selection">DOC : Bilan d'items sélectionnés.</a></span></div>

<hr />

<form action="#" method="post" id="form_select"><fieldset>
	<label class="tab">Type de bilan :</label><label for="f_type_individuel"><input type="checkbox" id="f_type_individuel" name="f_type[]" value="individuel"<?php echo $check_type_individuel ?> /> Relevé individuel</label>&nbsp;&nbsp;&nbsp;<label for="f_type_synthese"><input type="checkbox" id="f_type_synthese" name="f_type[]" value="synthese"<?php echo $check_type_synthese ?> /> Synthèse collective</label><br />
	<span id="options_individuel" class="<?php echo $class_form_individuel ?>">
		<label class="tab"><img alt="" src="./_img/bulle_aide.png" title="Pour le relévé individuel, deux lignes de synthèse peuvent être ajoutées.<br />Dans ce cas, une note sur 20 peut aussi être affichée." /> Opt. relevé :</label><label for="f_bilan_MS"><input type="checkbox" id="f_bilan_MS" name="f_bilan_MS" value="1"<?php echo $check_bilan_MS ?> /> Moyenne des scores</label>&nbsp;&nbsp;&nbsp;<label for="f_bilan_PA"><input type="checkbox" id="f_bilan_PA" name="f_bilan_PA" value="1"<?php echo $check_bilan_PA ?> /> Pourcentage d'items acquis</label>&nbsp;&nbsp;&nbsp;<label for="f_conv_sur20"><input type="checkbox" id="f_conv_sur20" name="f_conv_sur20" value="1"<?php echo $check_conv_sur20 ?> /> Proposition de note sur 20</label><br />
	</span>
	<span id="options_synthese" class="<?php echo $class_form_synthese ?>">
		<label class="tab"><img alt="" src="./_img/bulle_aide.png" title="Paramétrage du tableau de synthèse." /> Opt. synthèse :</label><?php echo $select_tri_objet ?> <?php echo $select_tri_mode ?><br />
	</span>
	<p>
		<label class="tab">Items :</label><input id="f_compet_nombre" name="f_compet_nombre" size="10" type="text" value="aucun" readonly /><input id="f_compet_liste" name="f_compet_liste" type="hidden" value="" /><q class="choisir_compet" title="Voir ou choisir les items."></q>
	</p>
	<label class="tab" for="f_groupe">Classe / groupe :</label><?php echo $select_groupe ?><input type="hidden" id="f_groupe_nom" name="f_groupe_nom" value="" /><label id="ajax_maj">&nbsp;</label><br />
	<label class="tab" for="f_eleve"><img alt="" src="./_img/bulle_aide.png" title="Utiliser la touche &laquo;&nbsp;Shift&nbsp;&raquo; pour une sélection multiple contiguë.<br />Utiliser la touche &laquo;&nbsp;Ctrl&nbsp;&raquo; pour une sélection multiple non contiguë." /> Élève(s) :</label><select id="f_eleve" name="f_eleve[]" multiple size="9"><option></option></select>
	<p id="zone_periodes" class="hide">
		<label class="tab" for="f_periode"><img alt="" src="./_img/bulle_aide.png" title="Les items pris en compte sont ceux qui sont évalués<br />au moins une fois sur cette période." /> Période :</label><?php echo $select_periode ?>
		<span id="dates_perso" class="show">
			du <input id="f_date_debut" name="f_date_debut" size="9" type="text" value="<?php echo $date_debut ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
			au <input id="f_date_fin" name="f_date_fin" size="9" type="text" value="<?php echo $date_fin ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
		</span><br />
		<span class="radio"><img alt="" src="./_img/bulle_aide.png" title="Le bilan peut être établi uniquement sur la période considérée<br />ou en tenant compte d'évaluations antérieures des items concernés." /> Prise en compte des évaluations antérieures :</span><label for="f_retro_oui"><input type="radio" id="f_retro_oui" name="f_retroactif" value="oui"<?php echo $check_retro_oui ?> /> oui</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="f_retro_non"><input type="radio" id="f_retro_non" name="f_retroactif" value="non"<?php echo $check_retro_non ?> /> non</label>
	</p>
	<div class="toggle">
		<span class="tab"></span><a href="#" class="puce_plus toggle">Afficher plus d'options</a>
	</div>
	<div class="toggle hide">
		<span class="tab"></span><a href="#" class="puce_moins toggle">Afficher moins d'options</a><br />
		<label class="tab"><img alt="" src="./_img/bulle_aide.png" title="Pour le relévé individuel, les paramètres des items peuvent être affichés." /> Indications :</label><label for="f_coef"><input type="checkbox" id="f_coef" name="f_coef" value="1"<?php echo $check_aff_coef ?> /> Coefficients</label>&nbsp;&nbsp;&nbsp;<label for="f_socle"><input type="checkbox" id="f_socle" name="f_socle" value="1"<?php echo $check_aff_socle ?> /> Appartenance au socle</label>&nbsp;&nbsp;&nbsp;<label for="f_lien"><input type="checkbox" id="f_lien" name="f_lien" value="1"<?php echo $check_aff_lien ?> /> Liens (ressources pour travailler)</label>&nbsp;&nbsp;&nbsp;<label for="f_domaine"><input type="checkbox" id="f_domaine" name="f_domaine" value="1"<?php echo $check_aff_domaine ?> /> Domaines</label>&nbsp;&nbsp;&nbsp;<label for="f_theme"><input type="checkbox" id="f_theme" name="f_theme" value="1"<?php echo $check_aff_theme ?> /> Thèmes</label><br />
		<label class="tab"><img alt="" src="./_img/bulle_aide.png" title="Pour le format pdf." /> Impression :</label><?php echo $select_orientation ?> <?php echo $select_couleur ?> <?php echo $select_legende ?> <?php echo $select_marge_min ?> <?php echo $select_pages_nb ?><br />
		<label class="tab">Évaluations :</label><?php echo $select_cases_nb ?> de largeur <?php echo $select_cases_larg ?>
	</div>
	<p><span class="tab"></span><button id="bouton_valider" type="submit" class="generer">Générer.</button><label id="ajax_msg">&nbsp;</label></p>
</fieldset></form>

<form action="#" method="post" id="zone_compet" class="arbre_dynamique arbre_check hide">
	<div>Tout déployer / contracter : <a href="m1" class="all_extend"><img alt="m1" src="./_img/deploy_m1.gif" /></a> <a href="m2" class="all_extend"><img alt="m2" src="./_img/deploy_m2.gif" /></a> <a href="n1" class="all_extend"><img alt="n1" src="./_img/deploy_n1.gif" /></a> <a href="n2" class="all_extend"><img alt="n2" src="./_img/deploy_n2.gif" /></a> <a href="n3" class="all_extend"><img alt="n3" src="./_img/deploy_n3.gif" /></a></div>
	<p>Cocher ci-dessous (<span class="astuce">cliquer sur un intitulé pour déployer son contenu</span>) :</p>
	<?php
	// Affichage de la liste des items pour toutes les matières d'un professeur, sur tous les niveaux
	$DB_TAB = DB_STRUCTURE_COMMUN::DB_recuperer_arborescence($_SESSION['USER_ID'],$matiere_id=0,$niveau_id=0,$only_socle=false,$only_item=false,$socle_nom=false);
	echo afficher_arborescence_matiere_from_SQL($DB_TAB,$dynamique=true,$reference=true,$aff_coef=false,$aff_cart=false,$aff_socle='texte',$aff_lien=false,$aff_input=true);
	?>
	<p><span class="tab"></span><button id="valider_compet" type="button" class="valider">Valider la sélection</button>&nbsp;&nbsp;&nbsp;<button id="annuler_compet" type="button" class="annuler">Annuler / Retour</button></p>
</form>

<div id="bilan"></div>
