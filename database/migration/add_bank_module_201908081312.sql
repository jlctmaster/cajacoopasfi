CREATE TABLE `iom_banks`(
	`bank_id` int(10) NOT NULL AUTO_INCREMENT,
	`ruc` varchar(50) NOT NULL,
	`name` varchar(255) NOT NULL,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `iom_bankaccounts`(
	`bankaccount_id` int(10) NOT NULL AUTO_INCREMENT,
	`bank_id` int(10) NOT NULL,
	`currency` char(3) NOT NULL DEFAULT 'PEN',
	`account_number` varchar(20) NOT NULL,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`bankaccount_id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_bankaccounts` 
	ADD CONSTRAINT `iom_bankaccounts_ibfk_1` FOREIGN KEY (`bank_id`) REFERENCES `iom_banks` (`bank_id`);