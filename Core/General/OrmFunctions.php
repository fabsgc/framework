<?php
	/*\
	 | ------------------------------------------------------
	 | @file : OrmFunctions.php
	 | @author : Fabien Beaujean
	 | @description : Orm functions trait
	 | @version : 3.0
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\General;

	use Gcs\Framework\Core\Database\Database;
	use Gcs\Framework\Core\Orm\Entity\Multiple;

	/**
	 * OrmFunctions trait
	 * @package Gcs\Framework\Core\General
	 */

	trait OrmFunctions {
		/**
		 * transform sql data in entity
		 * @access public
		 * @param $data   array
		 * @param $entity string
		 * @return array
		 * @since  2.4
		 */

		final public function ormToEntity($data = [], $entity = '') {
			$entities = [];

			foreach ($data as $value) {
				if ($entity != '') {
					$entityName = '\entity\\' . $entity;
					$entityObject = new $entityName(Database::instance()->db());

					foreach ($value as $key => $value2) {
						$entityObject->$key = $value2;
					}
				}
				else {
					$entityObject = new Multiple($data);
				}

				array_push($entities, $entityObject);
			}

			return $entities;
		}
	}