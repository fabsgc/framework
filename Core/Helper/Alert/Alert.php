<?php

namespace Helper\Alert;

use Gcs\Framework\Core\Facade\Facades;
use Gcs\Framework\Core\General\Errors;
use Gcs\Framework\Core\Helper\Helper;
use Gcs\Framework\Core\Template\Template;

/**
 * Class Alert
 * @package Helper\Alert
 */
class Alert extends Helper {
    use Errors, Facades;

    /**
     * @var string[]
     * @access private
     */

    private $content = [];

    /**
     * Alert constructor.
     * @param array $data
     * @access public
     */

    public function __construct($data = []) {
    }

    /**
     * @param $type string
     * @param $message string
     * @access public
     */

    public function add($type, $message) {
        if (!$_SESSION['alert']) {
            $_SESSION['alert'] = [];
        }

        $alert = ['type' => $type, 'message' => $message, 'time' => 0];
        array_push($_SESSION['alert'], $alert);
    }

    /**
     * @return string[]
     * @access public
     */

    public function view() {
        if (isset($_SESSION['alert']) && count($_SESSION['alert']) > 0) {
            foreach ($_SESSION['alert'] as $key => $value) {
                switch ($value['type']) {
                    case 'info' :
                        $info = new Template('.app/system/helper/alert/info', 'alert-info-' . $key);
                        $info->assign('message', $value['message']);
                        array_push($this->content, $info->show());
                        break;

                    case 'danger' :
                        $danger = new Template('.app/system/helper/alert/danger', 'alert-danger' . $key);
                        $danger->assign('message', $value['message']);
                        array_push($this->content, $danger->show());
                        break;

                    case 'error' :
                        $error = new Template('.app/system/helper/alert/error', 'alert-error' . $key);
                        $error->assign('message', $value['message']);
                        array_push($this->content, $error->show());
                        break;

                    case 'success' :
                        $success = new Template('.app/system/helper/alert/success', 'alert-success' . $key);
                        $success->assign(['message' => $value['message']]);
                        array_push($this->content, $success->show());
                        break;
                }

                if ($value['time'] >= 0) {
                    unset($_SESSION['alert'][$key]);
                }
                else {
                    $_SESSION['alert'][$key]['time']++;
                }
            }
        }

        return $this->content;
    }
}