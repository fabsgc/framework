<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Pagination.php
	 | @author : Fabien Beaujean
	 | @description : helper for pagination
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Helper\Pagination;

	use Gcs\Framework\Core\General\Facades;
	use Gcs\Framework\Core\Helper\Helper;
	use Gcs\Framework\Core\Template\Template;

	/**
	 * Class Pagination
	 * @package Helper\Pagination
	 */

	class Pagination extends Helper {
		use Facades;

		/**
		 * items by page
		 * @var int $_byPage
		 * @access protected
		 */

		protected $_byPage                = 2;

		/**
		 * data
		 * @var array[] $_entry
		 * @access protected
		 */

		protected $_entry;

		/**
		 * display the button "first"
		 * @var bool $_buttonFirst
		 * @access protected
		 */

		protected $_buttonFirst           = false;

		/**
		 * display the button "last"
		 * @var bool $_buttonLast
		 */

		protected $_buttonLast            = false;

		/**
		 * display the button "before"
		 * @var bool $_buttonBefore
		 * @access protected
		 */

		protected $_buttonBefore          = true;

		/**
		 * display the button "after"
		 * @var bool $_buttonAfter
		 * @access protected
		 */

		protected $_buttonAfter           = true;

		/**
		 * the url used by the pagination
		 * @var string $_url
		 * @access protected
		 */

		protected $_url;

		/**
		 * the current page to display
		 * @var int $_currentPage
		 * @access protected
		 */

		protected $_currentPage           = 0;

		/**
		 * the number of pages
		 * @var int $_nbrPage
		 * @access protected
		 */

		protected $_nbrPage               = 0;

		/**
		 * display the button "before"
		 * @var bool $_paginationFirstBefore
		 * @access protected
		 */

		protected $_paginationFirstBefore = true;

		/**
		 * display the button "next"
		 * @var bool $_paginationLastAfter
		 */

		protected $_paginationLastAfter   = true;

		/**
		 * display the total pages
		 * @var bool $_paginationTotalPage
		 * @access protected
		 */

		protected $_paginationTotalPage   = false;

		/**
		 * @var array $_paginationList
		 * @access protected
		 */

		protected $_paginationList        = [];

		/**
		 * @var array $_paginationFirst
		 * @access protected
		 */

		protected $_paginationFirst       = [];

		/**
		 * @var array $_paginationLast
		 * @access protected
		 */

		protected $_paginationLast        = [];

		/**
		 * @var array $_paginationBefore
		 * @access protected
		 */

		protected $_paginationBefore      = [];

		/**
		 * @var array $_paginationAfter
		 * @access protected
		 */

		protected $_paginationAfter       = [];

		/**
		 * Permit to specify how many links you want on both side of the current link
		 * @var bool|int $_paginationCut
		 * @access protected
		 */

		protected $_paginationCut         = false;

		/**
		 * @var array $_data
		 * @access protected
		 */

		protected $_data                  = [];

		/**
		 * Initialization of the helper
		 * @access public
		 * @param &$entry mixed : your data
		 * @param $data  mixed array,\Gcs\Framework\Core\Collection\Collection
		 *               buttonFl    : button first/last (true/false)
		 *               buttonBa    : button previous/after (true/false)
		 *               url         : url of each page. Replace the page number by "{page}"
		 *               bypage      : Entity by page
		 *               currentPage : current page
		 *               totalPage   : total page (true/false)
		 *               cut         : how many links before and after the current link
		 * @since 3.0
		 * @package helper\Pagination
		 */

		public function __construct($entry, $data) {
			parent::__construct();

			if (gettype($entry) == 'object') {
				$entry = $entry->data();
			}

			foreach ($data as $key => $val) {
				switch ($key) {
					case 'first':
						$this->_buttonFirst = $val;
					break;

					case 'last':
						$this->_buttonLast = $val;
					break;

					case 'before':
						$this->_buttonBefore = $val;
					break;

					case 'after':
						$this->_buttonAfter = $val;
					break;

					case 'url':
						$this->_url = $val;
					break;

					case 'bypage':
						$this->_byPage = intval($val);
					break;

					case 'currentPage':
						$this->_currentPage = intval($val);
					break;

					case 'totalPage':
						$this->_paginationTotalPage = $val;
					break;

					case 'cut':
						$this->_paginationCut = intval($val);
					break;
				}
			}

			if (is_array($entry)) {
				$this->_entry = count($entry);
			}
			else {
				$this->_entry = $entry;
			}

			$this->_setData();
		}

		/**
		 * Initialization of the helper
		 * @access protected
		 * @return void
		 * @since 3.0
		 * @package helper\Pagination
		 */

		protected function _setData() {
			if (($this->_currentPage == 0)) {
				$linkDisabled = false;
			}
			else {
				$linkDisabled = true;
			}

			if ($this->_currentPage < 1) {
				$this->_currentPage = 1;
			}

			$this->_nbrPage = ceil($this->_entry / $this->_byPage);

			if ($this->_nbrPage == 0) {
				$this->_nbrPage = 1;
			}

			if ($this->_currentPage > $this->_nbrPage) {
				$this->_currentPage = $this->_nbrPage;
			}

			if ($this->_currentPage == 1) {
				$this->_paginationFirstBefore = false;
			}

			if ($this->_currentPage == $this->_nbrPage) {
				$this->_paginationLastAfter = false;
			}

			//useless to hide links if there isn't pages enough
			if ($this->_paginationCut == false || $this->_paginationCut > $this->_nbrPage) {
				for ($i = 1; $i <= $this->_nbrPage; $i++) {
					if ($i == $this->_currentPage && $linkDisabled != false) {
						$this->_paginationList[$i] = false;
					}
					else {
						$this->_paginationList[$i] = preg_replace('#\{page\}#isU', $i, $this->_url);
					}
				}
			}
			else {
				if ($this->_paginationCut > (($this->_nbrPage / 2) - 2)) {
					$this->_paginationCut = intval(($this->_nbrPage / 2));
				}

				if ($this->_paginationCut < 1) {
					$this->_paginationCut = 1;
				}

				if (($this->_currentPage - $this->_paginationCut) > 0 && ($this->_currentPage + $this->_paginationCut) < $this->_nbrPage) {
					for ($i = $this->_currentPage - $this->_paginationCut; $i <= $this->_currentPage + $this->_paginationCut; $i++) {
						if ($i == $this->_currentPage && $linkDisabled != false) {
							$this->_paginationList[$i] = false;
						}
						else {
							$this->_paginationList[$i] = preg_replace('#\{page\}#isU', $i, $this->_url);
						}
					}
				}
				elseif (($this->_currentPage - $this->_paginationCut) > 0 && ($this->_currentPage + $this->_paginationCut) >= $this->_nbrPage) {
					for ($i = $this->_currentPage - $this->_paginationCut; $i <= $this->_nbrPage; $i++) {
						if ($i == $this->_currentPage && $linkDisabled != false) {
							$this->_paginationList[$i] = false;
						}
						else {
							$this->_paginationList[$i] = preg_replace('#\{page\}#isU', $i, $this->_url);
						}
					}
				}
				elseif (($this->_currentPage - $this->_paginationCut) <= 0 && ($this->_currentPage + $this->_paginationCut) < $this->_nbrPage) {
					for ($i = 1; $i <= $this->_currentPage + $this->_paginationCut; $i++) {
						if ($i == $this->_currentPage && $linkDisabled != false) {
							$this->_paginationList[$i] = false;
						}
						else {
							$this->_paginationList[$i] = preg_replace('#\{page\}#isU', $i, $this->_url);
						}
					}
				}
				else {
					for ($i = 1; $i <= $this->_nbrPage; $i++) {
						if ($i == $this->_currentPage && $linkDisabled != false) {
							$this->_paginationList[$i] = false;
						}
						else {
							$this->_paginationList[$i] = preg_replace('#\{page\}#isU', $i, $this->_url);
						}
					}
				}
			}
		}

		/**
		 * display the pagination
		 * @access public
		 * @return string
		 * @param $template string : template path
		 * @since 3.0
		 * @package helper\Pagination
		 */

		public function show($template = '.app/system/helper/pagination/default') {
			$rand = rand(0, 2);
			$tpl = new Template($template, 'pagination_' . $rand, 0);
			$tpl->assign([
				'paginationFirst'       => $this->_buttonFirst,
				'paginationLast'        => $this->_buttonLast,
				'paginationBefore'      => $this->_buttonBefore,
				'paginationAfter'       => $this->_buttonAfter,
				'currentPage'           => $this->_currentPage,
				'paginationFirstBefore' => $this->_paginationFirstBefore,
				'paginationLastAfter'   => $this->_paginationLastAfter,
				'urlFirst'              => preg_replace('#\{page\}#isU', 1, $this->_url),
				'urlLast'               => preg_replace('#\{page\}#isU', $this->_nbrPage, $this->_url),
				'urlBefore'             => preg_replace('#\{page\}#isU', $this->_currentPage - 1, $this->_url),
				'urlAfter'              => preg_replace('#\{page\}#isU', $this->_currentPage + 1, $this->_url),
				'pagination'            => $this->_paginationList,
				'totalPage'             => $this->_paginationTotalPage,
				'nbrPage'               => $this->_nbrPage
			]);

			return $tpl->show();
		}

		/**
		 * get the current page
		 * @access public
		 * @return int
		 * @since 3.0
		 * @package helper\Pagination
		 */

		public function getCurrentPage() {
			return $this->_currentPage;
		}

		/**
		 * get the number of pages
		 * @access public
		 * @return int
		 * @since 3.0
		 * @package helper\Pagination
		 */

		public function getNbrPage() {
			if ($this->_nbrPage != 0) {
				return $this->_nbrPage;
			}
			else {
				return 1;
			}
		}

		/**
		 * get the data to be displayed from all data
		 * @access public
		 * @return array
		 * @param $data : array of data
		 * @return array
		 * @since 3.0
		 * @package helper\Pagination
		 */

		public function getData($data = []) {
			for ($i = ((($this->_byPage * $this->_currentPage) - $this->_byPage)); $i <= (($this->_byPage * $this->_currentPage) - 1); $i++) {
				if (isset($data[$i])) {
					array_push($this->_data, $data[$i]);
				}
			}

			return $this->_data;
		}

		/**
		 * for the LIMIT syntax, get the two parameters : first,number
		 * @access public
		 * @return int
		 * @since 3.0
		 * @package helper\Pagination
		 */

		public function getDataFirstCase() {
			return $this->_byPage * $this->_currentPage - $this->_byPage;
		}

		/**
		 * desctructor
		 * @access public
		 * @since 3.0
		 * @package helper\Pagination
		 */
		public function __destruct() {
		}
	}