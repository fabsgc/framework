<?php
/*\
 | ------------------------------------------------------
 | @file : Injector.php
 | @author : Fabien Beaujean
 | @description : inject objects in a controller
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Controller\Injector;

use Gcs\Framework\Core\Facade\Facades;
use Gcs\Framework\Core\General\Singleton;

/**
 * Class Injector
 * @package Gcs\Framework\Core\Controller\Injector
 */
class Injector {
    use Facades, Singleton;

    /**
     * all class you can inject
     * @var array
     */

    private $_alias = [
        'Gcs\Framework\Core\Sql\Sql'                    => 'Sql',
        'Gcs\Framework\Core\Cron\Cron'                  => 'Cron',
        'Gcs\Framework\Core\Lang\Lang'                  => 'Lang',
        'Gcs\Framework\Core\Asset\Asset'                => 'Asset',
        'Gcs\Framework\Core\Cache\Cache'                => 'Cache',
        'Gcs\Framework\Core\Request\Data'               => 'RequestData',
        'Gcs\Framework\Core\Config\Config'              => 'Config',
        'Gcs\Framework\Core\Security\Spam'              => 'Spam',
        'Gcs\Framework\Core\Session\Session'            => 'Session',
        'Gcs\Framework\Core\Template\Parser'            => 'TemplateParser',
        'Gcs\Framework\Core\Library\Library'            => 'Library',
        'Gcs\Framework\Core\Request\Request'            => 'Request',
        'Gcs\Framework\Core\Response\Response'          => 'Response',
        'Gcs\Framework\Core\Database\Database'          => 'Database',
        'Gcs\Framework\Core\Profiler\Profiler'          => 'Profiler',
        'Gcs\Framework\Core\Security\Firewall'          => 'Firewall',
        'Gcs\Framework\Core\Template\Template'          => 'Template',
        'Gcs\Framework\Core\Terminal\Terminal'          => 'Terminal',
        'Gcs\Framework\Core\Orm\Entity\Multiple'        => 'EntityMultiple',
        'Gcs\Framework\Core\Facade\FacadeHelper'        => 'Helper',
        'Gcs\Framework\Core\Facade\FacadeEntity'        => 'Entity',
        'Gcs\Framework\Core\Collection\Collection'      => 'Collection',
        'Gcs\Framework\Core\Form\Validation\Validation' => 'FormValidation',
    ];

    /**
     * Constructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Controller\Injector
     */

    private function __construct() {
    }

    /**
     * singleton
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Controller\Injector
     */

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Injector();
        }

        return self::$_instance;
    }

    /**
     * Initialization of the application
     * @access public
     * @param &$class object
     * @param $method string
     * @return array mixed
     * @since 3.0
     * @package Gcs\Framework\Core\Controller\Injector
     */

    public function getArgsMethod(&$class, $method) {
        $params = [];

        $method = new \ReflectionMethod($class, $method);
        $parameters = $method->getParameters();

        foreach ($parameters as $parameter) {
            $object = $parameter->getClass();

            if ($object != null) {
                if (array_key_exists($object->name, $this->_alias)) {
                    $name = $this->_alias[$object->name];
                    array_push($params, self::$name());
                }
                else if (preg_match('#(Controller\\\Request\\\)#isU', $object->name)) {
                    array_push($params, Form::instance()->get($object));
                }
                else if (preg_match('#(Orm\\\Entity\\\)#isU', $object->name)) {
                    array_push($params, Orm::instance()->get($object));
                }
                else {
                    array_push($params, null);
                }
            }
            else {
                if (isset($_GET[$parameter->getName()])) {
                    array_push($params, $_GET[$parameter->getName()]);
                }
                else if (isset($_POST[$parameter->getName()])) {
                    array_push($params, $_POST[$parameter->getName()]);
                }
                else {
                    if (!$parameter->isOptional()) {
                        array_push($params, null);
                    }
                }
            }
        }

        return $params;
    }

    /**
     * destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Controller\Injector
     */

    public function __desctuct() {
    }
}