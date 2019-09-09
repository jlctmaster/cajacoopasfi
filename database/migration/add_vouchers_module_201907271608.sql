INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) 
	VALUES ('module_vouchers', 'module_vouchers_desc', '85', 'vouchers');
INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) 
	VALUES ('vouchers', 'vouchers', NULL);
INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) 
	VALUES ('vouchers', '1', 'pay_cash');

CREATE TABLE `iom_vouchers` (
	`voucher_id` int(10) NOT NULL AUTO_INCREMENT,
  `voucher_type` char(1) NOT NULL DEFAULT 'P',
	`voucherdate` date NOT NULL,
	`voucher_number` varchar(20) NULL,
  `person_id` int(10) NOT NULL,
  `detail` varchar(255) NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cash_type` char(1) NOT NULL DEFAULT 'C',
  `trx_number` char(20) NULL DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`voucher_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_vouchers`
  ADD CONSTRAINT `iom_vouchers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`);

-- Added 2019-07-29

CREATE TABLE `iom_payment_vouchers` (
  `payment_voucher_id` int(10) NOT NULL AUTO_INCREMENT,
  `voucher_id` int(10) NOT NULL,
  `paydate` date NOT NULL,
  `observations` varchar(255) NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_voucher_id`),
  KEY `voucher_id` (`voucher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_payment_vouchers`
  ADD CONSTRAINT `iom_payment_vouchers_ibfk_1` FOREIGN KEY (`voucher_id`) REFERENCES `iom_vouchers` (`voucher_id`);