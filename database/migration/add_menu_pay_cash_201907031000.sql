INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_pay_cash', 'module_pay_cash_desc', '788', 'pay_cash');
INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('pay_cash', 'pay_cash', NULL);
INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_loans_credits', 'module_loans_credits_des', '80', 'loans_credits');
INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('loans_credits', 'loans_credits', NULL);
INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_cashups', 'module_cashups_desc', '66', 'cashups');
INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('cashups', 'cashups', NULL);

-- --------------------------------------------------------

--
-- Dumping data for table `iom_cash_up`
--

CREATE TABLE `iom_cash_up` (
  `cashup_id` int(10) NOT NULL AUTO_INCREMENT,
  `cash_book_id` INT(10) NOT NULL,
  `open_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `close_date` timestamp NULL DEFAULT NULL,
  `open_amount_cash` decimal(15,2) NOT NULL,
  `transfer_amount_cash` decimal(15,2) NOT NULL,
  `note` int(1) NOT NULL,
  `closed_amount_cash` decimal(15,2) NOT NULL,
  `closed_amount_card` decimal(15,2) NOT NULL,
  `closed_amount_check` decimal(15,2) NOT NULL,
  `closed_amount_total` decimal(15,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `open_employee_id` int(10) NOT NULL,
  `close_employee_id` int(10) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `closed_amount_due` decimal(15,2) NOT NULL,
  PRIMARY KEY (`cashup_id`),
  KEY `open_employee_id` (`open_employee_id`),
  KEY `close_employee_id` (`close_employee_id`),
  KEY `cash_book_id` (`cash_book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `iom_loans`
--

CREATE TABLE `iom_loans` (
  `loan_id` int(10) NOT NULL AUTO_INCREMENT,
  `loan_type` char(1) NOT NULL DEFAULT 'P',
  `person_id` int(10) NOT NULL,
  `loandate` date NOT NULL,
  `returndate` DATE NULL,
  `motive` varchar(255) NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amt_interest` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`loan_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `iom_payment_loans`
--

CREATE TABLE `iom_payment_loans` (
  `payment_loan_id` int(10) NOT NULL AUTO_INCREMENT,
  `loan_id` int(10) NOT NULL,
  `paydate` date NOT NULL,
  `observations` varchar(255) NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_loan_id`),
  KEY `loan_id` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `iom_credits`
--

CREATE TABLE `iom_credits` (
  `credit_id` int(10) NOT NULL AUTO_INCREMENT,
  `person_id` int(10) NOT NULL,
  `creditdate` date NOT NULL,
  `returndate` DATE NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amt_interest` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`credit_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `iom_credit_items`
--

CREATE TABLE `iom_credit_items` (
  `credit_item_id` int(10) NOT NULL AUTO_INCREMENT,
  `credit_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`credit_item_id`),
  KEY `credit_id` (`credit_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `iom_payment_loans`
--

CREATE TABLE `iom_payment_credits` (
  `payment_credit_id` int(10) NOT NULL AUTO_INCREMENT,
  `credit_id` int(10) NOT NULL,
  `paydate` date NOT NULL,
  `observations` varchar(255) NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_credit_id`),
  KEY `credit_id` (`credit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for table `iom_cash_up`
--
ALTER TABLE `iom_cash_up`
  ADD CONSTRAINT `iom_cash_up_ibfk_1` FOREIGN KEY (`open_employee_id`) REFERENCES `iom_users` (`person_id`),
  ADD CONSTRAINT `iom_cash_up_ibfk_2` FOREIGN KEY (`close_employee_id`) REFERENCES `iom_users` (`person_id`),
  ADD CONSTRAINT `iom_cash_up_ibfk_3` FOREIGN KEY (`cash_book_id`) REFERENCES `iom_cash_books` (`cash_book_id`);

--
-- Constraints for table `iom_loans`
--
ALTER TABLE `iom_loans`
  ADD CONSTRAINT `iom_loans_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`);

--
-- Constraints for table `iom_payment_loans`
--
ALTER TABLE `iom_payment_loans`
  ADD CONSTRAINT `iom_payment_loans_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `iom_loans` (`loan_id`);

--
-- Constraints for table `iom_credits`
--
ALTER TABLE `iom_credits`
  ADD CONSTRAINT `iom_credits_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_suppliers` (`person_id`);

--
-- Constraints for table `iom_credit_items`
--
ALTER TABLE `iom_credit_items`
  ADD CONSTRAINT `iom_credit_items_ibfk_2` FOREIGN KEY (`credit_id`) REFERENCES `iom_credits` (`credit_id`),
  ADD CONSTRAINT `iom_credit_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `iom_items` (`item_id`);

--
-- Constraints for table `iom_payment_credits`
--
ALTER TABLE `iom_payment_credits`
  ADD CONSTRAINT `iom_payment_credits_ibfk_1` FOREIGN KEY (`credit_id`) REFERENCES `iom_credits` (`credit_id`);


-- Cambios 07-07-2019

ALTER TABLE `iom_credit_items` ADD `location_id` INT(11) NOT NULL AFTER `credit_id`, ADD INDEX (`location_id`) ;

ALTER TABLE `iom_credit_items`
  ADD CONSTRAINT `iom_credit_items_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `iom_stock_locations` (`location_id`);