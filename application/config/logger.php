<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package logger configuration
 * @author Ali Cheema
 *
 * "store_in" allows you to set your storage preferences currently in Current Version 1.0 we are handling only database storage.
 * "session_user_id" allows you to set session key in which you are stroing user id
 * "table_name" allows you to set table name in which you want to store transaction logs
 * By default library storing logs in "transaction_logs" table if you want to save logs in same table you 
 can leave blank "table_name" 

 * This is sql for "transaction_logs" table
 *
 *
DROP TABLE IF EXISTS `transaction_logs`;

CREATE TABLE `transaction_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `custom_message` varchar(500) NOT NULL,
  `url` varchar(250) DEFAULT NULL,
  `class_name` varchar(250) NOT NULL,
  `action` varchar(250) NOT NULL,
  `transaction_data` text NOT NULL,
  `user_details` text NOT NULL,
  `ip_address` varchar(250) NOT NULL,
  `user_agent_details` varchar(500) NOT NULL,
  `user_agent_name` varchar(250) NOT NULL,
  `user_agent_version` varchar(250) NOT NULL,
  `platform` varchar(250) NOT NULL,
  `is_bot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=Yes, 0=No',
  `transaction_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

 *
 *
 */
$config['store_in'] = 'database';
$config['session_user_id'] = 'userID'; // You can tell the library to take the user id from a session variable
$config['table_name'] = ''; // If you prefer to name the table other than "transaction_logs" you can set it here...