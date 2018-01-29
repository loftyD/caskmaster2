<?php
namespace components;
use \components\Barrel;
class Options {
	
	public function get($option) {
		$db = Barrel::getDbInstance();
		$sql = "SELECT `value` FROM `options` WHERE `option` = :option";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(
			":option" => $option,
			)
		);
		$stmt->setFetchMode(\PDO::FETCH_OBJ);
		$result = $statement->fetch();
		if(empty($result)) {
			$result = false;
		} 
		return $result->value;
	}

	public function set($option,$value) {
		$db = Barrel::getDbInstance();
		$sql = "UPDATE `options` SET `value` = :value WHERE `option` = :option";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(
			":option" => $option,
			":value"  => $value,
			)
		);
		$stmt->setFetchMode(\PDO::FETCH_OBJ);
		$result = $statement->fetch();
		if(empty($result)) {
			$result = false;
		} 
		return true;
	}
}
