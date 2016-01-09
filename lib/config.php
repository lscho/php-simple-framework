<?php
	return array(
		'db'=>array(
			//必选项
		    'database_type' => 'mysql',
		    'database_name' => 'ey',
		    'server' => '127.0.0.1',
		    'username' => 'root',
		    'password' => '123456',
		    'charset' => 'utf8',
		    //可选项
		    'port' => 3306,
		    'prefix' => 'ey_',		    			
		),
		'api'=>array(
			'name'=>'api',
			'type'=>'json'
		),
		'app'=>array(
			'view_file'=>'view/',
			'runtime_file'=>'runtime/'
		)
	);
?>