<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Comp�tences
 * � Thomas Crespin pour S�samath <http://www.sesamath.net> - Tous droits r�serv�s.
 * Logiciel plac� sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la �GNU General Public License� telle que publi�e par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (� votre gr�) toute version ult�rieure.
 * 
 * SACoche est distribu� dans l�espoir qu�il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans m�me la garantie implicite de COMMERCIALISABILIT� ni d�AD�QUATION � UN OBJECTIF PARTICULIER.
 * Consultez la Licence G�n�rale Publique GNU pour plus de d�tails.
 * 
 * Vous devriez avoir re�u une copie de la Licence G�n�rale Publique GNU avec SACoche ;
 * si ce n�est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

// Fabrique, ou r�cup�re si elle existe, une image png inclin�e � 90� � partir du nom et du pr�nom de l'�l�ve

header("Content-type: image/png");

require_once('../../_inc/fonction_clean.php');

$dossier = isset($_GET['dossier']) ? $_GET['dossier'] : '';
$nom     = isset($_GET['nom'])     ? $_GET['nom']     : '';
$prenom  = isset($_GET['prenom'])  ? $_GET['prenom']  : '';

$fichier = '../../__tmp/badge/'.clean_entier($dossier).'/'.clean_login($nom).'_'.clean_login($prenom).'.png';

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