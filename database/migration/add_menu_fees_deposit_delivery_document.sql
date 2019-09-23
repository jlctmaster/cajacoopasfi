//Modules
INSERT INTO `cajacoopafsidb`.`iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_fees_deposit', 'module_fees_deposit_desc', '611', 'fees_deposit');
INSERT INTO `cajacoopafsidb`.`iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES ('module_delivery_documents', 'module_delivery_documents_desc', '612', 'delivery_documents');

permissions
INSERT INTO `cajacoopafsidb`.`iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('fees_deposit', 'fees_deposit', NULL);
INSERT INTO `cajacoopafsidb`.`iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES ('delivery_documents', 'delivery_documents', NULL);



//Grants
INSERT INTO `cajacoopafsidb`.`iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('fees_deposit', '1', 'hoard');
INSERT INTO `cajacoopafsidb`.`iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('fees_deposit', '2', 'hoard');
INSERT INTO `cajacoopafsidb`.`iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('fees_deposit', '3', 'hoard');
INSERT INTO `cajacoopafsidb`.`iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('fees_deposit', '4', 'hoard');

INSERT INTO `cajacoopafsidb`.`iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('delivery_documents', '1', 'hoard');
INSERT INTO `cajacoopafsidb`.`iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('delivery_documents', '2', 'hoard');
INSERT INTO `cajacoopafsidb`.`iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('delivery_documents', '3', 'hoard');
INSERT INTO `cajacoopafsidb`.`iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES ('delivery_documents', '4', 'hoard');