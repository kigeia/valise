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
$TITRE = "Référentiels partagés sur le serveur communautaire";
?>

<script type="text/javascript">
	var url_debut                  = "<?php echo html(SERVEUR_COMMUNAUTAIRE) ?>";
	var structure_id               = "<?php echo $_SESSION['STRUCTURE_ID'] ?>";
	var structure_key              = "<?php echo $_SESSION['STRUCTURE_KEY'] ?>";
</script>

<ul class="puce">
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=referentiel_organisation_competences">DOC : Organisation des compétences dans les référentiels.</a></span></li>
	<li><span class="manuel"><a class="pop_up" href="./aide.php?fichier=referentiel_structure">DOC : Structure d'un référentiel.</a></span></li>
</ul>

<hr />

<?php
if( (!$_SESSION['STRUCTURE_ID']) || (!$_SESSION['STRUCTURE_KEY']) )
{
	echo'<p><label for="rien" class="erreur">Pour pouvoir effectuer la recherche d\'un référentiel partagé sur le serveur communautaire, un administrateur doit identifier cette installation de SACoche.</label></p>';
}
else
{
	// La balise object fonctionne sauf avec Internet Explorer qui n'affiche rien si on appelle une page provenant d'un autre domaine
	// Par ailleurs, il faut mettre une adresse valise au départ sous peine de se voir retirer la balise par son substitut (pour Opéra).
	require_once('./_inc/fonction_css_browser_selector.php');
	$chaine_detection = css_browser_selector();
	if(substr($chaine_detection,0,3)!='ie ')
	{
		$balise   = 'object';
		$attribut = 'data';
	}
	else
	{
		$balise   = 'iframe';
		$attribut = 'src';
	}
	echo'<p id="object_container"><'.$balise.' id="cadre" '.$attribut.'="./_img/ajax/ajax_loader.gif" type="text/html" height="350px" style="width:100%;border:none;"><img src="./_img/ajax/ajax_loader.gif" alt="Chargement..." /> Appel au serveur communautaire...</'.$balise.'></p>';
}
?>


