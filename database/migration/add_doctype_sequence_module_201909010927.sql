CREATE TABLE `iom_doctype_sequences`(
	`sequence_id` int(10) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`prefix` varchar(50) NOT NULL,
	`suffix` varchar(50) NOT NULL,
	`next_sequence` varchar(50) NOT NULL,
	`number_incremental` varchar(50) NOT NULL,
	`doctype` varchar(50) NOT NULL,
	`is_cashup` int(1) NOT NULL DEFAULT '0',
	`deleted` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`sequence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_doctypesequences', 'module_doctypesequences_desc', 520, 'doctypesequences');

INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES 
('doctypesequences', 'doctypesequences', NULL);

INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES 
('doctypesequences', 1, 'office'),
('doctypesequences', 2, 'office');