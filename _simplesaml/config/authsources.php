<?php

$config = array(

	// This is a authentication source which handles admin authentication for saml.
	//le mot de passe à utiliser est webmestre pour l'admin saml
	/*'admin' => array(
		'sacocheauth:LocalDB',
		'profil' => 'webmestre',
		'name' => array(
		    'fr' => 'Admin simplesaml',
		),
		'description' => array(
		    'fr' => 'Authentification spéciale pour l\'administrateur simplesaml',
		),
	),*/

	//authentification sacoche webmestre
	'webmestre' => array(
		'sacocheauth:LocalDB',
		'profil' => 'webmestre',
		'name' => array(
		    'fr' => 'Accès webmestre',
		),
		'description' => array(
		    'fr' => 'Authentification spéciale pour le webmestre de sacoche',
		),
	),

	//authentification sur la base locale
	'local-sacoche-db' => array(
		'sacocheauth:LocalDB',
		'profil' => 'normal',
		'name' => array(
		    'fr' => 'Accès normal',
		),
		'description' => array(
		    'fr' => 'Authentification normale sur la base utilisateur sacoche',
		),
	),

	//authentification sur la base distante gepi
	'distant-gepi-saml' => array(
		'saml:SP',
		'idp' => 'gepi-idp',
		'entityID' => 'sacoche-sp',
		//ce paramêtre doit correspondre avec l' entityID dans le fichier simplesaml/metadata/saml20-sp-remote.php du fournisseur d'identité (gepi)
		//En l'absence de ce paramètre, c'est l'url de départ (https://www.monserveur.fr/sacoche/simplesaml/module.php/saml/sp/saml2-acs.php/distant-gepi-saml) qui est utilisé comme entityID
		'name' => array(
		    'fr' => 'Gepi',
		),
		'description' => array(
		    'fr' => 'Authentification commune gepi et sacoche',
		),
		//on va travailler sur les attributs pour avoir des attributs au format sacoche
		'authproc' => array(
		    50 => array(
			'class' => 'core:AttributeMap',
			'login_gepi' => array('USER_ID_ENT', 'USER_ID_GEPI'),
			'statut' => 'USER_PROFIL',
			'nom' => 'USER_NOM',
			'prenom' => 'USER_PRENOM',
		    ),
		),
	),

	//choix d'authentification entre utilisateur webmestre et utilisateur gepi
	'multi-webmestre-gepi' => array(
		'multiauth:MultiAuth',
		'sources' => array('webmestre', 'distant-gepi-saml')
	),

	//choix d'authentification entre utilisateur webmestre et utilisateur local
	'multi-webmestre-local' => array(
		'multiauth:MultiAuth',
		'sources' => array('webmestre', 'local-sacoche-db')
	)

);
