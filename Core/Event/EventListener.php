<?php
	/*\
	 | ------------------------------------------------------
	 | @file : EventListener.php
	 | @author : Fabien Beaujean
	 | @description : EventListener interface
	 | @version : 3.0
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Event;

	/**
	 * EventListener interface 
	 * @package Gcs\Framework\Core\Event
	 */

	interface EventListener {
		public function implementedEvents();
	}