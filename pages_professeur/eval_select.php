<?php
/**
 * @version $Id: eval_select.php 8 2009-10-30 20:56:02Z thomas $
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
$TITRE = "Évaluer un ou plusieurs élèves sélectionnés";
?>

<?php
// Dates par défaut de début et de fin
$annee_debut = (date('n')>8) ? date('Y') : date('Y')-1 ;
$date_debut = '01/09/'.$annee_debut;
$date_fin   = date("d/m/Y");
?>

<span class="manuel"><a class="pop_up" href="./aide.php?fichier=evaluations_gestion">DOC : Gestion des évaluations.</a></span>
<div class="danger">Une évaluation dont la saisie a commencé ne devrait pas voir ses élèves ou ses items modifiés (sinon vous n'aurez plus accès à certaines données) !</div>
<hr />

<form action="" id="form0"><fieldset>
	<label class="tab" for="f_aff_periode">Période :</label>
		du <input id="f_date_debut" name="f_date_debut" size="9" type="text" value="<?php echo $date_debut ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
		au <input id="f_date_fin" name="f_date_fin" size="9" type="text" value="<?php echo $date_fin ?>" /><q class="date_calendrier" title="Cliquez sur cette image pour importer une date depuis un calendrier !"></q>
	<br />
	<span class="tab"></span><input type="hidden" name="f_action" value="Afficher_evaluations" /><input type="submit" value="Actualiser l'affichage." /><label id="ajax_msg0">&nbsp;</label>
</fieldset></form>

<form action="" id="form1">
	<hr />
	<table class="form">
		<thead>
			<tr>
				<th>Date</th>
				<th>Élèves</th>
				<th>Description</th>
				<th>Items</th>
				<th class="nu"><q class="ajouter" title="Ajouter une évaluation."></q></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</form>

<script type="text/javascript">var input_date="<?php echo date("d/m/Y") ?>";</script>

<form action="" id="zone_compet" class="hide">
	<div class="hc">
		<a class="valider_compet" href="#"><img alt="Valider" src="./_img/action/action_valider.png" /> Valider ce choix</a><br />
		<a class="annuler_compet" href="#"><img alt="Annuler" src="./_img/action/action_annuler.png" /> Annuler / Retour</a>
	</div>
	<?php
	$tab_matiere    = array();
	$tab_niveau     = array();
	$tab_domaine    = array();
	$tab_theme      = array();
	$tab_competence = array();
	$matiere_id = 0;
	$DB_TAB = select_arborescence_professeur($_SESSION['USER_ID']);
	foreach($DB_TAB as $key => $DB_ROW)
	{
		if($DB_ROW['livret_matiere_id']!=$matiere_id)
		{
			$matiere_id = $DB_ROW['livret_matiere_id'];
			$tab_matiere[$matiere_id] = $DB_ROW['livret_matiere_nom'];
			$niveau_id     = 0;
			$domaine_id    = 0;
			$theme_id      = 0;
			$competence_id = 0;
		}
		if( (!is_null($DB_ROW['livret_niveau_id'])) && ($DB_ROW['livret_niveau_id']!=$niveau_id) )
		{
			$niveau_id = $DB_ROW['livret_niveau_id'];
			$tab_niveau[$matiere_id][$niveau_id] = $DB_ROW['livret_niveau_nom'];
		}
		if( (!is_null($DB_ROW['livret_domaine_id'])) && ($DB_ROW['livret_domaine_id']!=$domaine_id) )
		{
			$domaine_id = $DB_ROW['livret_domaine_id'];
			$tab_domaine[$matiere_id][$niveau_id][$domaine_id] = $DB_ROW['livret_domaine_nom'];
		}
		if( (!is_null($DB_ROW['livret_theme_id'])) && ($DB_ROW['livret_theme_id']!=$theme_id) )
		{
			$theme_id = $DB_ROW['livret_theme_id'];
			$tab_theme[$matiere_id][$niveau_id][$domaine_id][$theme_id] = $DB_ROW['livret_theme_nom'];
		}
		if( (!is_null($DB_ROW['livret_competence_id'])) && ($DB_ROW['livret_competence_id']!=$competence_id) )
		{
			$competence_id = $DB_ROW['livret_competence_id'];
			$texte_socle = ($DB_ROW['livret_socle_id']) ? '[S] ' : '[–] ';
			$tab_competence[$matiere_id][$niveau_id][$domaine_id][$theme_id][$competence_id] = $texte_socle.$DB_ROW['livret_competence_nom'];
		}
	}
	$input_competences = '<ul class="ul_m1">'."\r\n";
	if(count($tab_matiere))
	{
		foreach($tab_matiere as $matiere_id => $matiere_nom)
		{
			$input_competences .= '	<li class="li_m1"><span>'.html($matiere_nom).'</span>'."\r\n";
			$input_competences .= '		<ul class="ul_m2">'."\r\n";
			if(isset($tab_niveau[$matiere_id]))
			{
				foreach($tab_niveau[$matiere_id] as $niveau_id => $niveau_nom)
				{
					$input_competences .= '			<li class="li_m2"><span>'.html($niveau_nom).'</span>'."\r\n";
					$input_competences .= '				<ul class="ul_n1">'."\r\n";
					if(isset($tab_domaine[$matiere_id][$niveau_id]))
					{
						foreach($tab_domaine[$matiere_id][$niveau_id] as $domaine_id => $domaine_nom)
						{
							$input_competences .= '					<li class="li_n1"><span>'.html($domaine_nom).'</span>'."\r\n";
							$input_competences .= '						<ul class="ul_n2">'."\r\n";
							if(isset($tab_theme[$matiere_id][$niveau_id][$domaine_id]))
							{
								foreach($tab_theme[$matiere_id][$niveau_id][$domaine_id] as $theme_id => $theme_nom)
								{
									$input_competences .= '							<li class="li_n2"><span>'.html($theme_nom).'</span>'."\r\n";
									$input_competences .= '								<ul class="ul_n3">'."\r\n";
									if(isset($tab_competence[$matiere_id][$niveau_id][$domaine_id][$theme_id]))
									{
										foreach($tab_competence[$matiere_id][$niveau_id][$domaine_id][$theme_id] as $competence_id => $competence_nom)
										{
											$input_competences .= '									<li class="li_n3"><input id="id_'.$competence_id.'" name="f_competences[]" type="checkbox" value="'.$competence_id.'" /><label for="id_'.$competence_id.'"> '.html($competence_nom).'</label></li>'."\r\n";
										}
									}
									$input_competences .= '								</ul>'."\r\n";
									$input_competences .= '							</li>'."\r\n";
								}
							}
							$input_competences .= '						</ul>'."\r\n";
							$input_competences .= '					</li>'."\r\n";
						}
					}
					$input_competences .= '				</ul>'."\r\n";
					$input_competences .= '			</li>'."\r\n";
				}
			}
			$input_competences .= '		</ul>'."\r\n";
			$input_competences .= '	</li>'."\r\n";
		}
	}
	$input_competences .= '</ul>'."\r\n";
	echo $input_competences;
	?>
</form>

<form action="" id="zone_eleve" class="hide">
	<div class="hc">
		<a class="valider_eleve" href="#"><img alt="Valider" src="./_img/action/action_valider.png" /> Valider ce choix</a><br />
		<a class="annuler_eleve" href="#"><img alt="Annuler" src="./_img/action/action_annuler.png" /> Annuler / Retour</a>
	</div>
	<?php
	$tab_regroupements = array();
	$tab_id = array('classe'=>'','groupe'=>'');
	// Recherche de la liste des classes et des groupes du professeur
	$DB_SQL = 'SELECT * FROM livret_groupe ';
	$DB_SQL.= 'LEFT JOIN livret_jointure_user_groupe USING (livret_structure_id,livret_groupe_id) ';
	$DB_SQL.= 'LEFT JOIN livret_niveau USING (livret_niveau_id) ';
	$DB_SQL.= 'WHERE livret_structure_id=:structure_id AND livret_user_id=:user_id AND livret_groupe_type IN (:type1,:type2) ';
	$DB_SQL.= 'ORDER BY livret_groupe_type ASC, livret_niveau_ordre ASC, livret_groupe_nom ASC';
	$DB_VAR = array(':structure_id'=>$_SESSION['STRUCTURE_ID'],':user_id'=>$_SESSION['USER_ID'],':type1'=>'classe',':type2'=>'groupe');
	$DB_TAB = DB::queryTab(SACOCHE_BD_NAME , $DB_SQL , $DB_VAR);
	foreach($DB_TAB as $key => $DB_ROW)
	{
		$tab_regroupements[$DB_ROW['livret_groupe_id']] = array('nom'=>$DB_ROW['livret_groupe_nom'],'eleve'=>array());
		$tab_id[$DB_ROW['livret_groupe_type']][] = $DB_ROW['livret_groupe_id'];
	}
	// Recherche de la liste des élèves pour chaque classe du professeur
	if(is_array($tab_id['classe']))
	{
		$listing = implode(',',$tab_id['classe']);
		$DB_SQL = 'SELECT * FROM livret_user ';
		$DB_SQL.= 'WHERE livret_structure_id=:structure_id AND livret_user_profil=:profil AND livret_user_statut=:statut AND livret_eleve_classe_id IN ('.$listing.') ';
		$DB_SQL.= 'ORDER BY livret_user_nom ASC, livret_user_prenom ASC';
		$DB_VAR = array(':structure_id'=>$_SESSION['STRUCTURE_ID'],':profil'=>'eleve',':statut'=>1);
		$DB_TAB = DB::queryTab(SACOCHE_BD_NAME , $DB_SQL , $DB_VAR);
		foreach($DB_TAB as $key => $DB_ROW)
		{
			$tab_regroupements[$DB_ROW['livret_eleve_classe_id']]['eleve'][$DB_ROW['livret_user_id']] = $DB_ROW['livret_user_nom'].' '.$DB_ROW['livret_user_prenom'].' ('.$DB_ROW['livret_user_login'].')';
		}
	}
	// Recherche de la liste des élèves pour chaque groupe du professeur
	if(is_array($tab_id['groupe']))
	{
		$listing = implode(',',$tab_id['groupe']);
		$DB_SQL = 'SELECT * FROM livret_user ';
		$DB_SQL.= 'LEFT JOIN livret_jointure_user_groupe USING (livret_structure_id,livret_user_id) ';
		$DB_SQL.= 'WHERE livret_structure_id=:structure_id AND livret_user_profil=:profil AND livret_user_statut=:statut AND livret_groupe_id IN ('.$listing.') ';
		$DB_SQL.= 'ORDER BY livret_user_nom ASC, livret_user_prenom ASC';
		$DB_VAR = array(':structure_id'=>$_SESSION['STRUCTURE_ID'],':profil'=>'eleve',':statut'=>1);
		$DB_TAB = DB::queryTab(SACOCHE_BD_NAME , $DB_SQL , $DB_VAR);
		foreach($DB_TAB as $key => $DB_ROW)
		{
			$tab_regroupements[$DB_ROW['livret_groupe_id']]['eleve'][$DB_ROW['livret_user_id']] = $DB_ROW['livret_user_nom'].' '.$DB_ROW['livret_user_prenom'].' ('.$DB_ROW['livret_user_login'].')';
		}
	}
	// Affichage de la liste des élèves (du professeur) pour chaque classe et groupe
	foreach($tab_regroupements as $groupe_id => $tab_groupe)
	{
		echo'<ul class="ul_m1">'."\r\n";
		echo'	<li class="li_m1"><span>'.html($tab_groupe['nom']).'</span>'."\r\n";
		echo'		<ul class="ul_n3">'."\r\n";
		foreach($tab_groupe['eleve'] as $eleve_id => $eleve_nom)
		{
			// C'est plus compliqué que pour les items car un élève peut appartenir à une classe et plusieurs groupes => id du groupe mélé à l'id et id de l'élève dans l'attribut "lang"
			echo'			<li class="li_n3"><input id="id_'.$eleve_id.'_'.$groupe_id.'" lang="'.$eleve_id.'" name="f_eleves[]" type="checkbox" value="'.$eleve_id.'" /><label for="id_'.$eleve_id.'_'.$groupe_id.'"> '.html($eleve_nom).'</label></li>'."\r\n";
		}
		echo'		</ul>'."\r\n";
		echo'	</li>'."\r\n";
		echo'</ul>'."\r\n";
	}
	?>
</form>

<form action="" id="zone_ordonner" class="hide">
	<p class="hc"><b id="titre_ordonner"></b><br /><label id="msg_ordonner"></label></p>
	<div id="div_ordonner">
	</div>
</form>

<form action="" id="zone_saisir" class="hide">
	<p class="hc"><b id="titre_saisir"></b><br /><label id="msg_saisir"></label></p>
	<table id="table_saisir" class="scor_eval">
		<tbody><tr><td></td></tr></tbody>
	</table>
	<div id="td_souris_container"><div class="td_souris">
		<img alt="RR" src="./_img/note/note_RR.gif" /><img alt="ABS" src="./_img/note/note_ABS.gif" /><br />
		<img alt="R" src="./_img/note/note_R.gif" /><img alt="NN" src="./_img/note/note_NN.gif" /><br />
		<img alt="V" src="./_img/note/note_V.gif" /><img alt="DISP" src="./_img/note/note_DISP.gif" /><br />
		<img alt="VV" src="./_img/note/note_VV.gif" /><img alt="X" src="./_img/note/note_X.gif" />
	</div></div>
	<p>
		<a lang="zone_deport" href="#td_souris_container"><img src="./_img/toggle_plus.gif" alt="" title="Voir / masquer la saisie déportée." class="toggle" /></a> Saisie déportée
		<div id="zone_deport" class="hide">
			<input type="hidden" name="filename" id="filename" value="<?php echo './__tmp/export/saisie_'.$_SESSION['STRUCTURE_ID'].'_'.$_SESSION['USER_ID'].'_'; ?>" />
			<span class="manuel"><a class="pop_up" href="./aide.php?fichier=evaluations_saisie_deportee">DOC : Saisie déportée.</a></span>
			<ul class="puce">
				<li><a id="export_file" class="lien_ext" href="">Récupérer un fichier vierge au format CSV pour une saisie déportée.</a></li>
				<li><input id="import_file" type="button" value="Envoyer un fichier complété au format CSV." /><label id="msg_import">&nbsp;</label></li>
			</ul>
		</div>
	</p>
</form>

<div id="zone_voir" class="hide">
	<p class="hc"><b id="titre_voir"></b><br /><label id="msg_voir"></label></p>
	<table id="table_voir" class="scor_eval">
		<tbody><tr><td></td></tr></tbody>
	</table>
</div>

<?php
// Fabrication des éléments select du formulaire
$tab_cookie = load_cookie_select();
$select_orientation = afficher_select($tab_select_orientation , $select_nom='f_orientation' , $option_first='non' , $selection=$tab_cookie['orientation']   , $optgroup='non');
$select_marge_min   = afficher_select($tab_select_marge_min   , $select_nom='f_marge_min'   , $option_first='non' , $selection=$tab_cookie['marge_min']     , $optgroup='non');
$select_couleur     = afficher_select($tab_select_couleur     , $select_nom='f_couleur'     , $option_first='non' , $selection=$tab_cookie['couleur']       , $optgroup='non');
?>

<form action="" id="zone_imprimer" class="hide"><fieldset>
	<p class="hc"><b id="titre_imprimer"></b><br /><a class="fermer_zone_imprimer" href="#"><img alt="Retourner" src="./_img/action/action_retourner.png" /> Retour</a></p>
	<label class="tab" for="f_valeur">Remplissage :</label><select id="f_valeur" name="f_valeur"><option value="vide">cartouche vierge de tout résultat</option><option value="plein">cartouche avec les résultats des élèves (si saisis)</option></select><br />
	<label class="tab" for="f_detail">Détail :</label><select id="f_detail" name="f_detail"><option value="complet">cartouche avec la dénomination complète de chaque item</option><option value="minimal">cartouche minimal avec uniquement les références des items</option></select><br />
	<div class="toggle">
		<span class="tab"></span><a href="#" class="puce_plus toggle">Afficher plus d'options</a>
	</div>
	<div class="toggle hide">
		<span class="tab"></span><a href="#" class="puce_moins toggle">Afficher moins d'options</a><br />
		<label class="tab" for="f_orientation">Orientation :</label><?php echo $select_orientation ?> en <?php echo $select_couleur ?> avec marges minimales de <?php echo $select_marge_min ?><br />
	</div>
	<span class="tab"></span><input id="f_submit_imprimer" type="button" value="Valider." /><label id="msg_imprimer">&nbsp;</label>
	<p id="zone_imprimer_retour"></p>
</fieldset></form>
