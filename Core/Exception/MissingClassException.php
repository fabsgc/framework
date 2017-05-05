<?php
/*\
 | ------------------------------------------------------
 | @file : MissingClassException.php
 | @author : Fabien Beaujean
 | @description : overriding of php exceptions
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Exception;

/**
 * Class MissingClassException
 * @package Gcs\Framework\Core\Exception
 */

class MissingClassException extends Exception {
    public function getType() {
        return 'MissingClassException';
    }
}