<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Parser.php
	 | @author : Fabien Beaujean
	 | @description : parser
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Annotation;

	use Gcs\Framework\Core\Exception\AnnotationNotExistingException;

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
			'Routing'    =>           '\System\Annotation\Annotations\Router\Routing',
			'Before'     =>            '\System\Annotation\Annotations\Common\Before',
			'After'      =>             '\System\Annotation\Annotations\Common\After',
			'Cron'       =>                '\System\Annotation\Annotations\Cron\Cron',
			'Form'       =>                 '\System\Annotation\Annotations\Orm\Form',
			'Table'      =>                '\System\Annotation\Annotations\Orm\Table',
			'Column'     =>               '\System\Annotation\Annotations\Orm\Column',
			'OneToOne'   =>   '\System\Annotation\Annotations\Orm\Relations\OneToOne',
			'OneToMany'  =>  '\System\Annotation\Annotations\Orm\Relations\OneToMany',
			'ManyToOne'  =>  '\System\Annotation\Annotations\Orm\Relations\ManyToOne',
			'ManyToMany' =>  '\System\Annotation\Annotations\Orm\Relations\ManyToMany'

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

			foreach ($annotations[1] as $key => $annotation){
				if(preg_match('#([A-Za-z]+)\((.*)\)#is', $annotation)){
					preg_match_all('#([A-Za-z]+)\((.*)\)#is', $annotation, $annotationData);
					$annotationTitle = $annotationData[1][0];

					if(isset(Parser::$_annotations[$annotationTitle])) {
						$annotationContents = explode(", ", $annotationData[2][0]);
						$data[$key] = [];

						foreach ($annotationContents as $annotationContent) {
							preg_match_all('#(.+)="(.*)"#isU', trim($annotationContent), $annotationContentData);

							$data[$key]['type'] = $annotationTitle;
							$data[$key]['class'] = Parser::$_annotations[$annotationTitle];
							$data[$key]['properties'][$annotationContentData[1][0]] = $annotationContentData[2][0];
						}
					}
					else{
						throw new AnnotationNotExistingException('The annotation "' . $annotationTitle . '" does not exist');
					}
				}
			}

			return $data;
		}
	}