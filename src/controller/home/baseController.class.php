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
			$this->assign('__basefile',BASE_FILE);
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
		//检测参数是否为空
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