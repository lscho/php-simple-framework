
**简介** 

一个简单的MVC框架，完成了路由、自动加载、模型、模版引擎、缓存等功能。[核心代码](https://github.com/eyblog/mvc/blob/master/lib/system.php)160行左右

**实例**

```php
<?php
    class mainController extends baseController{
        function indexAction(){
            $user=new Model();  //实例化一个空模型
            $data=$user->table('categorys')->select('*');//通过table设置表来进行查询
            $this->assign('cate',$data);                //赋值到模版
            $users=new usersModel();                    //实例化自定义模型
            $data=$users->select('*');                  //此时已经自动关联users表
            $this->assign('users',$data);               //赋值到模版
            $this->display();                           //加载模版
        }
    }
```

**路由**
  
  ```php
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
      }
      $_GET['m']=!empty($_GET['m'])?$_GET['m']:'home';
      APP::$__module=$_GET['m'];
      APP::$__controller=$_GET['c'];
      APP::$__action=$_GET['a'];
            $obj = new ReflectionClass($_GET['c'].'Controller');
            if($obj->hasMethod($_GET['a'].'Action')){
                $instance =$obj->newInstanceArgs();
                $obj->getmethod($_GET['a'].'Action')->invoke($instance);
            }else{
                APP::error($class.' not has Action:'.$_GET['a']);
      }
  ```

**控制器**

  基本没写东西，display 方法编译模版,assign 输出变量到模版。

```php
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
          $compliefile = APP_FILE.APP::$config['app']['runtime_file'].$module.md5($tpl).'.php'; //缓存文件
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
```
  配置伪静态
```php
    'rewrite' => array(
      't/<sign>'        =>'topic/read',
      'admin/<c>/<a>'       => 'admin/<c>/<a>', 
      '<c>/<a>'             => '<c>/<a>',
      '<c>'               => '<c>/index',
      '/'                   => 'main/index',
    ),
```
**模型**

  模型层基于 [medoo](http://medoo.in/),在 medoo 的基础上进行修改，实现了模型与数据库表的自动绑定。下面是一个多表联查的实例

  ```php
      $db=new Model();          //实例化自定义模型
      $join=array("[>]ey_tags" => array("tid" => "id"));  //关联表
      $field=array("ey_contents.title","ey_tags.name"); //查询字段
      $map=array('ey_contents.id'=>1);          //构造条件
      $data=$db->table('contents')->select($join,$field,$map);    
      var_dump($db->log()); //SELECT "ey_contents"."title","ey_tags"."name" FROM "ey_contents" LEFT JOIN "ey_tags" ON "ey_contents"."tid" = "ey_tags"."id" WHERE "ey_contents"."id" = 1
      var_dump($data);
  ```

**视图**

  模版引擎参考smarty的语法写了几条规则，只有简单的循环和判断。

```php
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
```


*其实都是百度谷歌抄的*