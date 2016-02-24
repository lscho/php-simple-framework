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
				$data['sign']=substr(md5($data['post_time'].$data['title']),8,16);
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
		function readAction(){
			$topic=new topicModel();
			$info=$topic->getInfo($_GET['sign']);
			if($info){
				$topic->setView($_GET['sign']);	//阅读量+1
				$info['post_time']=date('Y年m月d日 H:i:s',$info['post_time']);
				$info['_reply']=base64_encode($info['sign']);
				$info['view']+=1;
				$this->assign('info',$info);
				//获取评论列表
				$comment=new commentModel();
				$commentList=$comment->getComment($info['sign']);
				foreach($commentList['list'] as &$v) {
					$v['reply_time']=date('Y-m-d H:i:s',$v['reply_time']);
					unset($v);
				}
				$this->assign('comment',$commentList);				
			}
			$this->display();
		}
	}