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
if(($_SESSION['SESAMATH_ID']==ID_DEMO)&&($_GET['action']!='initialiser')){exit('Action désactivée pour la démo...');}

$action = (isset($_GET['action'])) ? $_GET['action'] : '';
$tab_select_professeurs = (isset($_POST['select_professeurs'])) ? array_map('clean_entier',explode(',',$_POST['select_professeurs'])) : array() ;
$tab_select_groupes     = (isset($_POST['select_groupes']))     ? array_map('clean_entier',explode(',',$_POST['select_groupes']))     : array() ;

function positif($n) {return $n;}
$tab_select_professeurs = array_filter($tab_select_professeurs,'positif');
$tab_select_groupes     = array_filter($tab_select_groupes,'positif');

// Ajouter des professeurs à des groupes
if($action=='ajouter')
{
	foreach($tab_select_professeurs as $user_id)
	{
		foreach($tab_select_groupes as $groupe_id)
		{
			DB_STRUCTURE_modifier_liaison_user_groupe($user_id,'professeur',$groupe_id,'groupe',true);
		}
	}
}

// Retirer des professeurs à des groupes
elseif($action=='retirer')
{
	foreach($tab_select_professeurs as $user_id)
	{
		foreach($tab_select_groupes as $groupe_id)
		{
			DB_STRUCTURE_modifier_liaison_user_groupe($user_id,'professeur',$groupe_id,'groupe',false);
		}
	}
}

// Affichage du bilan des affectations des professeurs dans les groupes
echo'<hr />';

// Deux requêtes préliminaires pour ne pas manquer les groupes sans professeurs et les professeurs sans groupes
$tab_lignes_tableau1  = array();
$tab_lignes_tableau2  = array();
$tab_profs            = array();
$tab_groupes          = array();
$tab_profs_par_groupe = array();
$tab_groupes_par_prof = array();
// Récupérer la liste des groupes
$DB_TAB = DB_STRUCTURE_lister_groupes_avec_niveaux();
foreach($DB_TAB as $DB_ROW)
{
	$tab_groupes[$DB_ROW['groupe_id']] = html($DB_ROW['groupe_nom']);
	$tab_profs_par_groupe[$DB_ROW['groupe_id']] = '';
	$tab_lignes_tableau1[$DB_ROW['niveau_id']][] = $DB_ROW['groupe_id'];
}
// Récupérer la liste des professeurs
$DB_TAB = DB_STRUCTURE_lister_users('professeur',$only_actifs=true,$with_classe=false);
$compteur = 0 ;
foreach($DB_TAB as $DB_ROW)
{
	$tab_profs[$DB_ROW['user_id']] = html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']);
	$tab_groupes_par_prof[$DB_ROW['user_id']] = '';
	$tab_lignes_tableau2[floor($compteur/8)][] = $DB_ROW['user_id'];
	$compteur++;
}
// Récupérer la liste des jointures
if( (count($tab_profs)) && (count($tab_groupes)) )
{
	$liste_profs_id   = implode(',',array_keys($tab_profs));
	$liste_classes_id = implode(',',array_keys($tab_groupes));
	$DB_SQL = 'SELECT groupe_id,user_id FROM sacoche_jointure_user_groupe ';
	$DB_SQL.= 'LEFT JOIN sacoche_user USING (user_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_groupe USING (groupe_id) ';
	$DB_SQL.= 'LEFT JOIN sacoche_niveau USING (niveau_id) ';
	$DB_SQL.= 'WHERE user_id IN('.$liste_profs_id.') AND groupe_id IN('.$liste_classes_id.') ';
	$DB_SQL.= 'ORDER BY niveau_ordre ASC, groupe_ref ASC, user_nom ASC, user_prenom ASC';
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , null);
	foreach($DB_TAB as $DB_ROW)
	{
		$tab_profs_par_groupe[$DB_ROW['groupe_id']] .= $tab_profs[$DB_ROW['user_id']].'<br />';
		$tab_groupes_par_prof[$DB_ROW['user_id']]   .= $tab_groupes[$DB_ROW['groupe_id']].'<br />';
	}
}
else
{
	echo (count($tab_profs)) ? '' : 'Aucun professeur n\'est enregistré !<br />';
	echo (count($tab_groupes)) ? '' : 'Aucun groupe n\'est enregistré !<br />';
	exit();
}
// Assemblage du tableau des profs par groupe
$TH = array();
$TB = array();
$TF = array();
foreach($tab_lignes_tableau1 as $niveau_id => $tab_groupe)
{
	$TH[$niveau_id] = '';
	$TB[$niveau_id] = '';
	$TF[$niveau_id] = '';
	foreach($tab_groupe as $groupe_id)
	{
		$nb = mb_substr_count($tab_profs_par_groupe[$groupe_id],'<br />','UTF-8');
		$s = ($nb>1) ? 's' : '' ;
		$TH[$niveau_id] .= '<th>'.$tab_groupes[$groupe_id].'</th>';
		$TB[$niveau_id] .= '<td>'.mb_substr($tab_profs_par_groupe[$groupe_id],0,-6,'UTF-8').'</td>';
		$TF[$niveau_id] .= '<td>'.$nb.' professeur'.$s.'</td>';
	}
}
echo'<h2>Professeurs par groupe</h2>';
foreach($tab_lignes_tableau1 as $niveau_id => $tab_groupe)
{
	echo'<table class="affectation">';
	echo'<thead><tr>'.$TH[$niveau_id].'</tr></thead>';
	echo'<tbody><tr>'.$TB[$niveau_id].'</tr></tbody>';
	echo'<tfoot><tr>'.$TF[$niveau_id].'</tr></tfoot>';
	echo'</table><p />';
}
// Assemblage du tableau des groupes par prof
$TH = array();
$TB = array();
$TF = array();
foreach($tab_lignes_tableau2 as $ligne_id => $tab_user)
{
	$TH[$ligne_id] = '';
	$TB[$ligne_id] = '';
	$TF[$ligne_id] = '';
	foreach($tab_user as $user_id)
	{
		$nb = mb_substr_count($tab_groupes_par_prof[$user_id],'<br />','UTF-8');
		$s = ($nb>1) ? 's' : '' ;
		$TH[$ligne_id] .= '<th>'.$tab_profs[$user_id].'</th>';
		$TB[$ligne_id] .= '<td>'.mb_substr($tab_groupes_par_prof[$user_id],0,-6,'UTF-8').'</td>';
		$TF[$ligne_id] .= '<td>'.$nb.' groupe'.$s.'</td>';
	}
}
echo'<h2>Groupes par professeur</h2>';
foreach($tab_lignes_tableau2 as $ligne_id => $tab_user)
{
	echo'<table class="affectation">';
	echo'<thead><tr>'.$TH[$ligne_id].'</tr></thead>';
	echo'<tbody><tr>'.$TB[$ligne_id].'</tr></tbody>';
	echo'<tfoot><tr>'.$TF[$ligne_id].'</tr></tfoot>';
	echo'</table><p />';
}
?>