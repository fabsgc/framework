<?php
/*\
 | ------------------------------------------------------
 | @file : MissingTemplateException.php
 | @author : Fabien Beaujean
 | @description : overriding of php exceptions
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Exception;

/**
 * Class MissingTemplateException
 * @package Gcs\Framework\Core\Exception
 */

class MissingTemplateException extends Exception {
    public function getType() {
        return 'MissingTemplateException';
    }
}