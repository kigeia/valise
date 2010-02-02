<?php
/**
 * @version $Id: referentiel_view.php 8 2009-10-30 20:56:02Z thomas $
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
$TITRE = "Référentiels de compétences utilisés";
?>

<div class="hc">
	<span class="manuel"><a class="pop_up" href="./aide.php?fichier=referentiel_organisation_competences">DOC : Organisation des compétences dans les référentiels.</a></span><br />
	<span class="manuel"><a class="pop_up" href="./aide.php?fichier=referentiel_structure">DOC : Structure d'un référentiel.</a></span>
</div>

<?php
// J'ai séparé en plusieurs requêtes au bout de plusieurs heures sans m'en sortir (entre les matières sans coordonnateurs, sans référentiel, les deux à la fois...).
// La recherche ne s'effectue que sur les matières et niveaux utilisés, sans débusquer des référentiels résiduels.
$tab_matiere = array();
$tab_niveau  = array();
$tab_colonne = array();

// On récupère la liste des matières utilisées par l'établissement
$DB_SQL = 'SELECT livret_matiere_id,livret_matiere_nom FROM livret_matiere ';
$DB_SQL.= ($_SESSION['MATIERES']) ? 'WHERE livret_matiere_structure_id=:structure_id OR livret_matiere_id IN('.$_SESSION['MATIERES'].') ' : 'WHERE livret_matiere_structure_id=:structure_id ';
$DB_SQL.= 'ORDER BY livret_matiere_nom ASC';
$DB_VAR = array(':structure_id'=>$_SESSION['STRUCTURE_ID']);
$DB_TAB = DB::queryTab(SACOCHE_BD_NAME , $DB_SQL , $DB_VAR);
if(count($DB_TAB))
{
	foreach($DB_TAB as $key => $DB_ROW)
	{
		$tab_matiere[$DB_ROW['livret_matiere_id']]['nom'] = html($DB_ROW['livret_matiere_nom']);
	}
}
$liste_matieres = implode(',',array_keys($tab_matiere));

if(!$liste_matieres)
{
	echo'<p><span class="danger">Aucune matière enregistrée ou associée à l\'établissement !</span></p>';
}
elseif(!$_SESSION['NIVEAUX'])
{
	echo'<p><span class="danger">Aucun niveau n\'est rattaché à l\'établissement !</span></p>';
}
else
{
	echo'<p><span class="astuce">Cliquer sur l\'&oelig;il pour voir le détail d\'un référentiel.</span></p>';
	// On récupère la liste des niveaux utilisés par l'établissement
	$DB_SQL = 'SELECT livret_niveau_id,livret_niveau_nom FROM livret_niveau ';
	$DB_SQL.= 'WHERE livret_niveau_id IN('.$_SESSION['NIVEAUX'].') ';
	$DB_SQL.= ($_SESSION['PALIERS']) ? 'OR livret_palier_id IN('.$_SESSION['PALIERS'].') ' : '' ;
	$DB_SQL.= 'ORDER BY livret_niveau_ordre ASC';
	$DB_TAB = DB::queryTab(SACOCHE_BD_NAME , $DB_SQL);
	$nb_niveaux = count($DB_TAB);
	foreach($DB_TAB as $key => $DB_ROW)
	{
		$tab_niveau[$DB_ROW['livret_niveau_id']] = html($DB_ROW['livret_niveau_nom']);
	}
	// On récupère la liste des coordonnateurs responsables par matières
	// DB::query(SACOCHE_BD_NAME , 'SET group_concat_max_len = ...'); // Pour lever si besoin une limitation de GROUP_CONCAT (group_concat_max_len est par défaut limité à une chaine de 1024 caractères).
	$DB_SQL = 'SELECT livret_matiere_id, GROUP_CONCAT(CONCAT(livret_user_nom," ",livret_user_prenom) SEPARATOR ";") AS coord_liste FROM livret_jointure_user_matiere ';
	$DB_SQL.= 'LEFT JOIN livret_user USING (livret_structure_id,livret_user_id) ';
	$DB_SQL.= 'WHERE livret_structure_id=:structure_id AND livret_matiere_id IN('.$liste_matieres.') AND livret_jointure_coord=:coord AND livret_user_statut=:statut ';
	$DB_SQL.= 'GROUP BY livret_matiere_id';
	$DB_VAR = array(':structure_id'=>$_SESSION['STRUCTURE_ID'],':coord'=>1,':statut'=>1);
	$DB_TAB = DB::queryTab(SACOCHE_BD_NAME , $DB_SQL , $DB_VAR);
	if(count($DB_TAB))
	{
		foreach($DB_TAB as $key => $DB_ROW)
		{
			$tab_matiere[$DB_ROW['livret_matiere_id']]['coord'] = str_replace(';','<br />',html($DB_ROW['coord_liste']));
		}
	}
	// On récupère la liste des référentiels par matière et niveau
	$tab_partage = array('oui'=>'<img title="Référentiel accessible par la communauté" alt="" src="./_img/partage1.gif" />','non'=>'<img title="Référentiel caché à la communauté." alt="" src="./_img/partage0.gif" />','bof'=>'<img title="Référentiel partage sans intérêt (pas novateur)." alt="" src="./_img/partage0.gif" />','hs'=>'<img title="Référentiel partage sans objet (matière spécifique)." alt="" src="./_img/partage0.gif" />');
	$DB_SQL = 'SELECT livret_matiere_id,livret_niveau_id,livret_niveau_nom,livret_referentiel_partage FROM livret_referentiel ';
	$DB_SQL.= 'LEFT JOIN livret_niveau USING (livret_niveau_id) ';
	$DB_SQL.= 'WHERE livret_structure_id=:structure_id AND livret_matiere_id IN('.$liste_matieres.') AND ( livret_niveau_id IN('.$_SESSION['NIVEAUX'].') OR livret_palier_id IN('.$_SESSION['PALIERS'].') ) ';
	$DB_SQL.= 'ORDER BY livret_matiere_id ASC, livret_niveau_ordre ASC';
	$DB_VAR = array(':structure_id'=>$_SESSION['STRUCTURE_ID']);
	$DB_TAB = DB::queryTab(SACOCHE_BD_NAME , $DB_SQL , $DB_VAR);
	if(count($DB_TAB))
	{
		foreach($DB_TAB as $key => $DB_ROW)
		{
			$partage = $tab_partage[$DB_ROW['livret_referentiel_partage']];
			$ref = 'Voir_'.$DB_ROW['livret_matiere_id'].'_'.$DB_ROW['livret_niveau_id'];
			$tab_colonne[$DB_ROW['livret_matiere_id']][$DB_ROW['livret_niveau_id']]['ref']   = 'Référentiel présent. '.$partage;
			$tab_colonne[$DB_ROW['livret_matiere_id']][$DB_ROW['livret_niveau_id']]['oeil']  = '<q class="voir" id="'.$ref.'" title="Voir le détail de ce référentiel."></q>';
			$tab_colonne[$DB_ROW['livret_matiere_id']][$DB_ROW['livret_niveau_id']]['label'] = '<label for="'.$ref.'"></label>';
		}
	}
	// On construit et affiche le tableau résultant
	$affichage = '<table class="comp_view"><thead><tr><th>Matière</th><th>Coordonnateur(s)</th><th>Niveau</th><th>Référentiel</th><th>Voir</th><th class="nu"></th></tr></thead><tbody>'."\r\n";
	foreach($tab_matiere as $matiere_id => $tab)
	{
		$rowspan = ($matiere_id!=ID_MATIERE_TRANSVERSALE) ? $nb_niveaux : mb_substr_count($_SESSION['PALIERS'],',','UTF-8')+1 ;
		$matiere_nom   = $tab['nom'];
		$matiere_coord = (isset($tab['coord'])) ? '>'.$tab['coord'] : ' class="r">Absence de coordonnateur.' ;
		$affichage .= '<tr><td colspan="6" class="nu">&nbsp;</td></tr>'."\r\n";
		$affichage .= '<tr><td rowspan="'.$rowspan.'">'.$matiere_nom.'</td><td rowspan="'.$rowspan.'"'.$matiere_coord.'</td>';
		$affichage_suite = false;
		foreach($tab_niveau as $niveau_id => $niveau_nom)
		{
			if( ($matiere_id!=ID_MATIERE_TRANSVERSALE) || (in_array($niveau_id,$GLOBALS['TAB_ID_NIVEAUX_PALIERS'])) )
			{
				$colonnes = (isset($tab_colonne[$matiere_id][$niveau_id])) ? '<td class="v">'.$tab_colonne[$matiere_id][$niveau_id]['ref'].'</td><td class="nu">'.$tab_colonne[$matiere_id][$niveau_id]['oeil'].'</td><td class="nu">'.$tab_colonne[$matiere_id][$niveau_id]['label'].'</td>' : '<td  class="r">Absence de référentiel.</td><td class="nu"></td><td class="nu"></td>' ;
				if($affichage_suite===false)
				{
					$affichage .= '<td>'.$niveau_nom.'</td>'.$colonnes;
					$affichage_suite = '';
				}
				else
				{
					$affichage_suite .= '<tr><td>'.$niveau_nom.'</td>'.$colonnes.'</tr>'."\r\n";
				}
			}
		}
		$affichage .= '</tr>'."\r\n".$affichage_suite;
	}
	$affichage .= '</tbody></table>'."\r\n";
	echo $affichage;
}
?>

<hr />

<div id="referentiel">
</div>


