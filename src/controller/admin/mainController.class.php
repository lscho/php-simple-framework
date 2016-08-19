<?php
/*
* main控制器
*/
class mainController extends baseController{

	/*
	* 首页
	*/
	function indexAction(){
		//数量
		$model=new model();
		$total['admin']=$model->table('admin')->count('*');
		$count=$model->table('seven_user')->select('*',array('GROUP'=>'openid'));
		$total['sign']=count($count);
		$this->assign('total',$total);

		//最新签到
		$map['LIMIT']=5;
		$map['ORDER']='addtime desc';
		$list=$model->table('seven_user')->select('*',$map);
		$this->assign('list',$list);
		$this->display();
	}
}