<?php
/*\
 | ------------------------------------------------------
 | @file : Before.php
 | @author : Fabien Beaujean
 | @description : annotation before
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Annotation\Annotations\Common;

use Gcs\Framework\Core\Annotation\Annotation;

/**
 * Class Before
 * @package Gcs\Framework\Core\Annotation\Annotations\Common
 */
class Before extends Annotation {

    /**
     * Parameter class
     * @var string
     */

    public $class;

    /**
     * Parameter class
     * @var string
     */

    public $method;
}