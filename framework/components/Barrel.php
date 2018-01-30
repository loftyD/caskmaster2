<?php

namespace components;
use \components\VarDumper;
class Barrel {

	/**
	 * Returns the latest up-to-date version of Caskmaster
	 * @return boolean
	 */
	public static function getLatestVersion() {
		if($_SERVER['app']->redis()->exists("caskmaster_latest_version")) {
			$version = json_decode($_SERVER['app']->redis()->get("caskmaster_latest_version"));
		} else {
			$current = $_SERVER['app']->options()->get("caskmaster.version");
			$url = "http://getcaskmaster.com/v?my_version=$current";
			$version = json_decode(file_get_contents($url));
			$_SERVER['app']->redis()->set("caskmaster_latest_version", json_encode($version) );
			$_SERVER['app']->redis()->expire("caskmaster_latest_version", 3600);
		}

		$latest = $version->version;
		$current = $_SERVER['app']->options()->get("caskmaster.version");

		if($current < $latest) {
			$updateManager = new \components\administration\CaskmasterUpdateManager(false);
			clearstatcache();
			if($updateManager->fetchLatestUpdateXml($latest) === false) {
				return false;
			}

		}


		return reset($version);
	}

	/**
	 * Returns session data for logged in user.
	 * @return Model user session data
	 */
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

	/**
	 * Returns a PDO instance
	 * @return PDO PDO Instance
	 */
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
	 * @return float version number
	 */
	public static function fetchLatestVersion() {
	
		$db = self::getDbInstance();
		if($_SERVER['SERVER_NAME'] == "getcaskmaster.com") {
			$version = $_GET['my_version'];
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

	/**
	 * Returns the correct upgrade path for 
	 * CaskmasterUpdateManager to fetch the update.xml file.
	 * @param  string $my_version this Caskmaster instance version
	 * @return mixed Returns false if this method is run on your install.
	 */
	public static function findCorrectUpgradePath($my_version) {

		$db = self::getDbInstance();
		if($_SERVER['SERVER_NAME'] == "getcaskmaster.com") {
			$sql = "SELECT * FROM  `caskmaster_upgrade_path` 
			WHERE v_to <> ( 
			SELECT version
			FROM caskmaster_versions
			WHERE latest_version =1 AND version > :my ) 
			AND v_from = :my";
			$stmt = $db->prepare($sql);
			$stmt->execute(array(":my" => $my_version));
			$stmt->setFetchMode(\PDO::FETCH_OBJ);
			$result = $stmt->fetch();
			if(empty($result)) {
				return self::fetchLatestVersion();
			}
			return $result->v_to;
		} else {
			return false;
		}

	}

}