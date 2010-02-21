<?php
/**
 * @version $Id: code_releve_competence.php 8 2009-10-30 20:56:02Z thomas $
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

/**
 * Code inclus commun aux pages
 * [./releve_matiere.ajax.php]
 * [./releve_multimatiere.ajax.php]
 * [./releve_selection.ajax.php]
 * 
 */

/*
$tab_type[]		individuel	synthese	bulletin
$format				matiere	selection	multimatiere
*/

$dossier         = './__tmp/export/';
$fichier_lien    = 'bilan_'.$format.'_etabl'.$_SESSION['STRUCTURE_ID'].'_user'.$_SESSION['USER_ID'].'_'.time();

$tab_modele_bon = array('RR','R','V','VV');	// les notes prises en compte dans le calcul du score

function non_nul($n)    {return $n!==false ;}
function acquis($n)     {return $n>$_SESSION['CALCUL_SEUIL']['V'] ;}
function non_acquis($n) {return $n<$_SESSION['CALCUL_SEUIL']['R'] ;}
function calculer_note($tab_devoirs,$calcul_methode,$calcul_limite)
{
	global $tab_modele_bon;
	$nb_devoir = count($tab_devoirs);
	// on passe en revue les évaluations disponibles, et on retient les scores exploitables
	$tab_note = array(); // pour les notes d'un élève
	for($i=0;$i<$nb_devoir;$i++)
	{
		if(in_array($tab_devoirs[$i]['note'],$tab_modele_bon))
		{
			$tab_note[] = $_SESSION['CALCUL_VALEUR'][$tab_devoirs[$i]['note']];
		}
	}
	// si pas de notes exploitables, on arrête de suite (sinon, on est certain de pouvoir renvoyer un score)
	$nb_note = count($tab_note);
	if($nb_note==0)
	{
		return false;
	}
	// si le paramétrage du référentiel l'indique, on tronque pour ne garder que les derniers résultats
	if( ($calcul_limite) && ($nb_note>$calcul_limite) )
	{
		$tab_note = array_slice($tab_note,-$calcul_limite);
		$nb_note = $calcul_limite;
	}
	// calcul de la note en fonction du mode du référentiel
	$somme_point = 0;
	$coef = 1;
	$somme_coef = 0;
	for($num_devoir=1 ; $num_devoir<=$nb_note ; $num_devoir++)
	{
		$somme_point += $tab_note[$num_devoir-1]*$coef;
		$somme_coef += $coef;
		// Calcul du coef de l'éventuel devoir suivant
		$coef = ($calcul_methode=='geometrique') ? $coef*2 : ( ($calcul_methode=='arithmetique') ? $coef+1 : 1 ) ;
	}
	return round($somme_point/$somme_coef,0);
}

if(!$aff_coef)  { $texte_coef       = ''; }
if(!$aff_socle) { $texte_socle      = ''; }
if(!$aff_lien)  { $texte_lien_avant = ''; }
if(!$aff_lien)  { $texte_lien_apres = ''; }

$date_complement = ($retroactif=='oui') ? ' (évaluations antérieures comptabilisées).' : '.';
$texte_periode   = ($format!='selection') ? 'Du '.$date_debut.' au '.$date_fin.$date_complement : false;
$tab_titre       = array('matiere'=>'sur une matière' , 'multimatiere'=>'transdisciplinaire' , 'selection'=>'sur une sélection d\'items');

require('./_fpdf/fpdf.php');
require('./_inc/class.PDF.php');

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Tableaux et variables pour mémoriser les infos ; dans cette section on ne fait que les calculs (aucun affichage)
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

$tab_score_eleve_competence             = array();	// Retenir les scores / élève / matière / item
$tab_score_competence_eleve             = array();	// Retenir les scores / item / élève
$tab_moyenne_scores_eleve               = array();	// Retenir la moyenne des scores d'acquisitions calculés / matière / élève
$tab_pourcentage_validations_eleve      = array();	// Retenir le pourcentage d'items validés / matière / élève
$tab_infos_validations_eleve            = array();	// Retenir les infos (nb A - VA - NA) à l'ogine du tableau précédent / matière / élève
$tab_moyenne_scores_competence          = array();	// Retenir la moyenne des scores d'acquisitions calculés / item
$tab_pourcentage_validations_competence = array();	// Retenir le pourcentage d'items validés / item
$moyenne_moyenne_scores                 = 0;	// moyenne des moyennes des scores d'acquisitions calculés
$moyenne_pourcentage_validations        = 0;	// moyenne des moyennes des pourcentages d'items validés

