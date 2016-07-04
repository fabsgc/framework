<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Mail.php
	 | @author : fab@c++
	 | @description : helper for sending mails
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Helper\Mail;

	use System\General\facades;
	use System\Helper\Helper;
	use System\Mime\Mime;
	use System\Template\Template;

	/**
	 * Class Mail
	 * @package Helper\Mail
	 */
	
	class Mail extends Helper {
		use facades;

		/**
		 * name and e-mail of the sendere
		 * @var string[] $_sender
		 * @access protected
		 */

		protected $_sender = ['mail', 'mail@mail.com'];

		/**
		 * name and e-mail of the person who you should reply to
		 * @var string[]
		 * @access protected
		 */

		protected $_reply = ['mail', 'mail@mail.com'];

		/**
		 * list of receivers
		 * @var string[]
		 * @access protected
		 */

		protected $_receiver = [];

		/**
		 * the subject of the e-mail you want to send
		 * @var string $_subject
		 * @access protected
		 */

		protected $_subject = '';

		/**
		 * the subject of the e-mail you want to send
		 * @var string $_subject
		 * @access protected
		 */

		protected $_message = [];

		/**
		 * all the files you want to send with the e-mail encoded in base 64
		 * @var string[] $_attachments
		 * @access protected
		 */

		protected $_attachments = [];

		/**
		 * the priority of the e-mail
		 * @var string $_priority
		 * @access protected
		 */

		protected $_priority = '3';

		/**
		 * list of e-mails in cc
		 * @var string[] $_cc
		 * @access protected
		 */

		protected $_cc = [];

		/**
		 * list of e-mails in bcc
		 * @var string[] $_bcc
		 * @access protected
		 */

		protected $_bcc = [];

		/**
		 * the reception's e-mails
		 * @var string[] $_reception
		 * @access protected
		 */

		protected $_reception = [];

		/**
		 * do you want to send a message in html ?
		 * @var boolean $_formatHtml
		 * @access protected
		 */

		protected $_formatHtml = true;

		/**
		 * the charset of the body, by default it use the value in config.php
		 * @var string $_charset
		 * @access protected
		 */

		protected $_charset = CHARSET;

		/**
		 * the e-mails has at least one attachment ?
		 * @var boolean $_hasAttachment
		 * @access protected
		 */

		protected $_hasAttachment = false;

		/**
		 * the backline format used in the body
		 * @var string $_backline
		 * @access protected
		 */

		protected $_backline = "\r\n";

		/**
		 * the boundary
		 * @var string $_boundary
		 * @access protected
		 */

		protected $_boundary;

		/**
		 * the boundary alt
		 * @var string $_boundaryAlt
		 * @access protected
		 */

		protected $_boundaryAlt;

		/**
		 * the content type
		 * @var string $_fileTransfert
		 * @access protected
		 */

		protected $_fileTransfert = 'multipart/alternative'; //content-type

		const FORMAT_HTML = true;
		const FORMAT_TEXT = false;
		const ATTACHMENT  = 'attachment';

		/**
		 * Initialization of the helper
		 * @param $data array
		 * @access  public
		 * @since   3.0
		 * @package Helper\Mail
		 */

		public function __construct($data = []) {
			parent::__construct();

			foreach ($data as $key => $value) {
				switch ($key) {
					case 'sender' :
						if (is_array($value)) {
							$this->_sender = $value;
						}
						else {
							array_push($this->_sender, $value);
						}
					break;

					case 'receiver':
						if (is_array($value)) {
							$this->_receiver = $value;
						}
						else {
							array_push($this->_receiver, $value);
						}
					break;

					case 'cc':
						if (is_array($value)) {
							$this->_cc = $value;
						}
						else {
							array_push($this->_cc, $value);
						}
					break;

					case 'bcc':
						if (is_array($value)) {
							$this->_bcc = $value;
						}
						else {
							array_push($this->_bcc, $value);
						}
					break;

					case 'reply':
						if (is_array($value)) {
							$this->_reply = $value;
						}
					break;

					case 'reception':
						$this->_reception = $value;
					break;

					case 'subject':
						$this->_subject = $value;
					break;

					case 'charset':
						$this->_charset = $value;
					break;

					case 'priority':
						$this->_priority = intval($value);
					break;

					case 'message':
						$this->_message = $value;
					break;

					case 'format':
						switch ($value) {
							case 'html':
								$this->_formatHtml = true;
							break;

							case 'text':
								$this->_formatHtml = false;
							break;

							default:
								$this->_formatHtml = true;
							break;
						}
					break;
				}
			}

			$this->_boundary = '-----=' . md5(rand());
			$this->_boundaryAlt = '-----=' . md5(rand());
		}

		/**
		 * Depending on the mail, we must change de back line type '\n' or '\r\n'
		 * @param $mail string
		 * @return string
		 * @access  protected
		 * @since   3.0
		 * @package Helper\Mail
		 */

		protected function _setBackLine($mail) {
			if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn|outlook).[a-z]{2,4}$#", $mail)) {
				return "\r\n";
			}
			else {
				return "\n";
			}
		}

		public function addText($text) {
			array_push($this->_message, $text);
		}

		/**
		 * You can use a template
		 * @param $template string : template path
		 * @param $vars     array
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package Helper\Mail
		 */

		public function addTemplate($template, $vars = []) {
			$tpl = new Template($template, 'template-mail', 0);

			foreach ($vars as $key => $var) {
				$tpl->assign($key, $var);
			}

			$message = $tpl->show();
			array_push($this->_message, $message);
		}

		/**
		 * You can add attachments
		 * @param $file string
		 * @param $name string
		 * @param $mime string
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package Helper\Mail
		 */

		public function addFile($file, $name = 'attachment', $mime = Mime::TXT) {
			$this->_fileTransfert = 'multipart/mixed';
			$this->_hasAttachment = true;

			if (file_exists($file)) {
				$data = file_get_contents($file);

				if ($name == self::ATTACHMENT) {
					$name = $name . uniqid();
				}

				if (isset($this->_attachments[$name])) {
					$name = $name . uniqid();
				}

				$this->_attachments[$name] = [chunk_split(base64_encode($data), $mime)];
			}
		}

		/**
		 * You can add receivers
		 * @param $receiver mixed
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package Helper\Mail
		 */

		public function addReceiver($receiver) {
			array_push($this->_receiver, $receiver);
		}

		/**
		 * You can add cc
		 * @param $cc mixed
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package Helper\Mail
		 */

		public function addCc($cc) {
			array_push($this->_cc, $cc);
		}

		/**
		 * You can add bcc
		 * @param $bcc mixed
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package Helper\Mail
		 */

		public function addBcc($bcc) {
			array_push($this->_bcc, $bcc);
		}

		/**
		 * Send the mail
		 * @return void
		 * @access  public
		 * @since   3.0
		 * @package Helper\Mail
		 */

		public function send() {
			$content = '';

			foreach ($this->_message as $message) {
				$content .= $message;
			}

			foreach ($this->_receiver as $receiver) {
				$this->_backline = $this->_setBackLine($receiver);

				//Create the header
				$header = "From: \"" . $this->_sender[0] . "\" <" . $this->_sender[1] . ">" . $this->_backline;
				$header .= "Reply-to: \"" . $this->_reply[0] . "\" <" . $this->_reply[1] . ">" . $this->_backline;
				$header .= "MIME-Version: 1.0" . $this->_backline;

				foreach ($this->_cc as $cc) {
					$header .= "Cc: " . $cc . "" . $this->_backline;
				}

				foreach ($this->_bcc as $bcc) {
					$header .= "Bcc: " . $bcc . "" . $this->_backline;
				}

				foreach ($this->_reception as $reception) {
					$header .= "DispositionNotificationTo: " . $reception . "" . $this->_backline;
				}

				$header .= "Content-Type: " . $this->_fileTransfert . ";" . $this->_backline . " boundary=\"" . $this->_boundary . "\"" . $this->_backline;

				//Create the message
				$message = $this->_backline . "--" . $this->_boundary . $this->_backline;
				$message .= "Content-Type: multipart/alternative;" . $this->_backline . " boundary=\"" . $this->_boundaryAlt . "\"" . $this->_backline;
				$message .= $this->_backline . "--" . $this->_boundaryAlt . $this->_backline;

				if ($this->_formatHtml == self::FORMAT_HTML) {
					$message .= "Content-Type: text/html; charset=\"" . $this->_charset . "\"" . $this->_backline;
					$message .= "Content-Transfer-Encoding: 8bit" . $this->_backline;
					$message .= $this->_backline . $content . $this->_backline;
				}
				elseif ($this->_formatHtml == self::FORMAT_TEXT) {
					$message .= "Content-Type: text/plain; charset=\"" . $this->_charset . "\"" . $this->_backline;
					$message .= "Content-Transfer-Encoding: 8bit" . $this->_backline;
					$message .= $this->_backline . $content . $this->_backline;
				}

				//We close the boundaries
				$message .= $this->_backline . "--" . $this->_boundaryAlt . "--" . $this->_backline;

				foreach ($this->_attachments as $key => $value) {
					$message .= $this->_backline . "--" . $this->_boundary . $this->_backline;
					$message .= "Content-Type: " . $value[1] . "; name=\"" . $key . "\"" . $this->_backline;
					$message .= "Content-Transfer-Encoding: base64" . $this->_backline;
					$message .= "Content-Disposition: attachment; filename=\"" . $key . "\"" . $this->_backline;
					$message .= $this->_backline . $value[0] . $this->_backline . $this->_backline;
				}

				$message .= $this->_backline . "--" . $this->_boundary . "--" . $this->_backline;

				if (MAIL) {
					mail($receiver, $this->_subject, $message, $header);
				}
			}
		}

		/**
		 * desctructor
		 * @access  public
		 * @since   3.0
		 * @package Helper\Mail
		 */

		public function __destruct() {
		}
	}