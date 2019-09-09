ALTER TABLE `iom_loans` ADD `percent` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `amount`;
ALTER TABLE `iom_loans` ADD `cuote` INT NOT NULL DEFAULT '0' AFTER `loandate`;
ALTER TABLE `iom_credits` ADD `percent` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `amount`;
ALTER TABLE `iom_credits` ADD `cuote` INT NOT NULL DEFAULT '0' AFTER `creditdate`;
ALTER TABLE `iom_payment_loans` ADD `paytype` INT NOT NULL DEFAULT '0' AFTER `paydate`;
ALTER TABLE `iom_payment_credits` ADD `paytype` INT NOT NULL DEFAULT '0' AFTER `paydate`;

-- Cambios 2019-07-17 

UPDATE `iom_loans` SET percent = ROUND(((amt_interest / amount) * 100),2) WHERE percent = 0;
UPDATE `iom_credits` SET percent = ROUND(((amt_interest / amount) * 100),2) WHERE percent = 0;

-- Cambios 2019-07-23

ALTER TABLE `iom_payment_loans` ADD `capital` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `amount`, ADD `interest` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `capital`;
ALTER TABLE `iom_payment_credits` ADD `capital` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `amount`, ADD `interest` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `capital`;

ALTER TABLE `iom_payment_loans` ADD `cumulate_interest` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `interest`;
ALTER TABLE `iom_payment_credits`  ADD `cumulate_interest` DECIMAL(10,2) NOT NULL DEFAULT '0.00'  AFTER `interest`;
