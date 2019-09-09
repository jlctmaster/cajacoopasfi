--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `iom_adjustnotes`
--
ALTER TABLE `iom_adjustnotes`
 ADD KEY `person_id` (`person_id`), ADD KEY `cash_concept_id` (`cash_concept_id`), ADD KEY `cash_book_id` (`cash_book_id`);

--
-- Indices de la tabla `iom_app_config`
--
ALTER TABLE `iom_app_config`
 ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `iom_bankaccounts`
--
ALTER TABLE `iom_bankaccounts`
 ADD KEY `iom_bankaccounts_ibfk_1` (`bank_id`);

--
-- Indices de la tabla `iom_cash_books`
--
ALTER TABLE `iom_cash_books`
 ADD UNIQUE KEY `code` (`code`,`stock_location_id`,`user_id`,`deleted`), ADD KEY `stock_location_id` (`stock_location_id`), ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `iom_cash_concepts`
--
ALTER TABLE `iom_cash_concepts`
 ADD UNIQUE KEY `code` (`code`,`deleted`), ADD KEY `cash_concept_parent_id` (`cash_concept_parent_id`);

--
-- Indices de la tabla `iom_cash_up`
--
ALTER TABLE `iom_cash_up`
 ADD KEY `open_employee_id` (`open_employee_id`), ADD KEY `close_employee_id` (`close_employee_id`), ADD KEY `cash_book_id` (`cash_book_id`);

--
-- Indices de la tabla `iom_costs`
--
ALTER TABLE `iom_costs`
 ADD KEY `person_id` (`person_id`), ADD KEY `bankaccount_id` (`bankaccount_id`), ADD KEY `cash_concept_id` (`cash_concept_id`), ADD KEY `cash_subconcept_id` (`cash_subconcept_id`), ADD KEY `voucher_operation_id` (`voucher_operation_id`);

--
-- Indices de la tabla `iom_creditnotes`
--
ALTER TABLE `iom_creditnotes`
 ADD KEY `person_id` (`person_id`), ADD KEY `cash_book_id` (`cash_book_id`);

--
-- Indices de la tabla `iom_credits`
--
ALTER TABLE `iom_credits`
 ADD KEY `person_id` (`person_id`);

--
-- Indices de la tabla `iom_credit_items`
--
ALTER TABLE `iom_credit_items`
 ADD KEY `credit_id` (`credit_id`), ADD KEY `item_id` (`item_id`), ADD KEY `location_id` (`location_id`);

--
-- Indices de la tabla `iom_customers`
--
ALTER TABLE `iom_customers`
 ADD UNIQUE KEY `account_number` (`account_number`), ADD KEY `person_id` (`person_id`), ADD KEY `iom_customers_ibfk_2` (`user_id`);

--
-- Indices de la tabla `iom_employees`
--
ALTER TABLE `iom_employees`
 ADD UNIQUE KEY `ruc` (`ruc`), ADD KEY `person_id` (`person_id`);

--
-- Indices de la tabla `iom_expenses`
--
ALTER TABLE `iom_expenses`
 ADD KEY `person_id` (`person_id`), ADD KEY `bankaccount_id` (`bankaccount_id`), ADD KEY `cash_concept_id` (`cash_concept_id`), ADD KEY `cash_subconcept_id` (`cash_subconcept_id`);

--
-- Indices de la tabla `iom_grants`
--
ALTER TABLE `iom_grants`
 ADD PRIMARY KEY (`permission_id`,`person_id`), ADD KEY `iom_grants_ibfk_2` (`person_id`);

--
-- Indices de la tabla `iom_growing_areas`
--
ALTER TABLE `iom_growing_areas`
 ADD UNIQUE KEY `name` (`name`,`district`,`state`,`country`);

--
-- Indices de la tabla `iom_incomes`
--
ALTER TABLE `iom_incomes`
 ADD KEY `person_id` (`person_id`), ADD KEY `bankaccount_id` (`bankaccount_id`), ADD KEY `cash_concept_id` (`cash_concept_id`), ADD KEY `cash_subconcept_id` (`cash_subconcept_id`), ADD KEY `voucher_operation_id` (`voucher_operation_id`);

