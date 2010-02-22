<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2009
 * 
 * ****************************************************************************************************
 * SACoche [http://competences.sesamath.net] - Suivi d'Acquisitions de Comp�tences
 * � Thomas Crespin pour S�samath [http://www.sesamath.net]
 * Distribution sous licence libre pr�vue pour l'�t� 2010.
 * ****************************************************************************************************
 * 
 * Fabrique, ou r�cup�re si elle existe, une image png inclin�e � 90� � partir du nom et du pr�nom de l'�l�ve
 * 
 */

header("Content-type: image/png");

require_once('../../_inc/fonction_clean.php');

$nom    = isset($_GET['nom'])    ? $_GET['nom']    : '';
$prenom = isset($_GET['prenom']) ? $_GET['prenom'] : '';

$fichier = '../../__tmp/badge/'.clean_login($nom).'_'.clean_login($prenom).'.png';

if(!file_exists($fichier))
{
	$taille_police = 10;
	$largeur       = 30;
	$hauteur       = ceil(max($taille_police*strlen($nom),$taille_police*strlen($prenom)*0.75));
	$image         = imagecreate($largeur,$hauteur);
	$couleur_fond  = imagecolorallocatealpha($image,255,221,136,127);
	$couleur_texte = imagecolorallocate($image,0,0,0);
	$police        = './arial.ttf';
	$tab = imagettftext($image,$taille_police,90,$largeur/2-4,$hauteur-2,$couleur_texte,$police,$nom."\r\n".$prenom);
	imagepng($image,$fichier);
}
else
{
	$image = imagecreatefrompng($fichier);
}

imagepng($image);
imagedestroy($image);
?>