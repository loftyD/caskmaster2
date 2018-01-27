<?php
/**
 * Authentication.php
 * Handles the logging in and logging out of users.
 */
namespace components;
use flight\Engine;
use \PDO;
use \models\User;
class Authentication {

	private $db;


	/**
	 * Constructor
	 */
	final public function __construct() { 
		$dsn = $_SERVER['app']->get("db.vendor") . ':host=' . $_SERVER['app']->get("db.host") . ';dbname=' . $_SERVER['app']->get("db.name");
		$this->db = new \PDO($dsn,$_SERVER['app']->get("db.user"),$_SERVER['app']->get("db.password"));
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	public function login($username,$password) {
		$user = new User;
		$data = $user->findByAttributes(array(
			"username" => $username,
		));
		if(isset($data->username) && $this->verifyHash($password, $data->status->password)) {
			if($data->status == User::STATUS_ACTIVE) {
				$_SESSION['username'] = $data->username;
				$_SESSION['uid'] = $data->user_id;
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function hash($str) {
		$options = [
    		'cost' => 10,
		];
		$hash = \password_hash($str, \PASSWORD_BCRYPT, $options);
		return $hash;
	}

	public function verifyHash($str,$hash) {
		if(\password_verify($str, $hash)) {
			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($_SESSION);
		session_destroy();
	}


}