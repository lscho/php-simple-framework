<?php
	//控制器基类
	class baseController extends Controller{
		public $use=array();
		function __construct(){
			//获取标签列表
			if(empty($_SESSION['tab'])){
				$tab=new tabModel();
				$_SESSION['tab']=$tab->getList();
			}
			$this->assign('tab',$_SESSION['tab']);
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
				$this->use=array_merge($this->use,$obj);
			}else{
				$this->use[]=$obj;
			}
		}
		function register_use($behavior,$data){
			foreach ($this->use as $v) {
				$v->notify($behavior,$data);
			}
		}
	}