CREATE TABLE `affiliate_earning_history` (
  `id` int(11) NOT NULL,
  `affiliate_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event` enum('signup','payment','recurring') NOT NULL,
  `amount` float NOT NULL,
  `event_date` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_forgot_password`
--

CREATE TABLE `affiliate_forgot_password` (
  `id` int(11) NOT NULL,
  `confirmation_code` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `success` int(11) NOT NULL DEFAULT 0,
  `expiration` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_payment_settings`
--

CREATE TABLE `affiliate_payment_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `signup_commission` enum('0','1') NOT NULL DEFAULT '0',
  `payment_commission` enum('0','1') NOT NULL DEFAULT '0',
  `payment_type` varchar(50) NOT NULL,
  `sign_up_amount` varchar(255) NOT NULL,
  `percentage` varchar(255) NOT NULL,
  `fixed_amount` varchar(255) NOT NULL,
  `is_recurring` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `affiliate_payment_settings`
--

INSERT INTO `affiliate_payment_settings` (`id`, `user_id`, `signup_commission`, `payment_commission`, `payment_type`, `sign_up_amount`, `percentage`, `fixed_amount`, `is_recurring`) VALUES
(1, 0, '1', '1', 'fixed', '.0', '', '0', '0');

