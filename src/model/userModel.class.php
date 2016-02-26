<?php  
	class userModel extends Model{
		//添加用户
		function add($data){
			$data['password']=md5(md5($data['password']).'es');
			//检测手机号码是否存在
			if(!empty($data['mobile'])){
				$this->has(array('mobile'=>$data['mobile']))&&$err_msg="手机号码已存在";
			}
			//检测邮箱是否存在
			if(!empty($data['email'])){
				$this->has(array('email'=>$data['email']))&&$err_msg="邮箱已存在";
			}			
			//检测用户名是否存在
			$this->has(array('nickname'=>$data['nickname']))&&$err_msg="昵称已存在";
			//添加用户
			if(empty($err_msg)){
				$last_user_id=$this->insert($data);
				$last_user_id||$err_msg="注册失败";
			}
			return array('err_msg'=>$err_msg,'uid'=>$last_user_id);
		}
		//登录
		function login($data){
			$userInfo=$this->get('*',array('nickname'=>$data['nickname']));
			if($userInfo){
				($userInfo['password']==md5(md5($data['password']).'es'))||$err_msg='密码不正确';
			}else{
				$err_msg='用户名不存在';
			}
			return array('err_msg'=>$err_msg,'userInfo'=>$userInfo);
		}
		//增加积分
		function setPoint($uid,$point=0){
			$rs=$this->update(array("reply[+]" => $point),array('uid'=>$uid));
			return $rs;	
		}
	}