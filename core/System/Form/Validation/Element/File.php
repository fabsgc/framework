<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Select.php
	 | @author : fab@c++
	 | @description : select validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Form\Validation\Element;

	use System\Lang\Lang;
	use System\Request\Data;

	/**
	 * Class File
	 * @package System\Form\Validation\Element
	 */

	class File extends Element {
		/**
		 * constructor
		 * @access  public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function __construct($field, $label) {
			parent::__construct($field, $label);

			$this->_data = Data::getInstance()->file;

			if (!isset($this->_data[$field])) {
				array_push($this->_errors, [
					'field'   => $this->_label,
					'message' => Lang::getInstance()->lang('.app.system.form.exist')
				]);

				$this->_exist = false;
			}

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
			if ($this->_exist) {
				foreach ($this->_constraints as $constraint) {
					switch ($constraint['type']) {
						case self::ACCEPT:
							if (!is_array($this->_data[$this->_field]['type'])) {
								$this->_data[$this->_field]['type'] = [$this->_data[$this->_field]['type']];
							}

							foreach ($this->_data[$this->_field]['type'] as $value) {
								if ($value != '') {
									if (!in_array($value, $constraint['value'])) {
										array_push($this->_errors, [
											'name'    => $this->_field,
											'field'   => $this->_label,
											'message' => $constraint['message']
										]);
									}
								}
							}
						break;

						case self::EXTENSION:
							if (!is_array($this->_data[$this->_field]['name'])) {
								$this->_data[$this->_field]['name'] = [$this->_data[$this->_field]['name']];
							}

							foreach ($this->_data[$this->_field]['name'] as $value) {
								if ($value != '') {
									if (!in_array(pathinfo($value)['extension'], $constraint['value'])) {
										array_push($this->_errors, [
											'name'    => $this->_field,
											'field'   => $this->_label,
											'message' => $constraint['message']
										]);
									}
								}
							}
						break;

						case self::SIZEMIN:
							if (!is_array($this->_data[$this->_field]['size'])) {
								$this->_data[$this->_field]['size'] = [$this->_data[$this->_field]['size']];
							}

							foreach ($this->_data[$this->_field]['size'] as $value) {
								if ($value != 0) {
									if ($value < $constraint['value']) {
										array_push($this->_errors, [
											'name'    => $this->_field,
											'field'   => $this->_label,
											'message' => $constraint['message']
										]);
									}
								}
							}
						break;

						case self::SIZEMAX:
							if (!is_array($this->_data[$this->_field]['size'])) {
								$this->_data[$this->_field]['size'] = [$this->_data[$this->_field]['size']];
							}

							foreach ($this->_data[$this->_field]['size'] as $value) {
								if ($value != 0) {
									if ($value > $constraint['value']) {
										array_push($this->_errors, [
											'name'    => $this->_field,
											'field'   => $this->_label,
											'message' => $constraint['message']
										]);
									}
								}
							}
						break;

						case self::SIZEBETWEEN:
							if (!is_array($this->_data[$this->_field]['size'])) {
								$this->_data[$this->_field]['size'] = [$this->_data[$this->_field]['size']];
							}

							foreach ($this->_data[$this->_field]['size'] as $value) {
								if ($value != 0) {
									if ($value < $constraint['value'][0] || $value > $constraint['value'][1]) {
										array_push($this->_errors, [
											'name'    => $this->_field,
											'field'   => $this->_label,
											'message' => $constraint['message']
										]);
									}
								}
							}
						break;

						case self::COUNT:
							if (!is_array($this->_data[$this->_field]['name'])) {
								$this->_data[$this->_field]['name'] = [$this->_data[$this->_field]['name']];
							}

							if (count($this->_data[$this->_field]['name']) != $constraint['value']) {
								array_push($this->_errors, [
									'name'    => $this->_field,
									'field'   => $this->_label,
									'message' => $constraint['message']
								]);
							}
						break;

						case self::COUNTMIN:
							if (!is_array($this->_data[$this->_field]['name'])) {
								$this->_data[$this->_field]['name'] = [$this->_data[$this->_field]['name']];
							}

							if (count($this->_data[$this->_field]['name']) < $constraint['value']) {
								array_push($this->_errors, [
									'name'    => $this->_field,
									'field'   => $this->_label,
									'message' => $constraint['message']
								]);
							}
						break;

						case self::COUNTMAX:
							if (!is_array($this->_data[$this->_field]['name'])) {
								$this->_data[$this->_field]['name'] = [$this->_data[$this->_field]['name']];
							}

							if (count($this->_data[$this->_field]['name']) > $constraint['value']) {
								array_push($this->_errors, [
									'name'    => $this->_field,
									'field'   => $this->_label,
									'message' => $constraint['message']
								]);
							}
						break;

						case self::COUNTIN:
							if (!is_array($this->_data[$this->_field]['name'])) {
								$this->_data[$this->_field]['name'] = [$this->_data[$this->_field]['name']];
							}

							if (!in_array(count($this->_data[$this->_field]['name']), $constraint['value'])) {
								array_push($this->_errors, [
									'name'    => $this->_field,
									'field'   => $this->_label,
									'message' => $constraint['message']
								]);
							}
						break;

						case self::CUSTOM:
							/** @var object[] $constraints */
							if ($constraints['value']->filter() == false) {
								array_push($this->_errors, [
									'name'    => $this->_field,
									'field'   => $this->_label,
									'message' => $constraints['value']->error()
								]);
							}
						break;
					}
				}
			}
		}

		/**
		 * mime type accepted
		 * @access  public
		 * @param $accept string[]
		 * @param $error  string
		 * @return \System\Form\Validation\Element\File
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
		 * extension accepted
		 * @access  public
		 * @param $extension string[]
		 * @param $error     string
		 * @return \System\Form\Validation\Element\File
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
		 * file size min (in byte)
		 * @access  public
		 * @param $sizeMin integer
		 * @param $error   string
		 * @return \System\Form\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function sizeMin($sizeMin, $error) {
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
		 * file size min (in byte)
		 * @access  public
		 * @param $sizeMax integer
		 * @param $error   string
		 * @return \System\Form\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function sizeMax($sizeMax, $error) {
			if ($this->_exist) {
				array_push($this->_constraints, [
					'type'    => self::SIZEMIN,
					'value'   => $sizeMax,
					'message' => $error
				]);
			}

			return $this;
		}

		/**
		 * file size min (in byte)
		 * @access  public
		 * @param $sizeBetween integer[]
		 * @param $error       string
		 * @return \System\Form\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function sizeBetween($sizeBetween, $error) {
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