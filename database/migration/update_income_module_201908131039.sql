ALTER TABLE `iom_incomes` CHANGE `person_id` `person_id` INT(10) NULL DEFAULT NULL;
ALTER TABLE `iom_incomes` ADD `person_name` VARCHAR(255) NULL DEFAULT NULL AFTER `person_id`;
