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
		$data=$model->setOrder('total desc,id asc')->getList(array(),$this->pageSize,$p);
		$this->assign('list',$data['list']);
		$this->assign('page',$data['page']);
		$this->display();
	}

	/*
	* 查看签到详情
	*/
	function infoAction(){
		$model=new sevenListModel();
		$map['openid']=$_GET['id'];
		$map['ORDER']='addtime asc';
		$info=$model->select('*',$map);
		$this->assign('total',count($info));
		$this->assign('info',$info);
		$this->display();
	}
}