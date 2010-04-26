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

		function curseur()
		{
			if($("#f_profil").val()=='webmestre')
			{
				$('#f_password').focus();
			}
			else if($("#f_login").length)
			{
				$('#f_login').focus();
			}
		}

		// Appel en ajax pour initialiser le formulaire au chargement
		function chargement()
		{
			$.ajax
			(
				{
					type : 'POST',
					url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
					data : 'f_action=initialiser&f_base='+$("#f_base").val()+'&f_profil='+$("#f_profil").val(),
					dataType : "html",
					error : function(msg,string)
					{
						$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion !');
						return false;
					},
					success : function(responseHTML)
					{
						if(responseHTML.substring(0,18)!='<label class="tab"')
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
						}
						else
						{
							$("fieldset").html(responseHTML);
							curseur();
						}
					}
				}
			);
		}
		chargement();

		// Choix dans le formulaire des structures => Afficher le formulaire de la structure
		$('#f_choisir').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$('#ajax_msg').removeAttr("class").addClass("loader").html("Chargement en cours... Veuillez patienter.");
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'f_action=charger&f_base='+$('#f_base option:selected').val()+'&f_profil='+$("#f_profil").val(),
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion !');
							return false;
						},
						success : function(responseHTML)
						{
							if(responseHTML.substring(0,18)!='<label class="tab"')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$("fieldset").html(responseHTML);
								curseur();
							}
						}
					}
				);
			}
		);

		// Clic sur le lien pour changer de structure
		$('#structure_changer').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$('#ajax_msg').removeAttr("class").addClass("loader").html("Chargement en cours... Veuillez patienter.");
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?dossier='+DOSSIER+'&fichier='+FICHIER,
						data : 'f_action=choisir&f_base='+$('#f_base').val()+'&f_profil='+$("#f_profil").val(),
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion !');
							return false;
						},
						success : function(responseHTML)
						{
							if(responseHTML.substring(0,18)!='<label class="tab"')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$("fieldset").html(responseHTML);
								curseur();
							}
						}
					}
				);
			}
		);

		// Le formulaire qui va être analysé et traité en AJAX
		var formulaire = $('form');

		// Vérifier la validité du formulaire (avec jquery.validate.js)
		var validation = formulaire.validate
		(
			{
				rules :
				{
					f_base     : { required:true },
					f_login    : { required:true , maxlength:20 },
					f_password : { required:true , maxlength:20 }
				},
				messages :
				{
					f_base     : { required:"établissement manquant" },
					f_login    : { required:"login manquant" , maxlength:"20 caractères maximum" },
					f_password : { required:"mot de passe manquant" , maxlength:"20 caractères maximum" }
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
				if($('#f_login').val()!='connexion ENT')
				{
					$(this).ajaxSubmit(ajaxOptions);
					return false;
				}
				else
				{
					document.location.href = './index.php?fichier=login_SSO&f_base='+$('#f_base').val()+'&f_sso='+$('#f_sso').val();
					return false;
				}
			}
		); 

		// Fonction précédent l'envoi du formulaire (avec jquery.form.js)
		function test_form_avant_envoi(formData, jqForm, options)
		{
			$('#ajax_msg').removeAttr("class").html("&nbsp;");
			var readytogo = validation.form();
			if(readytogo)
			{
				$('#f_submit').hide();
				$('#ajax_msg').removeAttr("class").addClass("loader").html("Soumission du formulaire en cours... Veuillez patienter.");
			}
			return readytogo;
		}

		// Fonction suivant l'envoi du formulaire (avec jquery.form.js)
		function retour_form_erreur(msg,string)
		{
			$('#f_submit').show();
			$('#ajax_msg').removeAttr("class").addClass("alerte").html("Echec de la connexion ! Veuillez valider de nouveau.");
		}

		// Fonction suivant l'envoi du formulaire (avec jquery.form.js)
		function retour_form_valide(responseHTML)
		{
			$('#f_submit').show();
			if((responseHTML=='eleve')||(responseHTML=='professeur')||(responseHTML=='directeur')||(responseHTML=='administrateur')||(responseHTML=='webmestre'))
			{
				$('#ajax_msg').removeAttr("class").addClass("valide").html("Identification réussie !");
				document.location.href = './index.php?dossier='+responseHTML;
			}
			else
			{
				$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
			}
		} 

	}
);

// Konami Code !!!
jQuery
(
	function()
	{
		var kKeys = [];
		function Kpress(e)
		{
			kKeys.push(e.keyCode);
			if (kKeys.toString().indexOf("38,38,40,40,37,39,37,39,66,65") >= 0)
			{
				jQuery(this).unbind('keydown', Kpress);
				setVolume(50);play("bennyhill");
				$('h1,h2,p,form,hr,ul,div').fadeOut(2000,function(){	// si on applique le fadeout au body, la musique s'arrête !
 					$('h1,h2,p,form,hr,ul,div').remove();
					$('body').append('<p class="hc"><img src="./_img/konami/laurel_hardy.gif" alt=""/><br /><img src="./_img/konami/piste_danse.gif" alt=""/></p>').fadeIn(2000); 
				});
			}
		}
		jQuery(document).keydown(Kpress);
	}
);
