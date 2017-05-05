<?php
/*\
 | ------------------------------------------------------
 | @file : EventListenerInterface.php
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

interface EventListenerInterface {
    public function implementedEvents();
}