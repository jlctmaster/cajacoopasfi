-- --------------------------------------------------------

--
-- Dumping data for table `iom_cash_concepts`
--

CREATE TABLE `iom_cash_concepts` (
  `cash_concept_id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `concept_type` char(1) NOT NULL DEFAULT '1',
  `document_sequence` VARCHAR(20) NULL DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_summary` tinyint(1) NOT NULL DEFAULT '0',
  `is_cash_general_used` tinyint(1) NOT NULL DEFAULT '0',
  `cash_concept_parent_id` int(10) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cash_concept_id`),
  UNIQUE KEY `code` (`code`,`deleted`),
  KEY `cash_concept_parent_id` (`cash_concept_parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for table `iom_cash_concepts`
--
ALTER TABLE `iom_cash_concepts`
  ADD CONSTRAINT `iom_cash_concepts_ibfk_1` FOREIGN KEY (`cash_concept_parent_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`);

INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES 
('module_cash_concepts', 'module_cash_concepts_desc', 48, 'cash_concepts');

INSERT INTO `iom_permissions` (`permission_id`, `module_id`) VALUES 
('cash_concepts', 'cash_concepts');

INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('cash_concepts', 1, 'home');