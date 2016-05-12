<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_Log extends CI_Log {

    function __construct()
    {
        parent::__construct();

		
    }

    function write_log($level = 'error', $msg, $php_error = FALSE, $additional=false, $additional2=false)

    {   
		
        if ($this->_enabled === FALSE)
        {
        return FALSE;
        }

         $level = strtoupper($level);

        if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
        {
        return FALSE;
        }

        $filepath = $this->_log_path.'log-'.date('Y-m-d').'.php';
        $message  = '';

        if ( ! file_exists($filepath))
        {
        $message .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
        }

        if ( ! $fp = @fopen($filepath, FOPEN_WRITE_CREATE))
        {
        return FALSE;
        }

        $message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, FILE_WRITE_MODE);
		
/* Extends the logging class to send an email when an error is triggered */
if (!class_exists('FirePHP')&&$_SESSION['environment']=="development") {
require_once(APPPATH . 'libraries/firephp.php');
}
ob_start();
$firephp = FirePHP::getInstance(true);
$firephp->error($level, $message);


        return TRUE;
    }

}