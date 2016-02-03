<?php
	class topicController extends baseController{
		function indexAction(){	
			if($this->ispost()){
				$data['title']=$_POST['title'];
				$data['content']=$_POST['content'];
				$data['uid']=$_SESSION['id'];
				$data['post_time']=time();
				$topic=new topicModel();
				$insert_id=$topic->add($data);
				if($insert_id){
					$this->json("发布成功");
				}else{
					$this->json("发布失败",0);
				}
			}else{
				$this->display();
			}
		}
	}