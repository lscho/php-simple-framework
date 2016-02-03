<?php
	/**
	* 验证码
	*/
	class verif{
		private $length;	//验证码长度
		private $is_int;	//是否纯数字验证码
		function __construct($length=6,$is_int=0){
			$this->length=$length;
			$this->is_int=$is_int;
		}
		/**
		* 生成方法
		* generate(time：时间[s])
		* return;
		*/		
		function generate($time=0){
			if(!empty($_SESSION['_verif'])){
				$_verif=unserialize($_SESSION['_verif']);
				if(time()<($_verif['settime']+$_verif['time'])){
					return false;
				}
			}
			$chars = $this->is_int?"abcdefghijklmnopqrstuvwxyz0123456789":"0123456789";
			$str ="";
			for ( $i = 0; $i < $this->length; $i++ )  {  
				$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
			}
			$data['verif']=$str;
			$data['time']=$time;
			$data['settime']=time();
			$_SESSION['_verif']=serialize($data);
			return $str;
		}
		/**
		* 检测方法
		* detection(verif,clean)
		* return bool;
		*/
		function detection($verif=0,$clean=0){
			$_verif=unserialize($_SESSION['_verif']);
			if($clean){
				unset($_SESSION['_verif']);
			}
			return $verif==$_verif['verif'];
		}
	}
?>