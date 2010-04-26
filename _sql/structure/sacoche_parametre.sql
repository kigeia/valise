DROP TABLE IF EXISTS sacoche_parametre;

CREATE TABLE sacoche_parametre (
	parametre_nom VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
	parametre_valeur TINYTEXT COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE sacoche_parametre DISABLE KEYS;

INSERT INTO sacoche_parametre VALUES 
( "structure_id"      , "0" ),
( "structure_uai"     , "" ),
( "structure_key"     , "" ),
( "denomination"      , "" ),
( "sso"               , "normal" ),
( "modele_professeur" , "ppp.nnnnnnnn" ),
( "modele_eleve"      , "ppp.nnnnnnnn" ),
( "matieres"          , "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,99" ),
( "niveaux"           , "1,2,3,4" ),
( "paliers"           , "3" ),
( "eleve_options"     , "ms,pv" ),
( "eleve_demandes"    , "0" ),
( "duree_inactivite"  , "30" ),
( "calcul_valeur_RR"  , "0" ),
( "calcul_valeur_R"   , "33" ),
( "calcul_valeur_V"   , "67" ),
( "calcul_valeur_VV"  , "100" ),
( "calcul_seuil_R"    , "40" ),
( "calcul_seuil_V"    , "60" ),
( "calcul_methode"    , "geometrique" ),
( "calcul_limite"     , "5" ),
( "blocage_statut"    , "0" ),
( "blocage_message"   , "" );

ALTER TABLE sacoche_parametre ENABLE KEYS;
