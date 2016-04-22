<?php
	class mainController extends baseController{
		function indexAction(){
			empty($_GET['tab'])&&$_GET['tab']='全部';
			$_GET['tab']=='全部'||$where['es_tab.name']=$_GET['tab'];
			$this->assign('tabs',$_GET['tab']);
			$topic=new topicModel();
			$p=empty($_GET['p'])?1:$_GET['p'];
			$data=$topic->getList($where,$p,10);
			$this->register_use('index','index');
			$this->assign('page',$data['page']);
			$this->assign('list',$data['list']);
			$this->display();
		}
	}