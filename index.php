<?php
	define('APP_DEBUG',true);	//调试模式
	define('LIB_FILE','./lib/');//核心文件目录
	define('APP_FILE','./src/');//项目文件目录
	require_once './lib/system.php';
	APP::run();