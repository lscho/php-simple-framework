<?php
	//登录
	class loginController extends baseController{
		function indexAction(){
			if($this->ispost()){	//处理请求
				//表单验证
				$param=array(
					'nickname'=>'昵称不能为空',
					'password'=>'密码不能为空',
				);
				$isempty=$this->isempty($param);
				$isempty['err']&&$this->json($isempty['err_msg'],0);
				//登录
				$user=new userModel();
				$rs=$user->login(array(
					'nickname'=>$_POST['nickname'],
					'password'=>$_POST['password']
				));
				if(!empty($rs['err_msg'])){
					$this->register_use('LOGIN_ERROR',$rs);
					$this->json($rs['err_msg'],0);
				}else{
					$_SESSION['userInfo']=$rs['userInfo'];
					$this->register_use('LOGIN_SUCCESS',$rs);
					$this->json('登录成功',1);
				}
			}else{					//渲染模版
				empty($_SESSION['userInfo'])||$this->jump(BASE_FILE.'/');
				$this->display();
			}
		}
		function outAction(){
			unset($_SESSION['userInfo']);
			$this->register_use('LOGIN_OUT',$rs);
			$this->jump($_SERVER['HTTP_REFERER']);			
		}
	}