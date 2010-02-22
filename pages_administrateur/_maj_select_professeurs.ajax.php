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
 * Mettre à jour l'élément de formulaire "select_professeurs" et le renvoyer en HTML
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
if($_SESSION['STRUCTURE_ID']==ID_DEMO) {}

echo afficher_select(DB_OPT_professeurs_etabl($_SESSION['STRUCTURE_ID']) , $select_nom=false , $option_first='non' , $selection=true , $optgroup='non');
?>
