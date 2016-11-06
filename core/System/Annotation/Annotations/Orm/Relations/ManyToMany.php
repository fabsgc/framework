<?php
	/*\
	 | ------------------------------------------------------
	 | @file : ManyToMany.php
	 | @author : fab@c++
	 | @description : annotation orm relation many to many
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation\Annotations\Orm\Relations;

	/**
	 * Class ManyToMany
	 * @package System\Annotation\Annotations\Orm\Relations
	 */

	class ManyToMany extends Relation  {

		/**
		 * Parameter type
		 * @var string
		 */

		public $type = 'MANY_TO_MANY';
	}