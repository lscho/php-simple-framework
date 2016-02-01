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
			if(empty(APP::$config)&&file_exists(APP_FILE.'config.php')) APP::$config=include APP_FILE.'config.php';
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
	class Model extends db{	//基于medoo[http://medoo.in]
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
class db {
    protected $database_type;
    protected $charset;
    protected $database_name;
    protected $server;
    protected $username;
    protected $password;
    protected $database_file;
    protected $socket;
    protected $port;
    protected $prefix;
    protected $option = array();
    protected $logs = array();
    protected $debug_mode = false;
    protected $table;
    public function __construct($options = null) {
        try {
            $commands = array();
            $dsn = '';
            if (is_array($options)) {
                foreach ($options as $option => $value) {
                    $this->$option = $value;
                }
            } else {
                return false;
            }
            if (isset($this->port) && is_int($this->port * 1)) {
                $port = $this->port;
            }
            $type = strtolower($this->database_type);
            $is_port = isset($port);
            if (isset($options['prefix'])) {
                $this->prefix = $options['prefix'];
            }
            switch ($type) {
                case 'mariadb':
                    $type = 'mysql';
                case 'mysql':
                    if ($this->socket) {
                        $dsn = $type . ':unix_socket=' . $this->socket . ';dbname=' . $this->database_name;
                    } else {
                        $dsn = $type . ':host=' . $this->server . ($is_port ? ';port=' . $port : '') . ';dbname=' . $this->database_name;
                    }
                    $commands[] = 'SET SQL_MODE=ANSI_QUOTES';
                    break;
                case 'pgsql':
                    $dsn = $type . ':host=' . $this->server . ($is_port ? ';port=' . $port : '') . ';dbname=' . $this->database_name;
                    break;
                case 'sybase':
                    $dsn = 'dblib:host=' . $this->server . ($is_port ? ':' . $port : '') . ';dbname=' . $this->database_name;
                    break;
                case 'oracle':
                    $dbname = $this->server ? '//' . $this->server . ($is_port ? ':' . $port : ':1521') . '/' . $this->database_name : $this->database_name;
                    $dsn = 'oci:dbname=' . $dbname . ($this->charset ? ';charset=' . $this->charset : '');
                    break;
                case 'mssql':
                    $dsn = strstr(PHP_OS, 'WIN') ? 'sqlsrv:server=' . $this->server . ($is_port ? ',' . $port : '') . ';database=' . $this->database_name : 'dblib:host=' . $this->server . ($is_port ? ':' . $port : '') . ';dbname=' . $this->database_name;
                    $commands[] = 'SET QUOTED_IDENTIFIER ON';
                    break;
                case 'sqlite':
                    $dsn = $type . ':' . $this->database_file;
                    $this->username = null;
                    $this->password = null;
                    break;
            }
            if (in_array($type, explode(' ', 'mariadb mysql pgsql sybase mssql')) && $this->charset) {
                $commands[] = "SET NAMES '" . $this->charset . "'";
            }
            $this->pdo = new PDO($dsn, $this->username, $this->password, $this->option);
            foreach ($commands as $value) {
                $this->pdo->exec($value);
            }
        }
        catch(PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function query($query) {
        if ($this->debug_mode) {
            echo $query;
            $this->debug_mode = false;
            return false;
        }
        array_push($this->logs, $query);
        return $this->pdo->query($query);
    }
    public function exec($query) {
        if ($this->debug_mode) {
            echo $query;
            $this->debug_mode = false;
            return false;
        }
        array_push($this->logs, $query);
        return $this->pdo->exec($query);
    }
    public function quote($string) {
        return $this->pdo->quote($string);
    }
    protected function column_quote($string) {
        return '"' . str_replace('.', '"."', preg_replace('/(^#|\(JSON\)\s*)/', '', $string)) . '"';
    }
    protected function column_push($columns) {
        if ($columns == '*') {
            return $columns;
        }
        if (is_string($columns)) {
            $columns = array(
                $columns
            );
        }
        $stack = array();
        foreach ($columns as $key => $value) {
            preg_match('/([a-zA-Z0-9_\-\.]*)\s*\(([a-zA-Z0-9_\-]*)\)/i', $value, $match);
            if (isset($match[1], $match[2])) {
                array_push($stack, $this->column_quote($match[1]) . ' AS ' . $this->column_quote($match[2]));
            } else {
                array_push($stack, $this->column_quote($value));
            }
        }
        return implode($stack, ',');
    }
    protected function array_quote($array) {
        $temp = array();
        foreach ($array as $value) {
            $temp[] = is_int($value) ? $value : $this->pdo->quote($value);
        }
        return implode($temp, ',');
    }
    protected function inner_conjunct($data, $conjunctor, $outer_conjunctor) {
        $haystack = array();
        foreach ($data as $value) {
            $haystack[] = '(' . $this->data_implode($value, $conjunctor) . ')';
        }
        return implode($outer_conjunctor . ' ', $haystack);
    }
    protected function fn_quote($column, $string) {
        return (strpos($column, '#') === 0 && preg_match('/^[A-Z0-9\_]*\([^)]*\)$/', $string)) ? $string : $this->quote($string);
    }
    protected function data_implode($data, $conjunctor, $outer_conjunctor = null) {
        $wheres = array();
        foreach ($data as $key => $value) {
            $type = gettype($value);
            if (preg_match("/^(AND|OR)(\s+#.*)?$/i", $key, $relation_match) && $type == 'array') {
                $wheres[] = 0 !== count(array_diff_key($value, array_keys(array_keys($value)))) ? '(' . $this->data_implode($value, ' ' . $relation_match[1]) . ')' : '(' . $this->inner_conjunct($value, ' ' . $relation_match[1], $conjunctor) . ')';
            } else {
                preg_match('/(#?)([\w\.\-]+)(\[(\>|\>\=|\<|\<\=|\!|\<\>|\>\<|\!?~)\])?/i', $key, $match);
                $column = $this->column_quote($match[2]);
                if (isset($match[4])) {
                    $operator = $match[4];
                    if ($operator == '!') {
                        switch ($type) {
                            case 'NULL':
                                $wheres[] = $column . ' IS NOT NULL';
                                break;

                            case 'array':
                                $wheres[] = $column . ' NOT IN (' . $this->array_quote($value) . ')';
                                break;

                            case 'integer':
                            case 'double':
                                $wheres[] = $column . ' != ' . $value;
                                break;

                            case 'boolean':
                                $wheres[] = $column . ' != ' . ($value ? '1' : '0');
                                break;

                            case 'string':
                                $wheres[] = $column . ' != ' . $this->fn_quote($key, $value);
                                break;
                        }
                    }
                    if ($operator == '<>' || $operator == '><') {
                        if ($type == 'array') {
                            if ($operator == '><') {
                                $column.= ' NOT';
                            }
                            if (is_numeric($value[0]) && is_numeric($value[1])) {
                                $wheres[] = '(' . $column . ' BETWEEN ' . $value[0] . ' AND ' . $value[1] . ')';
                            } else {
                                $wheres[] = '(' . $column . ' BETWEEN ' . $this->quote($value[0]) . ' AND ' . $this->quote($value[1]) . ')';
                            }
                        }
                    }
                    if ($operator == '~' || $operator == '!~') {
                        if ($type == 'string') {
                            $value = array(
                                $value
                            );
                        }
                        if (!empty($value)) {
                            $like_clauses = array();
                            foreach ($value as $item) {
                                if (preg_match('/^(?!%).+(?<!%)$/', $item)) {
                                    $item = '%' . $item . '%';
                                }
                                $like_clauses[] = $column . ($operator === '!~' ? ' NOT' : '') . ' LIKE ' . $this->fn_quote($key, $item);
                            }
                            $wheres[] = implode(' OR ', $like_clauses);
                        }
                    }
                    if (in_array($operator, array(
                        '>',
                        '>=',
                        '<',
                        '<='
                    ))) {
                        if (is_numeric($value)) {
                            $wheres[] = $column . ' ' . $operator . ' ' . $value;
                        } elseif (strpos($key, '#') === 0) {
                            $wheres[] = $column . ' ' . $operator . ' ' . $this->fn_quote($key, $value);
                        } else {
                            $wheres[] = $column . ' ' . $operator . ' ' . $this->quote($value);
                        }
                    }
                } else {
                    switch ($type) {
                        case 'NULL':
                            $wheres[] = $column . ' IS NULL';
                            break;

                        case 'array':
                            $wheres[] = $column . ' IN (' . $this->array_quote($value) . ')';
                            break;

                        case 'integer':
                        case 'double':
                            $wheres[] = $column . ' = ' . $value;
                            break;

                        case 'boolean':
                            $wheres[] = $column . ' = ' . ($value ? '1' : '0');
                            break;

                        case 'string':
                            $wheres[] = $column . ' = ' . $this->fn_quote($key, $value);
                            break;
                    }
                }
            }
        }
        return implode($conjunctor . ' ', $wheres);
    }
    protected function where_clause($where) {
        $where_clause = '';
        if (is_array($where)) {
            $where_keys = array_keys($where);
            $where_AND = preg_grep("/^AND\s*#?$/i", $where_keys);
            $where_OR = preg_grep("/^OR\s*#?$/i", $where_keys);
            $single_condition = array_diff_key($where, array_flip(explode(' ', 'AND OR GROUP ORDER HAVING LIMIT LIKE MATCH')));
            if ($single_condition != array()) {
                $where_clause = ' WHERE ' . $this->data_implode($single_condition, '');
            }
            if (!empty($where_AND)) {
                $value = array_values($where_AND);
                $where_clause = ' WHERE ' . $this->data_implode($where[$value[0]], ' AND');
            }
            if (!empty($where_OR)) {
                $value = array_values($where_OR);
                $where_clause = ' WHERE ' . $this->data_implode($where[$value[0]], ' OR');
            }
            if (isset($where['MATCH'])) {
                $MATCH = $where['MATCH'];
                if (is_array($MATCH) && isset($MATCH['columns'], $MATCH['keyword'])) {
                    $where_clause.= ($where_clause != '' ? ' AND ' : ' WHERE ') . ' MATCH ("' . str_replace('.', '"."', implode($MATCH['columns'], '", "')) . '") AGAINST (' . $this->quote($MATCH['keyword']) . ')';
                }
            }
            if (isset($where['GROUP'])) {
                $where_clause.= ' GROUP BY ' . $this->column_quote($where['GROUP']);
                if (isset($where['HAVING'])) {
                    $where_clause.= ' HAVING ' . $this->data_implode($where['HAVING'], ' AND');
                }
            }
            if (isset($where['ORDER'])) {
                $rsort = '/(^[a-zA-Z0-9_\-\.]*)(\s*(DESC|ASC))?/';
                $ORDER = $where['ORDER'];
                if (is_array($ORDER)) {
                    if (isset($ORDER[1]) && is_array($ORDER[1])) {
                        $where_clause.= ' ORDER BY FIELD(' . $this->column_quote($ORDER[0]) . ', ' . $this->array_quote($ORDER[1]) . ')';
                    } else {
                        $stack = array();
                        foreach ($ORDER as $column) {
                            preg_match($rsort, $column, $order_match);
                            array_push($stack, '"' . str_replace('.', '"."', $order_match[1]) . '"' . (isset($order_match[3]) ? ' ' . $order_match[3] : ''));
                        }
                        $where_clause.= ' ORDER BY ' . implode($stack, ',');
                    }
                } else {
                    preg_match($rsort, $ORDER, $order_match);
                    $where_clause.= ' ORDER BY "' . str_replace('.', '"."', $order_match[1]) . '"' . (isset($order_match[3]) ? ' ' . $order_match[3] : '');
                }
            }
            if (isset($where['LIMIT'])) {
                $LIMIT = $where['LIMIT'];
                if (is_numeric($LIMIT)) {
                    $where_clause.= ' LIMIT ' . $LIMIT;
                }
                if (is_array($LIMIT) && is_numeric($LIMIT[0]) && is_numeric($LIMIT[1])) {
                    if ($this->database_type === 'pgsql') {
                        $where_clause.= ' OFFSET ' . $LIMIT[0] . ' LIMIT ' . $LIMIT[1];
                    } else {
                        $where_clause.= ' LIMIT ' . $LIMIT[0] . ',' . $LIMIT[1];
                    }
                }
            }
        } else {
            if ($where != null) {
                $where_clause.= ' ' . $where;
            }
        }
        return $where_clause;
    }
    protected function select_context($table, $join, &$columns = null, $where = null, $column_fn = null) {
        $table = '"' . $this->prefix . $table . '"';
        $join_key = is_array($join) ? array_keys($join) : null;
        if (isset($join_key[0]) && strpos($join_key[0], '[') === 0) {
            $table_join = array();
            $join_array = array(
                '>' => 'LEFT',
                '<' => 'RIGHT',
                '<>' => 'FULL',
                '><' => 'INNER'
            );
            foreach ($join as $sub_table => $relation) {
                preg_match('/(\[(\<|\>|\>\<|\<\>)\])?([a-zA-Z0-9_\-]*)\s?(\(([a-zA-Z0-9_\-]*)\))?/', $sub_table, $match);
                if ($match[2] != '' && $match[3] != '') {
                    if (is_string($relation)) {
                        $relation = 'USING ("' . $relation . '")';
                    }
                    if (is_array($relation)) {
                        // For ['column1', 'column2']
                        if (isset($relation[0])) {
                            $relation = 'USING ("' . implode($relation, '", "') . '")';
                        } else {
                            $joins = array();
                            foreach ($relation as $key => $value) {
                                $joins[] = (strpos($key, '.') > 0 ?
                                '"' . str_replace('.', '"."', $key) . '"' :
                                $table . '."' . $key . '"') . ' = ' . '"' . (isset($match[5]) ? $match[5] : $match[3]) . '"."' . $value . '"';
                            }
                            $relation = 'ON ' . implode($joins, ' AND ');
                        }
                    }
                    $table_join[] = $join_array[$match[2]] . ' JOIN "' . $match[3] . '" ' . (isset($match[5]) ? 'AS "' . $match[5] . '" ' : '') . $relation;
                }
            }
            $table.= ' ' . implode($table_join, ' ');
        } else {
            if (is_null($columns)) {
                if (is_null($where)) {
                    if (is_array($join) && isset($column_fn)) {
                        $where = $join;
                        $columns = null;
                    } else {
                        $where = null;
                        $columns = $join;
                    }
                } else {
                    $where = $join;
                    $columns = null;
                }
            } else {
                $where = $columns;
                $columns = $join;
            }
        }
        if (isset($column_fn)) {
            if ($column_fn == 1) {
                $column = '1';
                if (is_null($where)) {
                    $where = $columns;
                }
            } else {
                if (empty($columns)) {
                    $columns = '*';
                    $where = $join;
                }
                $column = $column_fn . '(' . $this->column_push($columns) . ')';
            }
        } else {
            $column = $this->column_push($columns);
        }
        return 'SELECT ' . $column . ' FROM ' . $table . $this->where_clause($where);
    }
    public function select($join, $columns = null, $where = null) {
        $query = $this->query($this->select_context($this->table, $join, $columns, $where));
        return $query ? $query->fetchAll((is_string($columns) && $columns != '*') ? PDO::FETCH_COLUMN : PDO::FETCH_ASSOC) : false;
    }
    public function insert($datas) {
        $lastId = array();
        if (!isset($datas[0])) {
            $datas = array(
                $datas
            );
        }
        foreach ($datas as $data) {
            $values = array();
            $columns = array();
            foreach ($data as $key => $value) {
                array_push($columns, $this->column_quote($key));
                switch (gettype($value)) {
                    case 'NULL':
                        $values[] = 'NULL';
                        break;
                    case 'array':
                        preg_match("/\(JSON\)\s*([\w]+)/i", $key, $column_match);
                        $values[] = isset($column_match[0]) ? $this->quote(json_encode($value)) : $this->quote(serialize($value));
                        break;
                    case 'boolean':
                        $values[] = ($value ? '1' : '0');
                        break;
                    case 'integer':
                    case 'double':
                    case 'string':
                        $values[] = $this->fn_quote($key, $value);
                        break;
                }
            }
            $this->exec('INSERT INTO "' . $this->prefix . $this->table . '" (' . implode(', ', $columns) . ') VALUES (' . implode($values, ', ') . ')');
            $lastId[] = $this->pdo->lastInsertId();
        }
        return count($lastId) > 1 ? $lastId : $lastId[0];
    }
    public function update($data, $where = null) {
        $fields = array();
        foreach ($data as $key => $value) {
            preg_match('/([\w]+)(\[(\+|\-|\*|\/)\])?/i', $key, $match);
            if (isset($match[3])) {
                if (is_numeric($value)) {
                    $fields[] = $this->column_quote($match[1]) . ' = ' . $this->column_quote($match[1]) . ' ' . $match[3] . ' ' . $value;
                }
            } else {
                $column = $this->column_quote($key);
                switch (gettype($value)) {
                    case 'NULL':
                        $fields[] = $column . ' = NULL';
                        break;

                    case 'array':
                        preg_match("/\(JSON\)\s*([\w]+)/i", $key, $column_match);
                        $fields[] = $column . ' = ' . $this->quote(isset($column_match[0]) ? json_encode($value) : serialize($value));
                        break;

                    case 'boolean':
                        $fields[] = $column . ' = ' . ($value ? '1' : '0');
                        break;

                    case 'integer':
                    case 'double':
                    case 'string':
                        $fields[] = $column . ' = ' . $this->fn_quote($key, $value);
                        break;
                }
            }
        }
        return $this->exec('UPDATE "' . $this->prefix . $this->table . '" SET ' . implode(', ', $fields) . $this->where_clause($where));
    }
    public function delete($where) {
        return $this->exec('DELETE FROM "' . $this->prefix . $this->table . '"' . $this->where_clause($where));
    }
    public function replace($columns, $search = null, $replace = null, $where = null) {
        if (is_array($columns)) {
            $replace_query = array();
            foreach ($columns as $column => $replacements) {
                foreach ($replacements as $replace_search => $replace_replacement) {
                    $replace_query[] = $column . ' = REPLACE(' . $this->column_quote($column) . ', ' . $this->quote($replace_search) . ', ' . $this->quote($replace_replacement) . ')';
                }
            }
            $replace_query = implode(', ', $replace_query);
            $where = $search;
        } else {
            if (is_array($search)) {
                $replace_query = array();
                foreach ($search as $replace_search => $replace_replacement) {
                    $replace_query[] = $columns . ' = REPLACE(' . $this->column_quote($columns) . ', ' . $this->quote($replace_search) . ', ' . $this->quote($replace_replacement) . ')';
                }
                $replace_query = implode(', ', $replace_query);
                $where = $replace;
            } else {
                $replace_query = $columns . ' = REPLACE(' . $this->column_quote($columns) . ', ' . $this->quote($search) . ', ' . $this->quote($replace) . ')';
            }
        }
        return $this->exec('UPDATE "' . $this->prefix . $this->table . '" SET ' . $replace_query . $this->where_clause($where));
    }
    public function get($join = null, $column = null, $where = null) {
        $query = $this->query($this->select_context($this->table, $join, $column, $where) . ' LIMIT 1');
        if ($query) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            if (isset($data[0])) {
                $column = $where == null ? $join : $column;
                if (is_string($column) && $column != '*') {
                    return $data[0][$column];
                }
                return $data[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function has($join, $where = null) {
        $column = null;
        $query = $this->query('SELECT EXISTS(' . $this->select_context($this->table, $join, $column, $where, 1) . ')');
        return $query ? $query->fetchColumn() === '1' : false;
    }
    public function count($join = null, $column = null, $where = null) {
        $query = $this->query($this->select_context($this->table, $join, $column, $where, 'COUNT'));
        return $query ? 0 + $query->fetchColumn() : false;
    }
    public function max($join, $column = null, $where = null) {
        $query = $this->query($this->select_context($this->table, $join, $column, $where, 'MAX'));
        if ($query) {
            $max = $query->fetchColumn();
            return is_numeric($max) ? $max + 0 : $max;
        } else {
            return false;
        }
    }
    public function min($join, $column = null, $where = null) {
        $query = $this->query($this->select_context($this->table, $join, $column, $where, 'MIN'));
        if ($query) {
            $min = $query->fetchColumn();
            return is_numeric($min) ? $min + 0 : $min;
        } else {
            return false;
        }
    }
    public function avg($join, $column = null, $where = null) {
        $query = $this->query($this->select_context($this->table, $join, $column, $where, 'AVG'));
        return $query ? 0 + $query->fetchColumn() : false;
    }
    public function sum($join, $column = null, $where = null) {
        $query = $this->query($this->select_context($this->table, $join, $column, $where, 'SUM'));
        return $query ? 0 + $query->fetchColumn() : false;
    }
    public function action($actions) {
        if (is_callable($actions)) {
            $this->pdo->beginTransaction();
            $result = $actions($this);
            if ($result === false) {
                $this->pdo->rollBack();
            } else {
                $this->pdo->commit();
            }
        } else {
            return false;
        }
    }
    public function debug() {
        $this->debug_mode = true;
        return $this;
    }
    public function error() {
        return $this->pdo->errorInfo();
    }
    public function last_query() {
        return end($this->logs);
    }
    public function log() {
        return $this->logs;
    }
    public function info() {
        $output = array(
            'server' => 'SERVER_INFO',
            'driver' => 'DRIVER_NAME',
            'client' => 'CLIENT_VERSION',
            'version' => 'SERVER_VERSION',
            'connection' => 'CONNECTION_STATUS'
        );
        foreach ($output as $key => $value) {
            $output[$key] = $this->pdo->getAttribute(constant('PDO::ATTR_' . $value));
        }
        return $output;
    }
}