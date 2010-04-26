DROP TABLE IF EXISTS sacoche_socle_entree;

CREATE TABLE sacoche_socle_entree (
	entree_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	section_id SMALLINT(5) UNSIGNED NOT NULL,
	entree_ordre TINYINT(3) UNSIGNED NOT NULL COMMENT "Commence à 0.",
	entree_nom TINYTEXT COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (entree_id),
	KEY section_id (section_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE sacoche_socle_entree DISABLE KEYS;

INSERT INTO sacoche_socle_entree VALUES 
(   1,  1,  0, "S'exprimer clairement à l'oral en utilisant un vocabulaire approprié."),
(   2,  1,  1, "Participer en classe à un échange verbal en respectant les règles de la communication."),
(   3,  1,  2, "Dire de mémoire quelques textes en prose ou poèmes courts."),
(   4,  2,  0, "Lire seul, à haute voix, un texte comprenant des mots connus et inconnus."),
(   5,  2,  1, "Lire seul et écouter lire des textes du patrimoine et des oeuvres intégrales de la littérature de jeunesse adaptés à son âge."),
(   6,  2,  2, "Lire seul et comprendre un énoncé, une consigne simple."),
(   7,  2,  3, "Dégager le thème d'un paragraphe ou d'un texte court."),
(   8,  2,  4, "Lire silencieusement un texte en déchiffrant les mots inconnus et manifester sa compréhension dans un résumé, une reformulation, des réponses à des questions."),
(   9,  3,  0, "Copier un texte court sans erreur dans une écriture cursive lisible et avec une présentation soignée."),
(  10,  3,  1, "Utiliser ses connaissances pour mieux écrire un texte court."),
(  11,  3,  2, "Écrire de manière autonome un texte de cinq à dix lignes."),
(  12,  4,  0, "Utiliser des mots précis pour s’exprimer."),
(  13,  4,  1, "Donner des synonymes."),
(  14,  4,  2, "Trouver un mot de sens opposé."),
(  15,  4,  3, "Regrouper des mots par familles."),
(  16,  4,  4, "Commencer à utiliser l’ordre alphabétique."),
(  17,  5,  0, "Identifier la phrase, le verbe, le nom, l’article, l’adjectif qualificatif, le pronom personnel (sujet)."),
(  18,  5,  1, "Repérer le verbe d’une phrase et son sujet."),
(  19,  5,  2, "Conjuguer les verbes du 1er groupe, être et avoir, au présent, au futur, au passé composé de l’indicatif ; conjuguer les verbes faire, aller, dire, venir, au présent de l’indicatif."),
(  20,  5,  3, "Distinguer le présent, du futur et du passé."),
(  21,  6,  0, "Écrire en respectant les correspondances entre lettres et sons et les règles relatives à la valeur des lettres."),
(  22,  6,  1, "Écrire sans erreur des mots mémorisés."),
(  23,  6,  2, "Orthographier correctement des formes conjuguées, respecter l’accord entre le sujet et le verbe, ainsi que les accords en genre et en nombre dans le groupe nominal."),
(  24,  7,  0, "Écrire, nommer, comparer, ranger les nombres entiers naturels inférieurs à 1000."),
(  25,  7,  1, "Résoudre des problèmes de dénombrement."),
(  26,  7,  2, "Calculer : addition, soustraction, multiplication."),
(  27,  7,  3, "Diviser par 2 et par 5 dans le cas où le quotient exact est entier."),
(  28,  7,  4, "Restituer et utiliser les tables d'addition et de multiplication par 2, 3, 4 et 5."),
(  29,  7,  5, "Calculer mentalement en utilisant des additions, des soustractions et des multiplications simples."),
(  30,  7,  6, "Résoudre des problèmes relevant de l’addition, de la soustraction et de la multiplication."),
(  31,  7,  7, "Utiliser les fonctions de base de la calculatrice."),
(  32,  8,  0, "Situer un objet par rapport à soi ou à un autre objet, donner sa position et décrire son déplacement."),
(  33,  8,  1, "Reconnaître, nommer et décrire les figures planes et les solides usuels."),
(  34,  8,  2, "Utiliser la règle et l'équerre pour tracer avec soin et précision un carré, un rectangle, un triangle rectangle."),
(  35,  8,  3, "Percevoir et reconnaître quelques relations et propriétés géométriques : alignement, angle droit, axe de symétrie, égalité de longueurs."),
(  36,  8,  4, "Repérer des cases, des noeuds d’un quadrillage."),
(  37,  8,  5, "Résoudre un problème géométrique."),
(  38,  9,  0, "Utiliser les unités usuelles de mesure ; estimer une mesure."),
(  39,  9,  1, "Être précis et soigneux dans les tracés, les mesures et les calculs."),
(  40,  9,  2, "Résoudre des problèmes de longueur et de masse."),
(  41, 10,  0, "Utiliser un tableau, un graphique."),
(  42, 10,  1, "Organiser les données d’un énoncé."),
(  43, 11,  0, "Reconnaître les emblèmes et les symboles de la république française."),
(  44, 12,  0, "Respecter les autres et les règles de la vie collective."),
(  45, 12,  1, "Pratiquer un jeu ou un sport collectif en en respectant les règles."),
(  46, 12,  2, "Appliquer les codes de la politesse dans ses relations avec ses camarades, avec les adultes à l'école et hors de l'école, avec le maître au sein de la classe."),
(  47, 13,  0, "S'exprimer à l'oral comme à l'écrit dans un vocabulaire approprié et précis."),
(  48, 13,  1, "Prendre la parole en respectant le niveau de langue adapté."),
(  49, 13,  2, "Répondre à une question par une phrase complète à l’oral."),
(  50, 13,  3, "Prendre part à un dialogue : prendre la parole devant les autres, écouter autrui, formuler et justifier un point de vue."),
(  51, 13,  4, "Dire de mémoire, de façon expressive une dizaine de poèmes et de textes en prose."),
(  52, 14,  0, "Lire avec aisance (à haute voix, silencieusement) un texte."),
(  53, 14,  1, "Lire seul des textes du patrimoine et des oeuvres intégrales de la littérature de jeunesse, adaptés à son âge."),
(  54, 14,  2, "Lire seul et comprendre un énoncé, une consigne."),
(  55, 14,  3, "Dégager le thème d'un texte."),
(  56, 14,  4, "Repérer dans un texte des informations explicites."),
(  57, 14,  5, "Inférer des informations nouvelles (implicites)."),
(  58, 14,  6, "Repérer les effets de choix formels (emploi de certains mots, utilisation d'un niveau de langue)."),
(  59, 14,  7, "Utiliser ses connaissances pour réfléchir sur un texte, mieux le comprendre."),
(  60, 14,  8, "Effectuer, seul, des recherches dans des ouvrages documentaires (livres, produits multimédia)."),
(  61, 14,  9, "Se repérer dans une bibliothèque, une médiathèque."),
(  62, 15,  0, "Copier sans erreur un texte d'au moins quinze lignes en lui donnant une présentation adaptée."),
(  63, 15,  1, "Utiliser ses connaissances pour réfléchir sur un texte, mieux l’écrire."),
(  64, 15,  2, "Répondre à une question par une phrase complète à l’écrit."),
(  65, 15,  3, "Rédiger un texte d'une quinzaine de lignes (récit, description, dialogue, texte poétique, compte rendu) en utilisant ses connaissances en vocabulaire et en grammaire."),
(  66, 16,  0, "Comprendre des mots nouveaux et les utiliser à bon escient."),
(  67, 16,  1, "Maîtriser quelques relations de sens entre les mots."),
(  68, 16,  2, "Maîtriser quelques relations concernant la forme et le sens des mots."),
(  69, 16,  3, "Savoir utiliser un dictionnaire papier ou numérique."),
(  70, 17,  0, "Distinguer les mots selon leur nature."),
(  71, 17,  1, "Identifier les fonctions des mots dans la phrase."),
(  72, 17,  2, "Conjuguer les verbes, utiliser les temps à bon escient."),
(  73, 18,  0, "Maîtriser l'orthographe grammaticale."),
(  74, 18,  1, "Maîtriser l'orthographe lexicale."),
(  75, 18,  2, "Orthographier correctement un texte simple de dix lignes – lors de sa rédaction ou de sa dictée – en se référant aux règles connues d'orthographe et de grammaire ainsi qu'à la connaissance du vocabulaire."),
(  76, 19,  0, "Communiquer, au besoin avec des pauses pour chercher ses mots."),
(  77, 19,  1, "Se présenter ; présenter quelqu’un ; demander à quelqu’un de ses nouvelles en utilisant les formes de politesse les plus élémentaires ; accueil et prise de congé."),
(  78, 19,  2, "Répondre à des questions et en poser (sujets familiers ou besoins immédiats)."),
(  79, 19,  3, "Épeler des mots familiers."),
(  80, 20,  0, "Comprendre les consignes de classe."),
(  81, 20,  1, "Comprendre des mots familiers et des expressions très courantes."),
(  82, 20,  2, "Suivre des instructions courtes et simples."),
(  83, 21,  0, "Reproduire un modèle oral."),
(  84, 21,  1, "Utiliser des expressions et des phrases proches des modèles rencontrés lors des apprentissages."),
(  85, 21,  2, "Lire à haute voix et de manière expressive un texte bref après répétition."),
(  86, 22,  0, "Comprendre des textes courts et simples en s’appuyant sur des éléments connus (indications, informations)."),
(  87, 22,  1, "Se faire une idée du contenu d’un texte informatif simple, accompagné éventuellement d’un document visuel."),
(  88, 23,  0, "Copier des mots isolés et des textes courts."),
(  89, 23,  1, "Écrire un message électronique simple ou une courte carte postale en référence à des modèles."),
(  90, 23,  2, "Renseigner un questionnaire."),
(  91, 23,  3, "Produire de manière autonome quelques phrases."),
(  92, 23,  4, "Écrire sous la dictée des expressions connues."),
(  93, 24,  0, "Écrire, nommer, comparer et utiliser les nombres entiers, les nombres décimaux (jusqu’au centième) et quelques fractions simples."),
(  94, 24,  1, "Restituer les tables d’addition et de multiplication de 2 à 9."),
(  95, 24,  2, "Utiliser les techniques opératoires des quatre opérations sur les nombres entiers et décimaux (pour la division, le diviseur est un nombre entier)."),
(  96, 24,  3, "Ajouter deux fractions décimales ou deux fractions simples de même dénominateur."),
(  97, 24,  4, "Calculer mentalement en utilisant les quatre opérations."),
(  98, 24,  5, "Estimer l’ordre de grandeur d’un résultat."),
(  99, 24,  6, "Résoudre des problèmes relevant des quatre opérations."),
( 100, 24,  7, "Utiliser une calculatrice."),
( 101, 25,  0, "Reconnaître, décrire et nommer les figures et solides usuels."),
( 102, 25,  1, "Utiliser la règle, l’équerre et le compas pour vérifier la nature de figures planes usuelles et les construire avec soin et précision."),
( 103, 25,  2, "Percevoir et reconnaitre parallèles et perpendiculaires."),
( 104, 25,  3, "Résoudre des problèmes de reproduction, de construction."),
( 105, 26,  0, "Utiliser des instruments de mesure ; effectuer des conversions."),
( 106, 26,  1, "Connaître et utiliser les formules du périmètre et de l’aire d’un carré, d’un rectangle et d’un triangle."),
( 107, 26,  2, "Utiliser les unités de mesures usuelles."),
( 108, 26,  3, "Résoudre des problèmes dont la résolution implique des conversions."),
( 109, 27,  0, "Lire, interpréter et construire quelques représentations simples : tableaux, graphiques."),
( 110, 27,  1, "Savoir organiser des informations numériques ou géométriques, justifier et apprécier la vraisemblance d’un résultat."),
( 111, 27,  2, "Résoudre un problème mettant en jeu une situation de proportionnalité."),
( 112, 28,  0, "Pratiquer une démarche d'investigation : savoir observer, questionner."),
( 113, 28,  1, "Manipuler et expérimenter, formuler une hypothèse et la tester, argumenter, mettre à l'essai plusieurs pistes de solutions."),
( 114, 28,  2, "Exprimer et exploiter les résultats d'une mesure et d'une recherche en utilisant un vocabulaire scientifique à l'écrit ou à l'oral."),
( 115, 29,  0, "Le ciel et la Terre."),
( 116, 29,  1, "La matière."),
( 117, 29,  2, "L’énergie."),
( 118, 29,  3, "L’unité et la diversité du vivant."),
( 119, 29,  4, "Le fonctionnement du vivant."),
( 120, 29,  5, "Le fonctionnement du corps humain et la santé."),
( 121, 29,  6, "Les êtres vivants dans leur environnement."),
( 122, 29,  7, "Les objets techniques."),
( 123, 30,  0, "Questions ouvertes sur la thématique de l'environnement et du développement durable en lien avec les autres capacités."),
( 124, 31,  0, "Connaitre et maîtriser les fonctions de base d’un ordinateur et de ses périphériques : fonction des différents éléments, utilisation de la souris."),
( 125, 32,  0, "Prendre conscience des enjeux citoyens de l’usage de l’informatique et de l’internet et adopter une attitude critique face aux résultats obtenus."),
( 126, 33,  0, "Produire un document numérique : texte, image, son."),
( 127, 33,  1, "Utiliser l’outil informatique pour présenter un travail."),
( 128, 34,  0, "Lire un document numérique."),
( 129, 34,  1, "Chercher des informations par voie électronique."),
( 130, 34,  2, "Découvrir les richesses et les limites des ressources de l'internet."),
( 131, 35,  0, "Échanger avec les technologies de l’information et de la communication."),
( 132, 36,  0, "Lire et utiliser différents langages : textes, cartes, croquis, graphiques."),
( 133, 36,  1, "Identifier les périodes de l’histoire au programme."),
( 134, 36,  2, "Connaître et mémoriser les principaux repères chronologiques (évènements et personnages)."),
( 135, 36,  3, "Connaître les principaux caractères géographiques physiques et humains de la région où vit l’élève, de la France et de l’Union européenne, les repérer sur des cartes à différentes échelles."),
( 136, 36,  4, "Comprendre une ou deux questions liées au développement durable et agir en conséquence (l’eau dans la commune, la réduction et le recyclage des déchets)."),
( 137, 37,  0, "Lire des oeuvres majeures du patrimoine et de la littérature pour la jeunesse."),
( 138, 37,  1, "Établir des liens entre les textes lus."),
( 139, 38,  0, "Distinguer les grandes catégories de la création artistique (littérature, musique, danse, théâtre, cinéma, dessin, peinture, sculpture, architecture)."),
( 140, 38,  1, "Reconnaître et décrire des oeuvres préalablement étudiées."),
( 141, 38,  2, "Pratiquer le dessin et diverses formes d’expressions visuelles et plastiques."),
( 142, 38,  3, "Interpréter de mémoire une chanson, participer à un jeu rythmique ; repérer des éléments musicaux caractéristiques, simples."),
( 143, 38,  4, "Inventer et réaliser des textes, des oeuvres plastiques, des chorégraphies ou des enchaînements, à visée artistique ou expressive."),
( 144, 39,  0, "Reconnaître les symboles de la République et de l’Union européenne."),
( 145, 39,  1, "Comprendre les notions de droits et de devoirs, les accepter et les mettre en application."),
( 146, 39,  2, "Avoir conscience de la dignité de la personne humaine et en tirer les conséquences au quotidien."),
( 147, 40,  0, "Respecter les règles de la vie collective, notamment dans les pratiques sportives."),
( 148, 40,  1, "Respecter tous les autres, et notamment appliquer les principes de l’égalité des filles et des garçons."),
( 149, 41,  0, "Respecter des consignes simples, en autonomie."),
( 150, 41,  1, "Etre persévérant dans toutes les activités."),
( 151, 41,  2, "Commencer à savoir s’auto-évaluer dans des situations simples."),
( 152, 41,  3, "Soutenir une écoute prolongée (lecture, musique, spectacle, etc.)."),
( 153, 42,  0, "S’impliquer dans un projet individuel ou collectif."),
( 154, 43,  0, "Se respecter en respectant les principales règles d’hygiène de vie ; accomplir les gestes quotidiens sans risquer de se faire mal."),
( 155, 43,  1, "Réaliser une performance mesurée dans les activités athlétiques et en natation."),
( 156, 43,  2, "Se déplacer en s’adaptant à l’environnement."),
( 157, 44,  0, "Lire à haute voix, de façon expressive, un texte en prose ou en vers."),
( 158, 44,  1, "Analyser les éléments grammaticaux d'une phrase afin d'en éclairer le sens."),
( 159, 44,  2, "Dégager l'idée essentielle d'un texte lu ou entendu."),
( 160, 44,  3, "Manifester sa compréhension de textes variés, qu'ils soient documentaires ou littéraires."),
( 161, 44,  4, "Comprendre un énoncé, une consigne."),
( 162, 44,  5, "Lire des oeuvres littéraires intégrales, notamment classiques, et rendre compte de sa lecture."),
( 163, 45,  0, "Copier un texte sans erreur."),
( 164, 45,  1, "Ecrire lisiblement et correctement un texte spontanément ou sous la dictée."),
( 165, 45,  2, "Répondre à une question par une phrase complète."),
( 166, 45,  3, "Rédiger un texte bref, cohérent, construit en paragraphes, correctement ponctué, en respectant des consignes imposées : récit, description, explication, texte argumentatif, compte rendu, écrits courants (lettres…)."),
( 167, 45,  4, "Utiliser les principales règles d'orthographe lexicale et grammaticale."),
( 168, 45,  5, "Adapter le propos au destinataire et à l'effet recherché."),
( 169, 45,  6, "Résumer un texte."),
( 170, 46,  0, "Prendre la parole en public."),
( 171, 46,  1, "Adapter sa prise de parole (attitude et niveau de langue) à la situation de communication (lieu, destinataire, effet recherché)."),
( 172, 46,  2, "Prendre part à un dialogue, un débat : prendre en compte les propos d'autrui, faire valoir son propre point de vue."),
( 173, 46,  3, "Reformuler un texte ou des propos lus ou prononcés par un tiers."),
( 174, 46,  4, "Rendre compte d'un travail individuel ou collectif (exposés, expériences, démonstrations…)."),
( 175, 46,  5, "Dire de mémoire des textes patrimoniaux (textes littéraires, citations célèbres)."),
( 176, 47,  0, "Utiliser des dictionnaires, imprimés ou numériques, des ouvrages de grammaire ou des logiciels de correction orthographique."),
( 177, 48,  0, "Etablir un contact social."),
( 178, 48,  1, "Dialoguer sur des sujets familiers."),
( 179, 48,  2, "Demander et donner des informations."),
( 180, 48,  3, "Réagir à des propositions."),
( 181, 49,  0, "Comprendre un message oral pour réaliser une tâche."),
( 182, 49,  1, "Comprendre les points essentiels d'un message oral (conversation, information, récit, exposé)."),
( 183, 50,  0, "Reproduire un modèle oral."),
( 184, 50,  1, "Décrire, raconter, expliquer."),
( 185, 50,  2, "Présenter un projet et lire à haute voix."),
( 186, 51,  0, "Comprendre le sens général de documents écrits."),
( 187, 51,  1, "Savoir repérer des informations dans un texte."),
( 188, 52,  0, "Copier, écrire sous la dictée."),
( 189, 52,  1, "Renseigner un questionnaire."),
( 190, 52,  2, "Ecrire un message simple."),
( 191, 52,  3, "Rendre compte de faits."),
( 192, 52,  4, "Ecrire un court récit, une description."),
( 193, 53,  0, "Rechercher, extraire et organiser l'information utile."),
( 194, 53,  1, "Réaliser, manipuler, mesurer, calculer, appliquer des consignes."),
( 195, 53,  2, "Raisonner, argumenter, pratiquer une démarche expérimentale ou technologique, démontrer."),
( 196, 53,  3, "Présenter la démarche suivie, les résultats obtenus, communiquer à l'aide d'un langage adapté."),
( 197, 54,  0, "Organisation et gestion de données : reconnaître si deux grandeurs sont ou non proportionnelles ; si oui, déterminer et utiliser un coefficient de proportionnalité, utiliser les propriétés de linéarité, calculer une quatrième proportionnelle."),
( 198, 54,  1, "Organisation et gestion de données : utiliser des pourcentages, relier pourcentages et fractions, appliquer un taux de pourcentage, calculer un taux de pourcentage, calculer une fréquence."),
( 199, 54,  2, "Organisation et gestion de données : repérer un point sur une droite graduée, dans un plan muni d'un repère orthogonal ; lire, utiliser, interpréter des données présentées dans un tableau ou un graphique ; effectuer des traitements de données."),
( 200, 54,  3, "Organisation et gestion de données : utiliser un tableur-grapheur pour présenter des données, calculer des effectifs ou des fréquences ou des moyennes, créer un graphique ou un diagramme."),
( 201, 54,  4, "Organisation et gestion de données : déterminer des probabilités dans des contextes familiers, par un calcul exact lorsque la situation le permet, par des fréquences observées expérimentalement dans le cas contraire."),
( 202, 54,  5, "Nombres et calculs : connaître et utiliser les nombres entiers, décimaux et fractionnaires ; mobiliser des écritures différentes d'un même nombre ; comparer des nombres ; choisir l'opération qui convient au traitement de la situation étudiée."),
( 203, 54,  6, "Nombres et calculs : mener à bien un calcul mental (en particulier maîtriser de manière automatisée les tables de multiplication), à la main, à la calculatrice, avec un ordinateur ; conduire un calcul littéral simple."),
( 204, 54,  7, "Nombres et calculs : évaluer mentalement un ordre de grandeur du résultat avant de se lancer dans un calcul ; contrôler un résultat à l'aide d'une calculatrice ou d'un tableur."),
( 205, 54,  8, "Géométrie : connaître et représenter des figures géométriques ; effectuer des constructions simples en utilisant des outils (instruments de dessin, logiciels), des définitions ou des propriétés (sans nécessité de justifier)."),
( 206, 54,  9, "Géométrie : utiliser les propriétés d'une figure géométrique et les théorèmes de géométrie pour traiter une situation simple ; raisonner logiquement, pratiquer la déduction, démontrer."),
( 207, 54, 10, "Géométrie : connaître et représenter des objets de l'espace, utiliser leurs propriétés ; interpréter une représentation plane d'un objet de l'espace, un patron."),
( 208, 54, 11, "Grandeurs et mesure : mesurer une longueur, un angle, une durée ; calculer une longueur, une aire, un volume, une vitesse, une durée."),
( 209, 54, 12, "Grandeurs et mesure : effectuer des conversions d'unités relatives aux grandeurs étudiées."),
( 210, 55,  0, "L'univers et la terre : organisation de l'univers ; structure et évolution au cours des temps géologiques de la Terre, phénomènes physiques."),
( 211, 55,  1, "La matière : principales caractéristiques, états et transformations ; propriétés physiques et chimiques de la matière et des matériaux ; comportement électrique, interactions avec la lumière."),
( 212, 55,  2, "Le vivant : unité d'organisation (du vivant à l'échelle moléculaire) et diversité (caractères communs et liens de parenté) ; fonctionnement des organismes vivants, évolution des espèces, organisation et fonctionnement du corps humain."),
( 213, 55,  3, "L'énergie : différentes formes d'énergie, notamment l'énergie électrique, et transformations d'une forme à une autre."),
( 214, 55,  4, "Les objets techniques : analyse, conception et réalisation ; principe général de fonctionnement et conditions d'utilisation d'un objet technique."),
( 215, 56,  0, "Questions ouvertes sur la thématique de l'environnement et du développement durable en lien avec les autres capacités."),
( 216, 57,  0, "[C.1.1] Je sais m'identifier sur un réseau ou un site et mettre fin à cette identification."),
( 217, 57,  1, "[C.1.2] Je sais accéder aux logiciels et aux documents disponibles à partir de mon espace de travail."),
( 218, 57,  2, "[C.1.3] Je sais organiser mes espaces de stockage."),
( 219, 57,  3, "[C.1.4] Je sais lire les propriétés d'un fichier] nom, format, taille, dates de création et de dernière modification."),
( 220, 57,  4, "[C.1.5] Je sais paramétrer l'impression (prévisualisation, quantité, partie de documents…)."),
( 221, 57,  5, "[C.1.6] Je sais faire un autre choix que celui proposé par défaut (lieu d'enregistrement, format, imprimante…)."),
( 222, 58,  0, "[C.2.1] Je connais les droits et devoirs indiqués dans la charte d'usage des TIC et la procédure d'alerte de mon établissement."),
( 223, 58,  1, "[C.2.2] Je protège ma vie privée en ne donnant sur Internet des renseignements me concernant qu'avec l'accord de mon responsable légal."),
( 224, 58,  2, "[C.2.3] Lorsque j'utilise ou transmets des documents, je vérifie que j'en ai le droit."),
( 225, 58,  3, "[C.2.4] Je m'interroge sur les résultats des traitements informatiques (calcul, représentation graphique, correcteur…)."),
( 226, 58,  4, "[C.2.5] J'applique des règles de prudence contre les risques de malveillance (virus, spam…)."),
( 227, 58,  5, "[C.2.6] Je sécurise mes données (gestion des mots de passe, fermeture de session, sauvegarde)."),
( 228, 58,  6, "[C.2.7] Je mets mes compétences informatiques au service d'une production collective."),
( 229, 59,  0, "[C.3.1] Je sais modifier la mise en forme des caractères et des paragraphes, paginer automatiquement."),
( 230, 59,  1, "[C.3.2] Je sais utiliser l'outil de recherche et de remplacement dans un document."),
( 231, 59,  2, "[C.3.3] Je sais regrouper dans un même document plusieurs éléments (texte, image, tableau, son, graphique, vidéo…)."),
( 232, 59,  3, "[C.3.4] Je sais créer, modifier une feuille de calcul, insérer une formule."),
( 233, 59,  4, "[C.3.5] Je sais réaliser un graphique de type donné."),
( 234, 59,  5, "[C.3.6] Je sais utiliser un outil de simulation (ou de modélisation) en étant conscient de ses limites."),
( 235, 59,  6, "[C.3.7] Je sais traiter un fichier image ou son à l'aide d'un logiciel dédié notamment pour modifier ses propriétés élémentaires."),
( 236, 60,  0, "[C.4.1] Je sais rechercher des références de documents à l'aide du logiciel documentaire présent au CDI."),
( 237, 60,  1, "[C.4.2] Je sais utiliser les fonctions principales d'un logiciel de navigation sur le web (paramétrage, gestion des favoris, gestion des affichages et de l'impression)."),
( 238, 60,  2, "[C.4.3] je sais utiliser les fonctions principales d'un outil de recherche sur le web (moteur de recherche, annuaire…)."),
( 239, 60,  3, "[C.4.4] Je sais relever des éléments me permettant de connaître l'origine de l'information (auteur, date, source…)."),
( 240, 60,  4, "[C.4.5] Je sais sélectionner des résultats lors d'une recherche (et donner des arguments permettant de justifier mon choix)."),
( 241, 61,  0, "[C.5.1] Lorsque j'envoie ou je publie des informations, je réfléchis aux lectures possibles en fonction de l'outil utilisé."),
( 242, 61,  1, "[C.5.2] Je sais ouvrir et enregistrer un fichier joint à un message ou à une publication."),
( 243, 61,  2, "[C.5.3] Je sais envoyer ou publier un message avec un fichier joint."),
( 244, 61,  3, "[C.5.4] Je sais utiliser un carnet d'adresses ou un annuaire pour choisir un destinataire."),
( 245, 62,  0, "Situer et connaître les grands ensembles physiques et humains."),
( 246, 62,  1, "Situer et connaître les grands types d'aménagements."),
( 247, 62,  2, "Situer et connaître les principales caractéristiques de la France et de l'Union européenne."),
( 248, 63,  0, "Situer et connaître les différentes périodes de l'histoire de l'humanité."),
( 249, 63,  1, "Situer et connaître les grands traits de l'histoire de la France et de la construction européenne."),
( 250, 64,  0, "Lire des œuvres majeures issues de la culture française et européenne."),
( 251, 64,  1, "Situer des œuvres majeures dans l'histoire littéraire et culturelle."),
( 252, 65,  0, "Connaître des références essentielles de l'histoire des arts."),
( 253, 65,  1, "Situer les oeuvres dans leur contexte historique et culturel."),
( 254, 65,  2, "Pratiquer diverses formes d'expression à visée artistique."),
( 255, 66,  0, "Images - Cartes - Croquis - Textes - Graphiques."),
( 256, 67,  0, "Identifier la diversité des civilisations, des sociétés, des religions."),
( 257, 67,  1, "Identifier les enjeux du développement durable."),
( 258, 67,  2, "Avoir des éléments de culture politique et économique."),
( 259, 67,  3, "Utiliser ses connaissances pour donner du sens à l'actualité."),
( 260, 68,  0, "Les principaux droits de l'homme et du citoyen."),
( 261, 68,  1, "Les valeurs, les symboles, les institutions de la République."),
( 262, 68,  2, "Les règles fondamentales de la démocratie et de la justice."),
( 263, 68,  3, "Les grandes institutions de l'Union européenne et le rôle des grands organismes internationaux."),
( 264, 68,  4, "Le rôle de la défense nationale."),
( 265, 68,  5, "Le fonctionnement et le rôle de différents médias."),
( 266, 69,  0, "Connaître et respecter les règles de la vie collective."),
( 267, 69,  1, "Comprendre l'importance du respect mutuel et accepter toutes les différences."),
( 268, 69,  2, "Connaître des comportements favorables à sa santé et sa sécurité."),
( 269, 69,  3, "Connaître quelques notions juridiques de base."),
( 270, 69,  4, "Savoir utiliser quelques notions économiques et budgétaires de base."),
( 271, 70,  0, "Envisager son orientation de façon éclairée."),
( 272, 70,  1, "Se familiariser avec l'environnement économique, les entreprises, les métiers."),
( 273, 70,  2, "Connaître les systèmes d'éducation, de formation et de certification."),
( 274, 71,  0, "Etre autonome dans son travail : savoir l'organiser, le planifier, l'anticiper, rechercher et sélectionner des informations utiles."),
( 275, 71,  1, "Connaître son potentiel, savoir s'auto évaluer."),
( 276, 71,  2, "Savoir nager."),
( 277, 71,  3, "Avoir une bonne maîtrise de son corps."),
( 278, 72,  0, "S'impliquer dans un projet individuel ou collectif."),
( 279, 72,  1, "Savoir travailler en équipe."),
( 280, 72,  2, "Manifester curiosité, créativité, motivation, à travers des activités conduites ou reconnues par l'établissement."),
( 281, 72,  3, "Savoir prendre des initiatives et des décisions.");

ALTER TABLE sacoche_socle_entree ENABLE KEYS;
