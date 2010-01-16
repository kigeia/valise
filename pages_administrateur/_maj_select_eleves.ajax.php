<?php
/**
 * @version $Id: _maj_select_eleves.ajax.php 8 2009-10-30 20:56:02Z thomas $
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2009
 * 
 * ****************************************************************************************************
 * SACoche [http://competences.sesamath.net] - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath [http://www.sesamath.net]
 * Distribution sous licence libre prévue pour l'été 2010.
 * ****************************************************************************************************
 * 
 * Mettre à jour l'élément de formulaire "select_eleves" et le renvoyer en HTML
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if($_SESSION['STRUCTURE_ID']==ID_DEMO) {}

$groupe_type = (isset($_POST['f_groupe_type'])) ? clean_texte($_POST['f_groupe_type']) : '';
$groupe_id   = (isset($_POST['f_groupe_id']))   ? clean_entier($_POST['f_groupe_id'])  : 0;
$statut      = (isset($_POST['f_statut']))      ? clean_entier($_POST['f_statut'])     : 0;

$tab_types = array('d'=>'Divers' , 'n'=>'niveau' , 'c'=>'classe' , 'g'=>'groupe');

if( (!$groupe_id) || (!isset($tab_types[$groupe_type])) )
{
	exit('Erreur avec les données transmises !');
}

$groupe_type = $tab_types[$groupe_type];
if($groupe_type=='Divers')
{
	$groupe_type = ($groupe_id==1) ? 'sdf' : 'all' ;
}

$DB_TAB = eleves_regroupement($groupe_type,$groupe_id,$statut);

echo afficher_select($DB_TAB , $select_nom=false , $option_first='non' , $selection=true , $optgroup='non');
?>
