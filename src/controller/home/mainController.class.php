<?php
	class mainController extends baseController{
		function indexAction(){
			$topic=new topicModel();
			$rs=$topic->getList($where,1,2);
			$this->display();
		}
	}