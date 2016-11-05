<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Annotation.php
	 | @author : fab@c++
	 | @description : annotation system
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation;

	use Doctrine\Common\Annotations\AnnotationReader;

	/**
	 * Class Annotation
	 * @package System\Annotation
	 */

	class Annotation {

		/**
		 * Get annotations of a class
		 * @param $class string
		 * @return array
		 * @throws \Exception
		 */
		public static function getClass($class){
			$reflectionClass = new \ReflectionClass($class);
			$reader = new AnnotationReader();

			var_dump($reflectionClass);



			$apiMetaAnnotation = $reader->getClassAnnotation($reflectionClass, '\\System\\Annotation\\Annotations\\Before');

			var_dump('salut');

			if(!$apiMetaAnnotation) {
				throw new \Exception(sprintf('Entity class %s does not have required annotation ApiMeta', $class));
			}

			$resource = strtolower($apiMetaAnnotation->resource);
			$resourcePlural = strtolower($apiMetaAnnotation->resourcePlural);
			// do http lookup based on resource names

			return null;
		}

		/**
		 * Get class methods annotations
		 * @param $class string
		 */
		public static function getMethods($class){

		}

		/**
		 * Get method annotations
		 * @param $class string
		 * @param $method string
		 */
		public static function getMethod($class, $method){

		}

		public static function processAnnotations(){

		}
	}