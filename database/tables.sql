-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_adjustnotes`
--

DROP TABLE IF EXISTS `iom_adjustnotes`;
CREATE TABLE IF NOT EXISTS `iom_adjustnotes` (
  `adjustnote_id` int(10) NOT NULL AUTO_INCREMENT,
  `documentdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `documentno` varchar(50) NOT NULL,
  `person_id` int(10) NOT NULL,
  `cash_concept_id` int(10) NOT NULL,
  `cash_book_id` int(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `movementtype` char(1) NOT NULL DEFAULT 'C',
  `trx_number` varchar(50) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`adjustnote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_app_config`
--

DROP TABLE IF EXISTS `iom_app_config`;
CREATE TABLE IF NOT EXISTS `iom_app_config` (
  `key` varchar(50) NOT NULL,
  `value` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_app_config`
--

INSERT INTO `iom_app_config` (`key`, `value`) VALUES
('address', 'Jr. Santa Rosa # 345'),
('allow_duplicate_barcodes', '0'),
('barcode_content', 'id'),
('barcode_first_row', 'category'),
('barcode_font', 'Arial'),
('barcode_font_size', '10'),
('barcode_formats', '[]'),
('barcode_generate_if_empty', '0'),
('barcode_height', '50'),
('barcode_num_in_row', '2'),
('barcode_page_cellspacing', '20'),
('barcode_page_width', '100'),
('barcode_quality', '100'),
('barcode_second_row', 'item_code'),
('barcode_third_row', 'unit_price'),
('barcode_type', 'Code39'),
('barcode_width', '250'),
('cash_decimals', '2'),
('cash_rounding_code', '1'),
('company', 'Sistema de Gestión de Caja de la Cooperativa COOPAFSI'),
('company_logo', 'company_logo1.jpg'),
('country_codes', 'pe'),
('currency_decimals', '2'),
('currency_symbol', 'S/'),
('custom10_name', ''),
('custom1_name', ''),
('custom2_name', ''),
('custom3_name', ''),
('custom4_name', ''),
('custom5_name', ''),
('custom6_name', ''),
('custom7_name', ''),
('custom8_name', ''),
('custom9_name', ''),
('customer_reward_enable', '0'),
('customer_sales_tax_support', '0'),
('dateformat', 'd/m/Y'),
('date_or_time_format', ''),
('default_origin_tax_code', ''),
('default_register_mode', 'sale'),
('default_sales_discount', '0'),
('default_tax_1_name', 'IGV'),
('default_tax_1_rate', '18'),
('default_tax_2_name', ''),
('default_tax_2_rate', ''),
('default_tax_category', 'Standard'),
('default_tax_rate', '8'),
('derive_sale_quantity', '0'),
('dinner_table_enable', '0'),
('email', 'gerencia@coopafsi.com'),
('enforce_privacy', ''),
('fax', ''),
('financial_year', '1'),
('gcaptcha_enable', '0'),
('gcaptcha_secret_key', ''),
('gcaptcha_site_key', ''),
('giftcard_number', 'series'),
('invoice_default_comments', 'This is a default comment'),
('invoice_email_message', 'Dear {CU}, In attachment the receipt for sale $INV'),
('invoice_enable', '1'),
('language', 'spanish'),
('language_code', 'es'),
('last_used_invoice_number', '0'),
('last_used_quote_number', '0'),
('last_used_work_order_number', '0'),
('lines_per_page', '25'),
('line_sequence', '0'),
('mailpath', '/usr/sbin/sendmail'),
('msg_msg', ''),
('msg_pwd', ''),
('msg_src', ''),
('msg_uid', ''),
('multi_pack_enabled', '0'),
('notify_horizontal_position', 'center'),
('notify_vertical_position', 'bottom'),
('number_locale', 'es-PE'),
('payment_options_order', 'cashdebitcredit'),
('phone', '555-555-5555'),
('print_bottom_margin', '0'),
('print_footer', '0'),
('print_header', '0'),
('print_left_margin', '0'),
('print_right_margin', '0'),
('print_silently', '1'),
('print_top_margin', '0'),
('protocol', 'mail'),
('quantity_decimals', '0'),
('quote_default_comments', 'This is a default quote comment'),
('receipt_font_size', '12'),
('receipt_show_company_name', '1'),
('receipt_show_description', '1'),
('receipt_show_serialnumber', '1'),
('receipt_show_taxes', '0'),
('receipt_show_total_discount', '1'),
('receipt_template', 'receipt_default'),
('receiving_calculate_average_price', '0'),
('recv_invoice_format', '{CO}'),
('return_policy', 'Test'),
('sales_invoice_format', '{CO}'),
('sales_quote_format', 'Q%y{QSEQ:6}'),
('smtp_crypto', 'ssl'),
('smtp_host', ''),
('smtp_pass', ''),
('smtp_port', '465'),
('smtp_timeout', '5'),
('smtp_user', ''),
('suggestions_first_column', 'name'),
('suggestions_second_column', ''),
('suggestions_third_column', ''),
('tax_decimals', '2'),
('tax_included', '0'),
('theme', 'flatly'),
('thousands_separator', 'thousands_separator'),
('timeformat', 'H:i:s'),
('timezone', 'America/Bogota'),
('voucher_next_sequence', '0'),
('website', 'http://www.coopafsi.com'),
('work_order_enable', '0'),
('work_order_format', 'W%y{WSEQ:6}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_bankaccounts`
--

DROP TABLE IF EXISTS `iom_bankaccounts`;
CREATE TABLE IF NOT EXISTS `iom_bankaccounts` (
  `bankaccount_id` int(10) NOT NULL AUTO_INCREMENT,
  `bank_id` int(10) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PEN',
  `account_number` varchar(20) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bankaccount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_banks`
--

DROP TABLE IF EXISTS `iom_banks`;
CREATE TABLE IF NOT EXISTS `iom_banks` (
`bank_id` int(10) NOT NULL AUTO_INCREMENT,
  `ruc` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_cashup_currencys`
--

DROP TABLE IF EXISTS `iom_cashup_currencys`;
CREATE TABLE IF NOT EXISTS `iom_cashup_currencys` (
`cashup_currency_id` int(10) NOT NULL AUTO_INCREMENT,
  `cashup_id` int(10) NOT NULL,
  `currency` char(3) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'PEN',
  `denomination` varchar(100) NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cashup_currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_cash_books`
--

DROP TABLE IF EXISTS `iom_cash_books`;
CREATE TABLE IF NOT EXISTS `iom_cash_books` (
`cash_book_id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(40) NOT NULL,
  `stock_location_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_cash_general` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cash_book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_cash_concepts`
--

DROP TABLE IF EXISTS `iom_cash_concepts`;
CREATE TABLE IF NOT EXISTS `iom_cash_concepts` (
`cash_concept_id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL,
  `concept_type` char(1) NOT NULL DEFAULT '1',
  `document_sequence` varchar(20) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_summary` tinyint(1) NOT NULL DEFAULT '0',
  `is_cash_general_used` tinyint(1) NOT NULL DEFAULT '0',
  `cash_concept_parent_id` int(10) DEFAULT NULL,
  `affected_voucheroperation` int(1) NOT NULL DEFAULT '0',
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cash_concept_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_cash_concepts`
--

INSERT INTO `iom_cash_concepts` (`cash_concept_id`, `code`, `name`, `concept_type`, `document_sequence`, `description`, `is_summary`, `is_cash_general_used`, `cash_concept_parent_id`, `affected_voucheroperation`, `is_internal`, `deleted`) VALUES
(33, '01-00', 'SALDO INICIAL CAJA GENERAL', '1', NULL, 'SALDO DE APERTURA EN LA CAJA GENERAL, USADO SOLO PARA EL PRIMER MOVIMIENTO.', 0, 1, NULL, 0, 1, 0),
(1, '01-01', 'RECIBO DE INGRESOS', '1', NULL, 'AGRUPA A LOS MOVIMIENTOS DE INGRESO CON RECIBO DE INGRESOS.', 1, 0, NULL, 0, 0, 0),
(62, '01-02', 'CAJA PAGADORA', '1', NULL, 'Movimientos de ingreso para la caja pagadora', 1, 0, NULL, 0, 0, 0),
(2, '02-00', 'ASIGNACION DE EFECTIVO DE CAJA GENERAL', '2', NULL, 'AGRUPA TODOS LOS MOVIMIENTOS DE ASIGNACION DE EFECTIVO DESIGNADO POR CAJA GENERAL.', 1, 0, NULL, 0, 0, 0),
(3, '02-01', 'RECIBOS DE EGRESOS', '2', NULL, 'AGRUPA TODOS LOS MOVIMIENTOS DE EGRESOS', 1, 0, NULL, 0, 0, 0),
(4, '03-01', 'ARCH. G. ADMINISTRATIVO', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(5, '03-02', 'ARCH. G. DE VENTAS', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(6, '03-03', 'DEPARTAMENTO TÉCNICO', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(7, '03-04', 'CAFETERÍA', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(8, '03-05', 'ARCH. PREMIO SOCIAL', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(9, '03-06', 'ARCH. COMPOST', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(10, '03-07', 'ARCHH. PRODUCCION', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(11, '03-08', 'ARCH. NUEVA INSTALACION', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(12, '03-09', 'VENTA ABONO', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(13, '03-10', 'GASTOS COMITE DE EDUCACION', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(14, '03-11', 'ARCH. PLTA BENEFICIO GASTOS', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(16, '03-12', 'COMITE DE LA MUJER', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(17, '03-13', 'LOTIZACION HUAMBA', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(18, '03-14', 'ARCH. GASTOS CERTIFICACION', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(19, '03-15', 'ARCH. GASTOS CHICLAYO CAJA SI', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(20, '03-16', 'ARCH. VIVEROS', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(21, '03-17', 'GASTOS PLANTA DE ABONOS', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(22, '03-18', 'PROYECTOS', '3', NULL, '', 1, 0, NULL, 0, 0, 0),
(23, '01-01-01', 'APORTE SOCIAL', '1', NULL, '', 1, 0, 1, 0, 0, 0),
(24, '01-01-02', 'DEVOLUCION PAGO A CUENTA DE CAFE DEPOSITADO', '1', NULL, '', 1, 0, 23, 1, 0, 0),
(25, '01-01-03', 'DEVOLUCION PRESTAMO DE ABONO', '1', NULL, '', 1, 0, 23, 0, 0, 0),
(26, '01-01-04', 'AMORTIZACION DE PRESTAMO PRE COSECHA', '1', NULL, '', 1, 0, 23, 0, 0, 0),
(29, '01-01-05', 'RECIBOS DE INGRESOS CAFETERÍA', '1', NULL, '', 1, 0, 1, 0, 0, 0),
(63, '01-02-01', 'ASIGNACIÓN DE EFECTIVO', '2', '', '', 1, 0, 62, 0, 0, 0),
(27, '02-00-01', 'ENTREGA EFECTIVO CAJA PAGADOR', '2', NULL, '', 1, 1, 2, 0, 0, 0),
(28, '02-01-01', 'A CUENTA DE DEVOLUCION DE APORTE SOCIAL SEGUN ACUERDO DE CONSEJO', '2', NULL, '', 1, 0, 3, 0, 0, 0),
(31, '02-01-02', 'RECIBO EGRESO CAFETERIA', '2', NULL, '', 1, 0, 3, 0, 0, 0),
(32, '02-01-03', 'RECIBO DE EGRESOS VARIOS', '2', '', '', 1, 0, 3, 0, 0, 0),
(35, '02-01-04', 'ANTICIPO DEPOSITO A CAFE', '2', '', '', 1, 0, 3, 1, 0, 0),
(34, '00-01-00', 'SALDO APERTURA CAJA PAGADORA', '1', NULL, 'SALDO DE APERTURA EN LA CAJA PAGADORA POR USUARIO, USADO SOLO PARA EL PRIMER MOVIMIENTO.', 0, 1, NULL, 0, 1, 0),
(46, '00-01-01', 'PAGO PRÉSTAMO', '1', NULL, NULL, 0, 0, 1, 0, 1, 0),
(47, '00-01-02', 'PAGO INTERÉS PRÉSTAMO', '1', NULL, NULL, 0, 0, 1, 0, 1, 0),
(48, '00-01-03', 'PAGO CRÉDITO', '1', NULL, NULL, 0, 0, 1, 0, 1, 0),
(49, '00-01-04', 'PAGO INTERÉS CRÉDITO', '1', NULL, NULL, 0, 0, 1, 0, 1, 0),
(50, '00-01-05', 'BOLETA DE VENTAS', '1', NULL, NULL, 0, 0, NULL, 0, 1, 0),
(51, '00-01-06', 'FACTURAS', '1', NULL, NULL, 0, 0, NULL, 0, 1, 0),
(44, '00-02-01', 'PRESTAMOS', '2', NULL, NULL, 0, 0, 3, 0, 1, 0),
(59, '00-02-02', 'NOTAS DE CRÉDITO', '2', NULL, 'USADO PARA LAS NOTAS DE CRÉDITO', 1, 0, NULL, 0, 1, 0),
(60, '00-02-03', 'COMPROBANTES DE OPERACIÓN SERIE I', '2', NULL, 'USADO PARA LOS COMPROBANTES DE OPERACIÓN SERIE I', 1, 0, NULL, 0, 1, 0),
(61, '00-02-04', 'COMPROBANTES DE OPERACIÓN SERIE II', '2', NULL, 'USADO PARA LOS COMPROBANTES DE OPERACIÓN SERIE II', 1, 0, NULL, 0, 1, 0),
(52, '02-02', 'NOTAS DE AJUSTE', '2', NULL, 'USADO PARA LAS NOTAS DE AJUSTE', 1, 0, NULL, 0, 0, 0),
(57, '02-02-01', 'DISTRIBUCIÓN DE EXCEDENTE COMPAÑÍA CAFETALERA', '2', '', '', 1, 0, 52, 0, 0, 0);


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_cash_daily`
--

DROP TABLE IF EXISTS `iom_cash_daily`;
CREATE TABLE IF NOT EXISTS `iom_cash_daily` (
`cash_daily_id` int(10) NOT NULL AUTO_INCREMENT,
  `cashup_id` int(10) NOT NULL,
  `cash_concept_id` int(10) NOT NULL,
  `cash_book_id` int(10) NOT NULL,
  `operation_type` int(1) NOT NULL DEFAULT '1',
  `movementdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` varchar(255) DEFAULT NULL,
  `isbankmovement` int(1) NOT NULL DEFAULT '0',
  `currency` char(3) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'PEN',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `table_reference` varchar(255) DEFAULT NULL,
  `reference_id` int(10) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cash_daily_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_cash_flow`
--

DROP TABLE IF EXISTS `iom_cash_flow`;
CREATE TABLE IF NOT EXISTS `iom_cash_flow` (
`cash_flow_id` int(10) NOT NULL AUTO_INCREMENT,
  `overall_cash_id` int(10) NOT NULL,
  `cash_concept_id` int(10) NOT NULL,
  `cash_book_id` int(10) NOT NULL,
  `operation_type` int(1) NOT NULL DEFAULT '1',
  `movementdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` varchar(255) DEFAULT NULL,
  `currency` char(3) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'PEN',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `table_reference` varchar(255) DEFAULT NULL,
  `reference_id` int(10) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cash_flow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_cash_up`
--

DROP TABLE IF EXISTS `iom_cash_up`;
CREATE TABLE IF NOT EXISTS `iom_cash_up` (
`cashup_id` int(10) NOT NULL AUTO_INCREMENT,
  `cash_book_id` int(10) NOT NULL,
  `open_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `close_date` timestamp NULL DEFAULT NULL,
  `open_amount_cash` decimal(15,2) NOT NULL,
  `transfer_amount_cash` decimal(15,2) NOT NULL DEFAULT '0.00',
  `note` int(1) NOT NULL DEFAULT '0',
  `closed_amount_cash` decimal(15,2) NOT NULL DEFAULT '0.00',
  `closed_amount_card` decimal(15,2) NOT NULL DEFAULT '0.00',
  `closed_amount_check` decimal(15,2) NOT NULL DEFAULT '0.00',
  `closed_amount_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` varchar(255) NOT NULL,
  `open_employee_id` int(10) NOT NULL,
  `close_employee_id` int(10) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `closed_amount_due` decimal(15,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`cashup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_costs`
--

DROP TABLE IF EXISTS `iom_costs`;
CREATE TABLE IF NOT EXISTS `iom_costs` (
`cost_id` int(10) NOT NULL AUTO_INCREMENT,
  `documentno` varchar(50) NOT NULL,
  `documentdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `person_id` int(10) DEFAULT NULL,
  `cash_concept_id` int(10) NOT NULL,
  `cash_subconcept_id` int(10) NOT NULL,
  `detail` varchar(255) NOT NULL,
  `is_cashupmovement` int(11) NOT NULL DEFAULT '0',
  `movementtype` char(1) NOT NULL DEFAULT 'C',
  `bankaccount_id` int(10) DEFAULT NULL,
  `voucher_operation_id` int(10) DEFAULT NULL,
  `trx_number` varchar(50) DEFAULT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PEN',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cost_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_creditnotes`
--

DROP TABLE IF EXISTS `iom_creditnotes`;
CREATE TABLE IF NOT EXISTS `iom_creditnotes` (
`creditnote_id` int(10) NOT NULL AUTO_INCREMENT,
  `documentdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `documentno` varchar(50) NOT NULL,
  `person_id` int(10) NOT NULL,
  `cash_book_id` int(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `movementtype` char(1) NOT NULL DEFAULT 'C',
  `trx_number` varchar(50) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`creditnote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_credits`
--

DROP TABLE IF EXISTS `iom_credits`;
CREATE TABLE IF NOT EXISTS `iom_credits` (
`credit_id` int(10) NOT NULL AUTO_INCREMENT,
  `person_id` int(10) NOT NULL,
  `creditdate` date NOT NULL,
  `cuote` int(11) NOT NULL DEFAULT '0',
  `returndate` date DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amt_interest` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`credit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_credit_items`
--

DROP TABLE IF EXISTS `iom_credit_items`;
CREATE TABLE IF NOT EXISTS `iom_credit_items` (
`credit_item_id` int(10) NOT NULL AUTO_INCREMENT,
  `credit_id` int(10) NOT NULL,
  `location_id` int(11) NOT NULL,
  `item_id` int(10) NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`credit_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_customers`
--

DROP TABLE IF EXISTS `iom_customers`;
CREATE TABLE IF NOT EXISTS `iom_customers` (
  `person_id` int(10) NOT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `taxable` int(1) NOT NULL DEFAULT '1',
  `sales_tax_code` varchar(32) NOT NULL DEFAULT '1',
  `discount_percent` decimal(15,2) NOT NULL DEFAULT '0.00',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) NOT NULL,
  `consent` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_doctype_sequences`
--

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_employees`
--

DROP TABLE IF EXISTS `iom_employees`;
CREATE TABLE IF NOT EXISTS `iom_employees` (
  `person_id` int(10) NOT NULL,
  `ruc` varchar(20) NOT NULL,
  `admission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `job_title` varchar(255) DEFAULT NULL,
  `contract_type` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_expenses`
--

DROP TABLE IF EXISTS `iom_expenses`;
CREATE TABLE IF NOT EXISTS `iom_expenses` (
`expense_id` int(10) NOT NULL AUTO_INCREMENT,
  `documentno` varchar(50) NOT NULL,
  `documentdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `person_id` int(10) DEFAULT NULL,
  `person_name` varchar(255) DEFAULT NULL,
  `cash_concept_id` int(10) NOT NULL,
  `cash_subconcept_id` int(10) DEFAULT NULL,
  `detail` varchar(255) NOT NULL,
  `is_cashupmovement` int(11) NOT NULL DEFAULT '0',
  `movementtype` char(1) NOT NULL DEFAULT 'C',
  `bankaccount_id` int(10) DEFAULT NULL,
  `trx_number` varchar(50) DEFAULT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PEN',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`expense_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_grants`
--

DROP TABLE IF EXISTS `iom_grants`;
CREATE TABLE IF NOT EXISTS `iom_grants` (
  `permission_id` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  `menu_group` varchar(32) DEFAULT 'home'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_grants`
--

INSERT INTO `iom_grants` (`permission_id`, `person_id`, `menu_group`) VALUES
('adjustnotes', 1, 'pay_cash'),
('adjustnotes', 2, 'pay_cash'),
('cash', 1, 'home'),
('cash', 2, 'home'),
('cashups', 1, 'pay_cash'),
('cashups', 2, 'pay_cash'),
('cash_books', 1, 'cash'),
('cash_books', 2, 'cash'),
('cash_concepts', 1, 'cash'),
('cash_concepts', 2, 'cash'),
('config', 1, 'office'),
('config', 2, 'office'),
('creditnotes', 1, 'pay_cash'),
('creditnotes', 2, 'pay_cash'),
('customers', 1, 'home'),
('customers', 2, 'home'),
('employees', 1, 'home'),
('employees', 2, 'home'),
('growing_areas', 1, 'home'),
('growing_areas', 2, 'home'),
('home', 1, 'both'),
('home', 2, 'both'),
('invoices', 1, 'pay_cash'),
('invoices', 2, 'pay_cash'),
('items', 1, 'home'),
('items', 2, 'home'),
('items_Jaén', 1, '--'),
('items_Jaén', 2, '--'),
('items_San Ignacio', 1, '--'),
('items_San Ignacio', 2, '--'),
('loans_credits', 1, 'pay_cash'),
('loans_credits', 2, 'pay_cash'),
('messages', 1, 'office'),
('messages', 2, 'office'),
('office', 1, 'home'),
('office', 2, 'home'),
('overall_cashs', 1, 'home'),
('overall_cashs', 2, 'home'),
('pay_cash', 1, 'home'),
('pay_cash', 2, 'home'),
('reports', 1, 'home'),
('reports', 2, 'home'),
('reports_customers', 1, '--'),
('reports_inventory', 1, '--'),
('reports_suppliers', 1, '--'),
('reports_users', 1, '--'),
('suppliers', 1, 'home'),
('suppliers', 2, 'home'),
('ticketsales', 1, 'pay_cash'),
('ticketsales', 2, 'pay_cash'),
('uoms', 1, 'home'),
('uoms', 2, 'home'),
('users', 1, 'office'),
('users', 2, 'office'),
('vouchers', 1, 'pay_cash'),
('vouchers', 2, 'pay_cash'),
('voucher_operations', 1, 'pay_cash'),
('voucher_operations', 2, 'pay_cash');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_growing_areas`
--

DROP TABLE IF EXISTS `iom_growing_areas`;
CREATE TABLE IF NOT EXISTS `iom_growing_areas` (
`growing_area_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `district` varchar(150) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`growing_area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_growing_areas`
--

INSERT INTO `iom_growing_areas` (`growing_area_id`, `name`, `district`, `state`, `country`, `deleted`) VALUES
(1, 'CALABAZO', 'SAN IGNACIO', '', '', 0),
(2, 'CESARA', 'NAMBALLE', '', '', 0),
(3, 'EL ACERILLO', 'NAMBALLE', '', '', 0),
(4, 'EL SANTUARIO', 'SAN IGNACIO', '', '', 0),
(5, 'FRONTERA SAN FRANCISCO', 'SAN IGNACIO', '', '', 0),
(6, 'GALLITO DE LAS ROCAS', 'SAN IGNACIO', '', '', 0),
(7, 'IHUAMACA', 'SAN IGNACIO', '', '', 0),
(8, 'MANANTIAL DEL SANTUARIO', 'SAN IGNACIO', '', '', 0),
(9, 'NUEVA ESPERANZA', 'SAN IGNACIO', '', '', 0),
(10, 'PUERTO SAN IGNACIO', 'SAN IGNACIO', '', '', 0),
(11, 'SABIA DEL CAFE', 'SAN IGNACIO', '', '', 0),
(12, 'SAN ANTONIO', 'NAMBALLE', '', '', 0),
(13, 'SAN ANTONIO DE PAJON', 'NAMBALLE', '', '', 0),
(14, 'SAN IGNACIO', 'SAN IGNACIO', '', '', 0),
(15, 'SAN IGNACIO DE LOYOLA', 'SAN IGNACIO', '', '', 0),
(16, 'SAN JOSE', 'SAN IGNACIO', '', '', 0),
(17, 'SAN PEDRO', 'SAN IGNACIO', '', '', 0),
(18, 'SANTA ROSA', 'SAN IGNACIO', '', '', 0),
(19, 'YANDILUZA', 'SAN IGNACIO', '', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_incomes`
--

DROP TABLE IF EXISTS `iom_incomes`;
CREATE TABLE IF NOT EXISTS `iom_incomes` (
`income_id` int(10) NOT NULL AUTO_INCREMENT,
  `documentno` varchar(50) NOT NULL,
  `documentdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `person_id` int(10) DEFAULT NULL,
  `person_name` varchar(255) DEFAULT NULL,
  `bankaccount_id` int(10) DEFAULT NULL,
  `cash_concept_id` int(10) NOT NULL,
  `cash_subconcept_id` int(10) NOT NULL,
  `voucher_operation_id` int(10) DEFAULT NULL,
  `detail` varchar(255) NOT NULL,
  `is_cashupmovement` int(11) NOT NULL DEFAULT '0',
  `movementtype` char(1) NOT NULL DEFAULT 'C',
  `trx_number` varchar(50) DEFAULT NULL,
  `currency` char(3) NOT NULL DEFAULT 'PEN',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`income_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_inventory`
--

DROP TABLE IF EXISTS `iom_inventory`;
CREATE TABLE IF NOT EXISTS `iom_inventory` (
`trans_id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_items` int(11) NOT NULL DEFAULT '0',
  `trans_user` int(11) NOT NULL DEFAULT '0',
  `trans_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `trans_comment` text NOT NULL,
  `trans_location` int(11) NOT NULL,
  `trans_inventory` decimal(15,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_invoices`
--

DROP TABLE IF EXISTS `iom_invoices`;
CREATE TABLE IF NOT EXISTS `iom_invoices` (
`invoice_id` int(10) NOT NULL AUTO_INCREMENT,
  `documentdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `serieno` varchar(50) NOT NULL,
  `person_id` int(10) NOT NULL,
  `cash_book_id` int(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discountamt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `taxamt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `totalamt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `movementtype` char(1) NOT NULL DEFAULT 'C',
  `trx_number` varchar(50) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_items`
--

DROP TABLE IF EXISTS `iom_items`;
CREATE TABLE IF NOT EXISTS `iom_items` (
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `mark` varchar(255) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `uom_id` int(11) DEFAULT NULL,
  `item_number` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `reorder_level` decimal(15,3) NOT NULL DEFAULT '0.000',
  `receiving_quantity` decimal(15,3) NOT NULL DEFAULT '1.000',
  `item_id` int(10) NOT NULL AUTO_INCREMENT,
  `pic_filename` varchar(255) DEFAULT NULL,
  `allow_alt_description` tinyint(1) NOT NULL,
  `is_serialized` tinyint(1) NOT NULL,
  `is_bulk` tinyint(1) NOT NULL DEFAULT '0',
  `stock_type` tinyint(2) NOT NULL DEFAULT '0',
  `item_type` tinyint(2) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `qty_per_pack` decimal(15,3) NOT NULL DEFAULT '1.000',
  `pack_name` varchar(8) DEFAULT 'Each',
  `low_sell_item_id` int(10) DEFAULT '0',
  `hsn_code` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_items_taxes`
--

DROP TABLE IF EXISTS `iom_items_taxes`;
CREATE TABLE IF NOT EXISTS `iom_items_taxes` (
  `item_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_item_quantities`
--

DROP TABLE IF EXISTS `iom_item_quantities`;
CREATE TABLE IF NOT EXISTS `iom_item_quantities` (
  `item_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `quantity` decimal(15,3) NOT NULL DEFAULT '0.000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_lineinvoices`
--

DROP TABLE IF EXISTS `iom_lineinvoices`;
CREATE TABLE IF NOT EXISTS `iom_lineinvoices` (
`lineinvoice_id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `detail` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lineinvoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_lineticketsales`
--

DROP TABLE IF EXISTS `iom_lineticketsales`;
CREATE TABLE IF NOT EXISTS `iom_lineticketsales` (
`lineticketsale_id` int(10) NOT NULL AUTO_INCREMENT,
  `ticketsale_id` int(10) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `detail` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lineticketsale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_loans`
--

DROP TABLE IF EXISTS `iom_loans`;
CREATE TABLE IF NOT EXISTS `iom_loans` (
`loan_id` int(10) NOT NULL AUTO_INCREMENT,
  `loan_type` char(1) NOT NULL DEFAULT 'P',
  `person_id` int(10) NOT NULL,
  `loandate` date NOT NULL,
  `cuote` int(11) NOT NULL DEFAULT '0',
  `returndate` date DEFAULT NULL,
  `motive` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `percent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amt_interest` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_modules`
--

DROP TABLE IF EXISTS `iom_modules`;
CREATE TABLE IF NOT EXISTS `iom_modules` (
  `name_lang_key` varchar(255) NOT NULL,
  `desc_lang_key` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `module_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_modules`
--

INSERT INTO `iom_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_home', 'module_home_desc', 1, 'home'),
('module_customers', 'module_customers_desc', 10, 'customers'),
('module_items', 'module_items_desc', 20, 'items'),
('module_uoms', 'module_uoms_desc', 30, 'uoms'),
('module_suppliers', 'module_suppliers_desc', 40, 'suppliers'),
('module_growing_areas', 'module_growing_areas_desc', 45, 'growing_areas'),
('module_employees', 'module_employees_desc', 50, 'employees'),
('module_cash', 'module_cash_desc', 101, 'cash'),
('module_cash_concepts', 'module_cash_concepts_desc', 110, 'cash_concepts'),
('module_cash_books', 'module_cash_books_desc', 120, 'cash_books'),
('module_overall_cashs', 'module_overall_cashs_desc', 201, 'overall_cashs'),
('module_pay_cash', 'module_pay_cash_desc', 301, 'pay_cash'),
('module_cashups', 'module_cashups_desc', 310, 'cashups'),
('module_loans_credits', 'module_loans_credits_des', 320, 'loans_credits'),
('module_vouchers', 'module_vouchers_desc', 330, 'vouchers'),
('module_voucher_operations', 'module_voucher_operations_desc', 340, 'voucher_operations'),
('module_ticketsales', 'module_ticketsales_desc', 350, 'ticketsales'),
('module_invoices', 'module_invoices_desc', 360, 'invoices'),
('module_adjustnotes', 'module_adjustnotes_desc', 370, 'adjustnotes'),
('module_creditnotes', 'module_creditnotes_desc', 380, 'creditnotes'),
('module_reports', 'module_reports_desc', 410, 'reports'),
('module_users', 'module_users_desc', 501, 'users'),
('module_messages', 'module_messages_desc', 510, 'messages'),
('module_doctypesequences', 'module_doctypesequences_desc', 520, 'doctypesequences'),
('module_config', 'module_config_desc', 530, 'config'),
('module_office', 'module_office_desc', 999, 'office');


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_overallcash_currencys`
--

DROP TABLE IF EXISTS `iom_overallcash_currencys`;
CREATE TABLE IF NOT EXISTS `iom_overallcash_currencys` (
`overallcash_currency_id` int(10) NOT NULL AUTO_INCREMENT,
  `overall_cash_id` int(10) NOT NULL,
  `currency` char(3) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL DEFAULT 'PEN',
  `denomination` varchar(100) NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`overallcash_currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_overall_cashs`
--

DROP TABLE IF EXISTS `iom_overall_cashs`;
CREATE TABLE IF NOT EXISTS `iom_overall_cashs` (
`overall_cash_id` int(10) NOT NULL AUTO_INCREMENT,
  `opendate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `startbalance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `openingbalance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `closedate` timestamp NULL DEFAULT NULL,
  `endingbalance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `usdstartbalance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `usdopeningbalance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `usdendingbalance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `state` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`overall_cash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_payment_credits`
--

DROP TABLE IF EXISTS `iom_payment_credits`;
CREATE TABLE IF NOT EXISTS `iom_payment_credits` (
`payment_credit_id` int(10) NOT NULL AUTO_INCREMENT,
  `credit_id` int(10) NOT NULL,
  `paydate` date NOT NULL,
  `paytype` int(11) NOT NULL DEFAULT '0',
  `observations` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `capital` decimal(10,2) NOT NULL DEFAULT '0.00',
  `interest` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cumulate_interest` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_credit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_payment_loans`
--

DROP TABLE IF EXISTS `iom_payment_loans`;
CREATE TABLE IF NOT EXISTS `iom_payment_loans` (
`payment_loan_id` int(10) NOT NULL AUTO_INCREMENT,
  `loan_id` int(10) NOT NULL,
  `paydate` date NOT NULL,
  `paytype` int(11) NOT NULL DEFAULT '0',
  `observations` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `capital` decimal(10,2) NOT NULL DEFAULT '0.00',
  `interest` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cumulate_interest` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_payment_vouchers`
--

DROP TABLE IF EXISTS `iom_payment_vouchers`;
CREATE TABLE IF NOT EXISTS `iom_payment_vouchers` (
`payment_voucher_id` int(10) NOT NULL AUTO_INCREMENT,
  `voucher_id` int(10) NOT NULL,
  `paydate` date NOT NULL,
  `observations` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_voucher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_people`
--

DROP TABLE IF EXISTS `iom_people`;
CREATE TABLE IF NOT EXISTS `iom_people` (
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dni` varchar(50) NOT NULL,
  `gender` int(1) DEFAULT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `person_id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_people`
--

INSERT INTO `iom_people` (`first_name`, `last_name`, `dni`, `gender`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `person_id`) VALUES
('Jorge', 'Colmenarez', '20389586', 1, '+584149739547', 'jcolmenarez@frontuari.net', 'Urb. Llano Lindo II Casa H-08', '', 'Araure', 'Portuguesa', '3303', 'Venezuela', '', 1),
('Cesar', 'Jimenez', '40729918', 1, '921465974', 'kpinteractivo@gmail.com', 'Chiclayo', 'Chiclayo', 'Chiclayo', '', '', 'Perú', '', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_permissions`
--

DROP TABLE IF EXISTS `iom_permissions`;
CREATE TABLE IF NOT EXISTS `iom_permissions` (
  `permission_id` varchar(255) NOT NULL,
  `module_id` varchar(255) NOT NULL,
  `location_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_permissions`
--

INSERT INTO `iom_permissions` (`permission_id`, `module_id`, `location_id`) VALUES
('adjustnotes', 'adjustnotes', NULL),
('cash', 'cash', NULL),
('cashups', 'cashups', NULL),
('cash_books', 'cash_books', NULL),
('cash_concepts', 'cash_concepts', NULL),
('config', 'config', NULL),
('creditnotes', 'creditnotes', NULL),
('customers', 'customers', NULL),
('doctypesequences', 'doctypesequences', NULL),
('employees', 'employees', NULL),
('growing_areas', 'growing_areas', NULL),
('home', 'home', NULL),
('invoices', 'invoices', NULL),
('items', 'items', NULL),
('items_San Ignacio', 'items', 1),
('items_Jaén', 'items', 2),
('loans_credits', 'loans_credits', NULL),
('messages', 'messages', NULL),
('office', 'office', NULL),
('overall_cashs', 'overall_cashs', NULL),
('pay_cash', 'pay_cash', NULL),
('reports', 'reports', NULL),
('reports_customers', 'reports', NULL),
('reports_inventory', 'reports', NULL),
('reports_suppliers', 'reports', NULL),
('reports_users', 'reports', NULL),
('suppliers', 'suppliers', NULL),
('ticketsales', 'ticketsales', NULL),
('uoms', 'uoms', NULL),
('users', 'users', NULL),
('vouchers', 'vouchers', NULL),
('voucher_operations', 'voucher_operations', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_quality_certificates`
--

DROP TABLE IF EXISTS `iom_quality_certificates`;
CREATE TABLE IF NOT EXISTS `iom_quality_certificates` (
`quality_certificate_id` int(10) NOT NULL AUTO_INCREMENT,
  `depositdate` date NOT NULL,
  `serieno` char(2) NOT NULL DEFAULT '02',
  `certificate_number` varchar(20) DEFAULT NULL,
  `person_id` int(10) NOT NULL,
  `kg_dry` decimal(10,2) NOT NULL DEFAULT '0.00',
  `qq_dry` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rate_profile` decimal(10,2) NOT NULL DEFAULT '0.00',
  `physical_performance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quality` varchar(255) DEFAULT NULL,
  `location_id` int(10) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `voucher_operation_id` int(10) DEFAULT NULL,
  `imported` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `reference_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`quality_certificate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_sessions`
--

DROP TABLE IF EXISTS `iom_sessions`;
CREATE TABLE IF NOT EXISTS `iom_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_stock_locations`
--

DROP TABLE IF EXISTS `iom_stock_locations`;
CREATE TABLE IF NOT EXISTS `iom_stock_locations` (
`location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_code` varchar(10) DEFAULT NULL,
  `location_name` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_stock_locations`
--

INSERT INTO `iom_stock_locations` (`location_id`, `location_code`, `location_name`, `deleted`) VALUES
(1, '01', 'San Ignacio', 0),
(2, '02', 'Jaén', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_suppliers`
--

DROP TABLE IF EXISTS `iom_suppliers`;
CREATE TABLE IF NOT EXISTS `iom_suppliers` (
  `person_id` int(10) NOT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `company_name` varchar(255) NOT NULL,
  `agency_name` varchar(255) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `ispartner` int(1) NOT NULL DEFAULT '0',
  `growing_area_id` int(10) DEFAULT NULL,
  `association_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_ticketsales`
--

DROP TABLE IF EXISTS `iom_ticketsales`;
CREATE TABLE IF NOT EXISTS `iom_ticketsales` (
`ticketsale_id` int(10) NOT NULL AUTO_INCREMENT,
  `documentdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `serieno` varchar(50) NOT NULL,
  `documentno` varchar(50) NOT NULL,
  `person_id` int(10) DEFAULT NULL,
  `person_name` varchar(255) DEFAULT NULL,
  `cash_book_id` int(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discountamt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `taxamt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `totalamt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `movementtype` char(1) NOT NULL DEFAULT 'C',
  `trx_number` varchar(50) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticketsale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_uom`
--

DROP TABLE IF EXISTS `iom_uom`;
CREATE TABLE IF NOT EXISTS `iom_uom` (
`uom_id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `magnitude` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uom_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_uom`
--

INSERT INTO `iom_uom` (`uom_id`, `symbol`, `name`, `magnitude`, `deleted`) VALUES
(1, 'kg', 'Kilogramo', 'Masa', 0),
(2, 'm', 'Metro', 'Longitud', 0),
(3, 's', 'Segundo', 'Tiempo', 0),
(4, 'u', 'Unidad', 'Masa', 0),
(5, 's', 'Sacos', 'Masa', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_uom_conversions`
--

DROP TABLE IF EXISTS `iom_uom_conversions`;
CREATE TABLE IF NOT EXISTS `iom_uom_conversions` (
`uom_conversion_id` int(10) NOT NULL AUTO_INCREMENT,
  `item_id` int(10) NOT NULL,
  `uom_id` int(10) NOT NULL,
  `uomto_id` int(10) NOT NULL,
  `multiplierfactor` decimal(10,2) NOT NULL,
  `dividingfactor` decimal(10,2) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uom_conversion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_users`
--

DROP TABLE IF EXISTS `iom_users`;
CREATE TABLE IF NOT EXISTS `iom_users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `hash_version` int(1) NOT NULL DEFAULT '2',
  `stock_location_id` int(10) DEFAULT NULL,
  `language` varchar(48) DEFAULT NULL,
  `language_code` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `iom_users`
--

INSERT INTO `iom_users` (`username`, `password`, `person_id`, `deleted`, `hash_version`, `stock_location_id`, `language`, `language_code`) VALUES
('admin', '$2y$10$jzHVhtZGZjgOyFfz9P70g.fDmXXlAk5Gnkcz6WNgKe3.ONqVNSVfW', 1, 0, 2, 1, '', ''),
('cmjimenez', '$2y$10$64UVE7hY9taZNOT01P0AG.Tvpl1BWMrAHihO/AYnw8jOLWEks7NqK', 2, 0, 2, 1, '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_vouchers`
--

DROP TABLE IF EXISTS `iom_vouchers`;
CREATE TABLE IF NOT EXISTS `iom_vouchers` (
`voucher_id` int(10) NOT NULL AUTO_INCREMENT,
  `voucher_type` char(1) NOT NULL DEFAULT 'P',
  `voucherdate` date NOT NULL,
  `voucher_number` varchar(20) DEFAULT NULL,
  `person_id` int(10) NOT NULL,
  `cash_book_id` int(10) NOT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cash_type` char(1) NOT NULL DEFAULT 'C',
  `trx_number` char(20) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`voucher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iom_voucher_operations`
--

DROP TABLE IF EXISTS `iom_voucher_operations`;
CREATE TABLE IF NOT EXISTS `iom_voucher_operations` (
`voucher_operation_id` int(10) NOT NULL AUTO_INCREMENT,
  `voucherdate` date NOT NULL,
  `serieno` char(2) NOT NULL DEFAULT '02',
  `voucher_operation_number` varchar(20) DEFAULT NULL,
  `person_id` int(10) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `liquidatedate` date DEFAULT NULL,
  `cash_book_id` int(10) DEFAULT NULL,
  `printed` tinyint(1) NOT NULL DEFAULT '0',
  `state` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`voucher_operation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;