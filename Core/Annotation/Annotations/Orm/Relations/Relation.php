<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Table.php
	 | @author : Fabien Beaujean
	 | @description : annotation orm table
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Annotation\Annotations\Orm\Relations;

	use Gcs\Framework\Core\Annotation\Annotations\Annotation;

	/**
	 * Class Relation
	 * @package System\Annotation\Annotations\Orm\Relations
	 */

	abstract class Relation extends Annotation {

		/**
		 * Parameter type
		 * @var string
		 */

		public $type = 'ONE_TO_ONE';


		/**
		 * Parameter from
		 * @var string
		 */

		public $from;

		/**
		 * Parameter to
		 * @var string
		 */

		public $to;

		/**
		 * Parameter belong
		 * @var string
		 */

		public $belong = 'AGGREGATION';

		/**
		 * Parameter join
		 * @var string
		 */

		public $join = 'JOIN_INNER';
	}