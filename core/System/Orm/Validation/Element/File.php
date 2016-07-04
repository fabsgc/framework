<?php
	/*\
	 | ------------------------------------------------------
	 | @file : File.php
	 | @author : fab@c++
	 | @description : file validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Validation\Element;

	/**
	 * Class File
	 * @package System\Orm\Validation\Element
	 */

	class File extends Element {
		/**
		 * constructor
		 * @access  public
		 * @param $entity \System\Orm\Entity\Entity
		 * @param $field  string
		 * @param $label  string
		 * @return \System\Orm\Validation\Element\File
		 * @since   3.0
		 * @package System\Orm\Validation\Element
		 */

		public function __construct($entity, $field, $label) {
			parent::__construct($entity, $field, $label);
			return $this;
		}

		/**
		 * check validity
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function check() {
			$field = null;
			$this->_errors = [];
			$fields = $this->_getField();

			foreach ($this->_constraints as $constraints) {
				/** @var \System\Orm\Entity\Type\File $value */
				foreach ($fields as $value) {
					if ($value != null) {
						switch ($constraints['type']) {
							case self::ACCEPT:
								if (!in_array($value->contentType, $constraints['value'])) {
									array_push($this->_errors, [
										'name'    => $this->_field,
										'field'   => $this->_label,
										'message' => $constraints['message']
									]);
								}
							break;

							case self::EXTENSION:
								if (!in_array($value->extension(), $constraints['value'])) {
									array_push($this->_errors, [
										'name'    => $this->_field,
										'field'   => $this->_label,
										'message' => $constraints['message']
									]);
								}
							break;

							case self::SIZE:
								if (strlen($value->content) != $constraints['value']) {
									array_push($this->_errors, [
										'name'    => $this->_field,
										'field'   => $this->_label,
										'message' => $constraints['message']
									]);
								}
							break;

							case self::SIZEMIN:
								if (strlen($value->content) < $constraints['value']) {
									array_push($this->_errors, [
										'name'    => $this->_field,
										'field'   => $this->_label,
										'message' => $constraints['message']
									]);
								}
							break;

							case self::SIZEMAX:
								if (strlen($value->content) > $constraints['value']) {
									array_push($this->_errors, [
										'name'    => $this->_field,
										'field'   => $this->_label,
										'message' => $constraints['message']
									]);
								}
							break;

							case self::SIZEBETWEEN:
								if (!in_array(strlen($value->content), $constraints['value'])) {
									array_push($this->_errors, [
										'name'    => $this->_field,
										'field'   => $this->_label,
										'message' => $constraints['message']
									]);
								}
							break;
						}
					}
					else {
						array_push($this->_errors, [
							'field'   => $this->_label,
							'message' => $constraints['message']
						]);
					}
				}
			}
		}

		/**
		 * file types accepted
		 * @access  public
		 * @param $accept string[]
		 * @param $error  string
		 * @return \System\Orm\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function accept($accept, $error) {
			if ($this->_exist) {
				array_push($this->_constraints, [
					'type'    => self::ACCEPT,
					'value'   => $accept,
					'message' => $error
				]);
			}

			return $this;
		}

		/**
		 * file extensions accepted
		 * @access  public
		 * @param $extension string[]
		 * @param $error     string
		 * @return \System\Orm\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function extension($extension, $error) {
			if ($this->_exist) {
				array_push($this->_constraints, [
					'type'    => self::EXTENSION,
					'value'   => $extension,
					'message' => $error
				]);
			}

			return $this;
		}

		/**
		 * exact file size accepted (bytes)
		 * @access  public
		 * @param $size  int
		 * @param $error string
		 * @return \System\Orm\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function size($size = 1048576, $error) {
			if ($this->_exist) {
				array_push($this->_constraints, [
					'type'    => self::SIZE,
					'value'   => $size,
					'message' => $error
				]);
			}

			return $this;
		}

		/**
		 * minimal file size accepted (bytes)
		 * @access  public
		 * @param $sizeMin int
		 * @param $error   string
		 * @return \System\Orm\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function sizeMin($sizeMin = 1048576, $error) {
			if ($this->_exist) {
				array_push($this->_constraints, [
					'type'    => self::SIZEMIN,
					'value'   => $sizeMin,
					'message' => $error
				]);
			}

			return $this;
		}

		/**
		 * max file size accepted (bytes)
		 * @access  public
		 * @param $sizeMax int
		 * @param $error   string
		 * @return \System\Orm\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function sizeMax($sizeMax = 1048576, $error) {
			if ($this->_exist) {
				array_push($this->_constraints, [
					'type'    => self::SIZEMAX,
					'value'   => $sizeMax,
					'message' => $error
				]);
			}

			return $this;
		}

		/**
		 * max file size accepted (bytes)
		 * @access  public
		 * @param $sizeBetween int[]
		 * @param $error       string
		 * @return \System\Orm\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function sizeBetween($sizeBetween = [0, 1048576], $error) {
			if ($this->_exist) {
				array_push($this->_constraints, [
					'type'    => self::SIZEBETWEEN,
					'value'   => $sizeBetween,
					'message' => $error
				]);
			}

			return $this;
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function __destruct() {
		}
	}