--
-- Indices de la tabla `iom_inventory`
--
ALTER TABLE `iom_inventory`
 ADD KEY `trans_items` (`trans_items`), ADD KEY `trans_user` (`trans_user`), ADD KEY `trans_location` (`trans_location`);

--
-- Indices de la tabla `iom_invoices`
--
ALTER TABLE `iom_invoices`
 ADD KEY `person_id` (`person_id`), ADD KEY `cash_book_id` (`cash_book_id`);

--
-- Indices de la tabla `iom_items`
--
ALTER TABLE `iom_items`
 ADD KEY `supplier_id` (`supplier_id`), ADD KEY `item_number` (`item_number`), ADD KEY `iom_items_ibfk_2` (`uom_id`);

--
-- Indices de la tabla `iom_items_taxes`
--
ALTER TABLE `iom_items_taxes`
 ADD PRIMARY KEY (`item_id`,`name`,`percent`);

--
-- Indices de la tabla `iom_item_quantities`
--
ALTER TABLE `iom_item_quantities`
 ADD PRIMARY KEY (`item_id`,`location_id`), ADD KEY `item_id` (`item_id`), ADD KEY `location_id` (`location_id`);

--
-- Indices de la tabla `iom_lineinvoices`
--
ALTER TABLE `iom_lineinvoices`
 ADD KEY `invoice_id` (`invoice_id`);

--
-- Indices de la tabla `iom_lineticketsales`
--
ALTER TABLE `iom_lineticketsales`
 ADD KEY `ticketsale_id` (`ticketsale_id`);

--
-- Indices de la tabla `iom_loans`
--
ALTER TABLE `iom_loans`
 ADD KEY `person_id` (`person_id`);

--
-- Indices de la tabla `iom_modules`
--
ALTER TABLE `iom_modules`
 ADD PRIMARY KEY (`module_id`), ADD UNIQUE KEY `desc_lang_key` (`desc_lang_key`), ADD UNIQUE KEY `name_lang_key` (`name_lang_key`);

--
-- Indices de la tabla `iom_payment_credits`
--
ALTER TABLE `iom_payment_credits`
 ADD KEY `credit_id` (`credit_id`);

--
-- Indices de la tabla `iom_payment_loans`
--
ALTER TABLE `iom_payment_loans`
 ADD KEY `loan_id` (`loan_id`);

--
-- Indices de la tabla `iom_payment_vouchers`
--
ALTER TABLE `iom_payment_vouchers`
 ADD KEY `voucher_id` (`voucher_id`);

--
-- Indices de la tabla `iom_people`
--
ALTER TABLE `iom_people`
 ADD UNIQUE KEY `dni` (`dni`,`country`), ADD KEY `email` (`email`);

--
-- Indices de la tabla `iom_permissions`
--
ALTER TABLE `iom_permissions`
 ADD PRIMARY KEY (`permission_id`), ADD KEY `module_id` (`module_id`), ADD KEY `iom_permissions_ibfk_2` (`location_id`);

--
-- Indices de la tabla `iom_quality_certificates`
--
ALTER TABLE `iom_quality_certificates`
 ADD UNIQUE KEY `quality_certificate_number` (`serieno`,`certificate_number`,`deleted`), ADD KEY `person_id` (`person_id`), ADD KEY `location_id` (`location_id`), ADD KEY `voucher_operation_id` (`voucher_operation_id`);

--
-- Indices de la tabla `iom_sessions`
--
ALTER TABLE `iom_sessions`
 ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indices de la tabla `iom_suppliers`
--
ALTER TABLE `iom_suppliers`
 ADD UNIQUE KEY `account_number` (`account_number`), ADD KEY `person_id` (`person_id`), ADD KEY `growing_area_id` (`growing_area_id`);

--
-- Indices de la tabla `iom_ticketsales`
--
ALTER TABLE `iom_ticketsales`
 ADD KEY `person_id` (`person_id`), ADD KEY `cash_book_id` (`cash_book_id`);

