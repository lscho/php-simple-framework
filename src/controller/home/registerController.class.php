<?php
	//注册
	class registerController extends baseController{
		function indexAction(){
			if($this->ispost()){	//处理请求
				//表单验证
				$param=array(
					'mobile'=>'手机号码不能为空',
					'valicode'=>'验证码不能为空',
					'nickname'=>'昵称不能为空',
					'password'=>'密码不能为空',
				);
				$isempty=$this->isempty($param);
				$isempty['err']&&$this->json($isempty['err_msg'],0);
				//验证码
				$verif=new verif();
				$verif->detection($_POST['valicode'])||$this->json("验证码不正确",0);
				unset($_POST['valicode']);
				//添加用户
				$user=new userModel();
				$rs=$user->add($_POST);
				if(!empty($rs['err_msg'])){
					$this->json($rs['err_msg'],0);
				}else{
					$data=$_POST;
					$data['uid']=$rs['uid'];
					$this->register_use('REGISTER',$data);
					$this->json('注册成功');
				}
			}else{					//渲染模版
				empty($_SESSION['userInfo'])||$this->jump(BASE_FILE.'/');		
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
			$sms=new sms();
			$rs=$sms->use_register($data);
			if($rs){
				$this->register_use('REGISTER_SEND_SMS',$data);
				$this->json('发送成功');
			}else{
				$this->json('发送失败');
			}							
		}
	}