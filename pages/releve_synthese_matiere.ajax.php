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

//	////////////////////////////////////////////////////////////////////////////////////////////////////
//	Générer une synthèse d'une matière
//	////////////////////////////////////////////////////////////////////////////////////////////////////

$matiere_id    = (isset($_POST['f_matiere']))            ? clean_entier($_POST['f_matiere'])      : 0;
$matiere_nom   = (isset($_POST['f_matiere_nom']))        ? clean_texte($_POST['f_matiere_nom'])   : '';
$groupe_id     = (isset($_POST['f_groupe']))             ? clean_entier($_POST['f_groupe'])       : 0;
$groupe_nom    = (isset($_POST['f_groupe_nom']))         ? clean_texte($_POST['f_groupe_nom'])    : '';
$periode_id    = (isset($_POST['f_periode']))            ? clean_entier($_POST['f_periode'])      : 0;
$date_debut    = (isset($_POST['f_date_debut']))         ? clean_texte($_POST['f_date_debut'])    : '';
$date_fin      = (isset($_POST['f_date_fin']))           ? clean_texte($_POST['f_date_fin'])      : '';
$retroactif    = (isset($_POST['f_retroactif']))         ? clean_texte($_POST['f_retroactif'])    : '';
$niveau_id     = (isset($_POST['f_niveau']))             ? clean_entier($_POST['f_niveau'])       : 0; // Niveau transmis uniquement si on restreint sur un niveau
$aff_coef      = (isset($_POST['f_coef']))               ? 1                                      : 0;
$aff_socle     = (isset($_POST['f_socle']))              ? 1                                      : 0;
$aff_lien      = (isset($_POST['f_lien']))               ? 1                                      : 0;
$only_socle    = (isset($_POST['f_restriction_socle']))  ? 1                                      : 0;
$only_niveau   = (isset($_POST['f_restriction_niveau'])) ? $niveau_id                             : 0;
$mode_synthese = (isset($_POST['f_mode_synthese']))      ? clean_texte($_POST['f_mode_synthese']) : '';
$couleur       = (isset($_POST['f_couleur']))            ? clean_texte($_POST['f_couleur'])       : '';
$legende       = (isset($_POST['f_legende']))            ? clean_texte($_POST['f_legende'])       : '';
// Normalement c'est un tableau qui est transmis, mais au cas où...
$tab_eleve = (isset($_POST['f_eleve'])) ? ( (is_array($_POST['f_eleve'])) ? $_POST['f_eleve'] : explode(',',$_POST['f_eleve']) ) : array() ;
$tab_eleve = array_filter( array_map( 'clean_entier' , $tab_eleve ) , 'positif' );

$liste_eleve   = implode(',',$tab_eleve);

if( !$matiere_id || !$matiere_nom || !$groupe_id || !$groupe_nom || !count($tab_eleve) || ( !$periode_id && (!$date_debut || !$date_fin) ) || !$retroactif || !$mode_synthese || !$couleur || !$legende )
{
	exit('Erreur avec les données transmises !');
}

Formulaire::save_choix('synthese_matiere');

// Période concernée
if($periode_id==0)
{
	$date_mysql_debut = convert_date_french_to_mysql($date_debut);
	$date_mysql_fin   = convert_date_french_to_mysql($date_fin);
}
else
{
	$DB_ROW = DB_STRUCTURE_COMMUN::DB_recuperer_dates_periode($groupe_id,$periode_id);
	if(!count($DB_ROW))
	{
		exit('La classe et la période ne sont pas reliées !');
	}
	$date_mysql_debut = $DB_ROW['jointure_date_debut'];
	$date_mysql_fin   = $DB_ROW['jointure_date_fin'];
	$date_debut = convert_date_mysql_to_french($date_mysql_debut);
	$date_fin   = convert_date_mysql_to_french($date_mysql_fin);
}
if($date_mysql_debut>$date_mysql_fin)
{
	exit('La date de début est postérieure à la date de fin !');
}

