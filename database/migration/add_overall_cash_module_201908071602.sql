INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) 
	VALUES ('module_overall_cashs', 'module_overall_cashs_desc', '799', 'overall_cashs');
INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) 
	VALUES ('overall_cashs', 'overall_cashs', NULL);
INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('overall_cashs', '1', 'home');

CREATE TABLE `iom_overall_cashs`(
	`overall_cash_id` int(10) NOT NULL AUTO_INCREMENT,
	`opendate` timestamp NOT NULL,
	`startbalance` decimal(10,2) NOT NULL DEFAULT 0.00,
	`openingbalance` decimal(10,2) NOT NULL DEFAULT 0.00,
	`closedate` timestamp NULL DEFAULT NULL,
	`endingbalance` decimal(10,2) NOT NULL DEFAULT 0.00,
	`state` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`overall_cash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `iom_cash_flow`(
	`cash_flow_id` int(10) NOT NULL AUTO_INCREMENT,
	`overall_cash_id` int(10) NOT NULL,
	`cash_concept_id` int(10) NOT NULL,
	`cash_book_id` int(10) NOT NULL,
	`operation_type` int(1) NOT NULL DEFAULT 1,
	`movementdate` timestamp NOT NULL,
	`description` varchar(255) NULL DEFAULT NULL,
	`amount` decimal(10,2) NOT NULL DEFAULT 0.00,
	`table_reference` varchar(255) NULL,
	`reference_id` int(10) NULL,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`cash_flow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_cash_concepts` ADD `is_internal` TINYINT(1) NOT NULL DEFAULT '0' AFTER `cash_concept_parent_id`;

INSERT INTO `iom_cash_concepts` (`cash_concept_id`, `code`, `name`, `concept_type`, `document_sequence`, `description`, `is_summary`, `is_cash_general_used`, `cash_concept_parent_id`, `is_internal`, `deleted`) 
	VALUES (NULL, '01-00', 'SALDO INICIAL CAJA GENERAL', '1', NULL, 'SALDO DE APERTURA EN LA CAJA GENERAL, USADO SOLO PARA EL PRIMER MOVIMIENTO.', '0', '1', NULL, '1', '0');
