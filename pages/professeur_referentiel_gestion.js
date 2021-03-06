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

		// Préparation de select utiles
		var select_partage = '<select id="f_partage" name="f_partage"><option value="oui">Partagé sur le serveur communautaire.</option><option value="bof">Partage sans intérêt (pas novateur).</option><option value="non">Non partagé avec la communauté.</option></select>';
		var select_methode = '<select id="f_methode" name="f_methode"><option value="geometrique">Coefficients &times;2</option><option value="arithmetique">Coefficients +1</option><option value="classique">Moyenne classique</option><option value="bestof1">La meilleure</option><option value="bestof2">Les 2 meilleures</option><option value="bestof3">Les 3 meilleures</option></select>';
		var select_limite  = '<select id="f_limite" name="f_limite"><option value="0">de toutes les notes.</option><option value="1">de la dernière note.</option>';
		var tab_options = new Array(2,3,4,5,6,7,8,9,10,15,20,30,40,50);
		for(i=0 ; i<tab_options.length ; i++)
		{
			select_limite += '<option value="'+tab_options[i]+'">des '+tab_options[i]+' dernières notes.</option>';
		}
		select_limite += '</select>';

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Changement de méthode -> desactiver les limites autorisées suivant les cas
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		// Tableaux utilisés pour savoir quelles options desactiver
		var tableau_limites_autorisees = new Array();
		tableau_limites_autorisees['geometrique']  = '.1.2.3.4.5.';
		tableau_limites_autorisees['arithmetique'] = '.1.2.3.4.5.6.7.8.9.';
		tableau_limites_autorisees['classique']    = '.1.2.3.4.5.6.7.8.9.10.15.20.30.40.50.0.';
		tableau_limites_autorisees['bestof1']      = '.1.2.3.4.5.6.7.8.9.10.15.20.30.40.50.0.';
		tableau_limites_autorisees['bestof2']      =   '.2.3.4.5.6.7.8.9.10.15.20.30.40.50.0.';
		tableau_limites_autorisees['bestof3']      =     '.3.4.5.6.7.8.9.10.15.20.30.40.50.0.';
		// La fonction qui s'en occupe
		var actualiser_select_limite = function()
		{
			// Déterminer s'il faut modifier l'option sélectionnée
			var limite_valeur            = $('#f_limite option:selected').val();
			var findme                   = '.'+limite_valeur+'.';
			var methode_valeur           = $('#f_methode option:selected').val();
			var chaine_autorisee         = tableau_limites_autorisees[methode_valeur];
			var modifier_limite_selected = (chaine_autorisee.indexOf(findme)==-1) ? true : false ; // 1|3 Si true alors il faudra changer le selected actuel qui ne sera plus dans les nouveaux choix.
			if(modifier_limite_selected)
			{
				modifier_limite_selected = chaine_autorisee.substr(chaine_autorisee.length-2,1) ; // 2|3 On prendra alors la valeur maximale dans les nouveaux choix.
			}
			$("#f_limite option").each
			(
				function()
				{
					limite_valeur = $(this).val();
					findme = '.'+limite_valeur+'.';
					if(chaine_autorisee.indexOf(findme)==-1)
					{
						$(this).prop('disabled',true);
					}
					else
					{
						$(this).prop('disabled',false);
					}
					if(limite_valeur===modifier_limite_selected) // === pour éviter un (false==0) qui sélectionne la 1ère option...
					{
						$(this).prop('selected',true); // 3|3 C'est ici que le selected se fait.
					}
				}
			);
		};
		// Appel de la fonction à chaque changement de méthode
		$('#f_methode').live('change', actualiser_select_limite );

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Changement de nb de demandes autorisées pour une matière -> soumission
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('select[name=f_eleve_demandes]').change
		(
			function()
			{
				var element = $(this);
				var nb_demandes = $(this).attr('value');
				var matiere_id = $(this).closest('table').attr('id').substring(4);
				element.parent().find('label').removeAttr("class").addClass("loader").html("Demande envoyée...");
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=NbDemandes&matiere_id='+matiere_id+'&nb_demandes='+nb_demandes,
						dataType : "html",
						error : function(msg,string)
						{
							element.parent().find('label').removeAttr("class").addClass("alerte").html("Echec de la connexion !");
							return false;
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML!='ok')
							{
								element.parent().find('label').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								element.parent().find('label').removeAttr("class").addClass("valide").html("Valeur enregistrée.");
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Voir un référentiel de son établissement
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('table.vm_nug q.voir').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().attr('id');
				afficher_masquer_images_action('hide');
				var new_label = '<label for="'+ids+'" class="loader">Demande envoyée...</label>';
				$(this).after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Voir&ids='+ids,
						dataType : "html",
						error : function(msg,string)
						{
							$.fancybox( '<label class="alerte">'+'Echec de la connexion !'+'</label>' , {'centerOnScroll':true} );
							$('label[for='+ids+']').remove();
							afficher_masquer_images_action('show');
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML.substring(0,18)!='<ul class="ul_m1">')
							{
								$.fancybox( '<label class="alerte">'+responseHTML+'</label>' , {'centerOnScroll':true} );
							}
							else
							{
								$.fancybox( responseHTML.replace('<ul class="ul_m2">','<q class="imprimer" title="Imprimer le référentiel." />'+'<ul class="ul_m2">') , {'centerOnScroll':true} );
								infobulle();
							}
							$('label[for='+ids+']').remove();
							afficher_masquer_images_action('show');
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
				var ids   = $(this).parent().attr('id');
				var tab_ids = ids.split('_');
				var partage = tab_partage_etat[tab_ids[1]+'_'+tab_ids[2]];
				var new_span = '<span>'+select_partage.replace('"'+partage+'"','"'+partage+'" selected')+'<q class="valider" lang="partager" title="Valider les modifications du partage de ce référentiel."></q><q class="annuler" title="Annuler la modification du partage de ce référentiel."></q> <label id="ajax_msg">&nbsp;</label></span>';
				$(this).after(new_span);
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Mettre à jour sur le serveur de partage la dernière version d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.envoyer').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().attr('id');
				afficher_masquer_images_action('hide');
				var new_label = '<label for="'+ids+'" class="loader">Demande envoyée...</label>';
				$(this).after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Envoyer&ids='+ids,
						dataType : "html",
						error : function(msg,string)
						{
							$.fancybox( '<label class="alerte">'+'Echec de la connexion !'+'</label>' , {'centerOnScroll':true} );
							$('label[for='+ids+']').remove();
							afficher_masquer_images_action('show');
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML.substring(0,10)!='<img title')
							{
								$.fancybox( '<label class="alerte">'+responseHTML+'</label>' , {'centerOnScroll':true} );
							}
							else
							{
								$.fancybox( '<label class="valide">Référentiel partagé avec succès !</label>' , {'centerOnScroll':true} );
								$('#'+ids).prev().prev().html(responseHTML);
								infobulle();
							}
							$('label[for='+ids+']').remove();
							afficher_masquer_images_action('show');
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
				var ids   = $(this).parent().attr('id');
				var tab_ids = ids.split('_');
				var methode = tab_calcul_methode[tab_ids[1]+'_'+tab_ids[2]];
				var limite  = tab_calcul_limite[tab_ids[1]+'_'+tab_ids[2]];
				var new_span = '<span>'+select_methode.replace('"'+methode+'"','"'+methode+'" selected')+select_limite.replace('"'+limite+'"','"'+limite+'" selected')+'<q class="valider" lang="calculer" title="Valider les modifications du mode de calcul de ce référentiel."></q><q class="annuler" title="Annuler la modification du mode de calcul de ce référentiel."></q> <label id="ajax_msg">&nbsp;</label></span>';
				$(this).after(new_span);
				actualiser_select_limite();
				infobulle();
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
				var new_span = '<span class="danger">Tous les items et les résultats associés des élèves seront perdus !<q class="valider" lang="retirer" title="Confirmer la suppression de ce référentiel."></q><q class="annuler" title="Annuler la suppression de ce référentiel."></q> <label id="ajax_msg">&nbsp;</label></span>';
				$(this).after(new_span);
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Valider la modification du partage d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.valider[lang=partager]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().parent().attr('id');
				var partage = $('#f_partage').val();
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée...');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Partager&ids='+ids+'&partage='+partage,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion !');
							return false;
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML.substring(0,10)!='<img title')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								var tab_ids = ids.split('_');
								tab_partage_etat[tab_ids[1]+'_'+tab_ids[2]] = partage;
								$('#'+ids).prev().prev().html(responseHTML);
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
				var ids = $(this).parent().parent().attr('id');
				var methode = $('#f_methode').val();
				var limite  = $('#f_limite').val();
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée...');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Calculer&ids='+ids+'&methode='+methode+'&limite='+limite,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion !');
							return false;
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML.substring(0,2)!='ok')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								var tab_ids = ids.split('_');
								tab_calcul_methode[tab_ids[1]+'_'+tab_ids[2]] = methode;
								tab_calcul_limite[tab_ids[1]+'_'+tab_ids[2]]  = limite;
								$('#'+ids).prev().html( responseHTML.substring(2,responseHTML.length) );
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
				var ids = $(this).parent().parent().attr('id');
				var tab_ids = ids.split('_');
				var partage = tab_partage_etat[tab_ids[1]+'_'+tab_ids[2]];
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée...');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Retirer&ids='+ids+'&partage='+partage,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion !');
							return false;
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML!='ok')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('#'+ids).parent().remove();
								if( $('#mat_'+tab_ids[1]+' tbody tr').length == 1 )
								{
									$('#mat_'+tab_ids[1]+' tbody').prepend('<tr class="absent"><td class="r hc">---</td><td class="r hc">---</td><td class="r hc">---</td><td class="nu"></td></tr>');
								}
								afficher_masquer_images_action('show');
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Ajouter un référentiel => affichage de choisir_referentiel même dans le cas d'une matière spécifique à l'établissement
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.ajouter').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().attr('id');
				var tab_ids = ids.split('_');
				var matiere_id    = tab_ids[1];
				var matiere_perso = tab_ids[2];
				var matiere_nom = $('#h2_'+matiere_id).html();
				$('#matiere_id').val(matiere_id);
				$('#matiere_perso').val(matiere_perso);
				$('#choisir_referentiel h2 span').html(matiere_nom);
				$("#f_niveau_create option").each
				(
					function()
					{
						var matiere_valeur = $(this).val();
						if( matiere_valeur )
						{
							if( $('#ids_'+matiere_id+'_'+matiere_valeur+'_'+matiere_perso).length )
							{
								$(this).prop('disabled',true);
							}
							else
							{
								$(this).prop('disabled',false);
							}
						}
					}
				);
				afficher_masquer_images_action('hide');
				$('#div_tableaux').hide();
				$('#choisir_importer').parent().hide();
				$('#ajax_msg_choisir').removeAttr("class").html("&nbsp;");
				$('#choisir_referentiel').show();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour Annuler le choix d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_annuler').click
		(
			function()
			{
				$('#choisir_referentiel').hide();
				$('#ajax_msg_choisir').removeAttr("class").html("&nbsp;");
				$('#div_tableaux').show();
				afficher_masquer_images_action('show');
				return(false);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Charger le formulaire listant les structures ayant partagées un référentiel (appel au serveur communautaire)
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		var charger_formulaire_structures = function()
		{
			$('#rechercher').prop('disabled',true);
			$('#ajax_msg').removeAttr("class").addClass("loader").html('Chargement du formulaire...');
			$.ajax
			(
				{
					type : 'POST',
					url : 'ajax.php?page='+PAGE,
					data : 'action=Afficher_structures',
					dataType : "html",
					error : function(msg,string)
					{
						$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! <a href="#" id="charger_formulaire_structures">Veuillez essayer de nouveau.</a>');
						return false;
					},
					success : function(responseHTML)
					{
						initialiser_compteur();
						if(responseHTML.substring(0,7)!='<option')
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML+' <a href="#" id="charger_formulaire_structures">Veuillez essayer de nouveau.</a>');
						}
						else
						{
							$('#ajax_msg').removeAttr("class").html('&nbsp;');
							$('#f_structure').html(responseHTML);
							$('#rechercher').prop('disabled',false);
						}
					}
				}
			);
		};

		// Charger au clic sur le lien obtenu si échec
		$('#charger_formulaire_structures').live(  'click' , charger_formulaire_structures );

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour Afficher le formulaire de recherche sur le serveur communautaire
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_rechercher').click
		(
			function()
			{
				// Récup des infos
				var matiere_id = $('#matiere_id').val();
				var niveau_id  = $('#f_niveau_create option:selected').val();
				// MAJ et affichage du formulaire
				$('#ajax_msg_choisir').removeAttr("class").html('');
				if( $('#f_structure option').length == 1 )
				{
					charger_formulaire_structures();
				}
				$('#f_matiere option[value='+matiere_id+']').prop('selected',true);
				$('#f_niveau option[value='+niveau_id+']').prop('selected',true);
				$('#choisir_referentiel_communautaire ul').html('<li></li>');
				$('#lister_referentiel_communautaire').hide("fast");
				$('#form_instance').hide();
				$('#form_communautaire').show();
				initialiser_compteur();
				return(false);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Charger le select f_matiere en ajax
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		function maj_matiere(matiere_famille_id)
		{
			$.ajax
			(
				{
					type : 'POST',
					url : 'ajax.php?page=_maj_select_matieres_famille',
					data : 'f_famille_matiere='+matiere_famille_id,
					dataType : "html",
					error : function(msg,string)
					{
						$('#ajax_maj_matiere').removeAttr("class").addClass("alerte").html("Echec de la connexion !");
					},
					success : function(responseHTML)
					{
						initialiser_compteur();
						if(responseHTML.substring(0,7)=='<option')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
						{
							$('#f_matiere').html(responseHTML);
						}
					else
						{
							$('#ajax_maj_matiere').removeAttr("class").addClass("alerte").html(responseHTML);
						}
					}
				}
			);
		}

		$("#f_famille_matiere").change
		(
			function()
			{
				matiere_famille_id = $("#f_famille_matiere").val();
				if(matiere_famille_id)
				{
					maj_matiere(matiere_famille_id);
				}
				else
				{
					$('#f_matiere').html('<option value="0">Toutes les matières</option>');
					$('#ajax_maj_matiere').removeAttr("class").html("&nbsp;");
				}
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Charger le select f_niveau en ajax
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		function maj_niveau(niveau_famille_id)
		{
			$.ajax
			(
				{
					type : 'POST',
					url : 'ajax.php?page=_maj_select_niveaux_famille',
					data : 'f_famille_niveau='+niveau_famille_id,
					dataType : "html",
					error : function(msg,string)
					{
						$('#ajax_maj_niveau').removeAttr("class").addClass("alerte").html("Echec de la connexion !");
					},
					success : function(responseHTML)
					{
						initialiser_compteur();
						if(responseHTML.substring(0,7)=='<option')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
						{
							$('#f_niveau').html(responseHTML);
						}
					else
						{
							$('#ajax_maj_niveau').removeAttr("class").addClass("alerte").html(responseHTML);
						}
					}
				}
			);
		}

		$("#f_famille_niveau").change
		(
			function()
			{
				niveau_famille_id = $("#f_famille_niveau").val();
				if(niveau_famille_id)
				{
					maj_niveau(niveau_famille_id);
				}
				else
				{
					$('#f_niveau').html('<option value="0">Tous les niveaux</option>');
					$('#ajax_maj_niveau').removeAttr("class").html("&nbsp;");
				}
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Réagir au changement dans un select
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_referentiel_communautaire select').change
		(
			function()
			{
				$('#ajax_msg').removeAttr("class").html("&nbsp;");
				$('#choisir_referentiel_communautaire ul').html('<li></li>');
				$('#lister_referentiel_communautaire').hide("fast");
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour chercher des référentiels partagés sur d'autres niveaux ou matières
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#rechercher').click
		(
			function()
			{
				var matiere_id   = $('#f_matiere').val();
				var niveau_id    = $('#f_niveau').val();
				var structure_id = $('#f_structure').val();
				if( (matiere_id==0) && (niveau_id==0) && (structure_id==0) )
				{
					$('#ajax_msg').removeAttr("class").addClass("erreur").html("Il faut préciser au moins un critère parmi matière / niveau / structure !");
					return false;
				}
				$('#rechercher').prop('disabled',true);
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée...');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Lister_referentiels&matiere_id='+matiere_id+'&niveau_id='+niveau_id+'&structure_id='+structure_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('#rechercher').prop('disabled',false);
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion !');
							return false;
						},
						success : function(responseHTML)
						{
							$('#rechercher').prop('disabled',false);
							if(responseHTML.substring(0,3)!='<li')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								initialiser_compteur();
								$('#ajax_msg').removeAttr("class").html("&nbsp;");
								var reg = new RegExp('</q>',"g"); // Si on ne prend pas une expression régulière alors replace() ne remplace que la 1e occurence
								responseHTML = responseHTML.replace(reg,'</q><q class="valider" title="Sélectionner ce référentiel.<br />(choix à confirmer de retour à la page principale)"></q>'); // Ajouter les paniers
								$('#choisir_referentiel_communautaire ul').html(responseHTML);
								$('#lister_referentiel_communautaire').show("fast");
								infobulle();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur une image pour choisir un référentiel donné
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_referentiel_communautaire q.valider').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var referentiel_id = $(this).parent().attr('id').substr(3);
				var description    = $(this).parent().text(); // Pb : il prend le contenu du <sup> avec
				var longueur_sup   = $(this).prev().prev().text().length;
				var description    = description.substring(0,description.length-longueur_sup);
				$('#reporter').html(description).parent('#choisir_importer').val('id_'+referentiel_id).parent().show();
				initialiser_compteur();
				$('#rechercher_annuler').click();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Voir le détail d'un référentiel partagé
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_referentiel_communautaire q.voir').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var referentiel_id = $(this).parent().attr('id').substr(3);
				var description    = $(this).parent().text(); // Pb : il prend le contenu du <sup> avec
				var longueur_sup   = $(this).prev().text().length;
				var description    = description.substring(0,description.length-longueur_sup);
				var new_label = '<label id="temp" class="loader">Demande envoyée...</label>';
				$(this).next().after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Voir_referentiel&referentiel_id='+referentiel_id,
						dataType : "html",
						error : function(msg,string)
						{
							$.fancybox( '<label class="alerte">'+'Echec de la connexion !'+'</label>' , {'centerOnScroll':true} );
							$('label[id=temp]').remove();
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML.substring(0,18)!='<ul class="ul_n1">')
							{
								$.fancybox( '<label class="alerte">'+responseHTML+'</label>' , {'centerOnScroll':true} );
							}
							else
							{
								$.fancybox( '<ul class="ul_m1"><li class="li_m1"><b>'+description+'</b><q class="imprimer" title="Imprimer le référentiel."></q>'+responseHTML+'</li></ul>' , {'centerOnScroll':true} );
								infobulle();
							}
							$('label[id=temp]').remove();
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour Annuler la recherche sur le serveur communautaire
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#rechercher_annuler').click
		(
			function()
			{
				$('#form_instance').show();
				$('#form_communautaire').hide();
				$('#lister_referentiel_communautaire').hide();
				return(false);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur un bouton pour Valider le choix d'un referentiel (vierge ou issu du serveur communautaire)
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_initialiser , #choisir_importer').click
		(
			function()
			{
				var matiere_id = $('#matiere_id').val();
				var niveau_id  = $('#f_niveau_create option:selected').val();
				if(!niveau_id)
				{
					$('#ajax_msg_choisir').removeAttr("class").addClass("erreur").html('Choisir un niveau !');
					return false;
				}
				var matiere_perso = $('#matiere_perso').val();
				$('#ajax_msg_choisir').removeAttr("class").html('');
				var referentiel_id = $(this).val().substring(3);
				$('button').prop('disabled',true);
				$('#ajax_msg_choisir').removeAttr("class").addClass("loader").html("Demande envoyée...");
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Ajouter&ids=ids_'+matiere_id+'_'+niveau_id+'_'+matiere_perso+'&referentiel_id='+referentiel_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('button').prop('disabled',false);
							$('#ajax_msg_choisir').removeAttr("class").addClass("alerte").html('Echec de la connexion !');
							return false;
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							$('button').prop('disabled',false);
							if(responseHTML!='ok')
							{
								$('#ajax_msg_choisir').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								// niveau
								var td_niveau = '<td>'+$('#f_niveau_create option:selected').text()+'</td>';
								// partage
								if(matiere_perso=='1')
								{
									var td_partage = '<td class="hc"><img title="Référentiel dont le partage est sans objet (matière spécifique)." src="./_img/etat/partage_non.gif" /></td>';
									tab_partage_etat[matiere_id+'_'+niveau_id] = 'hs';
								}
								else if(referentiel_id!='0')
								{
									var td_partage = '<td class="hc"><img title="Référentiel dont le partage est sans intérêt (pas novateur)." src="./_img/etat/partage_non.gif" /></td>';
									tab_partage_etat[matiere_id+'_'+niveau_id] = 'bof';
								}
								else
								{
									var td_partage = '<td class="hc"><img title="Référentiel non partagé avec la communauté." src="./_img/etat/partage_non.gif" /></td>';
									tab_partage_etat[matiere_id+'_'+niveau_id] = 'non';
								}
								// méthode de calcul
								var td_calcul = '<td>'+calcul_texte+'</td>';
								tab_calcul_methode[matiere_id+'_'+niveau_id] = calcul_methode;
								tab_calcul_limite[matiere_id+'_'+niveau_id]  = calcul_limite;
								// actions
								var q_partager = (matiere_perso=='1') ? '<q class="partager_non" title="Le référentiel d\'une matière spécifique à l\'établissement ne peut être partagé."></q>' : '<q class="partager" title="Modifier le partage de ce référentiel."></q>' ;
								var td_actions = '<td id="ids_'+matiere_id+'_'+niveau_id+'_'+matiere_perso+'" class="nu"><q class="voir" title="Voir le détail de ce référentiel."></q>'+q_partager+'<q class="envoyer_non" title="Un référentiel non partagé ne peut pas être transmis à la collectivité."></q><q class="calculer" title="Modifier le mode de calcul associé à ce référentiel."></q><q class="supprimer" title="Supprimer ce référentiel."></q></td>';
								// ajout de la ligne
								$('#mat_'+matiere_id).children('tbody').prepend('<tr class="new">'+td_niveau+td_partage+td_calcul+td_actions+'</tr>');
								$('#mat_'+matiere_id).children('tbody').children('tr.absent').remove();
								infobulle();
								$('#choisir_annuler').click();
								var label_message  = (referentiel_id) ? "Référentiel importé avec succès !" : "Référentiel vierge ajouté." ;
								var astuce_message = (referentiel_id) ? "Pour éditer ce nouveau référentiel," : "Pour remplir ce nouveau référentiel," ;
								$.fancybox( '<label class="valide">'+label_message+'</label><p class="astuce">'+astuce_message+' utiliser la page "<a href="./index.php?page=professeur_referentiel&amp;section=edition">modifier le contenu des référentiels</a>".</p>' , {'centerOnScroll':true} );
							}
						}
					}
				);
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
