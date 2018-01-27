/**
 *  sql.sql
 *	The required sql for this project.
 */
CREATE DATABASE `demoproject` /*!40100 COLLATE 'utf8_general_ci' */
USE `demoproject`;

CREATE TABLE `user_existence` (
	`user_existence_id` INT NOT NULL AUTO_INCREMENT,
	`name` TEXT NOT NULL,
	`dob` DATE NOT NULL,
	PRIMARY KEY (`user_existence_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;
