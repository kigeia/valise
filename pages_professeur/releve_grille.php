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
$TITRE = "Grilles de compétences sur un niveau";
?>

<?php
// Fabrication des éléments select du formulaire
$tab_cookie = load_cookie_select($_SESSION['STRUCTURE_ID'],$_SESSION['USER_ID']);
$select_matiere     = afficher_select(DB_OPT_matieres_professeur($_SESSION['STRUCTURE_ID'],$_SESSION['USER_ID']) , $select_nom='f_matiere'     , $option_first='non' , $selection=false                        , $optgroup='non');
$select_niveau      = afficher_select(DB_OPT_niveaux_etabl($_SESSION['NIVEAUX'],$_SESSION['PALIERS'])            , $select_nom='f_niveau'      , $option_first='oui' , $selection=false                        , $optgroup='non');
$select_groupe      = afficher_select(DB_OPT_groupes_professeur($_SESSION['STRUCTURE_ID'],$_SESSION['USER_ID'])  , $select_nom='f_groupe'      , $option_first='val' , $selection=false                        , $optgroup='oui');
$select_orientation = afficher_select($tab_select_orientation                                                    , $select_nom='f_orientation' , $option_first='non' , $selection=$tab_cookie['orientation']   , $optgroup='non');
$select_marge_min   = afficher_select($tab_select_marge_min                                                      , $select_nom='f_marge_min'   , $option_first='non' , $selection=$tab_cookie['marge_min']     , $optgroup='non');
$select_couleur     = afficher_select($tab_select_couleur                                                        , $select_nom='f_couleur'     , $option_first='non' , $selection=$tab_cookie['couleur']       , $optgroup='non');
$select_cases_nb    = afficher_select($tab_select_cases_nb                                                       , $select_nom='f_cases_nb'    , $option_first='non' , $selection=$tab_cookie['cases_nb']      , $optgroup='non');
$select_cases_larg  = afficher_select($tab_select_cases_size                                                     , $select_nom='f_cases_larg'  , $option_first='non' , $selection=$tab_cookie['cases_largeur'] , $optgroup='non');
$select_cases_haut  = afficher_select($tab_select_cases_size                                                     , $select_nom='f_cases_haut'  , $option_first='non' , $selection=$tab_cookie['cases_hauteur'] , $optgroup='non');
?>

<div class="hc"><span class="manuel"><a class="pop_up" href="./aide.php?fichier=releve_grille_niveau">DOC : Grilles de compétences sur un niveau.</a></span></div>

<form id="form_select" action=""><fieldset>
	<label class="tab" for="f_matiere">Matière :</label><?php echo $select_matiere ?><input type="hidden" id="f_matiere_nom" name="f_matiere_nom" value="" /><br />
	<label class="tab" for="f_niveau">Niveau :</label><?php echo $select_niveau ?><input type="hidden" id="f_niveau_nom" name="f_niveau_nom" value="" /><p />
	<label class="tab" for="f_options">Affichage :</label><input type="checkbox" id="f_coef" name="f_coef" value="1" /> <label for="f_coef">Coefficients</label>&nbsp;&nbsp;&nbsp;<input type="checkbox" id="f_socle" name="f_socle" value="1" /> <label for="f_socle">Socle</label>&nbsp;&nbsp;&nbsp;<input type="checkbox" id="f_lien" name="f_lien" value="1" /> <label for="f_lien">Liens de remédiation</label><p />
	<label class="tab" for="f_groupe">Élève(s) :</label><?php echo $select_groupe ?><label id="ajax_maj">&nbsp;</label><br />
	<span id="option_groupe" class="hide">
		<label class="tab" for="f_remplissage">Remplissage :</label><select id="f_remplissage" name="f_remplissage"><option value="vide">fiche vierge de tout résultat</option><option value="plein">fiche avec les notes des dernières évaluations</option></select><br />
	</span>
	<span class="tab"></span><select id="f_eleve" name="f_eleve[]" multiple="multiple" size="9"><option></option></select><input type="hidden" id="eleves" name="eleves" value="" /><p />
	<div class="toggle">
		<span class="tab"></span><a href="#" class="puce_plus toggle">Afficher plus d'options</a>
	</div>
	<div class="toggle hide">
		<span class="tab"></span><a href="#" class="puce_moins toggle">Afficher moins d'options</a><br />
		<label class="tab" for="f_orientation">Orientation :</label><?php echo $select_orientation ?> en <?php echo $select_couleur ?> avec marges minimales de </label><?php echo $select_marge_min ?><br />
		<label class="tab" for="f_cases_nb">Évaluations :</label><?php echo $select_cases_nb ?> de largeur <?php echo $select_cases_larg ?> et de hauteur <?php echo $select_cases_haut ?><p />
	</div>
	<span class="tab"></span><input id="f_submit" type="submit" value="Valider." /><label id="ajax_msg">&nbsp;</label><br />
</fieldset></form>

<hr />

<div id="bilan">
</div>

