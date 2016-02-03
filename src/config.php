<?php
	return array(
		'db'=>array(
			//必选项
		    'database_type' => 'mysql',
		    'database_name' => 'es',
		    'server' => '127.0.0.1',
		    'username' => 'root',
		    'password' => '123456',
		    'charset' => 'utf8',
		    //可选项
		    'port' => 3306,
		    'prefix' => 'es_',		    			
		),
		'app'=>array(
			'view_file'=>'view/',
			'runtime_file'=>'runtime/'
		),
		'url'=>array(
			'suffix'=>'.html'
		),
		'rewrite' => array(
			'admin/index.html' => 'admin/main/index',
			'admin/<c>_<a>.html'    => 'admin/<c>/<a>', 
			'<c>/<a>'          => '<c>/<a>',
			'<c>/'          => '<c>/index',
			'<c>'          => '<c>/index',
			'/'                => 'main/index',
		),
		'upload'=>array(
			'list'=>'jpg,json,zip,rar',
			'file'=>'upload/'
		),
		'email'=>array(
			'user'=>'server@eyblog.com',
			'password'=>'123456.Server',
			'host'=>'smtp.exmail.qq.com',
			'port'=>25,
			'auth'=>false
		)
	);
?>