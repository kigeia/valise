<?php
/**
 * @version $Id: _menu.php 8 2009-10-30 20:56:02Z thomas $
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

$arbre='
<ul id="treeview">
	<li><span>Mon compte</span>
		<ul>
			<li class="accueil"><a href="./index.php?dossier='.$DOSSIER.'&amp;fichier=accueil">Accueil</a></li>
			<li class="password"><a href="./index.php?dossier='.$DOSSIER.'&amp;fichier=password">Mot de passe</a></li>
		</ul>
	</li>
	<li><span>Relevés de compétences</span>
		<ul>
			<li class="releve_matiere"><a href="./index.php?dossier='.$DOSSIER.'&amp;fichier=releve_matiere">Bilans sur une matière</a></li>';
if(mb_substr_count($_SESSION['ELEVE_OPTIONS'],'as'))
{
	$arbre.='
			<li class="releve_socle"><a href="./index.php?dossier='.$DOSSIER.'&amp;fichier=releve_socle">Attestation de maîtrise du socle</a></li>
	';
}
$arbre.='
			<li class="releve_grille"><a href="./index.php?dossier='.$DOSSIER.'&amp;fichier=releve_grille">Grilles sur un niveau</a></li>
		</ul>
	</li>
</ul>
';

echo'<div id="cadre_identite">';
echo	$_SESSION['STRUCTURE'].'<br />';
echo	$_SESSION['USER_PRENOM'].' '.$_SESSION['USER_NOM'].'<q class="deconnecter" title="Se déconnecter."></q><br />';
echo	'<i><img alt="'.$_SESSION['PROFIL'].'" src="./_img/menu/'.$_SESSION['PROFIL'].'.png" /> '.$_SESSION['PROFIL'].' <span id="clock"><img alt="" src="./_img/clock_fixe.png" /> '.$_SESSION['DUREE_INACTIVITE'].' min</span><img alt="" src="./_img/point.gif" /></i><br />';
echo'</div>';
echo'<div id="appel_menu">MENU</div>';
echo str_replace('class="'.$FICHIER.'"><a ','class="'.$FICHIER.'"><a class="actif" ',$arbre);
?>
