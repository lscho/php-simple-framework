<?php
	/*
	* admin 模块控制器基类
	*/
	class baseController extends Controller{
		function __construct(){
			if(empty($_SESSION['admin'])){
				$this->jump(__ROOT__.'/admin/login');
			}
			$this->assign('admin',$_SESSION['admin']);
			$this->pageSize=10;
		}
		
		/*
		* 检测参数是否为空
		*/
		function isempty($data=array(),$method='post'){
			if($method=='post'){
				$param=$_POST;
			}elseif($method=='get'){
				$param=$_GET;
			}
			foreach($data as $k=>$v){
				if(empty($param[$k])){
					return array('err'=>1,'err_msg'=>$v);
					break;
				}
			}
			return array('err'=>0,'err_msg'=>"");
		}			
	}