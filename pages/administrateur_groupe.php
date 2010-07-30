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
$TITRE = "Groupes";
?>

<div class="hc">
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=gestion">Groupes (gestion).</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=classe_groupe">Périodes &amp; classes / groupes.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=eleve">Élèves &amp; groupes.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=professeur">Professeurs &amp; groupes.</a>
</div>

<hr />

<?php
if(($SECTION=='eleve')||($SECTION=='professeur'))
{
	// échanger $PAGE et $SECTION pour piocher le bon fichier sans avoir besoin de le dupliquer, tout en gardant ce menu
	$PAGE    = 'administrateur_'.$SECTION;
	$SECTION = 'groupe';
}
elseif($SECTION=='classe-groupe')
{
	// remplacer $PAGE pour piocher le bon fichier sans avoir besoin de le dupliquer, tout en gardant ce menu
	$PAGE = 'administrateur_periode';
}
// Afficher la bonne page et appeler le bon js / ajax par la suite
$fichier_section = './pages/'.$PAGE.'_'.$SECTION.'.php';
if(is_file($fichier_section))
{
	require($fichier_section);
	$PAGE = $PAGE.'_'.$SECTION ;
}
else
{
	echo'<p><span class="astuce">Choisissez une rubrique ci-dessus...</span></p>';
	echo'<p><span class="manuel"><a class="pop_up" href="'.SERVEUR_DOCUMENTAIRE.'?fichier=support_administrateur__gestion_groupes">DOC : Gestion des groupes</a></span></p>';
}
?>