/*
	On renseigne :
	$tab_score_eleve_competence[$eleve_id][$matiere_id][$competence_id]
	$tab_score_competence_eleve[$competence_id][$eleve_id]
	$tab_moyenne_scores_eleve[$matiere_id][$eleve_id]
	$tab_pourcentage_validations_eleve[$matiere_id][$eleve_id]
	$tab_infos_validations_eleve[$matiere_id][$eleve_id]
*/

// Pour chaque élève...
foreach($tab_eleve as $tab)
{
	extract($tab);	// $eleve_id $eleve_nom $eleve_prenom $eleve_id_gepi
	// Si cet élève a été évalué...
	if(isset($tab_eval[$eleve_id]))
	{
		// Pour chaque matiere...
		foreach($tab_matiere as $matiere_id => $matiere_nom)
		{
			// Si cet élève a été évalué dans cette matière...
			if(isset($tab_eval[$eleve_id][$matiere_id]))
			{
				// Pour chaque item...
				foreach($tab_eval[$eleve_id][$matiere_id] as $competence_id => $tab_devoirs)
				{
					extract($tab_competence[$competence_id][0]);	// $competence_ref $competence_nom $competence_coef $competence_socle $competence_lien $calcul_methode $calcul_limite
					// calcul du bilan de l'item
					$tab_score_eleve_competence[$eleve_id][$matiere_id][$competence_id] = calculer_note($tab_devoirs,$calcul_methode,$calcul_limite);
					$tab_score_competence_eleve[$competence_id][$eleve_id] = $tab_score_eleve_competence[$eleve_id][$matiere_id][$competence_id];
				}
				// calcul des bilans des scores
				$tableau_score_filtre = array_filter($tab_score_eleve_competence[$eleve_id][$matiere_id],'non_nul');
				$nb_scores = count( $tableau_score_filtre );
				// la moyenne peut être pondérée par des coefficients
				$somme_scores_ponderes = 0;
				$somme_coefs = 0;
				if($nb_scores)
				{
					// En l'absence de coefficients, ces 2 lignes suffiraient :
					// $somme_scores_ponderes = array_sum($tableau_score_filtre);
					// $somme_coefs = $nb_scores;
					foreach($tableau_score_filtre as $competence_id => $competence_score)
					{
						$somme_scores_ponderes += $competence_score*$tab_competence[$competence_id][0]['competence_coef'];
						$somme_coefs += $tab_competence[$competence_id][0]['competence_coef'];
					}
				}
				// ... un pour la moyenne des pourcentages d'acquisition
				if($somme_coefs)
				{
					$tab_moyenne_scores_eleve[$matiere_id][$eleve_id] = round($somme_scores_ponderes/$somme_coefs,0);
				}
				else
				{
					$tab_moyenne_scores_eleve[$matiere_id][$eleve_id] = false;
				}
				// ... un pour le nombre d\'items considérés acquis ou pas
				if($nb_scores)
				{
					$nb_acquis      = count( array_filter($tableau_score_filtre,'acquis') );
					$nb_non_acquis  = count( array_filter($tableau_score_filtre,'non_acquis') );
					$nb_voie_acquis = $nb_scores - $nb_acquis - $nb_non_acquis;
					$tab_pourcentage_validations_eleve[$matiere_id][$eleve_id] = round( 50 * ( ($nb_acquis*2 + $nb_voie_acquis) / $nb_scores ) ,0);
					$tab_infos_validations_eleve[$matiere_id][$eleve_id]       = $nb_acquis.'A '. $nb_voie_acquis.'VA '. $nb_non_acquis.'NA';
				}
				else
				{
					$tab_pourcentage_validations_eleve[$matiere_id][$eleve_id] = false;
					$tab_infos_validations_eleve[$matiere_id][$eleve_id]       = false;
				}
			}
		}
	}
}

/*
	On renseigne (uniquement utile pour le tableau de synthèse) :
	$tab_moyenne_scores_competence[$competence_id]
	$tab_pourcentage_validations_competence[$competence_id]
*/

