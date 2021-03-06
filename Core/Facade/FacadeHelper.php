<?php
/*\
 | ------------------------------------------------------
 | @file : FacadeHelper.php
 | @author : Fabien Beaujean
 | @description : helper facades : permit to manipulate easily helpers class
 | @version : 3.0 bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Facade;

use Gcs\Framework\Core\Exception\MissingHelperException;

/**
 * Class FacadeHelper
 * @package Gcs\Framework\Core\Facade
 */
class FacadeHelper {
    /**
     * list of the class aliases and the real classes behind
     * @var array
     */

    private $_alias = ['Pagination' => '\Helper\Pagination\Pagination', 'Mail' => '\Helper\Mail\Mail', 'Alert' => '\Helper\Alert\Alert'];

    /**
     * Constructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Facade
     */

    final public function __construct() {
    }

    /**
     * instantiate the good helper
     * @access public
     * @param $name      string : helper class name
     * @param $arguments array : helper class arguments
     * @return object
     * @throws \Gcs\Framework\Core\Exception\MissingHelperException when the helper doesn't exist
     * @since 3.0
     * @package Gcs\Framework\Core\Facade
     */

    public function __call($name, $arguments) {
        if (array_key_exists($name, $this->_alias)) {
            $params = [];

            for ($i = 0; $i < count($arguments); $i++) {
                $params[$i + 5] = $arguments[$i];
            }

            $reflect = new \ReflectionClass($this->_alias[$name]);

            return $reflect->newInstanceArgs($params);
        }
        else {
            $file = '';
            $line = '';
            $stack = debug_backtrace(0);
            $trace = $this->getStackTraceFacade($stack);

            foreach ($trace as $value) {
                if ($value['function'] == $name) {
                    $file = $value['file'];
                    $line = $value['line'];
                    break;
                }
            }

            throw new MissingHelperException('undefined helper "' . $name . '" in "' . $file . '" line ' . $line);
        }
    }

    /**
     * @param $string
     * @return mixed
     * @since 3.0
     * @package Gcs\Framework\Core\Facade
     */

    public function getStackTraceFacade($string) {
        return $string;
    }

    /**
     * Destructor
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Facade
     */

    public function __destruct() {
    }
}