<?php
/*\
 | ------------------------------------------------------
 | @file : MissingLibraryException.php
 | @author : Fabien Beaujean
 | @description : overriding of php exceptions
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Exception;

/**
 * Class MissingLibraryException
 * @package Gcs\Framework\Core\Exception
 */

class MissingLibraryException extends Exception {
    public function getType() {
        return 'MissingLibraryException';
    }
}