<?php
/**
 *  UserAuthStatus.php
 *	UserAuthStatus model.
 */
namespace models;
class UserAuthStatus extends Model {

	public function loadRelations(Model $resource) {
		if(empty($resource->group)) {
			$resource->getRelated("group","Group", null, "group_id");
		}
		return true;
	}

	public function __toString() {
		return $this->status;
	}
}