INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_cash', 'module_cash_desc', '777', 'cash');

INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('cash', 'cash', NULL);

ALTER TABLE `iom_stock_locations` ADD `location_code` VARCHAR(10) NULL DEFAULT NULL AFTER `location_id`;

ALTER TABLE `iom_users` ADD `stock_location_id` INT(10) NULL DEFAULT NULL AFTER `hash_version`;

ALTER TABLE `iom_users`
  ADD CONSTRAINT `iom_users_ibfk_2` FOREIGN KEY (`stock_location_id`) REFERENCES `iom_stock_locations` (`location_id`);

CREATE TABLE `iom_cash_books` (
  `cash_book_id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(40) NOT NULL,
  `stock_location_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_cash_general` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cash_book_id`),
  UNIQUE KEY `code` (`code`,`stock_location_id`,`user_id`,`deleted`),
  KEY `stock_location_id` (`stock_location_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `iom_cash_books`
  ADD CONSTRAINT `iom_cash_books_ibfk_1` FOREIGN KEY (`stock_location_id`) REFERENCES `iom_stock_locations` (`location_id`),
  ADD CONSTRAINT `iom_cash_books_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `iom_users` (`person_id`);

INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_cash_books', 'module_cash_books_desc', '70', 'cash_books');

INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('cash_books', 'cash_books', NULL);

