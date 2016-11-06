<?php
	/*\
	 | ------------------------------------------------------
	 | @file : ManyToOne.php
	 | @author : fab@c++
	 | @description : annotation orm relation many to one
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation\Annotations\Orm\Relations;

	/**
	 * Class ManyToOne
	 * @package System\Annotation\Annotations\Orm\Relations
	 */

	class ManyToOne extends Relation  {

		/**
		 * Parameter type
		 * @var string
		 */

		public $type = 'MANY_TO_ONE';
	}