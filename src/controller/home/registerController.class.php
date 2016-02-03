<?php
	//登录
	class registerController extends baseController{
		function indexAction(){
			if($this->ispost()){	//处理请求
				
			}else{					//渲染模版
				$this->display();			
			}
		}
		function emailAction(){
			$verif=new verif();
			$code=$verif->generate();
			$email=new email();
			$str="您的验证码为".$code;
			//$this->register_use('LOGIN_EMAIL',$_POST['email']);
		}
	}