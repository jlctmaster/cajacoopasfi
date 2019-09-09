CREATE TABLE `iom_cash_daily`(
	`cash_daily_id` int(10) NOT NULL AUTO_INCREMENT,
	`cashup_id` int(10) NOT NULL,
	`cash_concept_id` int(10) NOT NULL,
	`cash_book_id` int(10) NOT NULL,
	`operation_type` int(1) NOT NULL DEFAULT 1,
	`movementdate` timestamp NOT NULL,
	`description` varchar(255) NULL DEFAULT NULL,
	`isbankmovement` int(1) NOT NULL DEFAULT '0',
	`currency` CHAR(3) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'PEN',
	`amount` decimal(10,2) NOT NULL DEFAULT 0.00,
	`table_reference` varchar(255) NULL,
	`reference_id` int(10) NULL,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`cash_daily_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE `iom_cash_concepts` SET `code` = '02-00', `concept_type` = '2' WHERE `iom_cash_concepts`.`cash_concept_id` = 2;

ALTER TABLE `iom_cash_up` CHANGE `transfer_amount_cash` `transfer_amount_cash` DECIMAL(15,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `iom_cash_up` CHANGE `note` `note` INT(1) NOT NULL DEFAULT '0';
ALTER TABLE `iom_cash_up` CHANGE `closed_amount_cash` `closed_amount_cash` DECIMAL(15,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `iom_cash_up` CHANGE `closed_amount_card` `closed_amount_card` DECIMAL(15,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `iom_cash_up` CHANGE `closed_amount_check` `closed_amount_check` DECIMAL(15,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `iom_cash_up` CHANGE `closed_amount_total` `closed_amount_total` DECIMAL(15,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `iom_cash_up` CHANGE `closed_amount_due` `closed_amount_due` DECIMAL(15,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `iom_cash_up` CHANGE `close_employee_id` `close_employee_id` INT(10) NULL DEFAULT NULL;

INSERT INTO `iom_cash_concepts` (`cash_concept_id`, `code`, `name`, `concept_type`, `document_sequence`, `description`, `is_summary`, `is_cash_general_used`, `cash_concept_parent_id`, `is_internal`, `deleted`) 
	VALUES (NULL, '00-01-00', 'SALDO APERTURA CAJA PAGADORA', '1', NULL, 'SALDO DE APERTURA EN LA CAJA PAGADORA POR USUARIO, USADO SOLO PARA EL PRIMER MOVIMIENTO.', '0', '1', NULL, '1', '0');

ALTER TABLE `iom_incomes` CHANGE `bankaccount_id` `bankaccount_id` INT(10) NULL DEFAULT NULL;