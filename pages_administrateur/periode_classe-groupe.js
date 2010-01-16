/**
 * @version $Id: periode_classe-groupe.js 8 2009-10-30 20:56:02Z thomas $
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

// jQuery !
$(document).ready
(
	function()
	{

		// Réagir au clic dans un select multiple

		$('select[multiple]').click
		(
			function()
			{
				$('#ajax_msg').removeAttr("class").addClass("alerte").html("Pensez à valider vos modifications !");
			}
		);

		// Pour tester si une date est valide

		function test_dateITA(value)
		{
			var check = false;
			var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/ ;
			if( re.test(value))
			{
				var adata = value.split('/');
				var gg = parseInt(adata[0],10);
				var mm = parseInt(adata[1],10);
				var aaaa = parseInt(adata[2],10);
				var xdata = new Date(aaaa,mm-1,gg);
				if ( ( xdata.getFullYear() == aaaa ) && ( xdata.getMonth () == mm - 1 ) && ( xdata.getDate() == gg ) )
					check = true;
				else
					check = false;
			}
			else
				check = false;
			return check;
		}

		// Réagir au clic sur une image pour reporter des dates

		$('input[type=image]').live
		('click',
			function()
			{
				texte = $(this).parent().html();
				$("#f_date_debut").val( texte.substring(0,10) );
				$("#f_date_fin").val(  texte.substring(13,23) );
				return false;
			}
		);

		// Réagir au clic sur un bouton (soumission du formulaire)

		$('input[type=button]').click
		(
			function()
			{
				id = $(this).attr('id');
				if( $("#select_periodes option:selected").length==0 || $("#select_classes_groupes option:selected").length==0 )
				{
					$('#ajax_msg').removeAttr("class").addClass("erreur").html("Sélectionnez dans les deux listes !");
					return(false);
				}
				if(id=='ajouter')
				{
					if( !test_dateITA( $("#f_date_debut").val() ) )
					{
						$('#ajax_msg').removeAttr("class").addClass("erreur").html("Date de début au format JJ/MM/AAAA incorrecte !");
						return(false);
					}
					if( !test_dateITA( $("#f_date_fin").val() ) )
					{
						$('#ajax_msg').removeAttr("class").addClass("erreur").html("Date de fin au format JJ/MM/AAAA incorrecte !");
						return(false);
					}
				}
				$('#ajax_msg').removeAttr("class").addClass("loader").html("Demande envoyée... Veuillez patienter.");
				// grouper les select multiples => normalement pas besoin si name de la forme nom[], mais ça plante curieusement sur le serveur competences.sesamath.net
				// alors j'ai remplacé le $("form").serialize() par les tableaux maison et mis un explode dans le fichier ajax
				var select_periodes = new Array(); $("#select_periodes option:selected").each(function(){select_periodes.push($(this).val());});
				var select_classes_groupes  = new Array(); $("#select_classes_groupes option:selected").each(function(){select_classes_groupes.push($(this).val());});
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER+'&action='+id,
						data : 'select_periodes=' + select_periodes + '&select_classes_groupes=' + select_classes_groupes + '&f_date_debut=' + $("#f_date_debut").val() + '&f_date_fin=' + $("#f_date_fin").val(),
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html("Echec de la connexion ! Veuillez recommencer.");
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,6)!='<hr />')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('#ajax_msg').removeAttr("class").addClass("valide").html("Demande réalisée !");
								$('#bilan').html(responseHTML);
								infobulle();
							}
						}
					}
				);
			}
		);

		// Initialisation : charger au chargement l'affichage du bilan

		$('#ajax_msg').addClass("loader").html("Chargement en cours... Veuillez patienter.");
		$.ajax
		(
			{
				type : 'GET',
				url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER+'&action=initialiser',
				data : '',
				dataType : "html",
				error : function(msg,string)
				{
					$('#ajax_msg').removeAttr("class").addClass("alerte").html("Echec de la connexion !");
					return false;
				},
				success : function(responseHTML)
				{
					maj_clock(1);
					if(responseHTML.substring(0,6)!='<hr />')
					{
						$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
					}
					else
					{
						$('#ajax_msg').removeAttr("class").html("&nbsp;");
						$('#bilan').html(responseHTML);
						infobulle();
					}
				}
			}
		);

	}
);
