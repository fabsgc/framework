<?php
/*\
 | ------------------------------------------------------
 | @file : Singleton.php
 | @author : Fabien Beaujean
 | @description : Trait singleton
 | @version : 3.0
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\General;

trait Singleton {
    /**
     * Singleton instance
     * @var object
     */

    public static $_instance = null;
}