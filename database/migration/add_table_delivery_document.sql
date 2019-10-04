CREATE TABLE `iom_delivery_documents` (
	`id_delivery_document` INT(11) NOT NULL AUTO_INCREMENT,
	`code` VARCHAR(10) NOT NULL,
	`supplier_id` INT(11) NOT NULL,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`created_by` VARCHAR(50) NOT NULL,
	`tasting_profile_rate` DECIMAL(10,2) NULL DEFAULT '0.00',
	`tasting_observation` VARCHAR(200) NULL DEFAULT NULL,
	`certifier_id` INT(11) NULL DEFAULT '0',
	`period` INT(11) NULL DEFAULT NULL,
	`item_id` INT(1) NULL DEFAULT NULL,
	`type_item_id` INT(1) NULL DEFAULT NULL,
	`amount_entered` DECIMAL(10,2) NULL DEFAULT NULL,
	`fee_deposit_id` INT(11) NULL DEFAULT NULL,
	`updated` DATETIME NULL DEFAULT NULL,
	`updated_by` VARCHAR(50) NULL DEFAULT NULL,
	`status` INT(11) NOT NULL COMMENT '1 - Registrado\\\\\\\\\\\\\\\\n2 - Catado\\\\\\\\\\\\\\\\n3 - Pagado\\\\\\\\\\\\\\\\n4 - Anulado',
	`observation` VARCHAR(200) NULL DEFAULT NULL,
	`deleted` INT(1) NULL DEFAULT NULL,
	PRIMARY KEY (`id_delivery_document`),
	UNIQUE INDEX `code_UNIQUE` (`code`),
	INDEX `supplier_delivery_document_idx` (`supplier_id`),
	CONSTRAINT `iom_delivery_documents_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `iom_suppliers` (`person_id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DYNAMIC
AUTO_INCREMENT=1
;
