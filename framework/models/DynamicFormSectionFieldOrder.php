<?php
/**
 *  DynamicFormSectionFieldOrder.php
 *	Dynamic Form Section Order model.
 */
namespace models;
class DynamicFormSectionFieldOrder extends Model {

	public function loadRelations(Model $resource) {
		if(empty($resource->properties)) {
			$sql = "SELECT `option`,`value` FROM dynamic_form_section_fields t 
					where t.`dynamic_form_section_id` = :id and t.field = :field";
			$dfsf = new DynamicFormSectionField();
			$resource->{'properties'} = $dfsf->executeSql($sql,array(
				":id" => $resource->{$this->primary_key},
				":field" => $resource->{$this->name_field},
				)
			);
		}

		return true;
	}

	public function getOptions($field) {

	}
	
}
