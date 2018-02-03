<?php
/**
 *  DynamicFormSection.php
 *	Dynamic Form Section model.
 */
namespace models;
class DynamicFormSection extends Model {

	public function loadRelations(Model $resource) {
		
		if(empty($resource->fields)) {
			$sql = "SELECT * FROM dynamic_form_section_field_orders dfso
					WHERE dfso.dynamic_form_section_id = :id
						ORDER BY order_index ASC";
			$dfso = new DynamicFormSectionFieldOrder();
			$resource->{'fields'} = $dfso->executeSql($sql,array(":id" => $resource->{$this->primary_key}));
			$dfso->loadConstructedRelations();
		}

		return true;
	}
}