<?php
/*\
 | ------------------------------------------------------
 | @file : Con.php
 | @author : Fabien Beaujean
 | @description : annotation cron
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Annotation\Annotations\Cron;

use Gcs\Framework\Core\Annotation\Annotation;

/**
 * Class Cron
 * @package Gcs\Framework\Core\Annotation\Annotations
 */
class Cron extends Annotation {

    /**
     * Parameter time
     * @var string
     */

    public $time;
}