<?php
/*\
 | ------------------------------------------------------
 | @file : TerminalClear.php
 | @author : Fabien Beaujean
 | @description : terminal command clear
 | @version : 3.0 bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Terminal;

/**
 * Class TerminalClear
 * @package Gcs\Framework\Core\Terminal
 */

class TerminalClear extends TerminalCommand {

    /**
     * return void
     * @access public
     */

    public function log() {
        Terminal::rrmdir(APP_LOG_PATH, false, ['.gitignore']);
        echo ' - log files were successfully deleted';
    }

    /**
     * return void
     * @access public
     */

    public function cache() {
        Terminal::rrmdir(APP_CACHE_PATH, false, ['.gitignore']);
        echo ' - cache files were successfully deleted';
    }
}