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

// jQuery !
$(document).ready
(
	function()
	{

		//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
		// Appel en ajax pour lancer un nettoyage
		//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

		$('button').click
		(
			function()
			{
				var action = $(this).attr('id').substring(7); // "purger" ou "nettoyer"
				if(action=='purger')
				{
					var continuer = (confirm("Attention : les scores déjà saisis ne seront plus modifiables !\nConfirmez-vous l'initialisation annuelle des données ?")) ? true : false ;
				}
				else
				{
					var continuer = true;
				}
				if(continuer)
				{
					$("button").attr('disabled','disabled');
					$("label").removeAttr("class").html('');
					$("#ajax_info").html('');
					$('#ajax_msg_'+action).addClass("loader").html("Demande envoyée... Veuillez patienter.");
					$.ajax
					(
						{
							type : 'POST',
							url : 'ajax.php?page='+PAGE,
							data : 'f_action='+action,
							dataType : "html",
							error : function(msg,string)
							{
								$("button").removeAttr('disabled');
								$('#ajax_msg_'+action).removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
								return false;
							},
							success : function(responseHTML)
							{
								$("button").removeAttr('disabled');
								if(responseHTML.substring(0,4)!='<li>')
								{
									$('#ajax_msg_'+action).removeAttr("class").addClass("alerte").html(responseHTML);
								}
								else
								{
									$('#ajax_msg_'+action).removeAttr("class").html('');
									$('#ajax_info').html(responseHTML);
									maj_clock(1);
								}
							}
						}
					);
				}
			}
		);

	}
);