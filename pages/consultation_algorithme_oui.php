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
?>

<?php
$tab_options = array();
$tab_options['geometrique']  = 'Coefficient multiplié par 2 à chaque devoir (sur les 5 dernières saisies maximum).';
$tab_options['arithmetique'] = 'Coefficient augmenté de 1 à chaque devoir (sur les 9 dernières saisies maximum).';;
$tab_options['classique']    = 'Moyenne classique non pondérée (comptabiliser autant chaque saisie).';
$tab_options['bestof1']      = 'Utiliser uniquement la meilleure saisie (une seule réussite suffit donc).';
$tab_options['bestof2']      = 'Utiliser seulement les 2 meilleures saisies (dont on effectue la moyenne).';
$tab_options['bestof3']      = 'Utiliser seulement les 3 meilleures saisies (dont on effectue la moyenne).';
$options_methode = '';
foreach($tab_options as $value => $texte)
{
	$selected = ($value==$_SESSION['CALCUL_METHODE']) ? ' selected' : '' ;
	$options_methode .= '<option value="'.$value.'"'.$selected.'>'.$texte.'</option>';
}

$tab_options = array(0,1,2,3,4,5,6,7,8,9,10,15,20,30,40,50);
$options_limite = '';
foreach($tab_options as $value)
{
	if($value>1)
	{
		$texte = 'Prendre en compte uniquement les '.$value.' dernières évaluations.';
	}
	else
	{
		$texte = ($value) ? 'Prendre en compte uniquement la dernière évaluation.' : 'Prendre en compte toutes les évaluations.' ;
	}
	$selected = ($value==$_SESSION['CALCUL_LIMITE']) ? ' selected' : '' ;
	$options_limite .= '<option value="'.$value.'"'.$selected.'>'.$texte.'</option>';
}

// Indication des profils ayant accès à cette page
require_once('./_inc/tableau_profils.php'); // Charge $tab_profil_libelle[$profil][court|long][1|2]
$tab_profils = array('directeur','professeur','eleve','parent');
$str_objet = $_SESSION['DROIT_VOIR_ALGORITHME'];
foreach($tab_profils as $profil)
{
	$str_objet = str_replace($profil,$tab_profil_libelle[$profil]['long'][2],$str_objet);
}
$texte = ($str_objet=='') ? 'aucun' : ( (strpos($str_objet,',')===false) ? 'uniquement les '.$str_objet : str_replace(',',' + ',$str_objet) ) ;
?>

<ul class="puce">
	<li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=referentiels_socle__calcul_scores_etats_acquisitions">DOC : Calcul des scores et des états d'acquisitions.</a></span></li>
	<li><span class="astuce">Profils autorisés par les administrateurs : <span class="u"><?php echo $texte ?></span>.</span></li>
</ul>
<hr />

<form action="#" method="post" id="form_input">
	<table>
	<thead>
		<tr><th>
			Valeur d'un code (sur 100)
		</th><th>
			Méthode de calcul par défaut (modifiable pour chaque référentiel)
		</th><th>
			Seuil d'aquisition (sur 100)
		</th></tr>
	</thead>
	<tbody>
		<tr><td>
			<label class="tab mini" for="valeurRR">saisie <img alt="" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/RR.gif" /> :</label><input type="text" size="3" id="valeurRR" name="valeurRR" value="<?php echo $_SESSION['CALCUL_VALEUR']['RR'] ?>" /><br />
			<label class="tab mini" for="valeurR" >saisie <img alt="" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/R.gif" />  :</label><input type="text" size="3" id="valeurR"  name="valeurR"  value="<?php echo $_SESSION['CALCUL_VALEUR']['R']  ?>" /><br />
			<label class="tab mini" for="valeurV" >saisie <img alt="" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/V.gif" />  :</label><input type="text" size="3" id="valeurV"  name="valeurV"  value="<?php echo $_SESSION['CALCUL_VALEUR']['V']  ?>" /><br />
			<label class="tab mini" for="valeurVV">saisie <img alt="" src="./_img/note/<?php echo $_SESSION['NOTE_DOSSIER'] ?>/h/VV.gif" /> :</label><input type="text" size="3" id="valeurVV" name="valeurVV" value="<?php echo $_SESSION['CALCUL_VALEUR']['VV'] ?>" /><br />
		</td><td>
			&nbsp;<br />
			<select id="f_methode" name="f_methode"><?php echo $options_methode ?></select><br />
			<select id="f_limite" name="f_limite"><?php echo $options_limite ?></select><br />
		</td><td>
			&nbsp;<br />
			<label class="tab mini" for="seuilR">non acquis :</label>&lt; <input type="text" size="3" id="seuilR" name="seuilR" value="<?php echo $_SESSION['CALCUL_SEUIL']['R'] ?>" /><br />
			<label class="tab mini" for="seuilV">acquis :</label>&gt; <input type="text" size="3" id="seuilV" name="seuilV" value="<?php echo $_SESSION['CALCUL_SEUIL']['V'] ?>" /><br />
		</td></tr>
	</tbody>
	</table>
	<p>
		<input type="hidden" id="action" name="action" value="calculer" />
		<button id="initialiser_defaut" type="button" class="retourner">Mettre les valeurs par défaut.</button>
		<button id="initialiser_etablissement" type="button" class="retourner">Mettre les valeurs de l'établissement.</button>
		<button id="calculer" type="button" class="actualiser">Simuler avec ces valeurs.</button>
		<label id="ajax_msg">&nbsp;</label>
	</p>
</form>

<hr />

<div id="bilan">
<table class="simulation">
	<thead>
		<tr>
			<th colspan="2">Cas de 1 devoir</th>
			<th></th>
			<th colspan="3">Cas de 2 devoirs</th>
			<th></th>
			<th colspan="4">Cas de 3 devoirs</th>
			<th></th>
			<th colspan="5">Cas de 4 devoirs</th>
		</tr>
		<tr>
			<th>unique</th><th>score</th>
			<th></th>
			<th>ancien</th><th>récent</th><th>score</th>
			<th></th>
			<th>ancien</th><th>médian</th><th>récent</th><th>score</th>
			<th></th>
			<th>très ancien</th><th>ancien</th><th>récent</th><th>très récent</th><th>score</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="17"></td>
		</tr>
	</tbody>
</table>

</div>

