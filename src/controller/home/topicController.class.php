<?php
	class topicController extends baseController{
		function indexAction(){	
			if($this->ispost()){
				empty($_POST['title'])&&$this->json('标题不能为空',0);				
				$data['title']=$_POST['title'];
				$data['content']=$_POST['content'];
				$data['tab']=$_POST['tab'];
				$data['uid']=$_SESSION['userInfo']['uid'];
				$data['post_time']=time();
				$topic=new topicModel();
				$insert_id=$topic->add($data);
				if($insert_id){
					$this->json("发布成功",1,BASE_FILE.'/');
				}else{
					$this->json("发布失败",0);
				}
			}else{
				$this->display();
			}
		}
	}