<?php
	//控制器基类
	class baseController extends Controller{
		public $_use=array();
		//常用数据可以使用radis优化
		function __construct(){
			//获取标签列表
			if(empty($_SESSION['tab'])){
				$tab=new tabModel();
				$_SESSION['tab']=$tab->getList();
			}
			$this->assign('tab',$_SESSION['tab']);
			//获取基础信息
			if(empty($_SESSION['baseinfo'])){
				$base=new BaseModel();
				$_SESSION['baseinfo']=$base->getinfo();
			}
			$this->assign('baseinfo',$_SESSION['baseinfo']);
		}
		//跳转
		function jump($url){
			header("Location: $url"); 
		}
		//输出json
		function json($msg="",$status=1,$url=""){
			header('Content-type:text/json');
			die(json_encode(array("msg"=>$msg,"status"=>$status,"url"=>$url)));
		}
		function add_use($obj){
			if(is_array($obj)){
				$this->_use=array_merge($this->_use,$obj);
			}else{
				$this->_use[]=$obj;
			}
		}
		function register_use($behavior,$data){
			foreach ($this->_use as $v) {
				$v->notify($behavior,$data);
			}
		}
	}