<?php
	/*
	* 开发环境配置文件
	*/
	return array(
		//数据库
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
		//项目
		'app'=>array(
			'view'=>'view/',			//模版文件目录
			'cache'=>'cache/',			//缓存目录
			'log'=>true,				//开启日志记录
			'module'=>'home',			//默认模块
			'controller'=>'main',		//默认控制器
			'action'=>'index',			//默认动作
		),
		//路由映射
		'rewrite' => array(
			'admin/<c>_<a>_<p>' => 'admin/<c>/<a>', 
			'admin/<c>_<a>'    	=> 'admin/<c>/<a>', 
			'admin/<c>'   		=> 'admin/<c>/index',
			'admin'				=> 'admin/main/index',
			'<c>/<a>'          	=> 'home/<c>/<a>',
			'<c>'          		=> 'home/<c>/index',
			'/'                	=> 'home/main/index',
		)
	);
