<?php
/**
 * PasswordField.php
 * This field renders a password field element.
 */
namespace components\fields;
class PasswordField extends BaseField {

	public function render() {
		$html = parent::render();
		$html .= "<input type=\"password\" name=\"$this->name\" id=\"field_$this->name\" class=\"form-control\" ". $this->setApplicableValue() . "/>";
		return $html;
	}
}