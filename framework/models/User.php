<?php
/**
 *  User.php
 *	User model.
 */
namespace models;
class User extends Model {
	protected $fields = array("name","dob");
	public function loadRelations(Model $resource) {
		
		if(empty($resource->status)) {
			$resource->getRelated("status","UserAuthStatus");
		}

		if(empty($resource->assigned_companies)) {
			$resource->getRelated("assigned_companies","Company", null, null, "t.status = 'active'", false);
		}
		return true;
	}
}