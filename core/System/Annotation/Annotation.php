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
	use System\Cache\Cache;
	use System\Config\Config;
	use System\Sql\Sql;

	/**
	 * Class Annotation
	 * @package System\Annotation
	 */

	class Annotation {

		/**
		 * Get annotations of a class
		 * @param $class mixed string|Object
		 * @return array
		 * @throws \Exception
		 */
		public static function getClass($class) {
			$cache = null;

			if(is_object($class)){
				$cache = new Cache('gcs_cache_annotation_' . strtolower(str_replace('\\', '-', get_class($class))), 0);
			}
			else{
				$cache = new Cache('gcs_cache_annotation_' . strtolower(str_replace('\\', '-', $class)), 0);
			}

			if($cache->isExist() && Config::config()['user']['debug']['environment'] != 'development'){
				return $cache->getCache();
			}

			$data = [
				'class' => [],
				'methods' => [],
				'properties' => []
			];

			$reflectionClass = new \ReflectionClass($class);

			//Class annotations
			$parser = new Parser($reflectionClass->getDocComment());
			$dataClasses = $parser->parse();

			foreach ($dataClasses as $key => $dataClass){
				$processorClass = new Processor($dataClass);
				array_push($data['class'], [
					0 => [
						'annotation' => $dataClass['type'],
						'instance' => $processorClass->process()
					]
				]);
			}

			//Method annotations
			$methodsName = $reflectionClass->getMethods();

			foreach ($methodsName as $methodName){
				$method = new \ReflectionMethod($class, $methodName->getName());

				$parser = new Parser($method->getDocComment());
				$dataMethods = $parser->parse();

				if(count($dataMethods) > 0){
					$data['methods'][$methodName->getName()] = [];

					foreach ($dataMethods as $key => $dataMethod){
						$processorMethods = new Processor($dataMethod);
						array_push($data['methods'][$methodName->getName()], [
							'annotation' => $dataMethod['type'],
							'instance' => $processorMethods->process()
						]);
					}
				}
			}

			//Properties annotations
			$propertiesNames = $reflectionClass->getProperties();

			foreach ($propertiesNames as $propertyName){
				$property = new \ReflectionProperty($class, $propertyName->getName());

				$parser = new Parser($property->getDocComment());
				$dataProperties = $parser->parse();

				if(count($dataProperties) > 0){
					$data['properties'][$propertyName->getName()] = [];

					foreach ($dataProperties as $key => $dataProperty){
						$processorProperties = new Processor($dataProperty);
						array_push($data['properties'][$propertyName->getName()], [
							'annotation' => $dataProperty['type'],
							'instance' => $processorProperties->process()
						]);
					}
				}
			}

			$cache->setContent($data);
			$cache->setCache();

			return $data;
		}
	}