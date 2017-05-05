<?php
	/*\
	 | ------------------------------------------------------
	 | @file : OneToOne.php
	 | @author : Fabien Beaujean
	 | @description : annotation orm relation one to one
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation\Annotations\Orm\Relations;

	/**
	 * Class OneToOne
	 * @package System\Annotation\Annotations\Orm\Relations
	 */

	class OneToOne extends Relation  {

		/**
		 * Parameter type
		 * @var string
		 */

		public $type = 'ONE_TO_ONE';
	}