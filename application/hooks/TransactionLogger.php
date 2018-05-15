<?php

/**
 * @package TransactionLogger
 * @author Ali Cheema <ali.nawaz@pitb.gov.pk>
 * @version V1.0
 * This is hook to save transaction logs
 * It enables you to save your required transaction logs 
 */
class TransactionLogger {

    private $ci;

    /**
     * @method initialize
     * Set ci instance 
     * loader library
     * call save transaction function     
     */
    function initialize() {
        $this->ci = & get_instance();
        $this->ci->load->library('logger');
        $this->save_transaction_log();
    }

    /**
     * @method save_transaction_log
     * You just need to set followings
     * 1. Transaction data if any
     * 2. User details as per your requirements
     * 3. custom_message if any     
     *  
     */
    function save_transaction_log() {
        $transaction_details = array(
            'transaction_data' => json_encode($_REQUEST),
            'user_details' => json_encode($this->ci->session->userdata()),
            'custom_message' => '',
        );
        $this->ci->logger->log_transaction($transaction_details);
    }

}
