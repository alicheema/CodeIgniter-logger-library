<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
/**
* Hooks configuation 
* Transaction logger hook will be called after execuation of each function
in application
*/
$hook['post_controller_constructor'][] = array(
    'class' => 'TransactionLogger',
    'function' => 'initialize',
    'filename' => 'TransactionLogger.php',
    'filepath' => 'hooks'
);
