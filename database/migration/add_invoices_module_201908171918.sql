CREATE TABLE `iom_invoices`(
	`invoice_id` int(10) NOT NULL AUTO_INCREMENT,
	`documentdate` timestamp NOT NULL,
	`serieno` varchar(50) NOT NULL,
	`person_id` int(10) NOT NULL,
	`cash_book_id` int(10) NOT NULL,
	`description` varchar(255) NULL,
	`subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
	`discount` decimal(10,2) NOT NULL DEFAULT 0.00,
	`discountamt` decimal(10,2) NOT NULL DEFAULT 0.00,
	`tax` decimal(10,2) NOT NULL DEFAULT 0.00,
	`taxamt` decimal(10,2) NOT NULL DEFAULT 0.00,
	`totalamt` decimal(10,2) NOT NULL DEFAULT 0.00,
	`movementtype` char(1) NOT NULL DEFAULT 'C',
	`trx_number` VARCHAR(50) NULL,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`invoice_id`),
	KEY `person_id` (`person_id`),
	KEY `cash_book_id` (`cash_book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `iom_lineinvoices`(
	`lineinvoice_id` int(10) NOT NULL AUTO_INCREMENT,
	`invoice_id` int(10) NOT NULL,
	`quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
	`detail` VARCHAR(255) NOT NULL,
	`price` decimal(10,2) NOT NULL DEFAULT 0.00,
	`amount` decimal(10,2) NOT NULL DEFAULT 0.00,
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`lineinvoice_id`),
	KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_invoices` 
	ADD CONSTRAINT `iom_invoices_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_customers` (`person_id`),
	ADD CONSTRAINT `iom_invoices_ibfk_2` FOREIGN KEY (`cash_book_id`) REFERENCES `iom_cash_books` (`cash_book_id`);

ALTER TABLE `iom_lineinvoices` 
	ADD CONSTRAINT `iom_lineinvoices_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `iom_invoices` (`invoice_id`);

INSERT INTO `iom_cash_concepts` (`cash_concept_id`, `code`, `name`, `concept_type`, `document_sequence`, `description`, `is_summary`, `is_cash_general_used`, `cash_concept_parent_id`, `affected_voucheroperation`, `is_internal`, `deleted`) VALUES (NULL, '00-01-06', 'FACTURAS', '1', NULL, NULL, '0', '0', '1', '0', '1', '0');

INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_invoices', 'module_invoices_desc', '99', 'invoices');
INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('invoices', 'invoices', NULL);
INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('invoices', '1', 'pay_cash');

ALTER TABLE `iom_customers` ADD `ruc` VARCHAR(20) NULL DEFAULT NULL AFTER `person_id`;

INSERT INTO `iom_app_config` (`key`, `value`) VALUES
('default_tax_1_name', 'IGV'),
('default_tax_1_rate', '18');