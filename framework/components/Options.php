<?php
namespace components;
use \components\Barrel;
use \components\VarDumper;
class Options {


	public function __construct($app) {
		$this->app = $app;
		$this->db = $this->getDbInstance();
		//VarDumper::dump($app);die;
	}
	/**
	 * Returns a PDO instance
	 * @return PDO PDO Instance
	 */
	private function getDbInstance() {
		$dsn = $this->app->get("db.vendor") . ':host=' . $this->app->get("db.host") . ';dbname=' . $this->app->get("db.name");
		$db = new \PDO($dsn,$this->app->get("db.user"),$this->app->get("db.password"), array(
			\PDO::ATTR_PERSISTENT => true,
			)
		);
		$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		return $db;
	}

	public function get($option) {
		$sql = "SELECT `value` FROM `options` WHERE `option` = :option";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(
			":option" => $option,
			)
		);
		$stmt->setFetchMode(\PDO::FETCH_OBJ);
		$result = $stmt->fetch();
		if(empty($result)) {
			throw new \components\exception\HttpException("The option $option does not exist.");
		} 
		return $result->{'value'};
	}

	public function set($option,$value) {

		$sql = "UPDATE `options` SET `value` = :value WHERE `option` = :option";
		$stmt = $this->db->prepare($sql);
		$result = $stmt->execute(array(
			":option" => $option,
			":value"  => $value,
			)
		);
		if(!$result) {
			return false;
		} 
		return true;
	}
}
