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