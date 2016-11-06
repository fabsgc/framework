<?php
	/*\
	 | ------------------------------------------------------
	 | @file : OneToMany.php
	 | @author : fab@c++
	 | @description : annotation orm relation one to many
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation\Annotations\Orm\Relations;

	/**
	 * Class OneToMany
	 * @package System\Annotation\Annotations\Orm\Relations
	 */

	class OneToMany extends Relation  {

		/**
		 * Parameter type
		 * @var string
		 */

		public $type = 'ONE_TO_MANY';
	}