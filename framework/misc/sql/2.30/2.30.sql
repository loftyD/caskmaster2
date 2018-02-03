/*
 * 2.30 SQL
 * Execute this script if you are upgrading else
 * Please run setup.sql for brand new installs.
 * @author ben.hassan
 */

CREATE TABLE `options` (
	`option` VARCHAR(255) NOT NULL,
	`value` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`option`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

INSERT INTO `options` (`option`, `value`) VALUES ('caskmaster.version', '2.30');


CREATE TABLE `dynamic_forms` (
	`dynamic_form_id` INT(11) NOT NULL AUTO_INCREMENT,
	`form_name` VARCHAR(45) NOT NULL,
	`form_class` VARCHAR(255) NOT NULL,
	`form_description` VARCHAR(128) NULL DEFAULT NULL,
	`status` ENUM('active','disabled','deleted') NOT NULL DEFAULT 'active',
	`added` DATETIME NOT NULL,
	`updated` DATETIME NOT NULL,
	PRIMARY KEY (`dynamic_form_id`),
	UNIQUE INDEX `unique_name` (`form_name`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2;



CREATE TABLE `dynamic_form_sections` (
	`dynamic_form_section_id` INT(11) NOT NULL AUTO_INCREMENT,
	`dynamic_form_id` INT(11) NULL DEFAULT NULL,
	`id` VARCHAR(50) NULL DEFAULT NULL,
	`name` VARCHAR(50) NULL DEFAULT NULL,
	`status` ENUM('active','disabled','deleted') NOT NULL DEFAULT 'active',
	`added` DATETIME NOT NULL,
	`updated` DATETIME NOT NULL,
	PRIMARY KEY (`dynamic_form_section_id`),
	INDEX `FK_dynamic_form_sections_dynamic_forms` (`dynamic_form_id`),
	CONSTRAINT `FK_dynamic_form_sections_dynamic_forms` FOREIGN KEY (`dynamic_form_id`) REFERENCES `dynamic_forms` (`dynamic_form_id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;




CREATE TABLE `dynamic_form_section_fields` (
	`dynamic_form_section_id` INT(11) NOT NULL,
	`field` VARCHAR(255) NOT NULL,
	`option` VARCHAR(255) NOT NULL,
	`value` TEXT NOT NULL,
	PRIMARY KEY (`dynamic_form_section_id`, `field`, `option`),
	CONSTRAINT `FK_dynamic_form_section_fields_dynamic_form_sections` FOREIGN KEY (`dynamic_form_section_id`) REFERENCES `dynamic_form_sections` (`dynamic_form_section_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;


CREATE TABLE `dynamic_form_section_field_orders` (
	`dynamic_form_section_id` INT(11) NOT NULL,
	`field` VARCHAR(255) NOT NULL,
	`order_index` INT(11) NOT NULL,
	PRIMARY KEY (`dynamic_form_section_id`, `field`),
	CONSTRAINT `FK__dynamic_form_section_fields` FOREIGN KEY (`dynamic_form_section_id`) REFERENCES `dynamic_form_section_fields` (`dynamic_form_section_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;


INSERT INTO `entity_factory` 
(`name`, `class`, `table`, `primary_key_field`, `name_field`, `added`, `updated`) 
VALUES 
('Dynamic Form', 'DynamicForm', 'dynamic_forms', 'form_id', 'form_name', '2018-02-01 21:44:26', '2018-02-01 21:44:26'),
('Dynamic Form Section', 'DynamicFormSection', 'dynamic_form_sections', 'dynamic_form_section_id', 'name', '2018-02-01 21:44:26', '2018-02-01 21:44:26'),
('Dynamic Form Section Field', 'DynamicFormSectionField', 'dynamic_form_section_fields', 'dynamic_form_section_id', 'field', '2018-02-01 21:44:26', '2018-02-01 21:44:26');


INSERT INTO `dynamic_forms` (`dynamic_form_id`, `form_name`, `form_class`, `form_description`, `status`, `added`, `updated`) VALUES (1, 'Users', 'User', 'User Detail', 'active', '2018-02-01 22:28:27', '2018-02-01 22:28:28');
INSERT INTO `dynamic_form_sections` (`dynamic_form_section_id`, `dynamic_form_id`, `id`, `name`, `status`, `added`, `updated`) VALUES (1, 1, 'main', 'Details', 'active', '2018-02-01 22:28:38', '2018-02-01 22:28:38');
INSERT INTO `dynamic_form_section_fields` (`dynamic_form_section_id`, `field`, `option`, `value`) VALUES (1, 'name', 'fieldType', 'TextField');
INSERT INTO `dynamic_form_section_field_orders` (`dynamic_form_section_id`, `field`, `order_index`) VALUES (1, 'name', 1);

INSERT INTO `entity_factory` (`name`, `class`, `table`, `primary_key_field`, `name_field`, `added`, `updated`) VALUES ('Dynamic Form Section Field Order', 'DynamicFormSectionFieldOrder', 'dynamic_form_section_field_orders', 'dynamic_form_section_id', 'field', '2018-02-02 22:55:13', '2018-02-02 22:55:14');
