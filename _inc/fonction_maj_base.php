<?php
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

// Fonction pour mettre à jour la base. Ce script est appelé :
// + par un administrateur après une restauration de la base (automatique)
// + par un utilisateur arrivant sur le portail d'identification de la structure si besoin il y a (automatique)

function maj_base($version_actuelle)
{
	if($version_actuelle=='2010-05-15')
	{
		$version_actuelle = '2010-06-03';
		// script pour migrer vers la version suivante : date/heure de dernière connexion effective
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_connexion_date DATETIME NOT NULL AFTER user_statut' );
		// y compris la mise à jour du champ "version_base" justement
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_actuelle.'" WHERE parametre_nom="version_base" LIMIT 1' );
	}
	if($version_actuelle=='2010-06-03')
	{
		$version_actuelle = '2010-06-12';
		// script pour migrer vers la version suivante : date/heure de dernière tentative de connexion
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD user_tentative_date DATETIME NOT NULL AFTER user_statut' );
		// y compris la mise à jour du champ "version_base" justement
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_actuelle.'" WHERE parametre_nom="version_base" LIMIT 1' );
	}
	if($version_actuelle=='2010-06-12')
	{
		$version_actuelle = '2010-07-04';
		// script pour migrer vers la version suivante : ajout d'index
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_groupe ADD INDEX groupe_type (groupe_type)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_groupe ADD INDEX groupe_prof_id (groupe_prof_id)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre ADD PRIMARY KEY (parametre_nom)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD UNIQUE (user_login)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD INDEX user_profil (user_profil)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD INDEX user_statut (user_statut)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_user ADD INDEX user_id_ent (user_id_ent)' );
		// y compris la mise à jour du champ "version_base" justement
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_actuelle.'" WHERE parametre_nom="version_base" LIMIT 1' );
	}
	if($version_actuelle=='2010-07-04')
	{
		$version_actuelle = '2010-07-13';
		// script pour migrer vers la version suivante : mise à jour majeure du socle
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_socle_section CHANGE section_nom section_nom VARCHAR(165) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_pilier SET pilier_nom=REPLACE(pilier_nom,"Pilier ","Compétence ")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_pilier SET pilier_nom=REPLACE(pilier_nom,"\'","’")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom=REPLACE(section_nom,"\'","’")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom=REPLACE(entree_nom,"\'","’")' );
		// mise à jour du socle - palier 1 - domaines
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - vocabulaire" WHERE section_id=4 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - grammaire" WHERE section_id=5 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - orthographe" WHERE section_id=6 LIMIT 1' );
		// mise à jour du socle - palier 1 - items
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Lire seul et écouter lire des textes du patrimoine et des œuvres intégrales de la littérature de jeunesse adaptés à son âge." WHERE entree_id=5 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Distinguer le présent du futur et du passé." WHERE entree_id=20 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Repérer des cases, des nœuds d’un quadrillage." WHERE entree_id=36 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Être précis et soigneux dans les mesures et les calculs." WHERE entree_id=39 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Reconnaître les emblèmes et les symboles de la République française." WHERE entree_id=43 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Appliquer les codes de la politesse dans ses relations avec ses camarades, avec les adultes de l’école et hors de l’école, avec le maître au sein de la classe." WHERE entree_id=46 LIMIT 1' );
		// mise à jour du socle - palier 2 - domaines
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - vocabulaire" WHERE section_id=16 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - grammaire" WHERE section_id=17 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Étude de la langue - orthographe" WHERE section_id=18 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Maîtriser des connaissances dans divers domaines scientifiques et les mobiliser dans des contextes scientifiques différents et dans des activités de la vie courante" WHERE section_id=29 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Environnement et développement durable" WHERE section_id=30 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Avoir des repères relevant du temps et de l’espace" WHERE section_id=36 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_ordre=4 WHERE section_id=38 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_section VALUES ( 73,  9, 3, "Lire et pratiquer différents langages")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Avoir une bonne maîtrise de son corps et une pratique physique (sportive ou artistique)" WHERE section_id=43 LIMIT 1' );
		// mise à jour du socle - palier 2 - items
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Dire de mémoire, de façon expressive, une dizaine de poèmes et de textes en prose." WHERE entree_id=51 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Lire seul des textes du patrimoine et des œuvres intégrales de la littérature de jeunesse, adaptés à son âge." WHERE entree_id=53 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser des instruments de mesure." WHERE entree_id=105 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Mobiliser ses connaissances pour comprendre quelques questions liées à l’environnement et au développement durable et agir en conséquence." WHERE entree_id=123 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Connaitre et maîtriser les fonctions de base d’un ordinateur et de ses périphériques." WHERE entree_id=124 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET section_id=73 , entree_nom="Lire et utiliser textes, cartes, croquis, graphiques." WHERE entree_id=132 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_ordre=entree_ordre-1 WHERE section_id=36 LIMIT 4' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Lire des œuvres majeures du patrimoine et de la littérature pour la jeunesse." WHERE entree_id=137 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Reconnaître et décrire des œuvres préalablement étudiées." WHERE entree_id=140 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Interpréter de mémoire une chanson, participer à un jeu rythmique ; repérer des éléments musicaux caractéristiques simples." WHERE entree_id=142 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Inventer et réaliser des textes, des œuvres plastiques, des chorégraphies ou des enchaînements, à visée artistique ou expressive." WHERE entree_id=143 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Respecter les règles de la vie collective." WHERE entree_id=147 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Être persévérant dans toutes les activités." WHERE entree_id=150 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Commencer à savoir s’autoévaluer dans des situations simples." WHERE entree_id=151 LIMIT 1' );
		// mise à jour du socle - palier 3 - domaines
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Dire" WHERE section_id=46 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_section WHERE section_id=47 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Savoir utiliser des connaissances dans divers domaines scientifiques" WHERE section_id=55 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Environnement et développement durable" WHERE section_id=56 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="S’approprier un environnement informatique de travail" WHERE section_id=57 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Adopter une attitude responsable" WHERE section_id=58 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Créer, produire, traiter, exploiter des données" WHERE section_id=59 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="S’informer, se documenter" WHERE section_id=60 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Communiquer, échanger" WHERE section_id=61 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Avoir des connaissances et des repères" WHERE section_id=62 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Situer dans le temps, l’espace, les civilisations" , section_ordre=2 WHERE section_id=67 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Lire et pratiquer différents langages" , section_ordre=3 WHERE section_id=66 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_section VALUES ( 74, 16, 4, "Faire preuve de sensibilité, d’esprit critique, de curiosité")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_section WHERE section_id=63 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_section WHERE section_id=64 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_section WHERE section_id=65 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Être acteur de son parcours de formation et d’orientation" WHERE section_id=70 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_section SET section_nom="Être capable de mobiliser ses ressources intellectuelles et physiques dans diverses situations" WHERE section_id=71 LIMIT 1' );
		// mise à jour du socle - palier 3 - items
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Adapter son mode de lecture à la nature du texte proposé et à l’objectif poursuivi." WHERE entree_id=157 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 282, 44,  1, "Repérer les informations dans un texte à partir des éléments explicites et des éléments implicites nécessaires.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser ses capacités de raisonnement, ses connaissances sur la langue, savoir faire appel à des outils appropriés pour lire." , entree_ordre=2 WHERE entree_id=158 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Dégager, par écrit ou oralement, l’essentiel d’un texte lu." , entree_ordre=3 WHERE entree_id=159 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Manifester, par des moyens divers, sa compréhension de textes variés." , entree_ordre=4 WHERE entree_id=160 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=161 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=162 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Reproduire un document sans erreur et avec une présentation adaptée." WHERE entree_id=163 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Écrire lisiblement un texte, spontanément ou sous la dictée, en respectant l’orthographe et la grammaire." WHERE entree_id=164 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=165 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Rédiger un texte bref, cohérent et ponctué, en réponse à une question ou à partir de consignes données." , entree_ordre=2 WHERE entree_id=166 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser ses capacités de raisonnement, ses connaissances sur la langue, savoir faire appel à des outils variés pour améliorer son texte." , entree_ordre=3 WHERE entree_id=167 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=168 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=169 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 283, 46,  0, "Formuler clairement un propos simple.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Développer de façon suivie un propos en public sur un sujet déterminé." , entree_ordre=1 WHERE entree_id=170 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Adapter sa prise de parole à la situation de communication." , entree_ordre=2 WHERE entree_id=171 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Participer à un débat, à un échange verbal." , entree_ordre=3 WHERE entree_id=172 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=173 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=174 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=175 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=176 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Établir un contact social." WHERE entree_id=177 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Écrire un message simple." WHERE entree_id=190 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Écrire un court récit, une description." WHERE entree_id=192 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=198 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=199 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=200 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=201 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Nombres et calculs : connaître et utiliser les nombres entiers, décimaux et fractionnaires ; mener à bien un calcul mental, à la main, à la calculatrice, avec un ordinateur." , entree_ordre=1 WHERE entree_id=202 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=203 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=204 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Géométrie : connaître et représenter des figures géométriques et des objets de l’espace ; utiliser leurs propriétés." , entree_ordre=2 WHERE entree_id=205 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=206 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=207 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Grandeurs et mesures : réaliser des mesures (longueurs, durées, …), calculer des valeurs (volumes, vitesses, …) en utilisant différentes unités." , entree_ordre=3 WHERE entree_id=208 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=209 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="L’univers et la Terre : organisation de l’univers ; structure et évolution au cours des temps géologiques de la Terre, phénomènes physiques." WHERE entree_id=210 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Le vivant : unité d’organisation et diversité ; fonctionnement des organismes vivants, évolution des espèces, organisation et fonctionnement du corps humain." WHERE entree_id=212 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Les objets techniques : analyse, conception et réalisation ; fonctionnement et conditions d’utilisation." WHERE entree_id=214 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Mobiliser ses connaissances pour comprendre des questions liées à l’environnement et au développement durable." WHERE entree_id=215 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser, gérer des espaces de stockage à disposition." , entree_ordre=0 WHERE entree_id=218 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser les périphériques à disposition." , entree_ordre=1 WHERE entree_id=220 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Utiliser les logiciels et les services à disposition." , entree_ordre=2 WHERE entree_id=216 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=217 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=219 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=221 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Connaître et respecter les règles élémentaires du droit relatif à sa pratique." WHERE entree_id=222 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Protéger sa personne et ses données." WHERE entree_id=223 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Faire preuve d’esprit critique face à l’information et à son traitement." , entree_ordre=2 WHERE entree_id=225 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Participer à des travaux collaboratifs en connaissant les enjeux et en respectant les règles." , entree_ordre=3 WHERE entree_id=228 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=224 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=226 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=227 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Saisir et mettre en page un texte." WHERE entree_id=229 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Traiter une image, un son ou une vidéo." , entree_ordre=1 WHERE entree_id=235 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Organiser la composition du document, prévoir sa présentation en fonction de sa destination." WHERE entree_id=231 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Différencier une situation simulée ou modélisée d’une situation réelle." , entree_ordre=3 WHERE entree_id=234 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=230 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=232 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=233 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Consulter des bases de données documentaires en mode simple (plein texte)." WHERE entree_id=236 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Identifier, trier et évaluer des ressources." , entree_ordre=1 WHERE entree_id=239 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Chercher et sélectionner l’information demandée." WHERE entree_id=238 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=237 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=240 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Écrire, envoyer, diffuser, publier." , entree_ordre=0 WHERE entree_id=243 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Recevoir un commentaire, un message y compris avec pièces jointes." WHERE entree_id=242 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 284, 61,  2, "Exploiter les spécificités des différentes situations de communication en temps réel ou différé.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=241 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=244 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant de l’espace : les grands ensembles physiques et humains et les grands types d’aménagements dans le monde, les principales caractéristiques géographiques de la France et de l’Europe." WHERE entree_id=245 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=246 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=247 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant du temps : les différentes périodes de l’histoire de l’humanité ; les grands traits de l’histoire (politique, sociale, économique, littéraire, artistique, culturelle) de la France et de l’Europe." , section_id=62 WHERE entree_id=249 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=248 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant de la culture littéraire : œuvres littéraires du patrimoine." , section_id=62 , entree_ordre=2 WHERE entree_id=251 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=250 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Relevant de la culture artistique : œuvres picturales, musicales, scéniques, architecturales ou cinématographiques du patrimoine." , section_id=62 , entree_ordre=3 WHERE entree_id=252 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=253 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 285, 62,  4, "Relevant de la culture civique : droits de l’Homme ; formes d’organisation politique, économique et sociale dans l’Union européenne ; place et rôle de l’État en France ; mondialisation ; développement durable.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 286, 67,  0, "Situer des événements, des œuvres littéraires ou artistiques, des découvertes scientifiques ou techniques, des ensembles géographiques.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Identifier la diversité des civilisations, des langues, des sociétés, des religions." , entree_ordre=1 WHERE entree_id=256 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 287, 67,  2, "Établir des liens entre les œuvres (littéraires, artistiques) pour mieux les comprendre.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Mobiliser ses connaissances pour donner du sens à l’actualité." WHERE entree_id=259 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=257 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=258 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Lire et employer différents langages : textes – graphiques – cartes – images – musique." WHERE entree_id=255 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 293, 66,  1, "Connaître et pratiquer diverses formes d’expression à visée littéraire.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Connaître et pratiquer diverses formes d’expression à visée artistique." , section_id=66 , entree_ordre=2 WHERE entree_id=254 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 288, 74,  0, "Être sensible aux enjeux esthétiques et humains d’un texte littéraire.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 289, 74,  1, "Être sensible aux enjeux esthétiques et humains d’une œuvre artistique.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 290, 74,  2, "Être capable de porter un regard critique sur un fait, un document, une œuvre.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 291, 74,  3, "Manifester sa curiosité pour l’actualité et pour les activités culturelles ou artistiques.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Principaux droits de l’Homme et du citoyen." WHERE entree_id=260 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Valeurs, symboles, institutions de la République." WHERE entree_id=261 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Règles fondamentales de la démocratie et de la justice." WHERE entree_id=262 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Grandes institutions de l’Union européenne et rôle des grands organismes internationaux." WHERE entree_id=263 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Rôle de la défense nationale." WHERE entree_id=264 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Fonctionnement et rôle de différents médias." WHERE entree_id=265 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Respecter les règles de la vie collective." WHERE entree_id=266 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Se familiariser avec l’environnement économique, les entreprises, les métiers de secteurs et de niveaux de qualification variés." , entree_ordre=0 WHERE entree_id=272 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Connaître les parcours de formation correspondant à ces métiers et les possibilités de s’y intégrer." , entree_ordre=1 WHERE entree_id=273 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_socle_entree WHERE entree_id=271 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_socle_entree VALUES ( 292, 70,  2, "Savoir s’autoévaluer et être capable de décrire ses intérêts, ses compétences et ses acquis.")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Être autonome dans son travail : savoir l’organiser, le planifier, l’anticiper, rechercher et sélectionner des informations utiles." WHERE entree_id=274 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Identifier ses points forts et ses points faibles dans des situations variées." WHERE entree_id=275 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Mobiliser à bon escient ses capacités motrices dans le cadre d’une pratique physique (sportive ou artistique) adaptée à son potentiel." , entree_ordre=2 WHERE entree_id=277 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_ordre=3 WHERE entree_id=276 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="S’engager dans un projet individuel." WHERE entree_id=278 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="S’intégrer et coopérer dans un projet collectif." WHERE entree_id=279 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Manifester curiosité, créativité, motivation à travers des activités conduites ou reconnues par l’établissement." WHERE entree_id=280 LIMIT 1' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Assumer des rôles, prendre des initiatives et des décisions." WHERE entree_id=281 LIMIT 1' );
		// mise à jour des liens des référentiels
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=0 WHERE entree_id IN(161,162,165,168,169,173,174,175,176,241,257,258)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=197 WHERE entree_id IN(198,199,200,201)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=202 WHERE entree_id IN(203,204)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=205 WHERE entree_id IN(206,207)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=208 WHERE entree_id=209' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=216 WHERE entree_id IN(217,221)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=218 WHERE entree_id=219' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=222 WHERE entree_id=224' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=223 WHERE entree_id IN(226,227)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=229 WHERE entree_id=230' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=197 WHERE entree_id IN(232,233)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=216 WHERE entree_id=237' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=238 WHERE entree_id=240' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=243 WHERE entree_id=244' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=245 WHERE entree_id IN(246,247)' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=249 WHERE entree_id=248' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=251 WHERE entree_id=250' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=252 WHERE entree_id=253' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_referentiel_item SET entree_id=273 WHERE entree_id=271' );
		// y compris la mise à jour du champ "version_base" justement
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'OPTIMIZE TABLE sacoche_socle_pilier');
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'OPTIMIZE TABLE sacoche_socle_section');
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'OPTIMIZE TABLE sacoche_socle_entree');
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_actuelle.'" WHERE parametre_nom="version_base" LIMIT 1' );
	}
	if($version_actuelle=='2010-07-13')
	{
		$version_actuelle = '2010-07-15';
		// script pour migrer vers la version suivante : paramétrage codes Lomer / background-color
		// La première instruction ayant été oubliée, quelques tentatives d'installations peuvent être corrompues => corrigé dans la v.2010-07-27.
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_nom parametre_nom VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_NA","#ff9999")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_VA","#ffdd33")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_A","#99ff99")' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_note_style","Lomer")' );
		// y compris la mise à jour du champ "version_base" justement
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_actuelle.'" WHERE parametre_nom="version_base" LIMIT 1' );
	}
	if($version_actuelle=='2010-07-15')
	{
		$version_actuelle = '2010-07-16';
		// script pour migrer vers la version suivante : oubli d'une modification du socle
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_socle_entree SET entree_nom="Organisation et gestion de données : reconnaître des situations de proportionnalité, utiliser des pourcentages, des tableaux, des graphiques ; exploiter des données statistiques et aborder des situations simples de probabilité." , entree_ordre=0 WHERE entree_id=197 LIMIT 1' );
		// y compris la mise à jour du champ "version_base" justement
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_actuelle.'" WHERE parametre_nom="version_base" LIMIT 1' );
	}
	if($version_actuelle=='2010-07-16')
	{
		$version_actuelle = '2010-07-27';
		// Correction du bug signalé dans le passage de la v.2010-07-13 à la v.2010-07-15.
		$DB_ROW = DB::queryRow(SACOCHE_STRUCTURE_BD_NAME , 'SELECT parametre_valeur FROM sacoche_parametre WHERE parametre_nom="css_background-color" LIMIT 1' , null);
		if(count($DB_ROW))
		{
			DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DELETE FROM sacoche_parametre WHERE parametre_nom="css_background-color" LIMIT 1' );
			DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_parametre CHANGE parametre_nom parametre_nom VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL' );
			DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_NA","#ff9999")' );
			DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_VA","#ffdd33")' );
			DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_background-color_A","#99ff99")' );
			DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("css_note_style","Lomer")' );
		}
		// script pour migrer vers la version suivante : ajout de modes de calculs
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_referentiel CHANGE referentiel_calcul_methode referentiel_calcul_methode ENUM( "geometrique", "arithmetique", "classique", "bestof1", "bestof2", "bestof3" ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT "geometrique" COMMENT "Coefficients en progression géométrique, arithmetique, ou moyenne classique non pondérée, ou conservation des meilleurs scores. Valeur surclassant la configuration par défaut." ' );
		// script pour migrer vers la version suivante : ajout de 2 tables pour la validation du socle
		// Les supprimer si elles existent : sinon dans le cas d'une restauration de base à une version antérieure (suivie de cette mise à jour), ces anciennes tables éventuellement existantes ne seraient pas réinitialisées.
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_jointure_user_entree' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_jointure_user_entree (user_id MEDIUMINT(8) UNSIGNED NOT NULL,entree_id SMALLINT(5) UNSIGNED NOT NULL,validation_entree_etat TINYINT(1) NOT NULL COMMENT "1 si validation positive ; 0 si validation négative.",validation_entree_date DATE NOT NULL,validation_entree_info TINYTEXT COLLATE utf8_unicode_ci NOT NULL COMMENT "Enregistrement statique du nom du validateur, conservé les années suivantes.",UNIQUE KEY validation_entree_key (user_id,entree_id),KEY user_id (user_id),KEY entree_id (entree_id) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'DROP TABLE IF EXISTS sacoche_jointure_user_pilier' );
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'CREATE TABLE sacoche_jointure_user_pilier (user_id MEDIUMINT(8) UNSIGNED NOT NULL,pilier_id SMALLINT(5) UNSIGNED NOT NULL,                                                                                                          validation_pilier_date DATE NOT NULL,validation_pilier_info TINYTEXT COLLATE utf8_unicode_ci NOT NULL COMMENT "Enregistrement statique du nom du validateur, conservé les années suivantes.",UNIQUE KEY validation_pilier_key (user_id,pilier_id),KEY user_id (user_id),KEY pilier_id (pilier_id) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ' );
		// y compris la mise à jour du champ "version_base" justement
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_actuelle.'" WHERE parametre_nom="version_base" LIMIT 1' );
	}
	if($version_actuelle=='2010-07-27')
	{
		$version_actuelle = '2010-07-29';
		// script pour migrer vers la version suivante : ajout d'un champ qui va finalement servir pour valider les piliers
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'ALTER TABLE sacoche_jointure_user_pilier ADD validation_pilier_etat TINYINT(1) NOT NULL COMMENT "1 si validation positive ; 0 si validation négative." AFTER pilier_id' );
		// script pour migrer vers la version suivante : ajout de 2 entrées pour gérer les droits de validation
			DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("profil_validation_entree" , "directeur,professeur")' );
			DB::query(SACOCHE_STRUCTURE_BD_NAME , 'INSERT INTO sacoche_parametre VALUES ("profil_validation_pilier" , "directeur,profprincipal")' );
		// y compris la mise à jour du champ "version_base" justement
		DB::query(SACOCHE_STRUCTURE_BD_NAME , 'UPDATE sacoche_parametre SET parametre_valeur="'.$version_actuelle.'" WHERE parametre_nom="version_base" LIMIT 1' );
	}
	// Log de l'action
	ajouter_log('Mise à jour automatique de la base '.SACOCHE_STRUCTURE_BD_NAME.'.');
}

?>