<?php
/**
 * @version $Id$
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

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
$TITRE = "L'environnement administrateur";
?>
<h2>Connexion</h2>
<p>
	Pour se connecter comme administrateur, il faut sélectionner son établissement, cocher la case "administrateur" et saisir son mot de passe.
</p>

<h2>Changer son mot de passe</h2>
<p>
	L'administrateur peut modifier son mot de passe.<br />
	Les mots de passe sont cryptés et ne peuvent pas être renvoyés. En cas d'oubli du mot de passe administrateur, <?php echo mailto('thomas.crespin@sesamath.net','Mot de passe administrateur oublié','contactez-moi'); ?> depuis votre messagerie académique.
</p>

<h2>Fonctionnalités</h2>
<p>L'administrateur peut :</p>
<ul class="puce">
	<li>choisir les matières, les niveaux, et autres caractéristiques de l'établissement</li>
	<li>importer les élèves et les classes de l'établissement</li>
	<li>importer les professeurs et les personnels de direction de l'établissement</li>
	<li>gérer les classes et les groupes de l'établissement</li>
	<li>gérer les élèves et leurs affectations aux classes et groupes</li>
	<li>gérer les professeurs et leurs affectations aux classes, groupes et matières</li>
	<li>définir des professeurs principaux et des professeurs coordonnateurs</li>
	<li>gérer les personnels de direction</li>
	<li>gérer les comptes désactivés</li>
	<li>consulter les référentiels de compétences utilisés dans l'établissement</li>
	<li>choisir le mode de calcul du score d'un élève associé à un item, et son état de validation</li>
</ul>
<p>
	L'administrateur de <em>SACoche</em> a la charge de distribuer les codes d'accès aux différents utilisateurs.
</p>

<h2>Avertissement</h2>
<p>
	<span class="danger">Le compte administrateur est sensible</span>, puisqu'il permet d'effacer des données (élèves et scores associés, professeurs et devoirs associés, etc.) d'un établissement !<br />
	Il doit donc être utilisé avec sagesse et prudence...
</p>

<h2>Documentations associées</h2>
<ul class="puce">
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_matieres">DOC : Gestion des matières</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_niveaux">DOC : Gestion des niveaux</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_periodes">DOC : Gestion des périodes</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_paliers_socle">DOC : Gestion des paliers du socle</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_format_logins">DOC : Gestion du format des noms d'utilisateurs</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_mode_identification">DOC : Gestion du mode d'identification</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=delai_inactivite_deconnexion_automatique">DOC : Délai d'inactivité et déconnexion automatique</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_options_eleve">DOC : Gestion des options de l'environnement élève</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=import_classes_eleves_Sconet">DOC : Import classes / élèves depuis Sconet</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=import_classes_eleves_tableur">DOC : Import classes / élèves avec un tableur</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=import_professeurs_directeurs_Sconet">DOC : Import professeurs / directeurs depuis Sconet</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=import_professeurs_directeurs_tableur">DOC : Import professeurs / directeurs avec un tableur</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=force_login_mdp_tableur">DOC : Imposer identifiants SACoche avec un tableur</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=import_identifiant_Gepi_SACoche">DOC : Import des identifiants de Gepi dans SACoche</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_classes">DOC : Gestion des classes</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_groupes">DOC : Gestion des groupes</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_eleves">DOC : Gestion des élèves</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_professeurs">DOC : Gestion des professeurs</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_directeurs">DOC : Gestion des directeurs</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=gestion_statuts">DOC : Statuts : désactiver / réintégrer / supprimer</a></span></li>
	<li><span class="manuel"><a href="./aide.php?fichier=calcul_scores_etats_acquisitions">DOC : Calcul des scores et des états d'acquisitions</a></span></li>
</ul>
