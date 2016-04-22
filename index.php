<?php
	define('APP_DEBUG',true);	//调试模式
	define('APP_FILE','./src/');//项目文件目录
	require_once './src/system.php';
	App::run();