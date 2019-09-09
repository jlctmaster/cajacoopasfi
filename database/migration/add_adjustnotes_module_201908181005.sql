CREATE TABLE `iom_adjustnotes`(
	`adjustnote_id` int(10) NOT NULL AUTO_INCREMENT,
	`documentdate` timestamp NOT NULL,
	`documentno` varchar(50) NOT NULL,
	`person_id` int(10) NOT NULL,
	`cash_concept_id` int(10) NOT NULL,
	`cash_book_id` int(10) NOT NULL,
	`description` varchar(255) NULL,
	`amount` decimal(10,2) NOT NULL DEFAULT 0.00,
	`movementtype` char(1) NOT NULL DEFAULT 'C',
	`trx_number` VARCHAR(50) NULL,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`adjustnote_id`),
	KEY `person_id` (`person_id`),
	KEY `cash_concept_id` (`cash_concept_id`),
	KEY `cash_book_id` (`cash_book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_adjustnotes` 
	ADD CONSTRAINT `iom_adjustnotes_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
	ADD CONSTRAINT `iom_adjustnotes_ibfk_2` FOREIGN KEY (`cash_concept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`),
	ADD CONSTRAINT `iom_adjustnotes_ibfk_3` FOREIGN KEY (`cash_book_id`) REFERENCES `iom_cash_books` (`cash_book_id`);

INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_adjustnotes', 'module_adjustnotes_desc', '99', 'adjustnotes');
INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('adjustnotes', 'adjustnotes', NULL);
INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('adjustnotes', '1', 'pay_cash');

INSERT INTO `iom_cash_concepts` (`cash_concept_id`, `code`, `name`, `concept_type`, `document_sequence`, `description`, `is_summary`, `is_cash_general_used`, `cash_concept_parent_id`, `affected_voucheroperation`, `is_internal`, `deleted`) VALUES
(52, '02-02', 'NOTAS DE AJUSTE', '2', NULL, 'USADO PARA LAS NOTAS DE AJUSTE', 1, 0, NULL, 0, 0, 0);
INSERT INTO `iom_cash_concepts` (`cash_concept_id`, `code`, `name`, `concept_type`, `document_sequence`, `description`, `is_summary`, `is_cash_general_used`, `cash_concept_parent_id`, `affected_voucheroperation`, `is_internal`, `deleted`) VALUES
(57, '02-02-01', 'DISTRIBUCIÓN DE EXCEDENTE COMPAÑÍA CAFETALERA', '2', '', '', 1, 0, 52, 0, 0, 0);

ALTER TABLE `iom_vouchers` ADD `cash_book_id` INT(10) NOT NULL AFTER `person_id`;
