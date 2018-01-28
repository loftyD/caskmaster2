<?php

namespace components;
class Barrel {

	public static function getLatestVersion() {
		if(!in_array($_SERVER['app']->get("caskmaster.environment"),array("production"))) {

			$url = "http://" . $_SERVER['SERVER_NAME'] . "/v";
			$version = json_decode(file_get_contents($url));
			return reset($version);
		} else {
			if($_SERVER['app']->redis()->exists("caskmaster_latest_version")) {
				return json_decode($_SERVER['app']->redis()->get("caskmaster_latest_version"));
			}
			$url = "http://getcaskmaster.com/v";
			$version = json_decode(file_get_contents($url));
			$_SERVER['app']->redis()->set("caskmaster_latest_version", json_encode($version) );
			$_SERVER['app']->redis()->expire("caskmaster_latest_version", 3600);


			return reset($version);
		}
	}

	public static function returnLatestSessionData() {
		$uid = $_SESSION['uid'];
		if($_SERVER['app']->redis()->exists("user_data_$uid")) {
			return json_decode($_SERVER['app']->redis()->get("user_data_$uid"));
		} else {
			$me = new \models\User;
			$data = $me->findByPk($uid);
			$_SERVER['app']->redis()->set("user_data_$uid",json_encode($data));
			$_SERVER['app']->redis()->expire("user_data_$uid", 600);
			return $data;
		}
	}

	private static function getDbInstance() {
		$dsn = $_SERVER['app']->get("db.vendor") . ':host=' . $_SERVER['app']->get("db.host") . ';dbname=' . $_SERVER['app']->get("db.name");
		$db = new \PDO($dsn,$_SERVER['app']->get("db.user"),$_SERVER['app']->get("db.password"), array(
			\PDO::ATTR_PERSISTENT => true,
			)
		);
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		return $db;
	}

	/**
	 * Do not call this method. This is used by getcaskmaster.com. This will
	 * throw an Exception if you are trying to use this on your install.
	 * 
	 * @return [type] [description]
	 */
	public static function fetchLatestVersion() {
	
		$db = self::getDbInstance();
		if($_SERVER['SERVER_NAME'] == "getcaskmaster.com") {
			$sql = "SELECT version FROM caskmaster_versions where latest_version = 1";
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$stmt->setFetchMode(\PDO::FETCH_OBJ);
			$version = $stmt->fetch();
			return $version->version;
		} else {
			throw new \components\exception\HttpException("Cannot call fetchLatestVersion() on this install.");
		}
	}

}