<?php
	//登录
	class loginController extends Controller{
		function indexAction(){
			if($this->ispost()){	//处理请求
				
			}else{					//渲染模版
				if(empty($_GET['ajax'])){
					$this->display();
				}else{
					$this->display('ajax_login');
				}				
			}
		}
	}