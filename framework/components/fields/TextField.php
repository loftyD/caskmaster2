<?php
/**
 * TextField.php
 * This field renders a text field element.
 */
namespace components\fields;
class TextField extends BaseField {

	public function render() {
		$html = parent::render();
		$html .= "<input type=\"text\" name=\"$this->name\" id=\"field_$this->name\" class=\"form-control\" ". $this->setApplicableValue() . "/>";
		return $html;
	}
}