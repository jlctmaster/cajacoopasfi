-- Cambios Para Egresos y Gastos
ALTER TABLE `iom_expenses` ADD `is_cashupmovement` INT(0) NOT NULL DEFAULT '0' AFTER `detail`;

CREATE TABLE `iom_costs`(
	`cost_id` int(10) NOT NULL AUTO_INCREMENT,
	`documentno` varchar(50) NOT NULL,
	`documentdate` timestamp NOT NULL,
	`person_id` int(10) NULL,
	`cash_concept_id` int(10) NOT NULL,
	`cash_subconcept_id` int(10) NOT NULL,
	`detail` varchar(255) NOT NULL,
	`is_cashupmovement` INT(0) NOT NULL DEFAULT '0',
	`movementtype` char(1) NOT NULL DEFAULT 'C',
	`bankaccount_id` int(10) NULL,
	`voucher_operation_id` int(10) NULL,
	`trx_number` VARCHAR(50) NULL,
	`currency` char(3) NOT NULL DEFAULT 'PEN',
	`amount` decimal(10,2) NOT NULL DEFAULT 0.00,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`cost_id`),
	KEY `person_id` (`person_id`),
	KEY `bankaccount_id` (`bankaccount_id`),
	KEY `cash_concept_id` (`cash_concept_id`),
	KEY `cash_subconcept_id` (`cash_subconcept_id`),
	KEY `voucher_operation_id` (`voucher_operation_id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_costs` 
	ADD CONSTRAINT `iom_costs_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
	ADD CONSTRAINT `iom_costs_ibfk_2` FOREIGN KEY (`bankaccount_id`) REFERENCES `iom_bankaccounts` (`bankaccount_id`),
	ADD CONSTRAINT `iom_costs_ibfk_3` FOREIGN KEY (`cash_concept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`),
	ADD CONSTRAINT `iom_costs_ibfk_4` FOREIGN KEY (`cash_subconcept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`)
	ADD CONSTRAINT `iom_costs_ibfk_5` FOREIGN KEY (`voucher_operation_id`) REFERENCES `iom_voucher_operations` (`voucher_operation_id`);

ALTER TABLE `iom_cash_concepts` ADD `affected_voucheroperation` INT(1) NOT NULL DEFAULT '0' AFTER `cash_concept_parent_id`;

ALTER TABLE `iom_incomes` ADD `voucher_operation_id` INT(10) NULL DEFAULT NULL AFTER `cash_subconcept_id`, ADD INDEX (`voucher_operation_id`) ;

ALTER TABLE `iom_incomes` 
	ADD CONSTRAINT `iom_incomes_ibfk_5` FOREIGN KEY (`voucher_operation_id`) REFERENCES `iom_voucher_operations` (`voucher_operation_id`);

ALTER TABLE `iom_overall_cashs` CHANGE `opendate` `opendate` TIMESTAMP NOT NULL;

ALTER TABLE `iom_expenses` CHANGE `cash_subconcept_id` `cash_subconcept_id` INT(10) NULL DEFAULT NULL;
