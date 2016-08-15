<?php
/*
* 登录
*/
	class loginController extends Controller{
		function indexAction(){
			if($this->isGet()){
				$this->display();
			}else{
				//表单验证
				if(empty($_POST['username'])){
					$this->json(lang('admin_user_empty'),0);	
				}
				if(empty($_POST['password'])){
					$this->json(lang('admin_passwd_empty'),0);
				}				
				$map['username']=$_POST['username'];
				$admin=new adminModel();
				
				$adminInfo=$admin->getInfo($map);
				if(!$adminInfo){
					$this->json(lang('admin_empty'),0);
				}
				if(md5(md5($_POST['password']).'es')!=$adminInfo['password']){
					$this->json(lang('admin_passwd_error'),0);
				}
				$_SESSION['admin']=$adminInfo;
				$this->json(lang('admin_login_success'),1);
			}
		}
	}