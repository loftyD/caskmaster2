<?php

namespace components;
class Barrel {

	public static function getLatestVersion() {
		if(!in_array($_SERVER['app']->get("caskmaster.environment"),array("production"))) {

			$url = "http://" . $_SERVER['SERVER_NAME'] . "/v";
			$version = json_decode(file_get_contents($url));

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

}