<?php
	/*\
	 | ------------------------------------------------------
	 | @file : ManyToMany.php
	 | @author : Fabien Beaujean
	 | @description : annotation orm relation many to many
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Annotation\Annotations\Orm\Relations;

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