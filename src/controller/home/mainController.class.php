<?php
	class mainController extends baseController{
		function indexAction(){
			$user=new Model();	//实例化一个空模型
			$data=$user->table('categorys')->select('*');//通过table设置表来进行查询
			$this->assign('cate',$data);				//赋值到模版
			$users=new usersModel();					//实例化自定义模型
			$data=$users->select('*');					//此时已经自动关联users表
			$this->assign('users',$data);				//赋值到模版
			$this->display();
		}
	}