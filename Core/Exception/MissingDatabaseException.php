<?php
/*\
 | ------------------------------------------------------
 | @file : MissingDatabaseException.php
 | @author : Fabien Beaujean
 | @description : overriding of php exceptions
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Exception;

/**
 * Class MissingDatabaseException
 * @package Gcs\Framework\Core\Exception
 */

class MissingDatabaseException extends Exception {
    public function getType() {
        return 'MissingDatabaseException';
    }
}