<?php
	class mainController extends baseController{
		function indexAction(){
			$db=new Model();					//实例化自定义模型
			$join=array("[>]ey_tags" => array("tid" => "id"));	//关联表
			$field=array("ey_contents.title","ey_tags.name");	//查询字段
			$map=array('ey_contents.id'=>1);					//构造条件
			$data=$db->table('contents')->select($join,$field,$map);	
			$this->assign('cate',$data);	
			$this->display();
		}
	}