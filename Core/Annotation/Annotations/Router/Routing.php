<?php
/*\
 | ------------------------------------------------------
 | @file : Routing.php
 | @author : Fabien Beaujean
 | @description : annotation routing
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Annotation\Annotations\Router;

use Gcs\Framework\Core\Annotation\Annotation;

/**
 * Class Routing
 * @package Gcs\Framework\Core\Annotation\Router
 */
class Routing extends Annotation {

    /**
     * Parameter name
     * @var string
     */

    public $name;

    /**
     * Parameter url
     * @var string
     */

    public $url = '/';

    /**
     * Parameter vars
     * @var string
     */

    public $vars = '';

    /**
     * Parameter url
     * @var string
     */

    public $method = '*';

    /**
     * Parameter access
     * @var string
     */

    public $access = '*';

    /**
     * Parameter cache
     * @var string
     */

    public $cache = '0';

    /**
     * Parameter logged
     * @var string
     */

    public $logged = '*';
}