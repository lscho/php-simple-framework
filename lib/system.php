<?php
	class APP{
		public static $config;
		public static $__module;
		public static $__controller;
		public static $__api;
		public static $__action;	
		static function run(){
			error_reporting(E_ALL || ~E_NOTICE);
			APP::init();
			APP::route();
		}
		static function init(){
			if(empty(APP::$config)&&file_exists('./config.php')) APP::$config=include './config.php';
		}
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
			$_GET['m']=!empty($_GET['m'])?$_GET['m']:'home';
			APP::$__module=$_GET['m'];
			APP::$__controller=$_GET['c'];
			APP::$__action=$_GET['a'];
			$class=$_GET['c'].'Controller';
			$action=$_GET['a'].'Action';
			$obj=new $class;
			$obj->$action();
		}
		static function error($msg){
			header("Content-Type:text/html;charset=utf8");
			APP_DEBUG||$msg='error';
			exit($msg);
		}
		static function classLoader($classname){
			$file=array('Model'=>APP_FILE.'model/','Controller'=>APP_FILE.'controller/');
			if(!empty(APP::$__module))$file['Controller']=$file['Controller'].APP::$__module.'/';
			foreach($file as $k=>$v){
				if(strstr($classname,$k)){
					APP::load($v.$classname.'.class.php');
					break;
				}
			}
		}
		static function load($file){
			if(file_exists($file)){
				include $file;
			}else{
				APP::error('File not found:'.$flie);
			}
		}
	}
	spl_autoload_register('APP::classLoader');
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
	class View{
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
	        $basefile=APP_FILE.APP::$config['app']['runtime_file'];
	        is_dir($basefile)||mkdir($basefile,0777);
	        if(!empty($module)&&!is_dir($basefile.$module))mkdir($basefile.$module,0777);
	        $compliefile = $basefile.$module.md5(basename($tpl,'.html')) . '.php';
	        if ($fp = @fopen($compliefile, 'w')) {
	            fputs($fp, $text);
	            fclose($fp);
	        }
	    }
	}
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
	    function __call($method,$arg){
	    	if(in_array(strtolower($method),array('ispost','isget','ishead','isdelete','isput'))){
	    		return strtolower($_SERVER['REQUEST_METHOD']) == strtolower(substr($method,2));
	    	}
	    }
	}
?>