$tab_item       = array();	// [item_id] => array(item_ref,item_nom,item_coef,item_cart,item_socle,item_lien,matiere_id,calcul_methode,calcul_limite,synthese_ref);
$tab_liste_item = array();	// [i] => item_id
$tab_eleve      = array();	// [i] => array(eleve_id,eleve_nom,eleve_prenom)
$tab_matiere    = array();	// [matiere_id] => matiere_nom
$tab_synthese   = array();	// [synthese_ref] => synthese_nom
$tab_eval       = array();	// [eleve_id][item_id][devoir] => array(note,date,info) On utilise un tableau multidimensionnel vu qu'on ne sait pas à l'avance combien il y a d'évaluations pour un élève et un item donnés.

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Récupération de la liste des items travaillés durant la période choisie, pour les élèves selectionnés, toutes matières confondues
// Récupération de la liste des synthèses concernées (nom de thèmes ou de domaines suivant les référentiels)
// Récupération de la liste des matières concernées
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

list($tab_item,$tab_synthese) = DB_STRUCTURE_BILAN::DB_recuperer_arborescence_synthese($liste_eleve,$matiere_id,$only_socle,$only_niveau,$mode_synthese,$date_mysql_debut,$date_mysql_fin);
$tab_matiere[$matiere_id] = $matiere_nom;

$item_nb = count($tab_item);
if(!$item_nb)
{
	exit('Aucun item n\'a été évalué durant cette période pour ces élèves dans cette configuration !');
}
$tab_liste_item = array_keys($tab_item);
$liste_item = implode(',',$tab_liste_item);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Récupération de la liste des élèves
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
$tab_eleve = DB_STRUCTURE_BILAN::DB_lister_eleves_cibles($liste_eleve,$with_gepi=FALSE,$with_langue=FALSE);
if(!is_array($tab_eleve))
{
	exit('Aucun élève trouvé correspondant aux identifiants transmis !');
}
$eleve_nb = count($tab_eleve);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Récupération de la liste des résultats des évaluations associées à ces items donnés d'une matiere donnée, pour les élèves selectionnés, sur la période sélectionnée
// Attention, il faut éliminer certains items qui peuvent potentiellement apparaitre dans des relevés d'élèves alors qu'ils n'ont pas été interrogés sur la période considérée (mais un camarade oui).
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
$tab_score_a_garder = array();
$DB_TAB = DB_STRUCTURE_BILAN::DB_lister_date_last_eleves_items($liste_eleve,$liste_item);
foreach($DB_TAB as $DB_ROW)
{
	$tab_score_a_garder[$DB_ROW['eleve_id']][$DB_ROW['item_id']] = ($DB_ROW['date_last']<$date_mysql_debut) ? false : true ;
}

$date_mysql_debut = ($retroactif=='non') ? $date_mysql_debut : false;
$DB_TAB = DB_STRUCTURE_BILAN::DB_lister_result_eleves_matiere($liste_eleve , $liste_item , $date_mysql_debut , $date_mysql_fin , $_SESSION['USER_PROFIL']);
foreach($DB_TAB as $DB_ROW)
{
	if($tab_score_a_garder[$DB_ROW['eleve_id']][$DB_ROW['item_id']])
	{
		$tab_eval[$DB_ROW['eleve_id']][$DB_ROW['item_id']][] = array('note'=>$DB_ROW['note'],'date'=>$DB_ROW['date'],'info'=>$DB_ROW['info']);
	}
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// INCLUSION DU CODE COMMUN À PLUSIEURS PAGES
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

$format = 'matiere' ;
require('./_inc/code_releve_synthese.php');

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// On retourne les résultats
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

if($affichage_direct)
{
	echo'<hr />';
	echo'<ul class="puce">';
	echo'<li><a class="lien_ext" href="'.$dossier.$fichier_lien.'.pdf"><span class="file file_pdf">Archiver / Imprimer (format <em>pdf</em>).</span></a></li>';
	echo'</ul>';
	echo $releve_HTML;
}
else
{
	echo'<ul class="puce">';
	echo'<li><a class="lien_ext" href="'.$dossier.$fichier_lien.'.pdf"><span class="file file_pdf">Archiver / Imprimer (format <em>pdf</em>).</span></a></li>';
	echo'<li><a class="lien_ext" href="./releve-html.php?fichier='.$fichier_lien.'"><span class="file file_htm">Explorer / Détailler (format <em>html</em>).</span></a></li>';
	echo'</ul>';
}

?>
