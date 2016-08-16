<?php
/**
* 七天打卡
*/
class signController extends baseController{

	/*
	* 打卡列表
	*/
	function sevenAction(){
		$p=empty($_GET['p'])?1:$_GET['p'];
		$model=new sevenUserModel();
		$data=$model->getList(array(),$this->pageSize,$p);
		$this->assign('list',$data['list']);
		$this->assign('page',$data['page']);
		$this->display();
	}
}