<?php
/*\
 | ------------------------------------------------------
 | @file : Orm.php
 | @author : Fabien Beaujean
 | @description : inject and valid Request object
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Controller\Injector;

use Gcs\Framework\Core\General\Singleton;
use Gcs\Framework\Core\Http\Request\Request;

/**
 * Class Orm
 * @package Gcs\Framework\Core\Controller\Injector
 */
class Orm {
    use Singleton;

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
            self::$_instance = new Orm();
        }

        return self::$_instance;
    }

    /**
     * Return a fully completed Request Object
     * @access public
     * @param \ReflectionClass $object
     * @return \Gcs\Framework\Core\Orm\Entity\Entity
     * @since 3.0
     * @package Gcs\Framework\Core\Controller\Injector
     */

    public static function get($object) {
        $class = $object->name;

        /** @var \Gcs\Framework\Core\Orm\Entity\Entity $class */
        $class = new $class();

        $request = Request::instance();

        if (($class->getForm() == '' && $request->data->form == true) || isset($request->data->post[$class->getForm()])) {
            switch ($request->data->method) {
                case 'get' :
                    $class->hydrate(strtolower($class->name()) . '_');
                    $class->beforeInsert();
                    break;

                case 'post' :
                    $class->hydrate(strtolower($class->name()) . '_');
                    $class->beforeInsert();
                    break;

                case 'put' :
                    $class->hydrate(strtolower($class->name()) . '_');
                    $class->beforeUpdate();
                    break;

                case 'patch' :
                    $class->hydrate(strtolower($class->name()) . '_');
                    $class->beforePatch();
                    break;

                case 'delete' :
                    $class->hydrate(strtolower($class->name()) . '_');
                    $class->beforeDelete();
                    break;
            }

            $class->check();
        }

        return $class;
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