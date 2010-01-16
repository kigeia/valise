<?php
/**
 * @version $Id: integration_ENT_departement_Loire.php 8 2009-10-30 20:56:02Z thomas $
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
$TITRE = "Intégration ENT département Loire";
?>

<h2>Introduction</h2>
<p>
	Si un établissement est inscrit à l'ENT <em>Cybercollège 42</em> mis en place sur le département de la <em>Loire</em> par le Conseil Général, les utilisateurs peuvent se connecter à <em>SACoche</em> en SSO avec leurs identifiants de l'ENT.
</p>

<h2>Préalable</h2>
<p>L'administrateur doit avoir importé les utilisateurs dans <em>SACoche</em></p>
<ul class="puce">
	<li>Menu <em>[Import / Export]</em> puis <em>[Importer élèves & classes]</em></li>
	<li>Menu <em>[Import / Export]</em> puis <em>[Importer professeurs & directeurs]</em></li>
</ul>
<p>Des noms d'utilisateurs et des mots de passe seront alors générés par <em>SACoche</em> ; il ne seront pas utilisés si on choisit ensuite un mode de connexion lié à un ENT.</p>

<h2>Indiquer le mode d'identification</h2>
<p>
	L'administrateur doit indiquer que la connexion s'effectuera avec l'authentification de l'ENT.<br />
	<ul class="puce">
		<li>En administrateur de <em>SACoche</em>, il faut passer par le menu <em>[Paramétrages]</em> puis <em>[Mode d'identification]</em>, cocher le bon bouton et valider.</li>
		<li>En administrateur de <em>Cybercollège 42</em>, il faut ajouter dans la gestion du Portail de l'ENT un service avec comme type de SSO <em>[SSO standard]</em>.</li>
	</ul>
</p>

<h2>Importer l'identifiant de l'ENT</h2>
<p>
	Lorsque l'utilisateur est connecté à l'ENT, le serveur d'authentification revoie un identifiant (pour <em>Cybercollège 42</em> il s'agit numéro interne "uid"). <em>SACoche</em> doit le connaître pour établir la liaison.
</p>
<ul class="puce">
	<li>En administrateur de <em>Cybercollège 42</em>, récupérer le fichier au format csv contenant les colonnes suivantes :</li>
</ul>
<table>
	<tbody>
		<tr><th>rne</th><th>uid</th><th>profil</th><th>prenom</th><th>nom</th><th>login</th><th>mdp</th></tr>
	</tbody>
</table>
<p />
<ul class="puce">
	<li>Se connecter avec son compte administrateur à <em>SACoche</em>.</li>
	<li>Menu <em>[Import / Export]</em> puis <em>[Importer identifiant ent]</em>.</li>
	<li>Cliquer sur <em>[Parcourir...]</em> et transférer le fichier précédent.</li>
</ul>
<p>La comparaison se fait sur les noms et prénoms ; comme c'est le même fichier Sconet qui est utilisé pour l'ENT et <em>SACoche</em>, ils devraient correspondre (il peut falloir traiter manuellement quelques cas d'homonymies).</p>

<h2>Mise à jour en cours d'année</h2>
<p>En cours d'année les données peuvent être mises à jour manuellement ou de la même façon :</p>
<ul class="puce">
	<li>Import des utilisateurs dans l'ENT.</li>
	<li>Import des utilisateurs dans <em>SACoche</em>.</li>
	<li>Récupération du csv de l'ENT.</li>
	<li>Import du fichier csv de l'ENT dans <em>SACoche</em>.</li>
</ul>

<h2>Remarques diverses</h2>
<ul class="puce">
	<li>Les deux modes de connexion ne sont pas compatibles, il faut choisir entre utiliser l'identification de l'ENT ou des identifiants <em>SACoche</em>.</li>
	<li>L'administrateur de <em>SACoche</em> est alors le seul utilisateur qui accède à <em>SACoche</em> sans passer par l'identification de l'ENT.</li>
	<li>Pour se connecter en tant qu'élève ou professeur ou directeur, il suffit en page d'accueil de cliquer sur <em>[Accéder à son espace]</em> : toute la suite est gérée automatiquement.</li>
	<li>Lors d'une déconnexion de <em>SACoche</em>, l'utilisateur n'est pas déconnecté de l'ENT (volontairement), ce qui fait que n'importe qui peut de nouveau entrer dans <em>SACoche</em> à sa place tant que le navigateur n'est pas entièrement fermé.</li>
</ul>
