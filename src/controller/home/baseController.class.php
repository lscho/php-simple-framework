<?php
	//控制器基类
	class baseController extends Controller{
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
				$base=new baseModel();
				$_SESSION['baseinfo']=$base->getinfo();
			}
			$this->assign('baseinfo',$_SESSION['baseinfo']);
			$this->assign('__basefile',BASE_DIR);
			//获取网站配置
			if(empty($_SESSION['web_config'])){
				$_SESSION['web_config']=array(
					'point'=>array(
						'post'=>10,
						'reply'=>5
					)
				);
			}
			//获取最新文章
			$topic=new topicModel();
			$newTopic=$topic->getNew();
			$this->assign('newTopic',$newTopic);			
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
		//插件机制
        function register_use($behavior,$data){
			$handle=opendir(APP_FILE.'common/plugins/');
            while($file=readdir($handle)) {
                if (($file!=".")&&($file!="..")&&strstr($file,'.class.php')){
                    $classname=str_replace('.class.php','',$file);
                    include APP_FILE.'common/plugins/'.$file;
                    $obj = new ReflectionClass($classname);
                    if($obj->hasMethod('notify')){
                        $instance =$obj->newInstanceArgs();
                        $obj->getmethod('notify')->invoke($instance,array('behavior'=>$behavior,'data'=>$data));
                    }
                }
            }
            closedir($handle);
        }		
	}