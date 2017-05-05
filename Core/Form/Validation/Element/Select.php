<?php
/*\
 | ------------------------------------------------------
 | @file : Select.php
 | @author : Fabien Beaujean
 | @description : select validation
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Form\Validation\Element;

/**
 * Class Select
 * @package Gcs\Framework\Core\Form\Validation\Element
 */

class Select extends Element {
    /**
     * constructor
     * @access public
     * @param $field string
     * @param $label string
     * @return \Gcs\Framework\Core\Form\Validation\Element\Select
     * @since 3.0
     * @package Gcs\Framework\Core\Form\Validation\Element
     */

    public function __construct($field, $label) {
        parent::__construct($field, $label);

        return $this;
    }

    /**
     * validity
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Form\Validation\Element
     */

    public function check() {
        if ($this->_exist) {
            parent::check();
        }
    }

    /**
     * destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Form\Validation\Element
     */

    public function __destruct() {
    }
}