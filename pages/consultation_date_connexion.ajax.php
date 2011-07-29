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
if($_SESSION['SESAMATH_ID']==ID_DEMO) {}

$groupe_type = (isset($_POST['f_groupe_type'])) ? clean_texte($_POST['f_groupe_type']) : ''; // d n c g b
$groupe_id   = (isset($_POST['f_groupe_id']))   ? clean_entier($_POST['f_groupe_id'])  : 0;
$tab_types   = array('d'=>'profs+dirs' , 'n'=>'niveau' , 'c'=>'classe' , 'g'=>'groupe' , 'b'=>'besoin');

if( (!$groupe_id) || (!isset($tab_types[$groupe_type])) )
{
	exit('Erreur avec les données transmises !');
}

$DB_TAB = ($groupe_type!='d') ? DB_STRUCTURE_lister_eleves_actifs_regroupement($tab_types[$groupe_type],$groupe_id) : DB_STRUCTURE_lister_users($profil=array('professeur','directeur'),$only_actifs=true,$with_classe=false) ;

foreach($DB_TAB as $DB_ROW)
{
	// Formater la date (dont on ne garde que le jour)
	$date_mysql  = substr($DB_ROW['user_connexion_date'],0,10);
	$date_affich = ($date_mysql!='0000-00-00') ? convert_date_mysql_to_french($date_mysql) : '-' ;
	// Afficher une ligne du tableau
	echo'<tr>';
	echo	'<td>'.html($DB_ROW['user_nom'].' '.$DB_ROW['user_prenom']).'</td>';
	echo	'<td><i>'.html($date_mysql).'</i>'.html($date_affich).'</td>';
	echo'</tr>';
}
exit();

?>
