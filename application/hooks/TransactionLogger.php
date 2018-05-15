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
     * Set your custom keys to save details transactions 
     * By default this will get following info
     * 1. Application current URL
     * 2. Class name
     * 3. Method Name
     * 4. Transaction data if any
     * 5. current session details
     * if you want to add/remove any additional info you are free to do that   
     */
    function save_transaction_log() {
        $transaction_details = array(
            'url_visited' => base_url() . implode('/', $this->ci->uri->segment_array()),
            'class' => $this->ci->router->fetch_class(),
            'action' => $this->ci->router->fetch_method(),
            'transaction_data' => json_encode($_REQUEST),
            'user_details' => $this->ci->session->userdata()
        );
        $this->ci->logger->log_transaction($transaction_details);
    }

}
