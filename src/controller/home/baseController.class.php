<?php
	//控制器基类
	class baseController extends Controller{
		function __construct(){
			//获取标签列表
			if(empty($_SESSION['tab'])){
				$tab=new tabModel();
				$_SESSION['tab']=$tab->getList();
			}
			$this->assign('tab',$_SESSION['tab']);
		}
	}