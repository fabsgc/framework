<?php

namespace Helper\Alert;

use System\General\error;
use System\General\facades;
use System\Helper\Helper;


class Alert extends Helper{
	use error, facades;

	private $content = array();

	public function __construct($data = array()){
	}

	public function add($type, $message){
		if(!$_SESSION['alert']){
			$_SESSION['alert'] = array();
		}

		$alert = array('type' => $type, 'message' => $message, 'time' => 0);
		array_push($_SESSION['alert'], $alert);
	}

	public function view(){
		if(isset($_SESSION['alert']) && count($_SESSION['alert']) > 0){
			foreach($_SESSION['alert'] as $key => $value)
			{
				switch($value['type']){
					case 'info' :
						$info = self::Template('.app/system/helper/alert/info', 'alert-info-'.$key);
						$info->assign('message',$value['message']);
						array_push($this->content,$info->show());
					break;

					case 'danger' :
						$danger = self::Template('.app/system/helper/alert/danger', 'alert-danger'.$key);
						$danger->assign('message',$value['message']);
						array_push($this->content,$danger->show());
					break;

					case 'error' :
						$error = self::Template('.app/system/helper/alert/error', 'alert-error'.$key);
						$error->assign('message',$value['message']);
						array_push($this->content,$error->show());
					break;

					case 'success' :
						$success = self::Template('.app/system/helper/alert/success', 'alert-success'.$key);
						$success->assign(array('message'=>$value['message']));
						array_push($this->content,$success->show());
					break;
				}

				if($value['time'] >= 0){
					unset($_SESSION['alert'][$key]);
				}
				else{
					$_SESSION['alert'][$key]['time']++;
				}
			}
		}
		return $this->content;
	}
}