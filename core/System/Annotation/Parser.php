<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Parser.php
	 | @author : fab@c++
	 | @description : parser
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation;

	use System\Exception\AnnotationNotExistingException;

	/**
	 * Class Parser
	 * @package System\Annotation
	 */

	class Parser {

		/**
		 * comments from
		 * @var string
		 */

		protected $_comments;

		/**
		 * annotation classes list
		 * @var array
		 */

		public static $_annotations = [
			'Before'  => '\System\Annotation\Annotations\Common\Before',
			'After'   => '\System\Annotation\Annotations\Common\After',
			'Routing' => '\System\Annotation\Annotations\Router\Routing'
		];

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package System\Annotation
		 * @param $comments string
		 */

		public function __construct($comments) {
			$this->_comments = $comments;
		}

		/**
		 * parse comments to retrieve annotations
		 * @access public
		 * @return array
		 * @throws AnnotationNotExistingException
		 */

		public function parse(){
			$data = [];

			preg_match_all('#@(.*?)\n#s', $this->_comments, $annotations);

			foreach ($annotations[1] as $annotation){
				if(preg_match('#([A-Za-z]+)\((.+)\)#is', $annotation)){
					preg_match_all('#([A-Za-z]+)\((.+)\)#is', $annotation, $annotationData);
					$annotationTitle = $annotationData[1][0];

					if(isset(Parser::$_annotations[$annotationTitle])) {
						$annotationContents = explode(", ", $annotationData[2][0]);
						$data[$annotationTitle] = [];

						foreach ($annotationContents as $annotationContent) {
							preg_match_all('#(.+)="(.+)"#isU', trim($annotationContent), $annotationContentData);

							$data[$annotationTitle]['class'] = Parser::$_annotations[$annotationTitle];
							$data[$annotationTitle]['properties'][$annotationContentData[1][0]] = $annotationContentData[2][0];
						}
					}
					else{
						throw new AnnotationNotExistingException('The annotaton "' . $annotationTitle . '" does not exist');
					}
				}
			}

			return $data;
		}
	}