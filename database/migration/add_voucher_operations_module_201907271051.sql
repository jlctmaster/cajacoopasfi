INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) 
	VALUES ('module_voucher_operations', 'module_voucher_operations_desc', '90', 'voucher_operations');
INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) 
	VALUES ('voucher_operations', 'voucher_operations', NULL);
INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('voucher_operations', '1', 'pay_cash');

CREATE TABLE `iom_voucher_operations` (
	`voucher_operation_id` int(10) NOT NULL AUTO_INCREMENT,
	`voucherdate` date NOT NULL,
	`serieno` char(2) NOT NULL DEFAULT '02',
	`voucher_operation_number` varchar(20) NULL,
  	`person_id` int(10) NOT NULL,
  	`amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  	`state` int(1) NOT NULL DEFAULT '0',
  	`deleted` int(1) NOT NULL DEFAULT '0',
  	PRIMARY KEY (`voucher_operation_id`),
  	KEY `person_id` (`person_id`),
  	UNIQUE KEY `voucher_operation_number` (`serieno`,`voucher_operation_number`,`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `iom_quality_certificates` (
	`quality_certificate_id` int(10) NOT NULL AUTO_INCREMENT,
	`depositdate` date NOT NULL,
	`serieno` char(2) NOT NULL DEFAULT '02',
	`certificate_number` varchar(20) NULL,
	`person_id` int(10) NOT NULL,
	`kg_dry` decimal(10,2) NOT NULL DEFAULT 0.00,
	`qq_dry` decimal(10,2) NOT NULL DEFAULT 0.00,
	`rate_profile` decimal(10,2) NOT NULL DEFAULT 0.00,
	`physical_performance` decimal(10,2) NOT NULL DEFAULT 0.00,
	`quality` varchar(255) NULL DEFAULT NULL,
	`location_id` int(10) NOT NULL,
	`price` decimal(10,2) NOT NULL DEFAULT 0.00,
	`amount` decimal(10,2) NOT NULL DEFAULT 0.00,
	`voucher_operation_id` int(10) NULL,
  	`reference_id` varchar(255) NULL,
	`imported` int(1) NOT NULL DEFAULT '0',
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`quality_certificate_id`),
	KEY `person_id` (`person_id`),
	KEY `location_id` (`location_id`),
	KEY `voucher_operation_id` (`voucher_operation_id`),
	UNIQUE KEY `quality_certificate_number` (`serieno`,`certificate_number`,`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_voucher_operations`
  ADD CONSTRAINT `iom_voucher_operations_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_suppliers` (`person_id`);

ALTER TABLE `iom_quality_certificates`
  ADD CONSTRAINT `iom_quality_certificates_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_suppliers` (`person_id`),
  ADD CONSTRAINT `iom_quality_certificates_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `iom_stock_locations` (`location_id`),
  ADD CONSTRAINT `iom_quality_certificates_ibfk_3` FOREIGN KEY (`voucher_operation_id`) REFERENCES `iom_voucher_operations` (`voucher_operation_id`);

-- Cambios 2019-08-07 

ALTER TABLE `iom_voucher_operations` 
	ADD `liquidatedate` DATE NULL AFTER `amount`, 
	ADD `cash_book_id` INT(10) NULL AFTER `liquidatedate`, 
	ADD `printed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `cash_book_id`;
