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
if($_SESSION['SESAMATH_ID']==ID_DEMO) {exit('Action désactivée pour la démo...');}

$top_depart = microtime(TRUE);

$action = (isset($_POST['f_action'])) ? clean_texte($_POST['f_action']) : '';

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
// Purger la base
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

if($action=='purger')
{

	// Bloquer l'application
	bloquer_application($_SESSION['USER_PROFIL'],'Purge annuelle de la base en cours.');
	// Supprimer tous les devoirs associés aux classes, mais pas les saisies associées
	DB_STRUCTURE_supprimer_devoirs_sans_saisies();
	echo'<li><label class="valide">Évaluations supprimées (saisies associées conservées).</label></li>';
	// Supprimer tous les types de groupes, sauf les classes (donc 'groupe' ; 'besoin' ; 'eval'), ainsi que les jointures avec les périodes.
	$DB_TAB = DB_STRUCTURE_lister_groupes_sauf_classes();
	if(count($DB_TAB))
	{
		foreach($DB_TAB as $DB_ROW)
		{
			DB_STRUCTURE_supprimer_groupe($DB_ROW['groupe_id'],$DB_ROW['groupe_type'],$with_devoir=false);
		}
	}
	echo'<li><label class="valide">Groupes supprimés (avec leurs associations).</label></li>';
	// Supprimer les jointures classes/périodes
	DB_STRUCTURE_modifier_liaison_groupe_periode($groupe_id=true,$periode_id=true,$etat=false,$date_debut_mysql='',$date_fin_mysql='');
	echo'<li><label class="valide">Jointures classes / périodes supprimées.</label></li>';
	// Supprimer les demandes d'évaluations
	DB_STRUCTURE_supprimer_demandes(true);
	echo'<li><label class="valide">Demandes d\'évaluations supprimées.</label></li>';
	// En profiter pour optimiser les tables (1 fois par an, ça ne peut pas faire de mal)
	DB_STRUCTURE_optimiser_tables_structure();
	echo'<li><label class="valide">Tables optimisées par MySQL (équivalent d\'un défragmentage).</label></li>';
	// Débloquer l'application
	debloquer_application($_SESSION['USER_PROFIL']);
	// Afficher le retour
	$top_arrivee = microtime(TRUE);
	$duree = number_format($top_arrivee - $top_depart,2,',','');
	echo'<li><label class="valide">Initialisation annuelle de la base réalisée en '.$duree.'s.</label></li>';
	exit();
}

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
// Nettoyer la base
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

elseif($action=='nettoyer')
{
	// Bloquer l'application
	bloquer_application($_SESSION['USER_PROFIL'],'Recherche et suppression de données orphelines en cours.');
	// Rechercher et corriger les anomalies
	$tab_bilan = DB_STRUCTURE_corriger_anomalies();
	// Débloquer l'application
	debloquer_application($_SESSION['USER_PROFIL']);
	// Afficher le retour
	$top_arrivee = microtime(TRUE);
	$duree = number_format($top_arrivee - $top_depart,2,',','');
	echo'<li>'.implode('</li><li>',$tab_bilan).'</li>';
	echo'<li><label class="valide">Recherche et suppression de données orphelines réalisée en '.$duree.'s.</label></li>';
	exit();
}

else
{
	exit('Erreur avec les données transmises !');
}
?>