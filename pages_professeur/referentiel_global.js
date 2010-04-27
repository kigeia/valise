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

// jQuery !
$(document).ready
(
	function()
	{

		// Préparation de select utiles
		var select_partage = '<select id="f_partage" name="f_partage"><option value="oui">Partagé sur le serveur communautaire.</option><option value="bof">Partage sans intérêt (pas novateur).</option><option value="non">Non partagé avec la communauté.</option></select>';
		var select_methode = '<select id="f_methode" name="f_methode"><option value="geometrique">Coefficients &times;2</option><option value="arithmetique">Coefficients +1</option><option value="classique">Moyenne classique</option></select>';
		var select_limite  = '<select id="f_limite" name="f_limite"><option value="0">avec toutes les notes.</option><option value="1">uniquement la dernière.</option>';
		var tab_options = new Array(2,3,4,5,6,7,8,9,10,15,20,30,40,50);
		for(i=0 ; i<tab_options.length ; i++)
		{
			select_limite += '<option value="'+tab_options[i]+'">des '+tab_options[i]+' dernières notes.</option>';
		}
		select_limite += '</select>';

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Inspection de l'URL : l'ajout d'un hash indique un retour de l'iframe suite à un choix de référentiel
//	Pour les explications : http://softwareas.com/cross-domain-communication-with-iframes (démo 1 : http://ajaxify.com/run/crossframe/ )
//	Attention, seule la 1e méthode fonctionne, la 2nde avec les iframes ajouté n'est pas compatible avec tous les navigateurs.
//	Voir aussi cette librairie : http://easyxdm.net/wp/
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		function surveiller_url()
		{
			$("body").everyTime
			('1ds', 'look_hash', function()
				{
					var hashVal = window.location.hash.substr(1);
					if(hashVal!="")
					{
						window.location.hash='';
						$("body").stopTime('look_hash');
						$('div.close a').click();
					}
					$('#voir_hash').html('...'+hashVal);
				}
			);
		}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Changement de méthode -> desactiver les limites autorisées suivant les cas
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		// Tableaux utilisés pour savoir quelles options desactiver
		var tableau_limites_autorisees = new Array();
		tableau_limites_autorisees['geometrique']  = '..1.2.3.4.5.';
		tableau_limites_autorisees['arithmetique'] = '..1.2.3.4.5.6.7.8.9.';
		tableau_limites_autorisees['classique']  = '..0.1.2.3.4.5.6.7.8.9.10.15.20.30.40.50.';
		// La fonction qui s'en occupe
		var actualiser_select_limite = function()
		{
			// Déterminer s'il faut modifier l'option sélectionnée
			limite_valeur = $('#f_limite option:selected').val();
			findme = '.'+limite_valeur+'.';
			methode_valeur = $('#f_methode option:selected').val();
			modifier_limite_selected = (tableau_limites_autorisees[methode_valeur].indexOf(findme)==-1) ? true : -1 ;
			if(modifier_limite_selected!=-1)
			{
				modifier_limite_selected = (methode_valeur=='geometrique') ? 5 : 9 ;
			}
			$("#f_limite option").each
			(
				function()
				{
					limite_valeur = $(this).val();
					findme = '.'+limite_valeur+'.';
					if(tableau_limites_autorisees[methode_valeur].indexOf(findme)==-1)
					{
						$(this).attr('disabled',true);
					}
					else
					{
						$(this).removeAttr('disabled');
					}
					if(limite_valeur==modifier_limite_selected)
					{
						$(this).attr('selected',true);
					}
				}
			);
		};
		// Appel de la fonction à chaque changement de méthode
		$('#f_methode').live('change', actualiser_select_limite );

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Voir un référentiel de son établissement
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('table.comp_view q.voir').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				ids = $(this).parent().attr('id');
				afficher_masquer_images_action('hide');
				new_label = '<label for="'+ids+'" class="loader">Demande envoyée... Veuillez patienter.</label>';
				$(this).after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'action=Voir&ids='+ids,
						dataType : "html",
						error : function(msg,string)
						{
							$('label[for='+ids+']').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.').fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,18)!='<ul class="ul_m1">')
							{
								$('label[for='+ids+']').removeAttr("class").addClass("alerte").html(responseHTML).fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							}
							else
							{
								$('#voir_referentiel').html(responseHTML+'<p />');
								infobulle();
								$('label[for='+ids+']').removeAttr("class").addClass("valide").html("Contenu affiché ci-dessous !").fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Modifier le partage d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.partager').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				afficher_masquer_images_action('hide');
				partage = $(this).parent().prev().prev().attr('lang');
				new_span = '<span>'+select_partage.replace('"'+partage+'"','"'+partage+'" selected="selected"')+'<q class="valider" lang="partager" title="Valider les modifications du partage de ce référentiel."></q><q class="annuler" title="Annuler la modification du partage de ce référentiel."></q> <label id="ajax_msg">&nbsp;</label></span>';
				$(this).after(new_span);
				infobulle();
				mode = 'partager';
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Mettre à jour sur le serveur de partage la dernière version d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.envoyer').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				ids = $(this).parent().attr('id');
				afficher_masquer_images_action('hide');
				new_label = '<label for="'+ids+'" class="loader">Demande envoyée... Veuillez patienter.</label>';
				$(this).after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'action=Envoyer&ids='+ids,
						dataType : "html",
						error : function(msg,string)
						{
							$('label[for='+ids+']').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.').fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,10)!='<img title')
							{
								$('label[for='+ids+']').removeAttr("class").addClass("alerte").html(responseHTML).fadeOut(4000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							}
							else
							{
								$('#'+ids).prev().prev().html('Référentiel présent. '+responseHTML);
								infobulle();
								$('label[for='+ids+']').removeAttr("class").addClass("valide").html("Référentiel transmis au serveur de partage avec succès !").fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Modifier le mode de calcul d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.calculer').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				afficher_masquer_images_action('hide');
				param   = $(this).parent().prev().attr('lang');
				tableau = param.split('_');
				methode = tableau[0];
				limite  = tableau[1];
				new_span = '<span>'+select_methode.replace('"'+methode+'"','"'+methode+'" selected="selected"')+select_limite.replace('"'+limite+'"','"'+limite+'" selected="selected"')+'<q class="valider" lang="calculer" title="Valider les modifications du mode de calcul de ce référentiel."></q><q class="annuler" title="Annuler la modification du mode de calcul de ce référentiel."></q> <label id="ajax_msg">&nbsp;</label></span>';
				$(this).after(new_span);
				actualiser_select_limite();
				infobulle();
				mode = 'calculer';
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Retirer un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.supprimer').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				afficher_masquer_images_action('hide');
				new_span = '<span class="danger">Tous les items et les résultats associés des élèves seront perdus !<q class="valider" lang="retirer" title="Confirmer la suppression de ce référentiel."></q><q class="annuler" title="Annuler la suppression de ce référentiel."></q> <label id="ajax_msg">&nbsp;</label></span>';
				$(this).after(new_span);
				infobulle();
				mode = 'retirer';
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Valider la modification du partage d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.valider[lang=partager]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				ids = $(this).parent().parent().attr('id');
				partage = $('#f_partage').val();
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'action=Partager&ids='+ids+'&partage='+partage,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,10)!='<img title')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('#'+ids).prev().prev().attr('lang',partage).html('Référentiel présent. '+responseHTML);
								if(partage=='oui')
								{
									$('#'+ids).children('q.envoyer_non').attr('class','envoyer').attr('title','Mettre à jour sur le serveur de partage la dernière version de ce référentiel.');
								}
								else
								{
									$('#'+ids).children('q.envoyer').attr('class','envoyer_non').attr('title','Un référentiel non partagé ne peut pas être transmis à la collectivité.');
								}
								$('#ajax_msg').parent().remove();
								afficher_masquer_images_action('show');
								infobulle();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Valider la modification du mode de calcul d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.valider[lang=calculer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				ids = $(this).parent().parent().attr('id');
				methode = $('#f_methode').val();
				limite  = $('#f_limite').val();
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'action=Calculer&ids='+ids+'&methode='+methode+'&limite='+limite,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,2)!='ok')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('#'+ids).prev().attr( 'lang',methode+'_'+limite ).html( responseHTML.substring(2,responseHTML.length) );
								$('#ajax_msg').parent().remove();
								afficher_masquer_images_action('show');
								infobulle();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Valider la suppression d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.valider[lang=retirer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				ids = $(this).parent().parent().attr('id');
				partage = $(this).parent().parent().prev().prev().attr('lang');
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'action=Retirer&ids='+ids+'&partage='+partage,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML!='ok')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								proposition = (ids.substring(0,5)=='ids_1') ? '' : ' ou importer un référentiel existant' ;
								$('#'+ids).html('<q class="ajouter" title="Créer un référentiel vierge'+proposition+'."></q>');
								$('#'+ids).prev().removeAttr("class").addClass("r").html('Sans objet.');
								$('#'+ids).prev().prev().removeAttr("class").addClass("r").html('Absence de référentiel.');
								afficher_masquer_images_action('show');
								infobulle();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Ajouter un référentiel ; cas d'une matière spécifique à l'établissement traité, ou chargement de choisir_referentiel sinon
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.ajouter').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				ids = $(this).parent().attr('id');
				if(ids.substring(0,5)=='ids_1')
				{
					// C'est une matière spécifique à l'établissement : on ne peut que créer un nouveau référentiel
					afficher_masquer_images_action('hide');
					new_span = '<span><input id="succes" name="succes" type="hidden" value="0" /><label for="'+ids+'" class="loader">Demande envoyée... Veuillez patienter.</label></span>';
					$(this).after(new_span);
					$.ajax
					(
						{
							type : 'POST',
							url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
							data : 'action=Ajouter&ids='+ids,
							dataType : "html",
							error : function(msg,string)
							{
								$('label[for='+ids+']').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.').fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
								return false;
							},
							success : function(responseHTML)
							{
								maj_clock(1);
								if(responseHTML!='ok')
								{
									$('label[for='+ids+']').removeAttr("class").addClass("alerte").html(responseHTML).fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
								}
								else
								{
									$('#'+ids).html('<q class="voir" title="Voir le détail de ce référentiel."></q><q class="partager_non" title="Le référentiel d\'une matière spécifique à l\'établissement ne peut être partagé."></q><q class="calculer" title="Modifier le mode de calcul associé à ce référentiel."></q><q class="supprimer" title="Supprimer ce référentiel."></q>');
									$('#'+ids).prev().removeAttr("class").addClass("v").attr('lang',methode_calcul_langue).html(methode_calcul_texte);
									$('#'+ids).prev().prev().removeAttr("class").addClass("v").attr('lang','hs').html('Référentiel présent. <img title="Référentiel dont le partage est sans objet (matière spécifique)." src="./_img/partage0.gif" />');
									afficher_masquer_images_action('show');
									infobulle();
								}
							}
						}
					);
				}
				else
				{
					// C'est une matière commune : on propose d'importer un référentiel partagé existant
					$('#voir_referentiel').html("&nbsp;");
					afficher_masquer_images_action('hide');
					new_span = '<span><input id="succes" name="succes" type="hidden" value="" /><label for="'+ids+'" class="loader">Demande envoyée... Veuillez patienter.</label></span>';
					$(this).after(new_span);
					$.ajax
					(
						{
							type : 'POST',
							url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
							data : 'action=Appeler&ids='+ids+'&adresse='+encodeURIComponent(document.location),
							dataType : "html",
							error : function(msg,string)
							{
								$('label[for='+ids+']').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.').fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
								return false;
							},
							success : function(responseHTML)
							{
								maj_clock(1);
								if(responseHTML.substring(0,7)=='<object')
								{
									$('label[for='+ids+']').removeAttr("class").addClass("valide").html("Référentiels disponibles affichés ci-dessous...");
									tab_ids = ids.split('_');
									$('#object_container').html(responseHTML).parent().show();
									surveiller_url();
								}
								else if(responseHTML.substring(0,6)=='<label')
								{
									$('label[for='+ids+']').removeAttr("class").addClass("valide").html("Référentiels disponibles affichés ci-dessous...");
									tab_ids = ids.split('_');
									$('#object_container').html(responseHTML).parent().show();
								}
								else
								{
									$('label[for='+ids+']').removeAttr("class").addClass("alerte").html(responseHTML).fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
								}
							}
						}
					);
				}
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Changement de matière -> desactiver les niveaux classiques en cas de matière transversale
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#f_matiere').change
		(
			function()
			{
				modif_niveau_selected = 0; // 0 = pas besoin modifier / 1 = à modifier / 2 = déjà modifié
				matiere_id = $('#f_matiere').val();
				$("#f_niveau option").each
				(
					function()
					{
						niveau_id = $(this).val();
						findme = '.'+niveau_id+'.';
						// Les niveaux "paliers" sont tout le temps accessibles
						if(listing_id_niveaux_paliers.indexOf(findme) == -1)
						{
							// matière classique -> tous niveaux actifs
							if(matiere_id != id_matiere_transversale)
							{
								$(this).removeAttr('disabled');
							}
							// matière transversale -> desactiver les autres niveaux
							else
							{
								$(this).attr('disabled',true);
								modif_niveau_selected = Math.max(modif_niveau_selected,1);
							}
						}
						// C'est un niveau palier ; le sélectionner si besoin
						else if(modif_niveau_selected==1)
						{
							$(this).attr('selected',true);
							modif_niveau_selected = 2;
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour chercher des référentiels partagés sur d'autres niveaux ou matières
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#f_submit_lister').click
		(
			function()
			{
				$('#voir_referentiel').html("&nbsp;");
				matiere_id = $('#f_matiere').val();
				niveau_id  = $('#f_niveau').val();
				if(!matiere_id)
				{
					$('#ajax_msg_actualiser').removeAttr("class").addClass("erreur").html("Il faut choisir une matière !");
					return false;
				}
				else if(!niveau_id)
				{
					$('#ajax_msg_actualiser').removeAttr("class").addClass("erreur").html("Il faut choisir un niveau !");
					return false;
				}
				$('#ajax_msg_actualiser').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'action=Lister&ids='+ids+'&matiere_id='+matiere_id+'&niveau_id='+niveau_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg_actualiser').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,4)!='<li>')
							{
								$('#ajax_msg_actualiser').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('#ajax_msg_actualiser').removeAttr("class").html("&nbsp;");
								$('#choisir_referentiel ul').html('<li><input id="etabl_0" name="donneur" type="radio" value="0" /><label for="etabl_0"> Référentiel vierge.</label></li>'+responseHTML);
								infobulle();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Voir un référentiel d'un autre établissement
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_referentiel q.voir').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				ids = $('#succes').parent().parent().attr('id');
				matiere_id = $('#f_matiere').val();
				niveau_id  = $('#f_niveau').val();
				matiere = $('#f_matiere option:selected').text();
				niveau = $('#f_niveau option:selected').text();
				donneur = $(this).parent().children('input').val();
				etabl   = $(this).parent().children('label').text();
				new_label = '<label id="temp" class="loader">Demande envoyée... Veuillez patienter.</label>';
				$(this).after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'action=Voir&ids='+ids+'&donneur='+donneur+'&matiere_id='+matiere_id+'&niveau_id='+niveau_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('label[id=temp]').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.').fadeOut(2000,function(){$('label[id=temp]').remove();});
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,18)!='<ul class="ul_m1">')
							{
								$('label[id=temp]').removeAttr("class").addClass("alerte").html(responseHTML).fadeOut(2000,function(){$('label[id=temp]').remove();});
							}
							else
							{
								$('#voir_referentiel').html('<h2>'+etabl+'</h2>'+responseHTML+'<p />');
								infobulle();
								$('label[id=temp]').removeAttr("class").addClass("valide").html("Contenu affiché ci-dessous !").fadeOut(2000,function(){$('label[id=temp]').remove();});
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Valider le choix d'un referentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('a.Valider_choisir').click
		(
			function()
			{
				ids = $('#succes').parent().parent().attr('id');
				matiere_id = $('#f_matiere').val();
				niveau_id  = $('#f_niveau').val();
				donneur = $("#choisir_referentiel input[type=radio]:checked").val();
				if(isNaN(donneur))	// normalement impossible, sauf si par exemple on triche avec la barre d'outils Web Developer...
				{
					$('#ajax_msg_choisir').removeAttr("class").addClass("erreur").html("Veuillez cocher un bouton !");
					return false;
				}
				$('#ajax_msg_choisir').removeAttr("class").addClass("loader").html("Demande envoyée... Veuillez patienter.");
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'action=Ajouter&ids='+ids+'&donneur='+donneur+'&matiere_id='+matiere_id+'&niveau_id='+niveau_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg_choisir').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML!='ok')
							{
								$('#ajax_msg_choisir').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('#'+ids).html('<q class="voir" title="Voir le détail de ce référentiel."></q><q class="partager" title="Modifier le partage de ce référentiel."></q><q class="envoyer_non" title="Un référentiel non partagé ne peut pas être transmis à la collectivité."></q><q class="calculer" title="Modifier le mode de calcul associé à ce référentiel."></q><q class="supprimer" title="Supprimer ce référentiel."></q>');
									$('#'+ids).prev().removeAttr("class").addClass("v").attr('lang',methode_calcul_langue).html(methode_calcul_texte);
								if(donneur>0)
								{
									$('#'+ids).prev().prev().removeAttr("class").addClass("v").attr('lang','bof').html('Référentiel présent. <img title="Référentiel dont le partage est sans intérêt (pas novateur)." src="./_img/partage0.gif" />');
								}
								else
								{
									$('#'+ids).prev().prev().removeAttr("class").addClass("v").attr('lang','non').html('Référentiel présent. <img title="Référentiel caché aux autres établissements." src="./_img/partage0.gif" />');
								}
								infobulle();
								$('a.Annuler_choisir').click();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Annuler le choix d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('a.Annuler_choisir').click
		(
			function()
			{
				$('#voir_referentiel').html("&nbsp;");
				$('#choisir_referentiel').hide();
				$('#choisir_referentiel ul.ul_m2').html("&nbsp;");
				$('#ajax_msg_choisir').removeAttr("class").html("&nbsp;");
				$('#succes').parent().remove();
				afficher_masquer_images_action('show');
				return(false);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Annuler la suppression ou la modification du partage ou la modification du mode de calcul d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.annuler').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$(this).parent().remove();
				afficher_masquer_images_action('show');
			}
		);

	}
);
