<?php
	/*\
	 | ------------------------------------------------------
	 | @file : ManyToOne.php
	 | @author : Fabien Beaujean
	 | @description : annotation orm relation many to one
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Annotation\Annotations\Orm\Relations;

	/**
	 * Class ManyToOne
	 * @package Gcs\Framework\Core\Annotation\Annotations\Orm\Relations
	 */

	class ManyToOne extends Relation  {

		/**
		 * Parameter type
		 * @var string
		 */

		public $type = 'MANY_TO_ONE';
	}