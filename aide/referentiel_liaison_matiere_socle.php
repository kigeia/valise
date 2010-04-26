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
$TITRE = "Liaison matières &amp; socle commun";
?>

	<div class="hc"><img alt="organigramme_enseignements_et_socle_commun_complet" src="./_img/aide/organigramme_enseignements_et_socle_commun_complet.png" /></div>

<h2>Introduction</h2>
<p>
	<em>SACoche</em> permet d'obtenir des bilans relatifs aux enseignements dispensés, et relatifs au socle commun de connaissance et de compétences.<br />
	Les trois paliers officiels du socle, ainsi que leurs contenus, sont déjà dans la base de données de <em>SACoche</em>.<br />
	Au sein de l'établissement, <b>l'administrateur doit sélectionner le ou les paliers concernés</b>, et <b>les coordonnateurs de disciplines doivent relier, lorsqu'il y a lieu, les items de leur arborescence aux items du socle</b>.
	<div class="hc"><img alt="organigramme_enseignements_et_socle_commun_extrait" src="./_img/aide/organigramme_enseignements_et_socle_commun_extrait.png" /></div>
</p>

<h2>Fonctionnement</h2>
<p>
	Pour chaque item du socle, <em>SACoche</em> prend en compte les items de tous les enseignements qui y sont rattachés, il calcule leur état d'acquisition, et il indique le pourcentage d'items validés (le détail des items évalués est disponible dans la version "HTML").<br />
	Pour chaque intitulé du socle (nommés "sections" et "piliers" ci-dessus), <em>SACoche</em> fait de même avec l'ensemble de leur contenu, ce qui permet de cocher les cases correspondantes sur l'attestation de maîtrise du livret scolaire de l'élève.
</p>

<h2>Documents officiels</h2>
<p>
	Voici des publications officielles au format <em>pdf</em> en rapport avec le socle commun :</span>
</p>
<ul class="puce">
	<li><a class="lien_ext" href="./_doc/officiel/socle_attestation_paliers1-2.pdf">Paliers 1&amp;2 - Attestation de maîtrise des connaissances et compétences du socle commun.</a></li>
	<li><a class="lien_ext" href="./_doc/officiel/socle_attestation_palier3_2009_12_03.pdf">Palier 3 - Attestation de maîtrise des connaissances et compétences du socle commun.</a></li>
	<li><a class="lien_ext" href="./_doc/officiel/socle_palier3_grilles_references.pdf">Palier 3 - Grilles de référence pour l'évaluation de la maîtrise des connaissances et compétences du socle commun.</a></li>
	<li><a class="lien_ext" href="./_doc/officiel/socle_palier3_bo_2007.pdf">Palier 3 - Bulletin Officiel avec le contenu de chacun des 7 piliers remis en page.</a></li>
</ul>

<h2>Documentations associées</h2>
<ul class="puce">
	<li><span class="manuel"><a href="./aide.php?fichier=environnement_coordonnateur">DOC : L'environnement professeur coordonnateur.</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=referentiel_organisation_competences">DOC : Organisation des compétences dans les référentiels.</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=referentiel_structure">DOC : Structure d'un référentiel.</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=referentiel_gerer">DOC : Gérer les référentiels.</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=referentiel_modifier">DOC : Modifier le contenu des référentiels.</a></span></li>
	<li><span class="manuel">DOC : Attestation de maîtrise du socle commun.</span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=calcul_scores_etats_acquisitions">DOC : Calcul des scores et des états d'acquisitions.</a></span></li>
</ul>
