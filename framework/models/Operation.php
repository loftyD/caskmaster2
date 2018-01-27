<?php
/**
 *  Operation.php
 *	Operation model.
 */
namespace models;
use \components\EntityFactory;
class Operation extends Model {

	public function loadRelations(Model $resource) {

	if(empty($resource->entity_model_class)) {
		$resource->{'entity_model_class'} = (new EntityFactory)->getEntityInfoById($resource->entity_id)->class;
	}
	
	return true;
	}
}