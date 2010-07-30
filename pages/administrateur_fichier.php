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
$TITRE = "Import / Export";
?>

<div class="hc">
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=import_eleve_classe">Importer élèves &amp; classes.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=import_professeur_directeur">Importer professeurs &amp; directeurs.</a>	<br />
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=init_loginmdp_eleve">Initialiser identifiants élèves.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=init_loginmdp_professeur_directeur">Initialiser identifiants professeurs &amp; directeurs.</a>	<br />
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=force_loginmdp">Imposer identifiants SACoche.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=import_id_ent">Importer identifiant ENT.</a>	||
	<a href="./index.php?page=<?php echo $PAGE ?>&amp;section=import_id_gepi">Importer identifiant Gepi.</a>
</div>

<hr />

<?php
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
}
?>