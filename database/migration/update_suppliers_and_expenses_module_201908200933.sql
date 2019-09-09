ALTER TABLE `iom_suppliers` ADD `ruc` VARCHAR(20) NULL DEFAULT NULL AFTER `person_id`;
UPDATE `iom_cash_concepts` SET `concept_type` = '1' WHERE `iom_cash_concepts`.`cash_concept_id` IN (24,25,26);
UPDATE `iom_cash_concepts` SET `affected_voucheroperation` = '1' WHERE `iom_cash_concepts`.`cash_concept_id` = 24;
ALTER TABLE `iom_expenses` ADD `doctype` VARCHAR(255) NULL DEFAULT NULL AFTER `detail`;
ALTER TABLE `iom_expenses` ADD `docnumber` VARCHAR(255) NULL DEFAULT NULL AFTER `doctype`;