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
$TITRE = "Grilles de compétences sur un niveau";
?>
<h2>Introduction</h2>
<p>
	Les professeurs, les élèves et les personnels de direction, peuvent générer des grilles de compétences portant sur une matière et un niveau donné.<br />
	Ces grilles peuvent être imprimées vierges (pour distribution aux élèves en début d'année), ou comportant les résultats des dernières évaluations (en cas de perte en cours d'année).
</p>
<ul class="puce">
	<li>Se connecter avec son compte.</li>
	<li>Dans <em>[Relevés de compétences]</em> menu <em>[Grilles sur un niveau]</em>.</li>
</ul>

<h2>Réglages</h2>
<p>Voici différents paramètres accessibles :</p>
<ul class="puce">
	<li>la matière et le niveau souhaité</li>
	<li>le ou les élèves concernés (sauf espace élève)</li>
	<li>coefficients associés aux items (affichage ou pas)</li>
	<li>appartenance au socle commun des items (affichage ou pas)</li>
	<li>liens de remédiation associés aux items (affichage ou pas)</li>
	<li>fiche générique ou fiche au nom de l'élève (sauf espace élève)</li>
	<li>fiche vierge ou fiche avec les notes des dernières évaluations</li>
</ul>
<p>Il existe des options de mise en page supplémentaires pour la sortie <em>pdf</em> :</p>
<ul class="puce">
	<li>format portrait ou paysage</li>
	<li>impression en couleur ou en noir et blanc</li>
	<li>marges minimales de 5mm / 10mm / 15mm</li>
	<li>nombre de cases pour y reporter les évaluations (de 1 à 10)</li>
	<li>largeur des cases (de 4mm à 16mm)</li>
	<li>hauteur des lignes (de 4mm à 16mm)</li>
</ul>
<p>
	Générer une sortie <em>pdf</em>, puis modifier ces valeurs et recommencer jusqu'à obtenir une mise en page satisfaisante (en fonction du nombre d'items et de la longueur de leurs intitulés).<br />
	<span class="astuce">Remarque : les réglages d'un utilisateur sont enregistrés.</span>
</p>

<h2>Grilles générées</h2>
<ul class="puce">
	<li>Le format <em>HTML</em> permet de pouvoir cliquer sur les liens de remédiation, et apporte des informations supplémentaires au survol d'un résultat avec la souris (nom et date de l'évaluation).</li>
	<li>Le format <em>PDF</em> permet d'obtenir un fichier adapté à l'impression, proprement mis en page.</li>
</ul>
