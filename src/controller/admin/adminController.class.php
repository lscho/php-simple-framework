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

			$this->display();
		}else{
			
		}
	}
}