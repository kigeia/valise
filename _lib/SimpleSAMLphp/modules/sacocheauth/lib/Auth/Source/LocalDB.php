<?php

/**
 * Simple SQL authentication source
 *
 * This class is an example authentication source which authenticates an user
 * against a SQL database.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class sspmod_sacocheauth_Auth_Source_LocalDB extends sspmod_core_Auth_UserPassBase {
	/**
	 * Le profil de connexion (normal ou webmestre)
	 */
	private $profil;

	/**
	 * Constructor for this authentication source.
	 *
	 * @param array $info  Information about this authentication source.
	 * @param array $config  Configuration.
	 */
	public function __construct($info, $config) {
		assert('is_array($info)');
		assert('is_array($config)');

		/* Call the parent constructor first, as required by the interface. */
		parent::__construct($info, $config);

		foreach (array('profil') as $param) {
			if (!array_key_exists($param, $config)) {
				throw new Exception('Missing required attribute \'' . $param .
					'\' for authentication source ' . $this->authId);
			}

			if (!is_string($config[$param])) {
				throw new Exception('Expected parameter \'' . $param .
					'\' for authentication source ' . $this->authId .
					' to be a string. Instead it was: ' .
					var_export($config[$param], TRUE));
			}
		}

		$this->profil = $config['profil'];
	}

	/**
	 * Attempt to log in using the given username and password.
	 *
	 * On a successful login, this function should return the users attributes. On failure,
	 * it should throw an exception. If the error was caused by the user entering the wrong
	 * username or password, a SimpleSAML_Error_Error('WRONGUSERPASS') should be thrown.
	 *
	 * Note that both the username and the password are UTF-8 encoded.
	 *
	 * @param string $username  The username the user wrote.
	 * @param string $password  The password the user wrote.
	 * @return array  Associative array with the users attributes.
	 */
	protected function login($login, $password) {
		assert('is_string($login)');
		assert('is_string($password)');

		$path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
		
		$auth_resultat = '';
		if(($this->profil=='webmestre') && ($login=='webmestre') && ($password!='') )
		{// Pour le webmestre d'un serveur
			require_once("$path/_inc/fonction_divers.php");
			$auth_resultat = tester_authentification_webmestre($password);
		} else if(($this->profil=='normal') && ($login!='') && ($password!='') )
		{// Pour un utilisateur normal, y compris un administrateur
			$path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
			if (!defined('SACoche')) {
				define('SACoche','ssaml');
			}
			
			require_once($path.'/_inc/config_serveur.php');
			$return_base = load_sacoche_mysql_config();
			if ($return_base === false) {
				throw new SimpleSAML_Error_Excepition('Impossible de charger la base');
			}
			require_once("$path/_inc/fonction_divers.php");
			list($auth_resultat,$auth_DB_ROW) = tester_authentification_user($return_base,$login,$password,$mode_connection='normal');
		}
		
		
		if($auth_resultat!='ok') {
			# Echec d'authentification.
			session_write_close();
			/* No rows returned - invalid username/password. */
			SimpleSAML_Logger::error('sacocheauth:' . $this->authId .
				': not authenticated. Probably wrong username/password.');
			throw new SimpleSAML_Error_Error('WRONGUSERPASS');
		}
		
		// Si on arrive ici c'est que l'identification s'est bien effectu�e !
		SimpleSAML_Logger::info('sacocheauth:' . $this->authId . ': authenticated');

		$attributes = array();
		if(($this->profil=='webmestre') && ($login=='webmestre') && ($password!='') )
		{
			$attributes['USER_ID'][]          = 0;
		} else if(($this->profil=='normal') && ($login!='') && ($password!='') )
		{
			$attributes['USER_ID'][]          = $auth_DB_ROW['user_id'];
		}
		
		SimpleSAML_Logger::info('sacocheauth:' . $this->authId . ': Attributes: ' .
			implode(',', array_keys($attributes)));
			
		return $attributes;
	}

}

?>
