<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @package Logger
 * @author Ali Cheema <ali.nawaz@pitb.gov.pk>
 * @version V1.0
 * This library allows you to save transaction logs of user
 * It also facilitates to save custom keys related to transaction as per your requirements
 */
require_once 'Mobile_Detect.php';

class Logger {

    private $_store_in;
    private $ci;

    public function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->config('logger', TRUE);
        $this->_store_in = $this->ci->config->item('store_in', 'logger');
        $this->_verify_settings();
    }

    /**
     * @method log_transaction
     * @param $transaction_details , $user_id = 0
     * @return Boolean (True/False)
     * Save transaction logs to database
     */
    public function log_transaction($transaction_details, $user_id = 0) {
        $session_user_id = $this->ci->config->item('session_user_id', 'logger');
        if (($user_id == 0) && !empty($session_user_id)) {
            $user_id = isset($_SESSION[$session_user_id]) ? $_SESSION[$session_user_id] : '0';
        }

        if ($this->_set_transaction($transaction_details, $user_id)) {
            return TRUE;
        } else {
            log_message('error', 'There is some issue in logger library...');
        }
        return FALSE;
    }

    /**
     * @method get_log
     * @param $user_id, $date, $order_by, $limit
     * @return Transaction logs
     * Get log details by passing $user_id and $date
     */
    public function get_log($user_id = NULL, $date = NULL, $order_by = NULL, $limit = NULL) {
        return $this->_get_transaction($user_id, $date, $order_by, $limit);
    }

    /**
     * @method _set_transaction
     * @param $transaction_details, $user_id
     * @return Boolean (TRUE/FALSE)
     * Save transaction logs
     * $transaction_details is an array which can contain any number of 
      custom keys which you want to store
     * By Default it will set additional_info no need to pass additional_information
     */
    private function _set_transaction($transaction_details, $user_id) {
        $detect = new Mobile_Detect();
        if ($detect->isMobile()) {
            $accessing_medium = 'mobile';
            $user_agent = $this->get_user_agent($detect->getUserAgent());
        } else if ($detect->isTablet()) {
            $accessing_medium = 'tablet';
            $user_agent = $this->get_user_agent($detect->getUserAgent());
        } else {
            $accessing_medium = 'desktop';
            $user_agent = $this->get_user_agent($_SERVER['HTTP_USER_AGENT']);
        }

        $additional_data = array(
            'url' => base_url() . implode('/', $this->ci->uri->segment_array()),
            'class_name' => $this->ci->router->fetch_class(),
            'action' => $this->ci->router->fetch_method(),
            'user_id' => $user_id,
            'ip_address' => $this->get_client_ip_address(),
            'user_agent_details' => $user_agent['userAgent'],
            'user_agent_name' => $user_agent['name'],
            'user_agent_version' => $user_agent['version'],
            'platform' => $user_agent['platform'],
            'accessing_medium' => $accessing_medium,
            'is_bot' => ($this->is_bot()) ? 1 : 0
        );
        $transaction = array_merge($transaction_details, $additional_data);
        if ($this->_store_in == 'database') {
            if ($this->ci->logger_model->set_transaction($transaction)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * @method _get_transaction
     * @param $user_id, $date, $order_by, $limit
     * @return Return Transaction details
     *
     */
    private function _get_transaction($user_id = NULL, $date = NULL, $order_by = NULL, $limit = NULL) {
        if ($this->_store_in == 'database') {
            $where = array();
            if (isset($user_id))
                $where['user_id'] = $user_id;

            if (isset($date)) {
                $input_date = str_replace('/', '-', $date);
                $formated_date = dbDateFormat($input_date);
                echo $formated_date . '<hr>';
                $where['transaction_time >='] = $formated_date . ' 00:00:00';
                $where['transaction_time <='] = $formated_date . ' 23:59:59';
            }
            if (!isset($order_by))
                $order_by = 'transaction_time DESC';
            return $this->ci->logger_model->get_transaction($where, $order_by, $limit);
        }
    }

    /**
     * @method _verify_settings
     * @param 
     * Check library configurations and load logger model
     *
     */
    private function _verify_settings() {
        if ($this->_store_in == 'database') {
            $this->ci->load->model('logger/logger_model');
        } else {
            log_message('error', 'please set store_in in logger configuration file');
        }
    }

    /**
     * @method get_client_ip_address
     * @return client's IP address
     *
     */
    function get_client_ip_address() {
        //Just get the headers if we can or else use the SERVER global
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }
        //Get the forwarded IP if it exists
        if (array_key_exists('X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $the_ip = $headers['X-Forwarded-For'];
        } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
        ) {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        } else {

            $the_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }
        return $the_ip;
    }

    /**
     * @method get_user_agent
     * @return details of user agent
     *
     */
    function get_user_agent($user_agent) {
        $u_agent = $user_agent;
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";
        $ub = "";
        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    /**
     * @method is_bot
     * @return true if requesting client is bot
     *
     */
    function is_bot() {
        $botlist = array("Teoma", "alexa", "froogle", "Gigabot", "inktomi",
            "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
            "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
            "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
            "msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
            "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
            "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler", "TweetmemeBot",
            "Butterfly", "Twitturls", "Me.dium", "Twiceler", "AhrefsBot", "bingbot", "DotBot", "DeuSu");
        foreach ($botlist as $bot) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
                return true;
            }
        }
    }

}
