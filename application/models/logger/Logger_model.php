<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Logger_model
 * @author Ali Cheema <ali.nawaz@pitb.gov.pk>
 * @version V1.0
 *
 */
class Logger_model extends CI_Model {

    private $_logger_table;

    public function __construct() {
        parent::__construct();
        $this->load->config('logger', TRUE);
        $this->_logger_table = $this->config->item('table_name', 'logger');
        if (empty($this->_logger_table))
            $this->_logger_table = 'transaction_logs';
        $this->_verify_table();
    }

    /**
     * @method _verify_table     
     * Check if specified table exists in database
     */
    private function _verify_table() {
        if (!$this->db->table_exists($this->_logger_table)) {
            log_message('error', 'Specified table "' . $this->_logger_table . '" does not exists');
        }
    }

    /**
     * @method set_transaction     
     * @param $insert_data (array of data)
     * Save passed data to database
     */
    public function set_transaction($insert_data) {
        if ($this->db->insert($this->_logger_table, $insert_data)) {
            return TRUE;
        } else {
            log_message('error', 'unable to save logs');
        }
        return FALSE;
    }

    /**
     * @method get_messages     
     * @param $where, $order_by, $limit
     * Get transaction details
     */
    public function get_transaction($where = NULL, $order_by = NULL, $limit = NULL) {
        if (isset($where) && !empty($where))
            $this->db->where($where);
        if (isset($order_by))
            $this->db->order_by($order_by);
        if (isset($limit)) {
            if (is_array($limit)) {
                $this->db->limit($limit[0], $limit[1]);
            } else {
                $this->db->limit($limit);
            }
        }
        return $this->db->get($this->_logger_table)->result();
    }

}
