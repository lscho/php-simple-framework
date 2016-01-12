<?php
	//核心
	class APP{
		public static $config;
		public static $__module;
		public static $__controller;
		public static $__api;
		public static $__action;	
		//启动
		static function run(){
			//初始化
			APP::init();
			//路由分发
			APP::route();
		}
		//初始化
		static function init(){
			//加载配置
			if(empty(APP::$config)&&file_exists('./config.php')) APP::$config=include './config.php';
		}
		//路由
		static function route(){
			if(!empty(APP::$config['rewrite'])){
				if( ($pos = strpos( $_SERVER['REQUEST_URI'], '?' )) !== false )
					parse_str( substr( $_SERVER['REQUEST_URI'], $pos + 1 ), $_GET );
				foreach(APP::$config['rewrite'] as $rule => $mapper){
					if('/' == $rule)$rule = '';
					if(0!==stripos($rule, 'http://'))
						$rule = 'http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\') .'/'.$rule;
					$rule = '/'.str_ireplace(array('\\\\', 'http://', '/', '<', '>',  '.'), 
						array('', '', '\/', '(?<', '>\w+)', '\.'), $rule).'/i';
					if(preg_match($rule, 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $matchs)){
						$route = explode("/", $mapper);
						if(isset($route[2])){
							list($_GET['m'], $_GET['c'], $_GET['a']) = $route;
						}else{
							list($_GET['c'], $_GET['a']) = $route;
						}
						foreach($matchs as $matchkey => $matchval){
							if(!is_int($matchkey))$_GET[$matchkey] = $matchval;
						}
						break;
					}
				}
				$parameter=str_replace($matchs[0],"",rtrim($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],APP::$config['url']['suffix']));
				$parames=explode('/',$parameter);
				if($_GET['c']==APP::$config['api']['file']&&isset($parames[1])){
					$_GET['id']=$parames[1];
				}else{
					for ($i=1; $i <count($parames) ; $i=$i+2) { 
						if(!empty($parames[$i]))$_GET[$parames[$i]]=$parames[$i+1];
					}
				}
			}
			if($_GET['c']==APP::$config['api']['file']){
				$class=$_GET['a'].'Rest';
				$action=strtolower($_SERVER['REQUEST_METHOD']).'Action';
				APP::$__api=$_GET['c'];
				APP::$__action=$_GET['a'];
			}else{
				APP::$__module=$_GET['m'];
				APP::$__controller=$_GET['c'];
				APP::$__action=$_GET['a'];
				$class=$_GET['c'].'Controller';
				$action=$_GET['a'].'Action';
			}
			$obj=new $class;
			$obj->$action();
		}
		//错误提示
		static function error($msg){
			$msg=APP_DEBUG?$msg:'error';
			exit($msg);
		}
		//自动加载
		static function classLoader($classname){
			//查找路径
			$file=array(
				'Rest'=>APP_FILE.'rest/',
				'Model'=>APP_FILE.'model/',
				'Controller'=>APP_FILE.'controller/',
			);
			if(!empty(APP::$__module))$file['Controller']=$file['Controller'].APP::$__module.'/';
			foreach($file as $k=>$v){
				if(strstr($classname,$k)){
					APP::load($v.$classname.'.class.php');
					break;
				}
			}
		}
		//手动加载
		static function load($file){
			if(file_exists($file)){
				include $file;
			}else{
				APP::error('File not found:'.$flie);
			}
		}
	}
	//注册自动加载
	spl_autoload_register('APP::classLoader');
	//模型
	require_once LIB_FILE.'db.php';
	class Model extends db{
		function __construct(){
			$this->table=str_replace("Model","",get_class($this));
			parent::__construct(APP::$config['db']);
		}
		function table($table){
			$this->table=$table;
			return $this;
		}
	}
	//视图
	class View{
	    //编译模版
	    function parse($tpl,$module=""){
	        $fp   = @fopen($tpl, 'r');
	        $text = fread($fp, filesize($tpl));
	        fclose($fp);
	        $text        = str_replace('{/if}', '<?php } ?>', $text);
	        $text        = str_replace('{/loop}', '<?php } ?>', $text);
	        $text        = str_replace('{foreachelse}', '<?php } else {?>', $text);
	        $text        = str_replace('{/foreach}', '<?php } ?>', $text);
	        $text        = str_replace('{else}', '<?php } else {?>', $text);
	        $text        = str_replace('{loopelse}', '<?php } else {?>', $text);
	        $pattern     = array(
	            '/\$(\w*[a-zA-Z0-9_])/',
	            '/\$this\-\>vars\[\'(\w*[a-zA-Z0-9_])\'\]+\.(\w*[a-zA-Z0-9])/',
	            '/\{include file=(\"|\'|)(\w*[a-zA-Z0-9_\.][a-zA-Z]\w*)(\"|\'|)\}/',
	            '/\{\$this\-\>vars(\[\'(\w*[a-zA-Z0-9_])\'\])(\[\'(\w*[a-zA-Z0-9_])\'\])?\}/',
	            '/\{if (.*?)\}/',
	            '/\{elseif (.*?)\}/',
	            '/\{loop \$(.*) as (\w*[a-zA-Z0-9_])\}/',
	            '/\{foreach \$(.*) (\w*[a-zA-Z0-9_])\=\>(\w*[a-zA-Z0-9_])\}/'
	        );
	        $replacement = array(
	            '$this->vars[\'\1\']',
	            '$this->vars[\'\1\'][\'\2\']',
	            '<?php $this->display(\'\2\')?>',
	            '<?php echo \$this->vars\1\3?>',
	            '<?php if(\1) {?>',
	            '<?php } elseif(\1) {?>',
	            '<?php if (count((array)\$\1)) foreach((array)\$\1 as \$this->vars[\'\2\']) {?>',
	            '<?php if (count((array)\$\1)) foreach((array)\$\1 as \$this->vars[\'\2\']=>$this->vars[\'\3\']) {?>'
	        );
	        $text = preg_replace($pattern, $replacement, $text);
	        $compliefile = APP_FILE.APP::$config['app']['runtime_file'].$module.md5(basename($tpl,'.html')) . '.php';
	        if ($fp = @fopen($compliefile, 'w')) {
	            fputs($fp, $text);
	            fclose($fp);
	        }
	    }
	}
	//控制器
	class Controller{
		private $_v;
		private $vars = array();
		function assign($k,$v =null){
        	$this->vars[$k] = $v;
    	}
	   function display($tpl=0){
	   		$module=empty(APP::$__module)?"":APP::$__module.'/';
	   		$tpl=$tpl?$tpl:APP::$__controller.'_'.APP::$__action;
	        $tplfile = APP_FILE.APP::$config['app']['view_file'].$module. $tpl.'.html';
	        if (!file_exists($tplfile)) APP::error('can not load template file : ' . $tplfile);
	        $compliefile = APP_FILE.APP::$config['app']['runtime_file'].$module.md5($tpl).'.php';	//缓存文件
	        if (!file_exists($compliefile) || filemtime($tplfile) > filemtime($compliefile)) {
	        	$_v=new View();
	            $_v->parse($tplfile,$module);
	        }
	        include_once($compliefile);
	    }
	}
	//Rest
	class Rest{
		function json($data=array(),$status=200,$err=''){
			header( 'HTTP/1.1 '.$status.' '.$srr);
		}
	}
?>