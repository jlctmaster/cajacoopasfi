DROP TABLE IF EXISTS `iom_certifiers`;
CREATE TABLE IF NOT EXISTS `iom_certifiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `iom_seals`;
CREATE TABLE IF NOT EXISTS `iom_seals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `iom_periods`;
CREATE TABLE IF NOT EXISTS `iom_periods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `iom_item_types`;
CREATE TABLE IF NOT EXISTS `iom_item_types` (
  `item_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `family` varchar(255) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `iom_models`;
CREATE TABLE IF NOT EXISTS `iom_models` (
  `model_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES 
  ('module_certifiers', 'module_certifiers_desc', '641', 'certifiers'),
  ('module_seals', 'module_seals_desc', '642', 'seals'),
  ('module_periods', 'module_periods_desc', '643', 'periods'),
  ('module_models', 'module_models_desc', '644', 'models'),
  ('module_item_types', 'module_item_types_desc', '645', 'item_types');

INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES 
  ('certifiers', 'certifiers', NULL),
  ('seals', 'seals', NULL),
  ('periods', 'periods', NULL),
  ('models', 'models', NULL),
  ('item_types', 'item_types', NULL);

INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES 
  ('certifiers', '1', 'operation_config'),
  ('certifiers', '2', 'operation_config'),
  ('seals', '1', 'operation_config'),
  ('seals', '2', 'operation_config'),
  ('periods', '1', 'operation_config'),
  ('periods', '2', 'operation_config'),
  ('models', '1', 'operation_config'),
  ('models', '2', 'operation_config'),
  ('item_types', '1', 'operation_config'),
  ('item_types', '2', 'operation_config');