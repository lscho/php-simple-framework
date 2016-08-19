<?php
/**
* 管理员
*/
class adminController extends baseController{
	
	function __construct(){
		parent::__construct();
		$this->model=new adminModel();
	}

	/*
	* 获取管理员列表
	*/
	function listAction(){
		$p=empty($_GET['p'])?1:$_GET['p'];
		$data=$this->model->getList(array(),$this->pageSize,$p);
		$this->assign('page',$data['page']);
		$this->assign('list',$data['list']);
		$this->display();
	}

	/*
	* 添加管理员
	*/
	function addAction(){
		if($this->isGet()){
			if(!empty($_GET['id'])){
				$info=$this->model->get('*',array('id'=>$_GET['id']));
				$this->assign('info',$info);
			}
			$this->display();
		}else{
			if(empty($_GET['id'])){
				//判断用户名是否存在
				if($this->model->has(array('username'=>$_POST['username']))){
					$this->json(lang('admin_user_has'),0);
				}
				//判断手机号码是否存在
				if($this->model->has(array('mobile'=>$_POST['mobile']))){
					$this->json(lang('admin_mobile_has'),0);
				}				
				//添加管理员
				$data=$_POST;
				$data['password']=md5(md5($data['password']).'es');
				$data['regtime']=time();
				$rs=$this->model->insert($data);
			}else{
				if(empty($_POST['password'])){
					unset($_POST['password']);
				}else{
					$_POST['password']=md5(md5($_POST['password']).'es');
				}
				//更新管理员
				$rs=$this->model->update($_POST,array('id'=>$_GET['id']));
			}
			if($rs){
				$this->json(lang('action_success'));
			}else{
				$this->json(lang('action_error'),0);
			}			
		}
	}
}