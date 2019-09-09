CREATE TABLE `iom_creditnotes`(
	`creditnote_id` int(10) NOT NULL AUTO_INCREMENT,
	`documentdate` timestamp NOT NULL,
	`documentno` varchar(50) NOT NULL,
	`person_id` int(10) NOT NULL,
	`cash_book_id` int(10) NOT NULL,
	`description` varchar(255) NULL,
	`amount` decimal(10,2) NOT NULL DEFAULT 0.00,
	`movementtype` char(1) NOT NULL DEFAULT 'C',
	`trx_number` VARCHAR(50) NULL,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`creditnote_id`),
	KEY `person_id` (`person_id`),
	KEY `cash_book_id` (`cash_book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_creditnotes` 
	ADD CONSTRAINT `iom_creditnotes_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
	ADD CONSTRAINT `iom_creditnotes_ibfk_2` FOREIGN KEY (`cash_book_id`) REFERENCES `iom_cash_books` (`cash_book_id`);

INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_creditnotes', 'module_creditnotes_desc', '99', 'creditnotes');
INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('creditnotes', 'creditnotes', NULL);
INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('creditnotes', '1', 'pay_cash');

INSERT INTO `iom_cash_concepts` (`code`, `name`, `concept_type`, `document_sequence`, `description`, `is_summary`, `is_cash_general_used`, `cash_concept_parent_id`, `affected_voucheroperation`, `is_internal`, `deleted`) VALUES
('00-02-02', 'NOTAS DE CRÉDITO', '2', NULL, 'USADO PARA LAS NOTAS DE CRÉDITO', 1, 0, NULL, 0, 1, 0);
INSERT INTO `iom_cash_concepts` (`code`, `name`, `concept_type`, `document_sequence`, `description`, `is_summary`, `is_cash_general_used`, `cash_concept_parent_id`, `affected_voucheroperation`, `is_internal`, `deleted`) VALUES
('00-02-03', 'COMPROBANTES DE OPERACIÓN SERIE I', '2', NULL, 'USADO PARA LOS COMPROBANTES DE OPERACIÓN SERIE I', 1, 0, NULL, 0, 1, 0);
INSERT INTO `iom_cash_concepts` (`code`, `name`, `concept_type`, `document_sequence`, `description`, `is_summary`, `is_cash_general_used`, `cash_concept_parent_id`, `affected_voucheroperation`, `is_internal`, `deleted`) VALUES
('00-02-04', 'COMPROBANTES DE OPERACIÓN SERIE II', '2', NULL, 'USADO PARA LOS COMPROBANTES DE OPERACIÓN SERIE II', 1, 0, NULL, 0, 1, 0);

UPDATE `iom_cash_concepts` SET `cash_concept_parent_id` = NULL WHERE `iom_cash_concepts`.`code` IN ('00-01-05','00-01-06');