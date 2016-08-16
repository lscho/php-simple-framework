<?php
	class mainController extends baseController{
		function indexAction(){
			$step=isset($_GET['step'])?$_GET['step']:1;
			$this->display('main_index'.$step);
		}
	}