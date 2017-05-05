<?php
	/*\
	 | ------------------------------------------------------
	 | @file : TerminalHelp.php
	 | @author : Fabien Beaujean
	 | @description : terminal command help
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Terminal;

	/**
	 * Class TerminalHelp
	 * @package Gcs\Framework\Core\Terminal
	 */

	class TerminalHelp extends TerminalCommand {
		public function help() {
			echo " - create module\n";
			echo " - create controller\n";
			echo " - create entity\n";
			echo " - delete module\n";
			echo " - delete controller\n";
			echo " - clear cache\n";
			echo " - clear log";
		}
	}