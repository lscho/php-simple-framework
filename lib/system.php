<?php
	//核心
	class APP{
		public static $config;
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
			//加载系统函数库
			include LIB_FILE.'function.php';
			//加载用户函数库
			if(file_exists(APP_FILE.'common/function.php')) include APP_FILE.'common/function.php';
		}
		//路由
		static function route(){
			$route=explode('/', $_SERVER['PATH_INFO']);
			if($route[1]==self::$config['api']['name']){
				$class=$route[2].'Rest';
				$action=strtolower($_SERVER['REQUEST_METHOD']).'Action';
				APP::$__api=$route[2];
				APP::$__action=strtolower($_SERVER['REQUEST_METHOD']);
			}else{
				$class=empty($route[1])?'indexController':strtolower($route[1]).'Controller';
				$action=empty($route[2])?'indexAction':strtolower($route[2]).'Action';
				APP::$__controller=empty($route[1])?'index':strtolower($route[1]);
				APP::$__action=empty($route[2])?'index':strtolower($route[2]);
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
	    function parse($tpl){
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
	        $compliefile = APP_FILE.APP::$config['app']['runtime_file'].md5(basename($tpl,'.html')) . '.php';
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
	   		$tpl=$tpl?$tpl:APP::$__controller.'_'.APP::$__action;
	        $tplfile = APP_FILE.APP::$config['app']['view_file']. $tpl.'.html';
	        if (!file_exists($tplfile)) APP::error('can not load template file : ' . $tplfile);
	        $compliefile = APP_FILE.APP::$config['app']['runtime_file'].md5($tpl).'.php';	//缓存文件
	        if (!file_exists($compliefile) || filemtime($tplfile) > filemtime($compliefile)) {
	        	$_v=new View();
	            $_v->parse($tplfile);
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