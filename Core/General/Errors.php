<?php
/*\
 | ------------------------------------------------------
 | @file : Errors.php
 | @author : Fabien Beaujean
 | @description : Errors trait
 | @version : 3.0
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\General;

use Gcs\Framework\Core\Config\Config;

/**
 * Errors trait
 * @package Gcs\Framework\Core\General
 */
trait Errors {

    /**
     * add an error in the log
     * @access public
     * @param $error string : error
     * @param $file  string : file with error
     * @param $line  int : line with error
     * @param $type  string : type of error
     * @param $log   string : log file
     * @return void
     * @since  3.0
     */

    public function addError($error, $file = __FILE__, $line = __LINE__, $type = ERROR_INFORMATION, $log = LOG_SYSTEM) {
        if ($log != LOG_HISTORY && $log != LOG_CRONS && $log != LOG_EVENT) {
            if (Config::config()['user']['debug']['log']) {
                if ($log == LOG_SQL) {
                    $error = preg_replace('#([\t]{2,})#isU', "", $error);
                }

                $data = date("d/m/Y H:i:s : ", time()) . '[' . $type . '] file ' . $file . ' / line ' . $line . ' / ' . $error;
                file_put_contents(APP_LOG_PATH . $log . '.log', $data . "\n", FILE_APPEND | LOCK_EX);

                if ((Config::config()['user']['debug']['error']['error'] && $type == ERROR_FATAL) || (Config::config()['user']['debug']['error']['exception'] == true && $type == ERROR_EXCEPTION) || preg_match('#Exception#isU', $type) || (Config::config()['user']['debug']['error']['fatal'] == true && $type == ERROR_ERROR)) {
                    if (CONSOLE_ENABLED == MODE_HTTP) {
                        echo $data . "\n<br />";
                    }
                    else {
                        echo $data . "\n";
                    }
                }
            }
        }
        else {
            file_put_contents(APP_LOG_PATH . $log . '.log', $error . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * add an hr line in the log
     * @access public
     * @param $log string : log file
     * @return void
     * @since  3.0
     */

    public function addErrorHr($log = LOG_SYSTEM) {
        if (Config::config()['user']['debug']['log']) {
            file_put_contents(APP_LOG_PATH . $log . '.log', "#################### END OF EXECUTION OF http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . " ####################\n", FILE_APPEND | LOCK_EX);
        }
    }
}