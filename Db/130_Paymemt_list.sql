CREATE TABLE `payment_config` (
  `id` int(11) NOT NULL,
  `paypal_email` varchar(250) NOT NULL,
  `paypal_payment_type` enum('manual','recurring') NOT NULL DEFAULT 'manual',
  `paypal_mode` enum('live','sandbox') NOT NULL DEFAULT 'live',
  `stripe_secret_key` varchar(150) NOT NULL,
  `stripe_publishable_key` varchar(150) NOT NULL,
  `razorpay_key_id` varchar(255) NOT NULL,
  `razorpay_key_secret` varchar(255) NOT NULL,
  `paystack_secret_key` varchar(255) NOT NULL,
  `paystack_public_key` varchar(255) NOT NULL,
  `mercadopago_public_key` text NOT NULL,
  `mercadopago_access_token` text NOT NULL,
  `marcadopago_country` varchar(20) NOT NULL,
  `mollie_api_key` varchar(255) NOT NULL,
  `currency` varchar(50) NOT NULL DEFAULT 'USD',
  `manual_payment` enum('no','yes') NOT NULL DEFAULT 'no',
  `manual_payment_instruction` mediumtext DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `sslcommerz_store_id` text NOT NULL,
  `sslcommerz_store_password` text NOT NULL,
  `sslcommers_mode` varchar(30) NOT NULL,
  `senangpay_merchent_id` text NOT NULL,
  `senangpay_secret_key` text NOT NULL,
  `senangpay_mode` varchar(30) NOT NULL,
  `instamojo_api_key` text NOT NULL,
  `instamojo_auth_token` text NOT NULL,
  `instamojo_mode` varchar(30) NOT NULL,
  `toyyibpay_secret_key` varchar(255) NOT NULL,
  `toyyibpay_category_code` varchar(255) NOT NULL,
  `toyyibpay_mode` varchar(50) NOT NULL,
  `xendit_secret_api_key` text NOT NULL,
  `myfatoorah_api_key` text NOT NULL,
  `myfatoorah_mode` varchar(30) NOT NULL,
  `paymaya_public_key` text NOT NULL,
  `paymaya_secret_key` varchar(256) NOT NULL,
  `paymaya_mode` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_config`
--

INSERT INTO `payment_config` (`id`, `paypal_email`, `paypal_payment_type`, `paypal_mode`, `stripe_secret_key`, `stripe_publishable_key`, `razorpay_key_id`, `razorpay_key_secret`, `paystack_secret_key`, `paystack_public_key`, `mercadopago_public_key`, `mercadopago_access_token`, `marcadopago_country`, `mollie_api_key`, `currency`, `manual_payment`, `manual_payment_instruction`, `deleted`, `sslcommerz_store_id`, `sslcommerz_store_password`, `sslcommers_mode`, `senangpay_merchent_id`, `senangpay_secret_key`, `senangpay_mode`, `instamojo_api_key`, `instamojo_auth_token`, `instamojo_mode`, `toyyibpay_secret_key`, `toyyibpay_category_code`, `toyyibpay_mode`, `xendit_secret_api_key`, `myfatoorah_api_key`, `myfatoorah_mode`, `paymaya_public_key`, `paymaya_secret_key`, `paymaya_mode`) VALUES
(1, 'rashad.m.tantawy@gmail.com', 'manual', 'live', '', '', '', '', '', '', '', '', 'ar', '', 'USD', 'no', '', '0', '', '', 'live', '', '', 'live', '', '', 'live', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `paypal_error_log`
--

CREATE TABLE `paypal_error_log` (
  `id` int(11) NOT NULL,
  `call_time` datetime DEFAULT NULL,
  `ipn_value` text DEFAULT NULL,
  `error_log` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `paypal_error_log`
--

INSERT INTO `paypal_error_log` (`id`, `call_time`, `ipn_value`, `error_log`) VALUES
(1, '2021-03-20 22:11:47', '{\"verify_status\":\"VERIFIED\",\"data\":{\"mc_gross\":\"1.00\",\"protection_eligibility\":\"Eligible\",\"address_status\":\"confirmed\",\"payer_id\":\"S5UKC5LTPC294\",\"address_street\":\"none\",\"payment_date\":\"13:11:15 Mar 20, 2021 PDT\",\"payment_status\":\"Completed\",\"charset\":\"windows-1252\",\"address_zip\":\"02554\",\"first_name\":\"RASHAD\",\"mc_fee\":\"0.34\",\"address_country_code\":\"US\",\"address_name\":\"Orman company\",\"notify_version\":\"3.9\",\"subscr_id\":\"I-LAJNDYAURUDW\",\"custom\":\"10_4\",\"payer_status\":\"unverified\",\"business\":\"rashad.m.tantawy@gmail.com\",\"address_country\":\"\",\"address_city\":\"egypt\",\"verify_sign\":\"AX2VzBOm4Yo4OS3XBSiBgU5UWUBIAp8I-RXs6L.f24wsCsnz-NV2Y4-M\",\"payer_email\":\"bebodeath@gmail.com\",\"txn_id\":\"4LF42907B74394348\",\"payment_type\":\"instant\",\"payer_business_name\":\"Orman company\",\"last_name\":\"Tantawy\",\"address_state\":\"AK\",\"receiver_email\":\"rashad.m.tantawy@gmail.com\",\"payment_fee\":\"0.34\",\"receiver_id\":\"EWMGE3URQ5LGC\",\"txn_type\":\"subscr_payment\",\"item_name\":\"Gaxa : Lite (30 days)\",\"mc_currency\":\"USD\",\"residence_country\":\"US\",\"transaction_subject\":\"Gaxa : Lite (30 days)\",\"payment_gross\":\"1.00\",\"ipn_track_id\":\"90c0fb6bc731d\"},\"api_key\":\"\"}', '0 : Operation timed out after 5000 milliseconds with 0 bytes received'),
(2, '2021-03-20 22:11:52', '{\"verify_status\":\"VERIFIED\",\"data\":{\"txn_type\":\"subscr_signup\",\"subscr_id\":\"I-LAJNDYAURUDW\",\"last_name\":\"Tantawy\",\"residence_country\":\"US\",\"mc_currency\":\"USD\",\"item_name\":\"Gaxa : Lite (30 days)\",\"business\":\"rashad.m.tantawy@gmail.com\",\"amount3\":\"1.00\",\"recurring\":\"1\",\"address_street\":\"none\",\"verify_sign\":\"ADxrz8JwFvJhQAFqLnlyszlYpIT4A8MBh8dNffQ7IFcvYCSjFK9sAj1d\",\"payer_status\":\"unverified\",\"payer_email\":\"bebodeath@gmail.com\",\"address_status\":\"confirmed\",\"first_name\":\"RASHAD\",\"receiver_email\":\"rashad.m.tantawy@gmail.com\",\"address_country_code\":\"US\",\"payer_id\":\"S5UKC5LTPC294\",\"address_city\":\"egypt\",\"reattempt\":\"1\",\"payer_business_name\":\"Orman company\",\"address_state\":\"AK\",\"subscr_date\":\"13:07:12 Mar 20, 2021 PDT\",\"address_zip\":\"02554\",\"custom\":\"10_4\",\"charset\":\"windows-1252\",\"notify_version\":\"3.9\",\"period3\":\"1 M\",\"address_country\":\"United States\",\"mc_amount3\":\"1.00\",\"address_name\":\"Orman company\",\"ipn_track_id\":\"90c0fb6bc731d\"},\"api_key\":\"\"}', '0 : Operation timed out after 5001 milliseconds with 0 bytes received'),
(3, '2021-04-20 14:07:51', '{\"verify_status\":\"VERIFIED\",\"data\":{\"txn_type\":\"subscr_failed\",\"subscr_id\":\"I-LAJNDYAURUDW\",\"last_name\":\"Tantawy\",\"residence_country\":\"US\",\"item_name\":\"Gaxa : Lite (30 days)\",\"payment_gross\":\"1.00\",\"mc_currency\":\"USD\",\"business\":\"rashad.m.tantawy@gmail.com\",\"verify_sign\":\"AM1L2k3mmn0Q.8vB1vgVdQb9-TPkA.mNQ92Byz2zvYaWa-HYNyC4NtIi\",\"payer_status\":\"unverified\",\"payer_email\":\"bebodeath@gmail.com\",\"first_name\":\"RASHAD\",\"receiver_email\":\"rashad.m.tantawy@gmail.com\",\"payer_id\":\"S5UKC5LTPC294\",\"retry_at\":\"03:00:00 Apr 25, 2021 PDT\",\"payer_business_name\":\"Orman company\",\"mc_gross\":\"1.00\",\"custom\":\"10_4\",\"charset\":\"windows-1252\",\"notify_version\":\"3.9\",\"ipn_track_id\":\"90c0fb6bc731d\"},\"api_key\":\"\"}', '200 : '),
(4, '2021-04-25 15:01:11', '{\"verify_status\":\"VERIFIED\",\"data\":{\"txn_type\":\"subscr_failed\",\"subscr_id\":\"I-LAJNDYAURUDW\",\"last_name\":\"Tantawy\",\"residence_country\":\"US\",\"item_name\":\"Gaxa : Lite (30 days)\",\"payment_gross\":\"1.00\",\"mc_currency\":\"USD\",\"business\":\"rashad.m.tantawy@gmail.com\",\"verify_sign\":\"AH2I0SIcClN7bJrDdlU39-FaOGHlAlQmlFcOLuFNMEKcuXhn8KuyEg5G\",\"payer_status\":\"unverified\",\"payer_email\":\"bebodeath@gmail.com\",\"first_name\":\"RASHAD\",\"receiver_email\":\"rashad.m.tantawy@gmail.com\",\"payer_id\":\"S5UKC5LTPC294\",\"retry_at\":\"03:00:00 Apr 30, 2021 PDT\",\"payer_business_name\":\"Orman company\",\"mc_gross\":\"1.00\",\"custom\":\"10_4\",\"charset\":\"windows-1252\",\"notify_version\":\"3.9\",\"ipn_track_id\":\"fba7003df9adb\"},\"api_key\":\"\"}', '200 : '),
(5, '2021-04-30 13:47:48', '{\"verify_status\":\"VERIFIED\",\"data\":{\"payment_cycle\":\"Monthly\",\"txn_type\":\"recurring_payment_suspended_due_to_max_failed_payment\",\"last_name\":\"Tantawy\",\"next_payment_date\":\"N\\/A\",\"residence_country\":\"US\",\"initial_payment_amount\":\"0.00\",\"currency_code\":\"USD\",\"time_created\":\"13:07:12 Mar 20, 2021 PDT\",\"verify_sign\":\"AgeFVB3HLVg6U0l5a5QVoq0mk0xQAhDf7-2rN5.OipIg6vLdB7rhjCDW\",\"period_type\":\" Regular\",\"payer_status\":\"unverified\",\"tax\":\"0.00\",\"payer_email\":\"bebodeath@gmail.com\",\"first_name\":\"RASHAD\",\"receiver_email\":\"rashad.m.tantawy@gmail.com\",\"payer_id\":\"S5UKC5LTPC294\",\"product_type\":\"1\",\"payer_business_name\":\"Orman company\",\"shipping\":\"0.00\",\"amount_per_cycle\":\"1.00\",\"profile_status\":\"Suspended\",\"custom\":\"10_4\",\"charset\":\"windows-1252\",\"notify_version\":\"3.9\",\"amount\":\"1.00\",\"outstanding_balance\":\"1.00\",\"recurring_payment_id\":\"I-LAJNDYAURUDW\",\"product_name\":\"Gaxa : Lite (30 days)\",\"ipn_track_id\":\"d05279d86e48c\"},\"api_key\":\"\"}', '200 : ');