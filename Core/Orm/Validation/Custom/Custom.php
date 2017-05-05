<?php
/*\
 | ------------------------------------------------------
 | @file : Custom.php
 | @author : Fabien Beaujean
 | @description : custom validation
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Orm\Validation\Custom;

/**
 * Class Custom
 * @package Gcs\Framework\Core\Orm\Validation\Custom
 */

abstract class Custom {
    /**
     * the field name
     * @var string $field
     */

    protected $field;

    /**
     * the field label
     * @var string $label
     */

    protected $label;

    /**
     * the Entity
     * @var $value \Gcs\Framework\Core\Orm\Entity\Entity
     */

    protected $entity;

    /**
     * only the concerned field
     * @var $value \Gcs\Framework\Core\Orm\Entity\Field
     */

    protected $value;

    /**
     * constructor
     * @access public
     * @param $field string
     * @param $label string
     * @param \Gcs\Framework\Core\Orm\Entity\Entity $entity
     * @param mixed $value
     * @since 3.0
     * @package Gcs\Framework\Core\Orm\Validation\Custom
     */

    final public function __construct($field, $label, $entity, $value) {
        $this->field = $field;
        $this->label = $label;
        $this->entity = $entity;
        $this->value = $value;
    }

    /**
     * you can define your own form filter here
     * @access public
     * @return boolean
     * @since 3.0
     * @package Gcs\Framework\Core\Orm\Validation\Custom
     */

    public function filter() {
        return true;
    }

    /**
     * if the filter return false, the framework call this method to get the
     * @access public
     * @return string
     * @since 3.0
     * @package Gcs\Framework\Core\Orm\Validation\Custom
     */

    public function error() {
        return '';
    }
}