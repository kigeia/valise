<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://competences.sesamath.net> - Suivi d'Acquisitions de Comp�tences
 * � Thomas Crespin pour S�samath <http://www.sesamath.net> - Tous droits r�serv�s.
 * Logiciel plac� sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la �GNU General Public License� telle que publi�e par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (� votre gr�) toute version ult�rieure.
 * 
 * SACoche est distribu� dans l�espoir qu�il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans m�me la garantie implicite de COMMERCIALISABILIT� ni d�AD�QUATION � UN OBJECTIF PARTICULIER.
 * Consultez la Licence G�n�rale Publique GNU pour plus de d�tails.
 * 
 * Vous devriez avoir re�u une copie de la Licence G�n�rale Publique GNU avec SACoche ;
 * si ce n�est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

/**
 * DB_WEBMESTRE_recuperer_structure (compl�mentaire � DB_WEBMESTRE_lister_structures car utilisation de queryRow � la place de queryTab)
 * 
 * @param int base_id
 * @return array
 */

function DB_WEBMESTRE_recuperer_structure($base_id)
{
	$DB_SQL = 'SELECT * FROM sacoche_structure ';
	$DB_SQL.= 'WHERE sacoche_base=:base_id ';
	$DB_SQL.= 'LIMIT 1 ';
	$DB_VAR = array(':base_id'=>$base_id);
	return DB::queryRow(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_WEBMESTRE_lister_zones
 * 
 * @param void
 * @return array
 */

function DB_WEBMESTRE_lister_zones()
{
	$DB_SQL = 'SELECT * FROM sacoche_geo ';
	$DB_SQL.= 'ORDER BY geo_ordre ASC';
	return DB::queryTab(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_WEBMESTRE_lister_structures (compl�mentaire � DB_WEBMESTRE_recuperer_structure car utilisation de queryTab � la place de queryRow)
 * 
 * @param void|string $listing_base_id   id des bases s�par�s par des virgules (tout si rien de transmis)
 * @return array
 */

function DB_WEBMESTRE_lister_structures($listing_base_id=false)
{
	$nb_ids = substr_count($listing_base_id,',')+1;
	$DB_SQL = 'SELECT * FROM sacoche_structure ';
	$DB_SQL.= 'LEFT JOIN sacoche_geo USING (geo_id) ';
	$DB_SQL.= ($listing_base_id==false) ? '' : 'WHERE sacoche_base IN('.$listing_base_id.') ' ;
	$DB_SQL.= 'ORDER BY geo_ordre ASC, structure_localisation ASC, structure_denomination ASC ';
	$DB_SQL.= ($listing_base_id==false) ? '' : 'LIMIT '.$nb_ids ;
	return DB::queryTab(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_WEBMESTRE_lister_contacts_cibles
 * 
 * @param string $listing_base_id   id des bases s�par�s par des virgules
 * @return array                    le tableau est de la forme [i] => array('contact_id'=>...,'contact_nom'=>...,'contact_prenom'=>...,'contact_courriel'=>...);
 */

function DB_WEBMESTRE_lister_contacts_cibles($listing_base_id)
{
	$DB_SQL = 'SELECT sacoche_base AS contact_id , structure_contact_nom AS contact_nom , structure_contact_prenom AS contact_prenom , structure_contact_courriel AS contact_courriel FROM sacoche_structure ';
	$DB_SQL.= 'WHERE sacoche_base IN('.$listing_base_id.') ';
	return DB::queryTab(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , null);
}

/**
 * DB_WEBMESTRE_tester_zone_nom
 * 
 * @param string $geo_nom
 * @param int    $geo_id    inutile si recherche pour un ajout, mais id � �viter si recherche pour une modification
 * @return int
 */

function DB_WEBMESTRE_tester_zone_nom($geo_nom,$geo_id=false)
{
	$DB_SQL = 'SELECT geo_id FROM sacoche_geo ';
	$DB_SQL.= 'WHERE geo_nom=:geo_nom ';
	$DB_VAR = array(':geo_nom'=>$geo_nom);
	if($geo_id)
	{
		$DB_SQL.= 'AND geo_id!=:geo_id ';
		$DB_VAR[':geo_id'] = $geo_id;
	}
	$DB_SQL.= 'LIMIT 1';
	$DB_ROW = DB::queryRow(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_ROW) ;
}

/**
 * DB_WEBMESTRE_tester_structure_UAI
 * 
 * @param string $structure_uai
 * @param int    $base_id       inutile si recherche pour un ajout, mais id � �viter si recherche pour une modification
 * @return int
 */

function DB_WEBMESTRE_tester_structure_UAI($structure_uai,$base_id=false)
{
	$DB_SQL = 'SELECT sacoche_base FROM sacoche_structure ';
	$DB_SQL.= 'WHERE structure_uai=:structure_uai ';
	$DB_VAR = array(':structure_uai'=>$structure_uai);
	if($base_id)
	{
		$DB_SQL.= 'AND sacoche_base!=:base_id ';
		$DB_VAR[':base_id'] = $base_id;
	}
	$DB_SQL.= 'LIMIT 1';
	$DB_ROW = DB::queryRow(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
	return count($DB_ROW) ;
}

/**
 * DB_WEBMESTRE_ajouter_zone
 * 
 * @param int    $geo_ordre
 * @param string $geo_nom
 * @return int
 */

function DB_WEBMESTRE_ajouter_zone($geo_ordre,$geo_nom)
{
	$DB_SQL = 'INSERT INTO sacoche_geo(geo_ordre,geo_nom) ';
	$DB_SQL.= 'VALUES(:geo_ordre,:geo_nom)';
	$DB_VAR = array(':geo_ordre'=>$geo_ordre,':geo_nom'=>$geo_nom);
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
	return DB::getLastOid(SACOCHE_WEBMESTRE_BD_NAME);
}

/**
 * DB_WEBMESTRE_ajouter_structure
 * 
 * @param int    $base_id   Pour forcer l'id de la base de la structure ; normalement transmis � 0 (=> auto-increment), sauf dans un cadre de gestion interne � S�samath
 * @param int    $geo_id
 * @param string $structure_uai
 * @param string $localisation
 * @param string $denomination
 * @param string $contact_nom
 * @param string $contact_prenom
 * @param string $contact_courriel
 * @return int
 */

function DB_WEBMESTRE_ajouter_structure($base_id,$geo_id,$structure_uai,$localisation,$denomination,$contact_nom,$contact_prenom,$contact_courriel)
{
	// Ins�rer l'enregistrement dans la base du webmestre
	if($base_id==0)
	{
		$DB_SQL = 'INSERT INTO sacoche_structure(geo_id,structure_uai,structure_localisation,structure_denomination,structure_contact_nom,structure_contact_prenom,structure_contact_courriel,structure_inscription_date) ';
		$DB_SQL.= 'VALUES(:geo_id,:structure_uai,:localisation,:denomination,:contact_nom,:contact_prenom,:contact_courriel,NOW())';
		$DB_VAR = array(':geo_id'=>$geo_id,':structure_uai'=>$structure_uai,':localisation'=>$localisation,':denomination'=>$denomination,':contact_nom'=>$contact_nom,':contact_prenom'=>$contact_prenom,':contact_courriel'=>$contact_courriel);
		DB::query(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
		$base_id = DB::getLastOid(SACOCHE_WEBMESTRE_BD_NAME);
	}
	else
	{
		$DB_SQL = 'INSERT INTO sacoche_structure(sacoche_base,geo_id,structure_uai,structure_localisation,structure_denomination,structure_contact_nom,structure_contact_prenom,structure_contact_courriel,structure_inscription_date) ';
		$DB_SQL.= 'VALUES(:base_id,:geo_id,:structure_uai,:localisation,:denomination,:contact_nom,:contact_prenom,:contact_courriel,NOW())';
		$DB_VAR = array(':base_id'=>$base_id,':geo_id'=>$geo_id,':structure_uai'=>$structure_uai,':localisation'=>$localisation,':denomination'=>$denomination,':contact_nom'=>$contact_nom,':contact_prenom'=>$contact_prenom,':contact_courriel'=>$contact_courriel);
		DB::query(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
	}
	// G�n�ration des param�tres de connexion � la base de donn�es
	$BD_name = 'sac_base_'.$base_id; // Limit� � 64 caract�res (tranquille...)
	$BD_user = 'sac_user_'.$base_id; // Limit� � 16 caract�res (attention !)
	$BD_pass = fabriquer_mdp();
	// Cr�er le fichier de connexion de la base de donn�es de la structure
	fabriquer_fichier_connexion_base($base_id,SACOCHE_WEBMESTRE_BD_HOST,$BD_name,$BD_user,$BD_pass);
	// Cr�er la base de donn�es de la structure
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , 'CREATE DATABASE sac_base_'.$base_id );
	// Cr�er un utilisateur pour la base de donn�es de la structure et lui attribuer ses droits
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , 'CREATE USER '.$BD_user.' IDENTIFIED BY "'.$BD_pass.'"' );
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , 'GRANT ALTER, CREATE, DELETE, DROP, INDEX, INSERT, SELECT, UPDATE ON '.$BD_name.'.* TO '.$BD_user );
	/* Il reste � :
		+ Lancer les requ�tes pour installer et remplir les tables, �ventuellement personnaliser certains param�tres de la structure
		+ Ins�rer le compte administrateur dans la base de cette structure, �ventuellement lui envoyer un courriel
		+ Cr�er un dossier pour les les vignettes images
	*/
	return $base_id;
}

/**
 * DB_WEBMESTRE_modifier_structure
 * 
 * @param int    $base_id
 * @param int    $geo_id
 * @param string $structure_uai
 * @param string $localisation
 * @param string $denomination
 * @param string $contact_nom
 * @param string $contact_prenom
 * @param string $contact_courriel
 * @return void
 */

function DB_WEBMESTRE_modifier_structure($base_id,$geo_id,$structure_uai,$localisation,$denomination,$contact_nom,$contact_prenom,$contact_courriel)
{
	$DB_SQL = 'UPDATE sacoche_structure ';
	$DB_SQL.= 'SET geo_id=:geo_id,structure_uai=:structure_uai,structure_localisation=:localisation,structure_denomination=:denomination,structure_contact_nom=:contact_nom,structure_contact_prenom=:contact_prenom,structure_contact_courriel=:contact_courriel ';
	$DB_SQL.= 'WHERE sacoche_base=:base_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':base_id'=>$base_id,':geo_id'=>$geo_id,':structure_uai'=>$structure_uai,':localisation'=>$localisation,':denomination'=>$denomination,':contact_nom'=>$contact_nom,':contact_prenom'=>$contact_prenom,':contact_courriel'=>$contact_courriel);
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_WEBMESTRE_modifier_zone
 * 
 * @param int    $geo_id
 * @param int    $geo_ordre
 * @param string $geo_nom
 * @return void
 */

function DB_WEBMESTRE_modifier_zone($geo_id,$geo_ordre,$geo_nom)
{
	$DB_SQL = 'UPDATE sacoche_geo ';
	$DB_SQL.= 'SET geo_ordre=:geo_ordre,geo_nom=:geo_nom ';
	$DB_SQL.= 'WHERE geo_id=:geo_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':geo_id'=>$geo_id,':geo_ordre'=>$geo_ordre,':geo_nom'=>$geo_nom);
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
}

/**
 * DB_WEBMESTRE_supprimer_zone
 * 
 * @param int $geo_id
 * @return void
 */

function DB_WEBMESTRE_supprimer_zone($geo_id)
{
	$DB_SQL = 'DELETE FROM sacoche_geo ';
	$DB_SQL.= 'WHERE geo_id=:geo_id ';
	$DB_SQL.= 'LIMIT 1';
	$DB_VAR = array(':geo_id'=>$geo_id);
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
	// Il faut aussi mettre � jour les jointures avec les structures
	$DB_SQL = 'UPDATE sacoche_structure ';
	$DB_SQL.= 'SET geo_id=1 ';
	$DB_SQL.= 'WHERE geo_id=:geo_id ';
	$DB_VAR = array(':geo_id'=>$geo_id);
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
	// Log de l'action
	ajouter_log('Suppression de la zone g�ographique '.$geo_id.'.');
}

/**
 * DB_WEBMESTRE_supprimer_multi_structure
 * 
 * @param int    $BASE 
 * @return void
 */

function DB_WEBMESTRE_supprimer_multi_structure($BASE)
{
	global $CHEMIN_MYSQL;
	// Param�tres de connexion � la base de donn�es
	$BD_name = 'sac_base_'.$BASE;
	$BD_user = 'sac_user_'.$BASE; // Limit� � 16 caract�res
	// Supprimer la base associ�e � la structure
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , 'DROP DATABASE '.$BD_name );
	// Retirer les droits et supprimer l'utilisateur pour la base de donn�es de la structure
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , 'REVOKE ALL PRIVILEGES ON '.$BD_name.'.* FROM '.$BD_user );
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , 'DROP USER '.$BD_user );
	// Supprimer le fichier de connexion
	unlink($CHEMIN_MYSQL.'serveur_sacoche_structure_'.$BASE.'.php');
	// Supprimer la structure dans la base du webmestre
	$DB_SQL = 'DELETE FROM sacoche_structure ';
	$DB_SQL.= 'WHERE sacoche_base=:base ';
	$DB_VAR = array(':base'=>$BASE);
	DB::query(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , $DB_VAR);
	// Supprimer le dossier pour accueillir les vignettes verticales avec l'identit� des �l�ves
	Supprimer_Dossier('./sacoche/__tmp/badge/'.$BASE);
	// Log de l'action
	ajouter_log('Suppression de la zone structure '.$BASE.'.');
}

/**
 * DB_WEBMESTRE_creer_remplir_tables_webmestre
 * 
 * @param string $dossier_requetes   '...../structure/' ou '...../webmestre/'
 * @return void
 */

function DB_WEBMESTRE_creer_remplir_tables_webmestre($dossier_requetes)
{
	$tab_files = scandir($dossier_requetes);
	foreach($tab_files as $file)
	{
		$extension = pathinfo($file,PATHINFO_EXTENSION);
		if($extension=='sql')
		{
			$requetes = file_get_contents($dossier_requetes.$file);
			DB::query(SACOCHE_WEBMESTRE_BD_NAME , $requetes );
			/*
			La classe PDO a un bug. Si on envoie plusieurs requ�tes d'un coup �a passe, mais si on recommence juste apr�s alors on r�colte : "Cannot execute queries while other unbuffered queries are active.  Consider using PDOStatement::fetchAll().  Alternatively, if your code is only ever going to run against mysql, you may enable query buffering by setting the PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute."
			La seule issue est de fermer la connexion apr�s chaque requ�te multiple en utilisant exceptionnellement la m�thode ajout� par SebR suite � mon signalement : DB::close(nom_de_la_connexion);
			*/
			DB::close(SACOCHE_WEBMESTRE_BD_NAME);
		}
	}
}

/**
 * Retourner un tableau [valeur texte optgroup] des structures (choix d'�tablissements en page d'accueil)
 * l'indice g�ographique sert � pouvoir regrouper les options
 * 
 * @param void
 * @return array|string
 */

function DB_WEBMESTRE_OPT_structures_sacoche()
{
	$DB_SQL = 'SELECT * FROM sacoche_structure ';
	$DB_SQL.= 'LEFT JOIN sacoche_geo USING (geo_id) ';
	$DB_SQL.= 'ORDER BY geo_ordre ASC, structure_localisation ASC, structure_denomination ASC';
	$DB_TAB = DB::queryTab(SACOCHE_WEBMESTRE_BD_NAME , $DB_SQL , null);
	if(count($DB_TAB))
	{
		$tab_retour_champs = array();
		foreach($DB_TAB as $DB_ROW)
		{
			$GLOBALS['tab_select_optgroup'][$DB_ROW['geo_id']] = $DB_ROW['geo_nom'];
			$tab_retour_champs[] = array('valeur'=>$DB_ROW['sacoche_base'],'texte'=>$DB_ROW['structure_localisation'].' | '.$DB_ROW['structure_denomination'],'optgroup'=>$DB_ROW['geo_id']);
		}
		return $tab_retour_champs;
	}
	else
	{
		return 'Aucun autre �tablissement n\'est enregistr� !';
	}
}

?>