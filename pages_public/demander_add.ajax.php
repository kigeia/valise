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
if($_SESSION['SESAMATH_ID']==ID_DEMO) {}

$eleve_id      = (isset($_GET['eleve_id']))      ? clean_entier($_GET['eleve_id'])      : 0;
$matiere_id    = (isset($_GET['matiere_id']))    ? clean_entier($_GET['matiere_id'])    : 0;
$competence_id = (isset($_GET['competence_id'])) ? clean_entier($_GET['competence_id']) : 0;
$score         = (isset($_GET['score']))         ? clean_entier($_GET['score'])         : -2; // normalement entier entre 0 et 100 ou -1 si non évalué

// Un élève demande souhaite ajouter une demande d'évaluation.
if( ($eleve_id==0) || ($matiere_id==0) || ($competence_id==0) || ($score==-2) )
{
	$reponse = 'Erreur avec les données transmises !';
}
// Vérifier que c'est autorisé par l'administrateur.
elseif($_SESSION['ELEVE_DEMANDES']==0)
{
	$reponse = 'Action non autorisée par l\'administrateur !';
}
// Vérifier qu'il reste des demandes disponibles pour l'élève et la matière concernés (on compte le nb de demandes en attente)
else
{
	$DB_SQL = 'SELECT demande_id FROM sacoche_demande ';
	$DB_SQL.= 'WHERE user_id=:eleve_id AND matiere_id=:matiere_id ';
	$DB_SQL.= 'LIMIT '.$_SESSION['ELEVE_DEMANDES'];
	$DB_VAR = array(':eleve_id'=>$eleve_id,':matiere_id'=>$matiere_id);
	$DB_TAB = DB::queryTab(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
	$nb_demandes_attente = count($DB_TAB);
	$nb_demandes_possibles = $_SESSION['ELEVE_DEMANDES'] - $nb_demandes_attente ;
	if($nb_demandes_possibles>0)
	{
		// Vérifier que cet item n'est pas déjà en attente d'évaluation pour cet élève
		$DB_SQL = 'SELECT demande_id FROM sacoche_demande ';
		$DB_SQL.= 'WHERE user_id=:eleve_id AND matiere_id=:matiere_id AND item_id=:competence_id ';
		$DB_SQL.= 'LIMIT 1';
		$DB_VAR = array(':eleve_id'=>$eleve_id,':matiere_id'=>$matiere_id,':competence_id'=>$competence_id);
		$DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
		if(count($DB_ROW))
		{
			$reponse = 'Cette demande est déjà enregistrée !';
		}
		else
		{
			$score = ($score!=-1) ? $score : NULL ;
			$date_mysql = date("Y-m-d");	// date_mysql de la forme aaaa-mm-jj
			$DB_SQL = 'INSERT INTO sacoche_demande(user_id,matiere_id,item_id,demande_date,demande_score,demande_statut) ';
			$DB_SQL.= 'VALUES(:eleve_id,:matiere_id,:competence_id,:demande_date,:demande_score,:demande_statut)';
			$DB_VAR = array(':eleve_id'=>$eleve_id,':matiere_id'=>$matiere_id,':competence_id'=>$competence_id,':demande_date'=>$date_mysql,':demande_score'=>$score,':demande_statut'=>'eleve');
			DB::query(SACOCHE_STRUCTURE_BD_NAME , $DB_SQL , $DB_VAR);
			$nb_demandes_attente++;
			$nb_demandes_possibles--;
			$reponse = 'Votre demande a été ajoutée.<br />';
			$s = ($nb_demandes_possibles>1) ? 's' : '' ;
			$reponse .= ($nb_demandes_possibles==0) ? 'Vous ne pouvez plus formuler d\'autres demandes pour cette matière.' : 'Vous pouvez encore formuler '.$nb_demandes_possibles.' demande'.$s.' pour cette matière.' ;
		}
	}
	else
	{
		$reponse = ($_SESSION['ELEVE_DEMANDES']>1) ? 'Vous avez déjà formulé les '.$nb_demandes_attente.' demandes autorisées pour cette matière.<br /><a href="./index.php?dossier=eleve&amp;fichier=eval_demande">Veuillez en supprimer pour en ajouter d\'autres !</a>' : 'Vous avez déjà formulé la demande autorisée pour cette matière.<br /><a href="./index.php?dossier=eleve&amp;fichier=eval_demande">Veuillez la supprimer pour en mettre une autre !</a>' ;
	}
}
echo'<form id="form_calque" action="">';
echo'	<div style="float:right""><input class="but" type="image" src="./_img/fermer.gif" name="fermer" value="Fermer" /></div>';
echo'	<div>'.$reponse.'</div>';
echo'</form>';

?>
