<?php
/*\
 | ------------------------------------------------------
 | @file : MissingMethodException.php
 | @author : Fabien Beaujean
 | @description : overriding of php exceptions
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Exception;

/**
 * Class MissingMethodException
 * @package Gcs\Framework\Core\Exception
 */

class MissingMethodException extends Exception {
    public function getType() {
        return 'MissingMethodException';
    }
}