if(in_array('synthese',$tab_type))
{
	// Pour chaque item...
	foreach($tab_liste_comp as $competence_id)
	{
		$tableau_score_filtre = array_filter($tab_score_competence_eleve[$competence_id],'non_nul');
		$nb_scores = count( $tableau_score_filtre );
		if($nb_scores)
		{
			$somme_scores = array_sum($tableau_score_filtre);
			$nb_acquis      = count( array_filter($tableau_score_filtre,'acquis') );
			$nb_non_acquis  = count( array_filter($tableau_score_filtre,'non_acquis') );
			$nb_voie_acquis = $nb_scores - $nb_acquis - $nb_non_acquis;
			$tab_moyenne_scores_competence[$competence_id]          = round($somme_scores/$nb_scores,0);
			$tab_pourcentage_validations_competence[$competence_id] = round( 50 * ( ($nb_acquis*2 + $nb_voie_acquis) / $nb_scores ) ,0);
		}
		else
		{
			$tab_moyenne_scores_competence[$competence_id]          = false;
			$tab_pourcentage_validations_competence[$competence_id] = false;
		}
	}
}

/*
	On renseigne (utile pour le tableau de synthèse et le bulletin) :
	$moyenne_moyenne_scores
	$moyenne_pourcentage_validations
*/
/*
	on pourrait calculer de 2 façons chacune des deux valeurs...
	pour la moyenne des moyennes obtenues par élève : c'est simple car les coefs ont déjà été pris en compte dans le calcul pour chaque élève
	pour la moyenne des moyennes obtenues par item : c'est compliqué car il faudrait repondérer par les coefs éventuels de chaque item
	donc la 1ère technique a été retenue, à défaut d'essayer de calculer les deux et d'en faire la moyenne ;-)
*/

