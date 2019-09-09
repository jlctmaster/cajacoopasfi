CREATE TABLE `iom_overallcash_currencys`(
	`overallcash_currency_id` int(10) NOT NULL AUTO_INCREMENT,
	`overall_cash_id` int(10) NOT NULL,
	`currency` CHAR(3) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'PEN',
	`denomination` varchar(100) NOT NULL,
	`quantity` int(10) NOT NULL DEFAULT 0,
	`amount` decimal(10,2) NOT NULL DEFAULT 0,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`overallcash_currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;