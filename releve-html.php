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

// Fichier appelé pour l'affichage d'un relevé HTML enregistré temporairement.
// Passage en GET d'un paramètre pour savoir quelle page charger.

// Atteste l'appel de cette page avant l'inclusion d'une autre
define('SACoche','releve');

// Constantes / Fonctions de redirections / Configuration serveur
require_once('./_inc/constantes.php');
require_once('./_inc/fonction_redirection.php');
require_once('./_inc/config_serveur.php');

// Paramètres transmis
$FICHIER = (isset($_GET['fichier'])) ? $_GET['fichier'] : '';

// Fonctions
require_once('./_inc/fonction_affichage.php');

ob_start();
// Chargement de la page concernée
$filename_html = './__tmp/export/'.$FICHIER.'.html';
if(is_file($filename_html))
{
	include($filename_html);
}
else
{
	echo'<h2>Relevé manquant</h2>';
	echo'Les relevés sont conservés sur le serveur pendant une durée limitée...';
}
// Affichage dans une variable
$CONTENU_PAGE = ob_get_contents();
ob_end_clean();

// Affichage de l'en-tête
entete();
?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" type="images/x-icon" href="./favicon.ico" />
	<link rel="icon" type="image/png" href="./favicon.png" />
	<link rel="stylesheet" type="text/css" href="./_css/<?php echo FILE_CSS_SCREEN ?>" />
	<link rel="stylesheet" type="text/css" href="./_css/<?php echo FILE_CSS_PRINT ?>" media="print" />
	<script type="text/javascript" charset="utf-8" src="./_js/<?php echo FILE_JS_BIBLIO ?>"></script>
	<script type="text/javascript" charset="utf-8" src="./_js/<?php echo FILE_JS_SCRIPT ?>"></script>
	<title>SACoche - Relevé HTML</title>
</head>
<body>
	<?php echo $CONTENU_PAGE; ?>
	<script type="text/javascript">
		var DOSSIER='public';
	</script>
</body>
</html>