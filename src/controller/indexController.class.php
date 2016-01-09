<?php
	class indexController extends baseController{
		function indexAction(){
			$user=new usersModel();
			$rs=$user->select('*');
			$this->assign('title','rccoder sb');
			$this->display();
		}
	}