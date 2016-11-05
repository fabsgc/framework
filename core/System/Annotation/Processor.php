<?php
/*\
 | ------------------------------------------------------
 | @file : Processor.php
 | @author : fab@c++
 | @description : parser
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace System\Annotation;

/**
 * Class Processor
 * @package System\Annotation
 */

class Processor {

	/**
	 * annotation information
	 * @var string
	 */

	protected $_annotation;

	/**
	 * constructor
	 * @access public
	 * @since 3.0
	 * @package System\Annotation
	 * @param array $annotation
	 */

	public function __construct($annotation = []) {
		$this->_annotation = $annotation;
	}

	/**
	 * parse comments to retrieve annotations
	 * @access public
	 * @return \System\Annotation\Annotations\Annotation;
	 */

	public function process(){
		$className = new $this->_annotation['class'];
		$class = new $className();

		foreach ($this->_annotation['properties'] as $property => $value){
			$class->$property = $value;
		}

		return $class;
	}
}