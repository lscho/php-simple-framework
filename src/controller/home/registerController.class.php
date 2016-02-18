<?php
	//登录
	class registerController extends baseController{
		function indexAction(){
			if($this->ispost()){	//处理请求
				
			}else{					//渲染模版
				$this->display();			
			}
		}
		//发送邮箱验证
		function emailAction(){
			$pattern="/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
			preg_match($pattern,$_POST['email'])||$this->json("请输入有效的邮箱地址！",0);
			$verif=new verif();
			$code=$verif->generate();
			$str="您的验证码为".$code;
			$data['email']=$_POST['email'];
			$data['str']=$str;
			$this->add_use(new email());
			$this->register_use('REGISTER',$data);
		}
		//发送短信验证
		function smsAction(){
			$pattern="/^1[34578]\d{9}$/";
			preg_match($pattern,$_POST['mobile'])||$this->json("请输入有效的手机号码！",0);			
			$verif=new verif();
			$data['mobile']=$_POST['mobile'];
			$data['code']=$verif->generate();
			$this->add_use(new sms());
			$this->register_use('REGISTER',$data);					
		}
	}