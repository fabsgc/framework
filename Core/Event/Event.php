<?php
/*\
 | ------------------------------------------------------
 | @file : Event.php
 | @author : Fabien Beaujean
 | @description : implementation of the pattern design observer
 | @version : 2.4 bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Event;

/**
 * Class Event
 * @package Gcs\Framework\Core\Event
 */

class Event {
    const START = true;
    const STOP  = false;

    /**
     * reference to the object which start the event
     * @var $parent object
     */

    public $parent = null;

    /**
     * data you can pass from the controller to the event
     * @var $data array
     */

    public $data = null;

    /**
     * name of the event which is started
     * @var $_name string
     */

    protected $_name;

    /**
     * status of the event : stopped or running
     * @var $_status bool
     */

    protected $_status = true;

    /**
     * data returned by the event after each call
     * @var $_result array
     */

    protected $_result = [];

    /**
     * Constructor
     * @access public
     * @param $name string : name of the event
     * @param $data array : data given
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function __construct($name = 'event', $data = null) {
        $this->_name = $name;
        $this->data = $data;
    }

    /**
     * Override method
     * @access public
     * @return array
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function implementedEvents() {
        return [];
    }

    /**
     * Return the name
     * @access public
     * @return string
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function getName() {
        return $this->_name;
    }

    /**
     * Set the name
     * @access public
     * @param $name string
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function setName($name = 'event') {
        $this->_name = $name;
    }

    /**
     * Return the status : true if it's running and false otherwise
     * @access public
     * @return boolean
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function getStatus() {
        return $this->_status;
    }

    /**
     * Set the status
     * @access public
     * @param $status
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function setStatus($status = self::START) {
        $this->_status = $status;
    }

    /**
     * Return data returned by the event after his call
     * @access public
     * @return array
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function getResult() {
        return $this->_result;
    }

    /**
     * Set the result returned by the event (function used by the framework at the end of the call)
     * @access public
     * @param $result   string : result returned by the listener
     * @param $index    int : order of the event (0,1,2,3)
     * @param $class    string : name of the called class
     * @param $function string : name of the method called
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function setResult($result = '', $index, $class, $function) {
        $this->_result[$index] = ['class' => $class, 'function' => $function, 'return' => $result];
    }

    /**
     * Set the name
     * @access public
     * @param $parent \Gcs\Framework\Core\Controller\Controller
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * Destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Event
     */

    public function __destruct() {
    }
}