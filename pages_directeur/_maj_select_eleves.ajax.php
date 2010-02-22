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
 * Mettre à jour l'élément de formulaire "f_eleve" et le renvoyer en HTML
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if($_SESSION['STRUCTURE_ID']==ID_DEMO) {}

$groupe = (isset($_POST['f_groupe'])) ? clean_entier($_POST['f_groupe']) : 0;
$type   = (isset($_POST['f_type']))   ? clean_texte($_POST['f_type'])    : '';
$statut = (isset($_POST['f_statut'])) ? clean_entier($_POST['f_statut']) : 0;

$tab_types = array('Classes'=>'classe' , 'Groupes'=>'groupe' , 'Besoins'=>'groupe');

if( (!$groupe) || (!isset($tab_types[$type])) )
{
	exit('Erreur avec les données transmises !');
}

$type = $tab_types[$type];

echo afficher_select(DB_OPT_eleves_regroupement($_SESSION['STRUCTURE_ID'],$type,$groupe,$statut) , $select_nom=false , $option_first='non' , $selection=true , $optgroup='non');
?>
