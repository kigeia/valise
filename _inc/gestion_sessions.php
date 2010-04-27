<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://competences.sesamath.net> - Suivi d'Acquisitions de Comp�tences
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

// Ouverture de la session et gestion des cas possibles.
// N�cessite "fonction_sessions.php".

$ALERTE_SSO = false;
$tab_msg_alerte = array();
$tab_msg_alerte['I.2']['index'] = 'Tentative d\'acc�s direct � une page r�serv�e !\nRedirection vers l\'accueil...';
$tab_msg_alerte['I.2']['ajax']  = 'Session perdue. D�connectez-vous et reconnectez-vous...';
$tab_msg_alerte['II.1.a']['index'] = 'Votre session a expir� !\nRedirection vers l\'accueil...';
$tab_msg_alerte['II.1.a']['ajax']  = 'Session expir�e. D�connectez-vous et reconnectez-vous...';
$tab_msg_alerte['II.3.a']['index'] = 'Tentative d\'acc�s direct � une page r�serv�e !\nRedirection vers l\'accueil...';
$tab_msg_alerte['II.3.a']['ajax']  = 'Page r�serv�e. Retournez � l\'accueil...';
$tab_msg_alerte['II.4.c']['index'] = 'Tentative d\'acc�s direct � une page r�serv�e !\nRedirection vers l\'accueil...';
$tab_msg_alerte['II.4.c']['ajax']  = 'Page r�serv�e. D�connexion effectu�e. Retournez � l\'accueil...';

if(!isset($_COOKIE[SESSION_NOM]))
{
	// I. Aucune session transmise
	open_new_session(); init_session();
	if($PROFIL_REQUIS!='public')
	{
		// I.2. Redirection : demande d'acc�s � une page r�serv�e donc identification avant acc�s direct
		alert_redirection_exit($tab_msg_alerte['I.2'][SACoche]);
	}
}
else
{
	// II. id de session transmis
	open_old_session();
	if(!isset($_SESSION['USER_PROFIL']))
	{
		// II.1. Pas de session retrouv�e (sinon cette variable serait renseign�e)
		if($PROFIL_REQUIS!='public')
		{
			// II.1.a. Session perdue ou expir�e et demande d'acc�s � une page r�serv�e : redirection pour une nouvelle identification
			$ALERTE_SSO = close_session(); open_new_session(); init_session();
			alert_redirection_exit($tab_msg_alerte['II.1.a'][SACoche]);
		}
		else
		{
			// II.1.b. Session perdue ou expir�e et page publique : cr�ation d'une nouvelle session (�ventuellement un message d'alerte pour indiquer session perdue ?)
			$ALERTE_SSO = close_session();open_new_session();init_session();
		}
	}
	elseif($_SESSION['USER_PROFIL'] == 'public')
	{
		// II.3. Personne non identifi�e
		if($PROFIL_REQUIS!='public')
		{
			// II.3.a. Espace non identifi� => Espace identifi� : redirection pour identification
			init_session();
			alert_redirection_exit($tab_msg_alerte['II.3.a'][SACoche]);
		}
		else
		{
			// II.3.b. Espace non identifi� => Espace non identifi� : RAS
		}
	}
	else
	{
		// II.4. Personne identifi�e
		if($_SESSION['USER_PROFIL'] == $PROFIL_REQUIS)
		{
			// II.4.a. Espace identifi� => Espace identifi� identique : RAS
		}
		elseif($PROFIL_REQUIS=='public')
		{
			// II.4.b. Espace identifi� => Espace non identifi� : cr�ation d'une nouvelle session vierge (�ventuellement un message d'alerte pour indiquer session perdue ?)
			if (SACoche!='ajax')
			{
				// Ne pas d�connecter si on appelle le calendrier de l'espace public
				$ALERTE_SSO = close_session();open_new_session();init_session();
			}
		}
		elseif($PROFIL_REQUIS!='public')
		{
			// II.4.c. Espace identifi� => Autre espace identifi� incompatible : redirection pour une nouvelle identification
			init_session();
			alert_redirection_exit($tab_msg_alerte['II.4.c'][SACoche]);
		}
	}
}
?>