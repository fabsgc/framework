<?php
/*\
 | ------------------------------------------------------
 | @file : Form.php
 | @author : Fabien Beaujean
 | @description : abstract to validate a form
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Request;

use Gcs\Framework\Core\Form\Validation\Validation;

/**
 * Class Form
 * @property string method
 * @property array get
 * @property array post
 * @property array put
 * @property array patch
 * @package Gcs\Framework\Core\Request
 */
abstract class Form {
    /**
     * parameters of each action
     * @var \Gcs\Framework\Core\Request\Data
     */

    public $data = null;

    /**
     * We put errors inside
     * @var \Gcs\Framework\Core\Form\Validation\Validation
     */

    protected $validation = null;

    /**
     * name of the form
     * @var string
     */

    protected $form = '';

    /**
     * the form is already sent and checked
     * @var bool
     */

    protected $_sent = false;

    /**
     * constructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function __construct() {
        $this->data = Data::instance();
        $this->validation = new Validation();
    }

    /**
     * Initialization
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function init() {
    }

    /**
     * Get form name
     * @access public
     * @return string
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function getForm() {
        return $this->form;
    }

    /**
     * We can check the validity of a GET request thanks to this method that you can override
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function get() {
    }

    /**
     * We can check the validity of a POST request thanks to this method that you can override
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function post() {
    }

    /**
     * We can check the validity of a PUT request thanks to this method that you can override
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function put() {
    }

    /**
     * We can check the validity of a PATCH request thanks to this method that you can override
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function patch() {
    }

    /**
     * We can check the validity of a DELETE request thanks to this method that you can override
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function delete() {
    }

    /**
     * Check
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function check() {
        $this->validation->check();
        $this->_sent = true;
    }

    /**
     * Is the request valid ?
     * @access public
     * @return boolean
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function valid() {
        return $this->validation->valid();
    }

    /**
     * Is the form sent ?
     * @access public
     * @return boolean
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function sent() {
        return $this->_sent;
    }

    /**
     * get errors list
     * @access public
     * @return string[]
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function errors() {
        return $this->validation->errors();
    }

    /**
     * destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public function __destruct() {
    }
}