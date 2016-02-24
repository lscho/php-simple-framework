<?php
	class mainController extends baseController{
		function indexAction(){
			empty($_GET['tab'])&&$_GET['tab']='全部';
			$_GET['tab']=='全部'||$where['es_tab.name']=$_GET['tab'];
			$topic=new topicModel();
			$data=$topic->getList($where,1,10);
			$this->assign('page',$data['page']);
			$this->assign('list',$data['list']);
			$this->display();
		}
	}