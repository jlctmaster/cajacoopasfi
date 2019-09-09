ALTER TABLE `iom_expenses` ADD `person_name` VARCHAR(255) NULL DEFAULT NULL AFTER `person_id`;
ALTER TABLE `iom_expenses` CHANGE `person_id` `person_id` INT(10) NULL DEFAULT NULL;

CREATE TABLE `iom_cashup_currencys`(
	`cashup_currency_id` int(10) NOT NULL AUTO_INCREMENT,
	`cashup_id` int(10) NOT NULL,
	`currency` CHAR(3) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'PEN',
	`denomination` varchar(100) NOT NULL,
	`quantity` int(10) NOT NULL DEFAULT 0,
	`amount` decimal(10,2) NOT NULL DEFAULT 0,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`cashup_currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;