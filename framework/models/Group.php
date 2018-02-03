<?php
/**
 *  Group.php
 *	Group model.
 */
namespace models;
use \components\VarDumper;
class Group extends Model {

	public function loadRelations(Model $resource) {
		if(empty($resource->members)) {
			$resource->getRelated("members","User","UserAuthStatus","users_auth_status.group_id", null, false);
		}

		if(empty($resource->operations)) {
			$resource->getRelated("operations","GroupOperation","Operation",null,null, null, false);
		}
		
		return true;
	}

	public function can($mode, $model) {
		if(!in_array($mode, array("create","update","read","delete"))) {
			throw new \Exception('$mode can only be create, read, update or delete');
		}
		$sql = "SELECT t.* FROM operations t INNER JOIN entity_factory e ON t.entity_id = e.entity_factory_id WHERE e.class = :model";
		$result = $this->executeSql($sql,array(":model" => $model));
		$result = reset($result);
		
		if(count($result) != 0) {
			if($result->{$mode} == '1') {
				return true;
			}
		}

		return false;
	}

}