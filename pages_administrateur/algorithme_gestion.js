/**
 * @version $Id: algorithme_gestion.js 8 2009-10-30 20:56:02Z thomas $
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
		}
		// Appel de la fonction au chargement de la page puis à chaque changement de méthode
		actualiser_select_limite();
		$('#f_methode').change( actualiser_select_limite );


		// Demande de soumission du formulaire
		$('#calculer').click
		(
			function()
			{
				$('#action').val('calculer');
				formulaire.submit();
			}
		);
		$('#enregistrer').click
		(
			function()
			{
				$('#action').val('enregistrer');
				formulaire.submit();
			}
		);

		// Vaiables globales
		var memo_valeurRR = 0;
		var memo_valeurR  = 0;
		var memo_valeurV  = 0;
		var memo_valeurVV = 0;
		var memo_methode  = '';
		var memo_limite   = 0;
		var memo_seuilR   = 0;
		var memo_seuilV   = 0;
		// Demande d'initialisation du formulaire avec les valeurs de l'établissement
		// Un simple boutton de type "reset" ne peut être utilisé en cas d'enregistrement en cours de procédure
		$('#initialiser_etablissement').click
		(
			function()
			{
				$('#valeurRR').val(memo_valeurRR);
				$('#valeurR').val(memo_valeurR);
				$('#valeurV').val(memo_valeurV);
				$('#valeurVV').val(memo_valeurVV);
				$('#f_methode option[value='+memo_methode+']').attr("selected",true);
				$('#f_limite option[value='+memo_limite+']').attr("selected",true);
				$('#seuilR').val(memo_seuilR);
				$('#seuilV').val(memo_seuilV);
				actualiser_select_limite();
			}
		);
		// Donc il faut retenir les valeurs initiales et les replacer
		function memoriser_valeurs()
		{
			memo_valeurRR = $('#valeurRR').val();
			memo_valeurR  = $('#valeurR').val();
			memo_valeurV  = $('#valeurV').val();
			memo_valeurVV = $('#valeurVV').val();
			memo_methode  = $('#f_methode option:selected').val();
			memo_limite   = $('#f_limite option:selected').val();
			memo_seuilR   = $('#seuilR').val();
			memo_seuilV   = $('#seuilV').val();
		}
		memoriser_valeurs();

		// Demande d'initialisation du formulaire avec les valeurs par défaut
		$('#initialiser_defaut').click
		(
			function()
			{
				$('#valeurRR').val(0);
				$('#valeurR').val(33);
				$('#valeurV').val(67);
				$('#valeurVV').val(100);
				$('#f_methode option[value="geometrique"]').attr("selected",true);
				$('#f_limite option[value="5"]').attr("selected",true);
				$('#seuilR').val(40);
				$('#seuilV').val(60);
				actualiser_select_limite();
			}
		);

		// Le formulaire qui va être analysé et traité en AJAX
		var formulaire = $("#form_input");

		// Vérifier la validité du formulaire (avec jquery.validate.js)
		var validation = formulaire.validate
		(
			{
				rules :
				{
					valeurRR  : { required:true, digits:true },
					valeurR   : { required:true, digits:true },
					valeurV   : { required:true, digits:true },
					valeurVV  : { required:true, digits:true },
					f_methode : { required:true },
					f_limite  : { required:true },
					seuilR    : { required:true, digits:true },
					seuilV    : { required:true, digits:true }
				},
				messages :
				{
					valeurRR :  { required:"valeur requise", digits:"nombre entier requis" },
					valeurR :   { required:"valeur requise", digits:"nombre entier requis" },
					valeurV :   { required:"valeur requise", digits:"nombre entier requis" },
					valeurVV :  { required:"valeur requise", digits:"nombre entier requis" },
					f_methode : { required:"méthode requise" },
					f_limite :  { required:"méthode requise" },
					seuilR :    { required:"valeur requise", digits:"nombre entier requis" },
					seuilV :    { required:"valeur requise", digits:"nombre entier requis" }
				},
				errorElement : "label",
				errorClass : "erreur",
				errorPlacement : function(error,element) { element.after(error); }
				// success: function(label) {label.text("ok").removeAttr("class").addClass("valide");} Pas pour des champs soumis à vérification PHP
			}
		);

		// Options d'envoi du formulaire (avec jquery.form.js)
		var ajaxOptions =
		{
			url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
			type : 'POST',
			dataType : "html",
			clearForm : false,
			resetForm : false,
			target : "#ajax_msg",
			beforeSubmit : test_form_avant_envoi,
			error : retour_form_erreur,
			success : retour_form_valide
		};

		// Envoi du formulaire (avec jquery.form.js)
    formulaire.submit
		(
			function()
			{
				$(this).ajaxSubmit(ajaxOptions);
				return false;
			}
		); 

		// Fonction précédent l'envoi du formulaire (avec jquery.form.js)
		function test_form_avant_envoi(formData, jqForm, options)
		{
			$('#ajax_msg').removeAttr("class").html("&nbsp;");
			var readytogo = validation.form();
			if(readytogo)
			{
				readytogo = false;
				if( (Math.min($('#valeurRR').val(),$('#valeurR').val(),$('#valeurV').val(),$('#valeurVV').val())<0) || (Math.max($('#valeurRR').val(),$('#valeurR').val(),$('#valeurV').val(),$('#valeurVV').val())>100) )
				{
					$('#ajax_msg').removeAttr("class").addClass("erreur").html("Valeur d'un code : valeurs entre 0 et 100 requises.").show();
				}
				else if( (parseInt($('#valeurRR').val())>parseInt($('#valeurR').val())) || (parseInt($('#valeurR').val())>parseInt($('#valeurV').val())) || (parseInt($('#valeurV').val())>parseInt($('#valeurVV').val())) )
				{
					$('#ajax_msg').removeAttr("class").addClass("erreur").html("Valeur d'un code : valeurs croissantes requises.").show();
				}
				else if( (Math.min($('#seuilR').val(),$('#seuilV').val())<0) || (Math.max($('#seuilR').val(),$('#seuilV').val())>100) )
				{
					$('#ajax_msg').removeAttr("class").addClass("erreur").html("Seuil d'aquisition : valeurs entre 0 et 100 requises.").show();
				}
				else if( parseInt($('#seuilR').val()) > parseInt($('#seuilV').val()) )
				{
					$('#ajax_msg').removeAttr("class").addClass("erreur").html("Seuil d'aquisition : valeurs croissantes requises.").show();
				}
				else
				{
					readytogo = true;
				}
			}
			if(readytogo)
			{
				if( $('#action').val()=='calculer' )
				{
					$('#bilan table tbody').hide();
				}
				else if( $('#action').val()=='enregistrer' )
				{
				memoriser_valeurs();
				}
				$('#ajax_msg').removeAttr("class").addClass("loader").html("Demande envoyée... Veuillez patienter.").show();
			}
			return readytogo;
		}

		// Fonction suivant l'envoi du formulaire (avec jquery.form.js)
		function retour_form_erreur(msg,string)
		{
			$('#ajax_msg').removeAttr("class").addClass("alerte").html("Echec de la connexion ! Veuillez valider de nouveau.");
		}

		// Fonction suivant l'envoi du formulaire (avec jquery.form.js)
		function retour_form_valide(responseHTML)
		{
			maj_clock(1);
			if(responseHTML.substring(0,1)!='<')
			{
				$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
			}
			else if(responseHTML.substring(0,4)=='<tr>')
			{
				$('#ajax_msg').removeAttr("class").addClass("valide").html("Calcul effectué !");
				$('#bilan table tbody').html(responseHTML).show();
			}
			else if(responseHTML.substring(0,4)=='<ok>')
			{
				$('#ajax_msg').removeAttr("class").addClass("valide").html("Valeurs mémorisées !");
			}
		} 

		// Initialisation
		formulaire.submit();

	}
);