--
-- Indices de la tabla `iom_uom_conversions`
--
ALTER TABLE `iom_uom_conversions`
 ADD KEY `iom_uom_conversions_ibfk_1` (`item_id`), ADD KEY `iom_uom_conversions_ibfk_2` (`uom_id`), ADD KEY `iom_uom_conversions_ibfk_3` (`uomto_id`);

--
-- Indices de la tabla `iom_users`
--
ALTER TABLE `iom_users`
 ADD UNIQUE KEY `username` (`username`), ADD KEY `person_id` (`person_id`), ADD KEY `iom_users_ibfk_2` (`stock_location_id`);

--
-- Indices de la tabla `iom_vouchers`
--
ALTER TABLE `iom_vouchers`
 ADD KEY `person_id` (`person_id`);

--
-- Indices de la tabla `iom_voucher_operations`
--
ALTER TABLE `iom_voucher_operations`
 ADD UNIQUE KEY `voucher_operation_number` (`serieno`,`voucher_operation_number`,`deleted`), ADD KEY `person_id` (`person_id`);

--
-- Filtros para la tabla `iom_adjustnotes`
--
ALTER TABLE `iom_adjustnotes`
ADD CONSTRAINT `iom_adjustnotes_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_adjustnotes_ibfk_2` FOREIGN KEY (`cash_concept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`),
ADD CONSTRAINT `iom_adjustnotes_ibfk_3` FOREIGN KEY (`cash_book_id`) REFERENCES `iom_cash_books` (`cash_book_id`);

--
-- Filtros para la tabla `iom_bankaccounts`
--
ALTER TABLE `iom_bankaccounts`
ADD CONSTRAINT `iom_bankaccounts_ibfk_1` FOREIGN KEY (`bank_id`) REFERENCES `iom_banks` (`bank_id`);

--
-- Filtros para la tabla `iom_cash_books`
--
ALTER TABLE `iom_cash_books`
ADD CONSTRAINT `iom_cash_books_ibfk_1` FOREIGN KEY (`stock_location_id`) REFERENCES `iom_stock_locations` (`location_id`),
ADD CONSTRAINT `iom_cash_books_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `iom_users` (`person_id`);

--
-- Filtros para la tabla `iom_cash_concepts`
--
ALTER TABLE `iom_cash_concepts`
ADD CONSTRAINT `iom_cash_concepts_ibfk_1` FOREIGN KEY (`cash_concept_parent_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`);

--
-- Filtros para la tabla `iom_cash_up`
--
ALTER TABLE `iom_cash_up`
ADD CONSTRAINT `iom_cash_up_ibfk_1` FOREIGN KEY (`open_employee_id`) REFERENCES `iom_users` (`person_id`),
ADD CONSTRAINT `iom_cash_up_ibfk_2` FOREIGN KEY (`close_employee_id`) REFERENCES `iom_users` (`person_id`),
ADD CONSTRAINT `iom_cash_up_ibfk_3` FOREIGN KEY (`cash_book_id`) REFERENCES `iom_cash_books` (`cash_book_id`);

--
-- Filtros para la tabla `iom_costs`
--
ALTER TABLE `iom_costs`
ADD CONSTRAINT `iom_costs_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_costs_ibfk_2` FOREIGN KEY (`bankaccount_id`) REFERENCES `iom_bankaccounts` (`bankaccount_id`),
ADD CONSTRAINT `iom_costs_ibfk_3` FOREIGN KEY (`cash_concept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`),
ADD CONSTRAINT `iom_costs_ibfk_4` FOREIGN KEY (`cash_subconcept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`),
ADD CONSTRAINT `iom_costs_ibfk_5` FOREIGN KEY (`voucher_operation_id`) REFERENCES `iom_voucher_operations` (`voucher_operation_id`);

--
-- Filtros para la tabla `iom_creditnotes`
--
ALTER TABLE `iom_creditnotes`
ADD CONSTRAINT `iom_creditnotes_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_creditnotes_ibfk_2` FOREIGN KEY (`cash_book_id`) REFERENCES `iom_cash_books` (`cash_book_id`);

--
-- Filtros para la tabla `iom_credits`
--
ALTER TABLE `iom_credits`
ADD CONSTRAINT `iom_credits_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_suppliers` (`person_id`);

--
-- Filtros para la tabla `iom_credit_items`
--
ALTER TABLE `iom_credit_items`
ADD CONSTRAINT `iom_credit_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `iom_items` (`item_id`),
ADD CONSTRAINT `iom_credit_items_ibfk_2` FOREIGN KEY (`credit_id`) REFERENCES `iom_credits` (`credit_id`),
ADD CONSTRAINT `iom_credit_items_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `iom_stock_locations` (`location_id`);

--
-- Filtros para la tabla `iom_customers`
--
ALTER TABLE `iom_customers`
ADD CONSTRAINT `iom_customers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_customers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `iom_users` (`person_id`);

