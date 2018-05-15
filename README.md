# Logger Library

This is CodeIgniter based logger library which helps you to save custom transaction logs of user.
This package also allows you to use hooks to save logs. if you use hooks then forgot to call log function in each method of each controller.

## Getting Started

These instructions will get you a copy of the package at your machine and do required configurations to use this logger library

### Installing

Download the package from GIT user git clone https://github.com/alicheema/CodeIgniter-logger-library.git Or Go to https://github.com/alicheema/CodeIgniter-logger-library and directory download to your machine

You will find following files and folders
application
	1. config
		a. hooks.php
		b. logger.php
	2. hooks
		a. TransacationLogger.php
	3. libraries
		a. Logger.php
	4. models
		a.logger
			a. Logger_model.php

[NOTE: To use the hooks you need to enable hooks on your default config file (config->config.php)
	Set $config['enable_hooks'] = TRUE;
]
If you want to go with default configuration just place files in relevant folders and you are ready to go.

For custom configruations Read config->logger.php 

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

If you don't want to use hooks and want to call library in each or required controller and functions you are free to do so in that case don't enable hooks and you can call library in a function like

//Load Library 
$this->load->library('logger');

//Call function to save logs
$transaction_details = array(
        'transaction_data' => 'You transaction data in json format',
        'user_details' => your user detail in json format,
        'custom_message' => 'your custom message',
    );
$this->logger->log_transaction($transaction_details);

## Versioning

This is first version (V1.0) of logger library. I will keep updating to make it more flexiable

## Authors

* **Ali Nawaz Cheema** - *Initial work* - [Ali Cheema](https://github.com/alicheema/)
