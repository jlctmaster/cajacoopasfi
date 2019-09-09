CREATE TABLE `iom_expenses`(
	`expense_id` int(10) NOT NULL AUTO_INCREMENT,
	`documentno` varchar(50) NOT NULL,
	`documentdate` timestamp NOT NULL,
	`person_id` int(10) NOT NULL,
	`cash_concept_id` int(10) NOT NULL,
	`cash_subconcept_id` int(10) NOT NULL,
	`detail` varchar(255) NOT NULL,
	`movementtype` char(1) NOT NULL DEFAULT 'C',
	`bankaccount_id` int(10) NULL,
	`trx_number` VARCHAR(50) NULL,
	`currency` char(3) NOT NULL DEFAULT 'PEN',
	`amount` decimal(10,2) NOT NULL DEFAULT 0.00,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`expense_id`),
	KEY `person_id` (`person_id`),
	KEY `bankaccount_id` (`bankaccount_id`),
	KEY `cash_concept_id` (`cash_concept_id`),
	KEY `cash_subconcept_id` (`cash_subconcept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_expenses` 
	ADD CONSTRAINT `iom_expenses_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
	ADD CONSTRAINT `iom_expenses_ibfk_2` FOREIGN KEY (`bankaccount_id`) REFERENCES `iom_bankaccounts` (`bankaccount_id`),
	ADD CONSTRAINT `iom_expenses_ibfk_3` FOREIGN KEY (`cash_concept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`),
	ADD CONSTRAINT `iom_expenses_ibfk_4` FOREIGN KEY (`cash_subconcept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`);