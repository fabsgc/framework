<?php
/*\
 | ------------------------------------------------------
 | @file : ArgvInput.php
 | @author : Fabien Beaujean
 | @description : arguments processor
 | @version : 3.0 bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Terminal;

/**
 * Class ArgvInput
 */
class ArgvInput {
    public static function get() {
        $data = fgets(STDIN);
        $data = substr($data, 0, -2);

        return $data;
    }
}