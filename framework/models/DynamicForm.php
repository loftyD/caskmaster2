<?php
/**
 *  DynamicForm.php
 *	Dynamic Form model.
 */
namespace models;
class DynamicForm extends Model {
	public function loadRelations(Model $resource) {
		
		if(empty($resource->sections)) {
			$resource->getRelated("sections","DynamicFormSection", null, null, "t.status = 'active'", false);
		}
		
		return true;
	}

	public function getMainForm($class) {
		$sql = "SELECT t.* FROM dynamic_forms t 
 				INNER JOIN dynamic_form_sections dfs ON t.dynamic_form_id = dfs.dynamic_form_id
				WHERE t.form_class = :class and dfs.id = 'main' and t.status = 'active';
		";
		return $this->executeSql($sql,array(":class" => $class),false);
	}
}