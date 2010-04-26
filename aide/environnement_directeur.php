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
$TITRE = "L'environnement directeur";
?>

<h2>Introduction</h2>
<p>
	On appelle un compte <b>"directeur"</b> celui d'un <b>personnel de direction</b> : principal d'un collège, proviseur d'un lycée, leurs adjoints...
</p>

<h2>Connexion</h2>
<p>
	Pour se connecter comme directeur, sélectionner son établissement (si besoin) puis saisir son nom d'utilisateur et son mot de passe (seul l'administrateur de l'établissement peut générer l'inscription du directeur et lui communiquer ses paramètres).
</p>

<h2>Changer son mot de passe</h2>
<p>
	Les directeurs peuvent modifier leur mot de passe (sauf si la connexion est dépendante d'un service extérieur, tel un ENT).<br />
	Les mots de passe sont cryptés et ne peuvent pas être renvoyés. En cas d'oubli du mot de passe directeur, contacter un administrateur qui est seul habilité à en générer un nouveau.
</p>

<h2>Fonctionnalités</h2>
<p>Le directeur peut :</p>
<ul class="puce">
	<li>générer des grilles de compétences</li>
	<li>établir des bilans de compétences</li>
	<li>estimer des attestations de maîtrise du socle</li>
</ul>
<p>
	L'administrateur de <em>SACoche</em> a la charge de gérer les élèves, les professeurs, les classes, les matières, les périodes...<br />
	Il faut le contacter si on constate une anomalie dans les données.
</p>
