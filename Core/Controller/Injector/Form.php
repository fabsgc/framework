<?php
/*\
 | ------------------------------------------------------
 | @file : Form.php
 | @author : Fabien Beaujean
 | @description : inject and valid Request object
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Controller\Injector;

use Gcs\Framework\Core\General\Singleton;
use Gcs\Framework\Core\Request\Request;

/**
 * Class Form
 * @package Gcs\Framework\Core\Controller\Injector
 */
class Form {
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
     * @package Gcs\Framework\Core\Request\Injector
     */

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Form();
        }

        return self::$_instance;
    }

    /**
     * Return a fully completed Request Object
     * @access public
     * @param \ReflectionClass $object
     * @return \Gcs\Framework\Core\Request\Form
     * @since 3.0
     * @package Gcs\Framework\Core\Controller\Injector
     */

    public static function get($object) {
        /** @var \Gcs\Framework\Core\Request\Form $class */
        $class = $object->name;
        $class = new $class();
        $class->init();

        $request = Request::instance();

        if (($class->getForm() == '' && $request->data->form == true) || isset($request->data->post[$class->getForm()])) {
            switch ($request->data->method) {
                case 'get' :
                    $class->get();
                    break;

                case 'post' :
                    $class->post();
                    break;

                case 'put' :
                    $class->put();
                    break;

                case 'patch' :
                    $class->patch();
                    break;

                case 'delete' :
                    $class->delete();
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