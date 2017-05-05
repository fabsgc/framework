<?php
/*\
 | ------------------------------------------------------
 | @file : FacadeEntity.php
 | @author : Fabien Beaujean
 | @description : easier way to instantiate entities
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Facade;

use Gcs\Framework\Core\Exception\MissingEntityException;

/**
 * Class FacadeEntity
 * @package Gcs\Framework\Core\Facade
 */
class FacadeEntity {
    /**
     * Constructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Facade
     */

    final public function __construct() {
    }

    /**
     * instantiate the good Entity
     * @access public
     * @param $name      string
     * @param $arguments array
     * @throws \Gcs\Framework\Core\Exception\MissingEntityException
     * @return \Gcs\Framework\Core\Orm\Entity\Entity
     * @since 3.0
     * @package Gcs\Framework\Core\Facade
     */

    public function __call($name, $arguments) {
        if (file_exists(APP_RESOURCE_ENTITY_PATH . $name . '.php')) {
            include_once(APP_RESOURCE_ENTITY_PATH . $name . '.php');

            $class = '\Orm\Entity\\' . $name;

            $params = [];

            foreach ($arguments as $value) {
                array_push($params, $value);
            }

            $reflect = new \ReflectionClass($class);

            /** @var \Gcs\Framework\Core\Orm\Entity\Entity $instance */
            $instance = $reflect->newInstanceArgs($params);

            return $instance;
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

            throw new MissingEntityException('undefined Entity "' . $name . '" in "' . $file . '" line ' . $line);
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