if( (in_array('synthese',$tab_type)) || (in_array('bulletin',$tab_type)) )
{
	// $moyenne_moyenne_scores
	$somme  = array_sum($tab_moyenne_scores_eleve[$matiere_id]);
	$nombre = count( array_filter($tab_moyenne_scores_eleve[$matiere_id],'non_nul') );
	$moyenne_moyenne_scores = ($nombre) ? round($somme/$nombre,0) : false;
	// $moyenne_pourcentage_validations
	$somme  = array_sum($tab_pourcentage_validations_eleve[$matiere_id]);
	$nombre = count( array_filter($tab_pourcentage_validations_eleve[$matiere_id],'non_nul') );
	$moyenne_pourcentage_validations = ($nombre) ? round($somme/$nombre,0) : false;
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Elaboration du bilan individuel, disciplinaire ou transdisciplinaire, en HTML et PDF
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

if(in_array('individuel',$tab_type))
{
	$releve_html_individuel  = '<h1>Bilan '.$tab_titre[$format].'</h1>';
	if($texte_periode)
	{
		$releve_html_individuel .= '<h2>'.html($texte_periode).'</h2>';
	}
	// Appel de la classe et définition de qqs variables supplémentaires pour la mise en page PDF
	$releve_pdf = new PDF($orientation,$marge_min,$couleur);
	if($format=='matiere')      {$releve_pdf->bilan_periode_individuel_initialiser($cases_nb,$cases_largeur,$cases_hauteur,$lignes_nb=$competence_nb+$aff_bilan_ms+$aff_bilan_pv,$new_page=true);}
	if($format=='multimatiere') {$releve_pdf->bilan_periode_individuel_initialiser($cases_nb,$cases_largeur,$cases_hauteur,$lignes_nb=0,$new_page=false);}
	if($format=='selection')    {$releve_pdf->bilan_periode_individuel_initialiser($cases_nb,$cases_largeur,$cases_hauteur,$lignes_nb=0,$new_page=false);}
	$bilan_colspan = $cases_nb + 2 ;
	// Pour chaque élève...
	foreach($tab_eleve as $tab)
	{
		extract($tab);	// $eleve_id $eleve_nom $eleve_prenom $eleve_id_gepi
		// Si cet élève a été évalué...
		if(isset($tab_eval[$eleve_id]))
		{
			if($format=='matiere')      {$releve_pdf->bilan_periode_individuel_entete($matiere_nom,$texte_periode,$groupe_nom,$eleve_nom,$eleve_prenom);}
			if($format=='multimatiere') {$releve_pdf->bilan_periode_individuel_entete_transdisciplinaire_principal($tab_titre[$format],$texte_periode,$groupe_nom,$eleve_nom,$eleve_prenom);}
			if($format=='selection')    {$releve_pdf->bilan_periode_individuel_entete_transdisciplinaire_principal($tab_titre[$format],false,$groupe_nom,$eleve_nom,$eleve_prenom);}
			// Intitulé
			$releve_html_individuel .= '<hr /><h2>'.html($groupe_nom).' - '.html($eleve_nom).' '.html($eleve_prenom).'</h2>';
			// Pour chaque matiere...
			foreach($tab_matiere as $matiere_id => $matiere_nom)
			{
				// Si cet élève a été évalué dans cette matière...
				if(isset($tab_eval[$eleve_id][$matiere_id]))
				{
					if( ($format=='multimatiere') || ($format=='selection') )
					{
						$competence_matiere_nb = count($tab_eval[$eleve_id][$matiere_id]);
						$releve_pdf->bilan_periode_individuel_entete_transdisciplinaire_secondaire($matiere_nom,$lignes_nb=$competence_matiere_nb+$aff_bilan_ms+$aff_bilan_pv);
					}
					$releve_html_individuel .= '<h3>'.html($matiere_nom).'</h3>';
					// On passe au tableau
					$releve_html_table_head = '<thead><tr><th>Ref.</th><th>Nom de l\'item</th>';
					for($num_case=0;$num_case<$cases_nb;$num_case++)
					{
						$releve_html_table_head .= '<th></th>';	// Pas de colspan sinon pb avec le tri
					}
					$releve_html_table_head .= '<th>score</th></tr></thead>';
					$releve_html_table_body = '<tbody>';
					// Pour chaque item...
					foreach($tab_eval[$eleve_id][$matiere_id] as $competence_id => $tab_devoirs)
					{
						extract($tab_competence[$competence_id][0]);	// $competence_ref $competence_nom $competence_coef $competence_socle $competence_lien $calcul_methode $calcul_limite
						// cases référence et nom
						if($aff_coef)
						{
							$texte_coef = '['.$competence_coef.'] ';
						}
						if($aff_socle)
						{
							$texte_socle = ($competence_socle) ? '[S] ' : '[–] ';
						}
						if($aff_lien)
						{
							$texte_lien_avant = ($competence_lien) ? '<a class="lien_ext" href="'.html($competence_lien).'">' : '';
							$texte_lien_apres = ($competence_lien) ? '</a>' : '';
						}
						$texte_demande_eval = ( ($_SESSION['PROFIL']=='eleve') && ($_SESSION['ELEVE_DEMANDES']>0) ) ? '<q class="demander_add" lang="ids_'.$eleve_id.'_'.$matiere_id.'_'.$competence_id.'_'.$tab_score_eleve_competence[$eleve_id][$matiere_id][$competence_id].'" title="Ajouter aux demandes d\'évaluations."></q>' : '' ;
						$releve_html_table_body .= '<tr><td>'.$competence_ref.'</td><td>'.$texte_coef.$texte_socle.$texte_lien_avant.html($competence_nom).$texte_lien_apres.$texte_demande_eval.'</td>';
						$releve_pdf->bilan_periode_individuel_competence($competence_ref,$texte_coef.$texte_socle.$competence_nom);
						// cases d'évaluations
						$devoirs_nb = count($tab_devoirs);
						// on passe en revue les cases disponibles et on remplit en fonction des évaluations disponibles
						$decalage = $devoirs_nb - $cases_nb;
						for($i=0;$i<$cases_nb;$i++)
						{
							// on doit remplir une case
							if($decalage<0)
							{
								// il y a moins d'évaluations que de cases à remplir : on met un score dispo ou une case blanche si plus de score dispo
								if($i<$devoirs_nb)
								{
									extract($tab_devoirs[$i]);	// $note $date $info
									$releve_html_table_body .= '<td>'.affich_note_html($note,$date,$info,true).'</td>';
									$releve_pdf->afficher_note_lomer($note);
									$releve_pdf->Cell($cases_largeur,$cases_hauteur,'',1,0,'C',false,'');
								}
								else
								{
									$releve_html_table_body .= '<td>&nbsp;</td>';
									$releve_pdf->Cell($cases_largeur,$cases_hauteur,'',1,0,'C',false,'');
								}
							}
							// il y a plus d'évaluations que de cases à remplir : on ne prend que les dernières (décalage d'indice)
							else
							{
								extract($tab_devoirs[$i+$decalage]);	// $note $date $info
								$releve_html_table_body .= '<td>'.affich_note_html($note,$date,$info,true).'</td>';
								$releve_pdf->afficher_note_lomer($note);
								$releve_pdf->Cell($cases_largeur,$cases_hauteur,'',1,0,'C',false,'');
							}
						}
						// affichage du bilan de l'item
						$releve_html_table_body .= affich_score_html($tab_score_eleve_competence[$eleve_id][$matiere_id][$competence_id],'score');
						$releve_pdf->afficher_score_bilan($tab_score_eleve_competence[$eleve_id][$matiere_id][$competence_id],$br=1);
						$releve_html_table_body .= '</tr>'."\r\n";
					}
					$releve_html_table_body .= '</tbody>';
					$releve_html_table_foot = '';
					// affichage des bilans des scores
					// ... un pour la moyenne des pourcentages d'acquisition
					if( $aff_bilan_ms )
					{
						if($tab_moyenne_scores_eleve[$matiere_id][$eleve_id] !== false)
						{
							$texte_bilan  = $tab_moyenne_scores_eleve[$matiere_id][$eleve_id].'%';
							$texte_bilan .= ($aff_conv_sur20) ? ' soit '.sprintf("%04.1f",$tab_moyenne_scores_eleve[$matiere_id][$eleve_id]/5).'/20' : '' ;
						}
						else
						{
							$texte_bilan = '---';
						}
						$releve_html_table_foot .= '<tr><td class="nu">&nbsp;</td><td colspan="'.$bilan_colspan.'">Moyenne pondérée des scores d\'acquisitions calculés : '.$texte_bilan.'</td><td class="nu"></td></tr>'."\r\n";
						$releve_pdf->bilan_periode_individuel_synthese('Moyenne pondérée des scores d\'acquisitions calculés : '.$texte_bilan);
					}
					// ... un pour le nombre d'items considérés acquis ou pas
					if( $aff_bilan_pv )
					{
						if($tab_pourcentage_validations_eleve[$matiere_id][$eleve_id] !== false)
						{
							$texte_bilan  = '('.$tab_infos_validations_eleve[$matiere_id][$eleve_id].') : '.$tab_pourcentage_validations_eleve[$matiere_id][$eleve_id].'%';
							$texte_bilan .= ($aff_conv_sur20) ? ' soit '.sprintf("%04.1f",$tab_pourcentage_validations_eleve[$matiere_id][$eleve_id]/5).'/20' : '' ;
						}
						else
						{
							$texte_bilan = '---';
						}
						$releve_html_table_foot .= '<tr><td class="nu">&nbsp;</td><td colspan="'.$bilan_colspan.'">Pourcentage d\'items validés '.$texte_bilan.'</td><td class="nu"></td></tr>'."\r\n";
						$releve_pdf->bilan_periode_individuel_synthese('Pourcentage d\'items validés : '.$texte_bilan);
					}
					$releve_html_table_foot = ($releve_html_table_foot) ? '<tfoot>'.$releve_html_table_foot.'</tfoot>'."\r\n" : '';
					$releve_html_individuel .= '<table id="table'.$eleve_id.'x'.$matiere_id.'" class="bilan">'.$releve_html_table_head.$releve_html_table_foot.$releve_html_table_body.'</table><p />';
					$releve_html_individuel .= '<script type="text/javascript">$("#table'.$eleve_id.'x'.$matiere_id.'").tablesorter();</script>';
					$releve_pdf->bilan_periode_individuel_interligne();
				}
			}
		}
	}
	// On enregistre les sorties HTML et PDF
	file_put_contents($dossier.$fichier_lien.'_individuel.html',$releve_html_individuel);
	$releve_pdf->Output($dossier.$fichier_lien.'_individuel.pdf','F');
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Elaboration de la synthèse collective en HTML et PDF
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

if(in_array('synthese',$tab_type))
{
	$releve_html_synthese  = '<h1>Bilan '.$tab_titre[$format].'</h1>';
	$releve_html_synthese .= '<h2>'.html($matiere_nom.' - '.$groupe_nom).'</h2>';
	if($texte_periode)
	{
		$releve_html_synthese .= '<h2>'.html($texte_periode).'</h2>';
	}
	// Appel de la classe et redéfinition de qqs variables supplémentaires pour la mise en page PDF ; on impose l'orientation paysage
	$releve_pdf = new PDF('landscape',$marge_min,$couleur);
	$releve_pdf->bilan_periode_synthese_initialiser($eleve_nb,$competence_nb);

	if($format=='matiere')   {$releve_pdf->bilan_periode_synthese_entete($tab_titre[$format],$matiere_nom,$texte_periode,$groupe_nom);}
	if($format=='selection') {$releve_pdf->bilan_periode_synthese_entete($tab_titre[$format],$matiere_nom,false,$groupe_nom);}

			
	// 1ère ligne commune aux deux tableaux
	$releve_pdf->Cell($releve_pdf->eleve_largeur , $releve_pdf->cases_hauteur , '' , 0 , 0 , 'C' , false , '');
	$releve_pdf->choisir_couleur_fond('gris_clair');
	$releve_html_table_head = '<thead><tr><th>Elève</th>';
	// Pour chaque item...
	foreach($tab_liste_comp as $competence_id)
	{
		$memo_x = $releve_pdf->GetX();
		$memo_y = $releve_pdf->GetY();
		list($ref_matiere,$ref_suite) = explode('.',$tab_competence[$competence_id][0]['competence_ref'],2);
		$releve_pdf->SetFont('Arial' , '' , $releve_pdf->taille_police-1);
		$releve_pdf->Cell($releve_pdf->cases_largeur , $releve_pdf->cases_hauteur/2 , pdf($ref_matiere) , 0 , 2 , 'C' , true , '');
		$releve_pdf->Cell($releve_pdf->cases_largeur , $releve_pdf->cases_hauteur/2 , pdf($ref_suite) , 0 , 2 , 'C' , true , '');
		$releve_pdf->SetFont('Arial' , '' , $releve_pdf->taille_police);
		$releve_pdf->SetXY($memo_x , $memo_y);
		$releve_pdf->Cell($releve_pdf->cases_largeur , $releve_pdf->cases_hauteur , '' , 1 , 0 , 'C' , false , '');
		$releve_html_table_head .= '<th title="'.html($tab_competence[$competence_id][0]['competence_nom']).'">'.html($tab_competence[$competence_id][0]['competence_ref']).'</th>';
	}
	$releve_pdf->SetX( $releve_pdf->GetX()+2 );
	$releve_pdf->choisir_couleur_fond('gris_fonce');
	$releve_pdf->Cell($releve_pdf->cases_largeur , $releve_pdf->cases_hauteur , '[ * ]'  , 1 , 0 , 'C' , true , '');
	$releve_pdf->Cell($releve_pdf->cases_largeur , $releve_pdf->cases_hauteur , '[ ** ]' , 1 , 1 , 'C' , true , '');
	$releve_html_table_head .= '<th class="nu">&nbsp;</th><th>[ * ]</th><th>[ ** ]</th></tr></thead>'."\r\n";
	// lignes suivantes
	$releve_html_table_body1 = '';
	$releve_html_table_body2 = '';
	// Pour chaque élève...
	foreach($tab_eleve as $tab)
	{
		extract($tab);	// $eleve_id $eleve_nom $eleve_prenom $eleve_id_gepi
		$releve_pdf->choisir_couleur_fond('gris_clair');
		$releve_pdf->Cell($releve_pdf->eleve_largeur , $releve_pdf->cases_hauteur , pdf($eleve_nom.' '.$eleve_prenom) , 1 , 0 , 'L' , true , '');
		$releve_html_table_body1 .= '<tr><td>'.html($eleve_nom.' '.$eleve_prenom).'</td>';
		$releve_html_table_body2 .= '<tr><td>'.html($eleve_nom.' '.$eleve_prenom).'</td>';
		// Pour chaque item...
		foreach($tab_liste_comp as $competence_id)
		{
			$score = (isset($tab_score_eleve_competence[$eleve_id][$matiere_id][$competence_id])) ? $tab_score_eleve_competence[$eleve_id][$matiere_id][$competence_id] : false ;
			$releve_pdf->afficher_score_bilan($score,$br=0);
			$releve_html_table_body1 .= affich_score_html($score,'score');
			$releve_html_table_body2 .= affich_score_html($score,'etat');
		}
		$releve_pdf->bilan_periode_synthese_pourcentages($tab_moyenne_scores_eleve[$matiere_id][$eleve_id],$tab_pourcentage_validations_eleve[$matiere_id][$eleve_id],false,true);
		$releve_html_table_body1 .= '<td class="nu">&nbsp;</td>'.affich_score_html($tab_moyenne_scores_eleve[$matiere_id][$eleve_id],'score','%').affich_score_html($tab_pourcentage_validations_eleve[$matiere_id][$eleve_id],'score','%').'</tr>'."\r\n";
		$releve_html_table_body2 .= '<td class="nu">&nbsp;</td>'.affich_score_html($tab_moyenne_scores_eleve[$matiere_id][$eleve_id],'etat','%').affich_score_html($tab_pourcentage_validations_eleve[$matiere_id][$eleve_id],'etat','%').'</tr>'."\r\n";
	}
	$releve_html_table_body1 = '<tbody>'.$releve_html_table_body1.'</tbody>'."\r\n";
	$releve_html_table_body2 = '<tbody>'.$releve_html_table_body2.'</tbody>'."\r\n";
	// dernière ligne (doublée)
	$colspan = $competence_nb+4;
	$memo_y = $releve_pdf->GetY()+2;
	$releve_pdf->SetY( $memo_y );
	$releve_pdf->choisir_couleur_fond('gris_fonce');
	$releve_pdf->Cell($releve_pdf->eleve_largeur , $releve_pdf->cases_hauteur , 'moyenne scores [*]' , 1 , 2 , 'C' , true , '');
	$releve_pdf->Cell($releve_pdf->eleve_largeur , $releve_pdf->cases_hauteur , '% validations [**]' , 1 , 0 , 'C' , true , '');
	$releve_html_table_foot1 = '<tr><th>moyenne scores [*]</th>';
	$releve_html_table_foot2 = '<tr><th>% validations [**]</th>';
	$memo_x = $releve_pdf->GetX();
	$releve_pdf->SetXY($memo_x,$memo_y);
	// Pour chaque item...
	foreach($tab_liste_comp as $competence_id)
	{
		$releve_pdf->bilan_periode_synthese_pourcentages($tab_moyenne_scores_competence[$competence_id],$tab_pourcentage_validations_competence[$competence_id],true,false);
		$releve_html_table_foot1 .= affich_score_html($tab_moyenne_scores_competence[$competence_id],'score','%');
		$releve_html_table_foot2 .= affich_score_html($tab_pourcentage_validations_competence[$competence_id],'score','%');
	}
	// les deux dernières cases (moyenne des moyennes)
	$releve_pdf->bilan_periode_synthese_pourcentages($moyenne_moyenne_scores,$moyenne_pourcentage_validations,true,true);
	$releve_html_table_foot1 .= '<th class="nu">&nbsp;</th>'.affich_score_html($moyenne_moyenne_scores,'score','%').'<th class="nu">&nbsp;</th></tr>';
	$releve_html_table_foot2 .= '<th class="nu">&nbsp;</th><th class="nu">&nbsp;</th>'.affich_score_html($moyenne_pourcentage_validations,'score','%').'</tr>';
	$releve_html_table_foot = '<tfoot><tr><td class="nu" colspan="'.$colspan.'" style="font-size:0;height:9px">&nbsp;</td></tr>'.$releve_html_table_foot1.$releve_html_table_foot2.'</tfoot>'."\r\n";
	$num_hide = $competence_nb+1;
	// pour la sortie HTML, on peut placer les tableaux de synthèse au début
	$releve_html_synthese .= '<hr /><h2>SYNTHESE - Colonnes triables par score (intérêt pour un tri simple)</h2>';
	$releve_html_synthese .= '<table id="table_s1" class="bilan_synthese">'.$releve_html_table_head.$releve_html_table_foot.$releve_html_table_body1.'</table>';
	$releve_html_synthese .= '<script type="text/javascript">$("#table_s1").tablesorter({ headers:{'.$num_hide.':{sorter:false}} });</script>'; // Non placé dans le fichier js car mettre une valeur à la place d'une variable pour $num_hide ne fonctionne pas
	$releve_html_synthese .= '<hr /><h2>SYNTHESE - Colonnes triables par état de validation (intérêt pour un tri multiple)</h2></h2>';
	$releve_html_synthese .= '<table id="table_s2" class="bilan_synthese">'.$releve_html_table_head.$releve_html_table_foot.$releve_html_table_body2.'</table>';
	$releve_html_synthese .= '<script type="text/javascript">$("#table_s2").tablesorter({ headers:{'.$num_hide.':{sorter:false}} });</script>'; // Non placé dans le fichier js car mettre une valeur à la place d'une variable pour $num_hide ne fonctionne pas
	// On enregistre les sorties HTML et PDF
	file_put_contents($dossier.$fichier_lien.'_synthese.html',$releve_html_synthese);
	$releve_pdf->Output($dossier.$fichier_lien.'_synthese.pdf','F');
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Elaboration du bulletin (moyenne & appréciation) en HTML et CSV pour GEPI
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

if(in_array('bulletin',$tab_type))
{
	/*
	$tab_bad[] = '0NA'; $tab_bon[] = '0 non acquise';
	$tab_bad[] = '1NA'; $tab_bon[] = '1 non acquise';
	$tab_bad[] =  'NA'; $tab_bon[] = ' non acquises';
	$tab_bad[] = '0VA'; $tab_bon[] = '0 partiellement acquise ;';
	$tab_bad[] = '1VA'; $tab_bon[] = '1 partiellement acquise ;';
	$tab_bad[] =  'VA'; $tab_bon[] = ' partiellement acquises ;';
	$tab_bad[] =  '0A'; $tab_bon[] = '0 acquise ;';
	$tab_bad[] =  '1A'; $tab_bon[] = '1 acquise ;';
	$tab_bad[] =   'A'; $tab_bon[] = ' acquises ;';
	// pour str_replace($tab_bad,$tab_bon,$tab_infos_validations_eleve[$matiere_id][$eleve_id])
	*/
	$bulletin_body = '';
	$bulletin_csv_gepi = 'GEPI_IDENTIFIANT;NOTE;APPRECIATION'."\r\n";	// Ajout du préfixe 'GEPI_' pour éviter un bug avec M$ Excel « SYLK : Format de fichier non valide » (http://support.microsoft.com/kb/215591/fr)
	// Pour chaque élève...
	foreach($tab_eleve as $tab)
	{
		extract($tab);	// $eleve_id $eleve_nom $eleve_prenom $eleve_id_gepi
		$note         = ($tab_moyenne_scores_eleve[$matiere_id][$eleve_id] !== false)          ? sprintf("%04.1f",$tab_moyenne_scores_eleve[$matiere_id][$eleve_id]/5)                                                                             : '-' ;
		$appreciation = ($tab_pourcentage_validations_eleve[$matiere_id][$eleve_id] !== false) ? $tab_pourcentage_validations_eleve[$matiere_id][$eleve_id].'% d\'items validés ('.$tab_infos_validations_eleve[$matiere_id][$eleve_id].')' : '-' ;
		$bulletin_body     .= '<tr><th>'.html($eleve_nom.' '.$eleve_prenom).'</th><td>'.$note.'</td><td>'.$appreciation.'</td></tr>'."\r\n";
		// Pour gépi je remplace le point décimal par une virgule sinon le tableur convertit en date...
		$bulletin_csv_gepi .= $eleve_id_gepi.';'.str_replace('.',',',$note).';'.$appreciation."\r\n";
	}
	$bulletin_head  = '<thead><tr><th>Elève</th><th>Moyenne pondérée sur 20<br />(des scores d\'acquisitions)</th><th>Élément d\'appréciation<br />(pourcentage d\'items validés)</th></tr></thead>'."\r\n";
	$bulletin_body  = '<tbody>'."\r\n".$bulletin_body.'</tbody>'."\r\n";
	$bulletin_foot  = '<tfoot><tr><th>Moyenne sur 20</th><th>'.sprintf("%04.1f",$moyenne_moyenne_scores/5).'</th><th>'.$moyenne_pourcentage_validations.'% d\'items validés</th></tr></tfoot>'."\r\n";
	$bulletin_html  = '<h1>Bilan disciplinaire</h1>';
	$bulletin_html .= '<h2>'.html($matiere_nom.' - '.$groupe_nom).'</h2>';
	$bulletin_html .= '<h2>Du '.$date_debut.' au '.$date_fin.$date_complement.'</h2>';
	$bulletin_html .= '<h2>Tableau de notes sur 20</h2>';
	$bulletin_html .= '<table id="export20">'."\r\n".$bulletin_head.$bulletin_foot.$bulletin_body.'</table>'."\r\n";
	$bulletin_html .= '<script type="text/javascript">$("#export20").tablesorter({ headers:{2:{sorter:false}} });</script>';
	// On enregistre la sortie HTML et CSV
	file_put_contents($dossier.$fichier_lien.'_bulletin.html',$bulletin_html);
	file_put_contents($dossier.$fichier_lien.'_bulletin.csv',utf8_decode($bulletin_csv_gepi));
}

?>