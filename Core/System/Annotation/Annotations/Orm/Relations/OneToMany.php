<?php
	/*\
	 | ------------------------------------------------------
	 | @file : OneToMany.php
	 | @author : Fabien Beaujean
	 | @description : annotation orm relation one to many
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Annotation\Annotations\Orm\Relations;

	/**
	 * Class OneToMany
	 * @package Gcs\Framework\Core\Annotation\Annotations\Orm\Relations
	 */

	class OneToMany extends Relation  {

		/**
		 * Parameter type
		 * @var string
		 */

		public $type = 'ONE_TO_MANY';
	}