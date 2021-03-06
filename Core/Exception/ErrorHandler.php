<?php
/*\
 | ------------------------------------------------------
 | @file : ErrorHandler.php
 | @author : Fabien Beaujean
 | @description : overriding of php errors
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Exception;

use Gcs\Framework\Core\General\Errors;

/**
 * Class ErrorHandler
 * @package Gcs\Framework\Core\Exception
 */
class ErrorHandler {
    use Errors;

    /**
     * constructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Exception
     */

    public function __construct() {
        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
    }

    /**
     * capture error
     * @access public
     * @param $errno   integer
     * @param $errstr  string
     * @param $errfile string
     * @param $errline integer
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Exception
     */

    public function errorHandler($errno, $errstr, $errfile, $errline) {
        $error = sprintf("[%d] (%s)", $errno, $errstr);

        switch ($errno) {
            case E_USER_NOTICE:
                $this->addError($error, $errfile, $errline, ERROR_ERROR, LOG_ERROR);
                break;

            case E_USER_WARNING:
                $this->addError($error, $errfile, $errline, ERROR_ERROR, LOG_ERROR);
                break;

            case E_WARNING:
                $this->addError($error, $errfile, $errline, ERROR_ERROR, LOG_ERROR);
                break;

            case E_USER_ERROR:
                $this->addError($error, $errfile, $errline, ERROR_ERROR, LOG_SYSTEM);
                break;

            default:
                $this->addError($error, $errfile, $errline, ERROR_ERROR, LOG_ERROR);
                break;
        }
    }

    /**
     * capture exception
     * @access public
     * @param $e \Exception
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Exception
     */

    public function exceptionHandler($e) {
        if (method_exists($e, 'getType')) {
            $this->addError($e->getMessage(), $e->getFile(), $e->getLine(), $e->getType());
        }
        else {
            $this->addError($e->getMessage(), $e->getFile(), $e->getLine(), gettype($e));
        }
    }

    /**
     * destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Exception
     */

    public function __destruct() {
    }
}