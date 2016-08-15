<?php
/*
* 系统设置
*/
class systemController extends baseController{
	
	function __construct(){
		parent::__construct();
	}

	/*
	*系统基础设置
	*/
	function indexAction(){
		$system= new systemModel();
		if($this->isGet()){
			$info=$system->getInfo(array('id'=>1));
			$this->assign('web',json_decode($info['web'],true));
			$this->display();
		}else{
			$web=json_encode($_POST['web']);
			$rs=$system->update(array('web'=>$web),array('id'=>1));
			$rs?$this->json(lang('action_success')):$this->json(lang('action_error'),0);
		}
	}
}