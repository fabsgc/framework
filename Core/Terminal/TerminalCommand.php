<?php
/*\
 | ------------------------------------------------------
 | @file : TerminalCommand.php
 | @author : Fabien Beaujean
 | @description : terminal command
 | @version : 3.0 bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Terminal;

/**
 * Class TerminalCommand
 * @package Gcs\Framework\Core\Terminal
 */
class TerminalCommand {
    /**
     * command content
     * @var
     */

    protected $_argv;

    /**
     * init terminal command
     * @access public
     * @param $argv
     * @since 3.0
     * @package Gcs\Framework\Core\Terminal
     */

    public function __construct($argv) {
        $this->_argv = $argv;
    }
}