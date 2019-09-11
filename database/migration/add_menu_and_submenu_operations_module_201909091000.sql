INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES 
	('module_operation', 'module_operation_desc', '600', 'operation'),
	('module_hoard', 'module_hoard_desc', '610', 'hoard'),
	('module_commercialization', 'module_commercialization_desc', '620', 'commercialization'),
	('module_handling', 'module_handling_desc', '630', 'handling'),
	('module_operation_config', 'module_operation_config_desc', '640', 'operation_config');

INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES 
	('operation', 'operation', NULL),
	('hoard', 'hoard', NULL),
	('commercialization', 'commercialization', NULL),
	('handling', 'handling', NULL),
	('operation_config', 'operation_config', NULL);

INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES 
('operation', '1', 'home'),
('operation', '2', 'home'),
('hoard', '1', 'operation'),
('hoard', '2', 'operation'),
('commercialization', '1', 'operation'),
('commercialization', '2', 'operation'),
('handling', '1', 'operation'),
('handling', '2', 'operation'),
('operation_config', '1', 'operation'),
('operation_config', '2', 'operation');

UPDATE `iom_grants` SET `menu_group` = 'operation_config' WHERE `iom_grants`.`permission_id` IN ('items','uoms') AND `iom_grants`.`person_id` IN (1,2,3,4);