--
-- Filtros para la tabla `iom_employees`
--
ALTER TABLE `iom_employees`
ADD CONSTRAINT `iom_employees_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`);

--
-- Filtros para la tabla `iom_expenses`
--
ALTER TABLE `iom_expenses`
ADD CONSTRAINT `iom_expenses_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_expenses_ibfk_2` FOREIGN KEY (`bankaccount_id`) REFERENCES `iom_bankaccounts` (`bankaccount_id`),
ADD CONSTRAINT `iom_expenses_ibfk_3` FOREIGN KEY (`cash_concept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`),
ADD CONSTRAINT `iom_expenses_ibfk_4` FOREIGN KEY (`cash_subconcept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`);

--
-- Filtros para la tabla `iom_grants`
--
ALTER TABLE `iom_grants`
ADD CONSTRAINT `iom_grants_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `iom_permissions` (`permission_id`) ON DELETE CASCADE,
ADD CONSTRAINT `iom_grants_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `iom_users` (`person_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `iom_incomes`
--
ALTER TABLE `iom_incomes`
ADD CONSTRAINT `iom_incomes_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_incomes_ibfk_2` FOREIGN KEY (`bankaccount_id`) REFERENCES `iom_bankaccounts` (`bankaccount_id`),
ADD CONSTRAINT `iom_incomes_ibfk_3` FOREIGN KEY (`cash_concept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`),
ADD CONSTRAINT `iom_incomes_ibfk_4` FOREIGN KEY (`cash_subconcept_id`) REFERENCES `iom_cash_concepts` (`cash_concept_id`),
ADD CONSTRAINT `iom_incomes_ibfk_5` FOREIGN KEY (`voucher_operation_id`) REFERENCES `iom_voucher_operations` (`voucher_operation_id`);

--
-- Filtros para la tabla `iom_invoices`
--
ALTER TABLE `iom_invoices`
ADD CONSTRAINT `iom_invoices_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_invoices_ibfk_2` FOREIGN KEY (`cash_book_id`) REFERENCES `iom_cash_books` (`cash_book_id`);

--
-- Filtros para la tabla `iom_items`
--
ALTER TABLE `iom_items`
ADD CONSTRAINT `iom_items_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `iom_suppliers` (`person_id`),
ADD CONSTRAINT `iom_items_ibfk_2` FOREIGN KEY (`uom_id`) REFERENCES `iom_uom` (`uom_id`);

--
-- Filtros para la tabla `iom_items_taxes`
--
ALTER TABLE `iom_items_taxes`
ADD CONSTRAINT `iom_items_taxes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `iom_items` (`item_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `iom_item_quantities`
--
ALTER TABLE `iom_item_quantities`
ADD CONSTRAINT `iom_item_quantities_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `iom_items` (`item_id`),
ADD CONSTRAINT `iom_item_quantities_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `iom_stock_locations` (`location_id`);

--
-- Filtros para la tabla `iom_lineinvoices`
--
ALTER TABLE `iom_lineinvoices`
ADD CONSTRAINT `iom_lineinvoices_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `iom_invoices` (`invoice_id`);

--
-- Filtros para la tabla `iom_lineticketsales`
--
ALTER TABLE `iom_lineticketsales`
ADD CONSTRAINT `iom_lineticketsales_ibfk_1` FOREIGN KEY (`ticketsale_id`) REFERENCES `iom_ticketsales` (`ticketsale_id`);

--
-- Filtros para la tabla `iom_loans`
--
ALTER TABLE `iom_loans`
ADD CONSTRAINT `iom_loans_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`);

--
-- Filtros para la tabla `iom_payment_credits`
--
ALTER TABLE `iom_payment_credits`
ADD CONSTRAINT `iom_payment_credits_ibfk_1` FOREIGN KEY (`credit_id`) REFERENCES `iom_credits` (`credit_id`);

--
-- Filtros para la tabla `iom_payment_loans`
--
ALTER TABLE `iom_payment_loans`
ADD CONSTRAINT `iom_payment_loans_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `iom_loans` (`loan_id`);

--
-- Filtros para la tabla `iom_payment_vouchers`
--
ALTER TABLE `iom_payment_vouchers`
ADD CONSTRAINT `iom_payment_vouchers_ibfk_1` FOREIGN KEY (`voucher_id`) REFERENCES `iom_vouchers` (`voucher_id`);

--
-- Filtros para la tabla `iom_permissions`
--
ALTER TABLE `iom_permissions`
ADD CONSTRAINT `iom_permissions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `iom_modules` (`module_id`) ON DELETE CASCADE,
ADD CONSTRAINT `iom_permissions_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `iom_stock_locations` (`location_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `iom_quality_certificates`
--
ALTER TABLE `iom_quality_certificates`
ADD CONSTRAINT `iom_quality_certificates_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_suppliers` (`person_id`),
ADD CONSTRAINT `iom_quality_certificates_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `iom_stock_locations` (`location_id`),
ADD CONSTRAINT `iom_quality_certificates_ibfk_3` FOREIGN KEY (`voucher_operation_id`) REFERENCES `iom_voucher_operations` (`voucher_operation_id`);

--
-- Filtros para la tabla `iom_suppliers`
--
ALTER TABLE `iom_suppliers`
ADD CONSTRAINT `iom_suppliers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_suppliers_ibfk_2` FOREIGN KEY (`growing_area_id`) REFERENCES `iom_growing_areas` (`growing_area_id`);

--
-- Filtros para la tabla `iom_ticketsales`
--
ALTER TABLE `iom_ticketsales`
ADD CONSTRAINT `iom_ticketsales_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_ticketsales_ibfk_2` FOREIGN KEY (`cash_book_id`) REFERENCES `iom_cash_books` (`cash_book_id`);

--
-- Filtros para la tabla `iom_uom_conversions`
--
ALTER TABLE `iom_uom_conversions`
ADD CONSTRAINT `iom_uom_conversions_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `iom_items` (`item_id`),
ADD CONSTRAINT `iom_uom_conversions_ibfk_2` FOREIGN KEY (`uom_id`) REFERENCES `iom_uom` (`uom_id`),
ADD CONSTRAINT `iom_uom_conversions_ibfk_3` FOREIGN KEY (`uomto_id`) REFERENCES `iom_uom` (`uom_id`);

--
-- Filtros para la tabla `iom_users`
--
ALTER TABLE `iom_users`
ADD CONSTRAINT `iom_users_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`),
ADD CONSTRAINT `iom_users_ibfk_2` FOREIGN KEY (`stock_location_id`) REFERENCES `iom_stock_locations` (`location_id`);

--
-- Filtros para la tabla `iom_vouchers`
--
ALTER TABLE `iom_vouchers`
ADD CONSTRAINT `iom_vouchers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_people` (`person_id`);

--
-- Filtros para la tabla `iom_voucher_operations`
--
ALTER TABLE `iom_voucher_operations`
ADD CONSTRAINT `iom_voucher_operations_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `iom_suppliers` (`person_id`);