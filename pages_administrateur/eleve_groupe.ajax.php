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
if(($_SESSION['STRUCTURE_ID']==ID_DEMO)&&($_GET['action']!='initialiser')){exit('Action désactivée pour la démo...');}

$action = (isset($_GET['action'])) ? $_GET['action'] : '';
$tab_select_eleves  = (isset($_POST['select_eleves']))  ? array_map('clean_entier',explode(',',$_POST['select_eleves']))  : array() ;
$tab_select_groupes = (isset($_POST['select_groupes'])) ? array_map('clean_entier',explode(',',$_POST['select_groupes'])) : array() ;

function positif($n) {return($n);}
$tab_select_eleves = array_filter($tab_select_eleves,'positif');
$tab_select_groupes = array_filter($tab_select_groupes,'positif');

// Ajouter des élèves à des groupes
if($action=='ajouter')
{
	foreach($tab_select_eleves as $user_id)
	{
		foreach($tab_select_groupes as $groupe_id)
		{
			DB_modifier_liaison_user_groupe($_SESSION['STRUCTURE_ID'],$user_id,'eleve',$groupe_id,'groupe',true);
		}
	}
}

// Retirer des élèves à des groupes
elseif($action=='retirer')
{
	foreach($tab_select_eleves as $user_id)
	{
		foreach($tab_select_groupes as $groupe_id)
		{
			DB_modifier_liaison_user_groupe($_SESSION['STRUCTURE_ID'],$user_id,'eleve',$groupe_id,'groupe',false);
		}
	}
}

// Affichage du bilan des affectations des élèves dans les groupes ; en deux requêtes pour récupérer les élèves sans groupes et les groupes sans élèves
$tab_niveau_groupe = array();
$tab_user          = array();
// Récupérer la liste des groupes
$DB_SQL = 'SELECT * FROM livret_groupe ';
$DB_SQL.= 'LEFT JOIN livret_niveau USING (livret_niveau_id) ';
$DB_SQL.= 'WHERE livret_structure_id=:structure_id AND livret_groupe_type=:type ';
$DB_SQL.= 'ORDER BY livret_niveau_ordre ASC, livret_groupe_nom ASC';
$DB_VAR = array(':structure_id'=>$_SESSION['STRUCTURE_ID'],':type'=>'groupe');
$DB_TAB = DB::queryTab(SACOCHE_BD_NAME , $DB_SQL , $DB_VAR);
foreach($DB_TAB as $DB_ROW)
{
	$tab_niveau_groupe[$DB_ROW['livret_niveau_id']][$DB_ROW['livret_groupe_id']] = html($DB_ROW['livret_groupe_nom']);
	$tab_user[$DB_ROW['livret_groupe_id']] = '';
}
// Récupérer la liste des élèves / groupes
$DB_SQL = 'SELECT * FROM livret_user ';
$DB_SQL.= 'LEFT JOIN livret_jointure_user_groupe USING (livret_structure_id,livret_user_id) ';
$DB_SQL.= 'LEFT JOIN livret_groupe USING (livret_structure_id,livret_groupe_id) ';
$DB_SQL.= 'WHERE livret_structure_id=:structure_id AND livret_user_profil=:profil AND livret_groupe_type=:type ';
$DB_SQL.= 'ORDER BY livret_user_nom ASC, livret_user_prenom ASC';
$DB_VAR = array(':structure_id'=>$_SESSION['STRUCTURE_ID'],':profil'=>'eleve',':type'=>'groupe');
$DB_TAB = DB::queryTab(SACOCHE_BD_NAME , $DB_SQL , $DB_VAR);
foreach($DB_TAB as $DB_ROW)
{
	$tab_user[$DB_ROW['livret_groupe_id']]  .= html($DB_ROW['livret_user_nom'].' '.$DB_ROW['livret_user_prenom']).'<br />';
}
// Assemblage du tableau résultant
$TH = array();
$TB = array();
$TF = array();
foreach($tab_niveau_groupe as $niveau_id => $tab_groupe)
{
	$TH[$niveau_id] = '';
	$TB[$niveau_id] = '';
	$TF[$niveau_id] = '';
	foreach($tab_groupe as $groupe_id => $groupe_nom)
	{
		$nb = mb_substr_count($tab_user[$groupe_id],'<br />','UTF-8');
		$s = ($nb>1) ? 's' : '' ;
		$TH[$niveau_id] .= '<th>'.$groupe_nom.'</th>';
		$TB[$niveau_id] .= '<td>'.mb_substr($tab_user[$groupe_id],0,-6,'UTF-8').'</td>';
		$TF[$niveau_id] .= '<td>'.$nb.' élève'.$s.'</td>';
	}
}
echo'<hr />';
foreach($tab_niveau_groupe as $niveau_id => $tab_groupe)
{
	echo'<table class="affectation">';
	echo'<thead><tr>'.$TH[$niveau_id].'</tr></thead>';
	echo'<tbody><tr>'.$TB[$niveau_id].'</tr></tbody>';
	echo'<tfoot><tr>'.$TF[$niveau_id].'</tr></tfoot>';
	echo'</table><p />';
}
?>
