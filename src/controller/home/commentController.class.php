<?php
	//评论
	class commentController extends baseController{
		//发表回复
		function indexAction(){
			empty($_SESSION['userInfo'])&&$this->json('需要登录',0);
			empty($_POST['_reply'])&&$this->json('回复异常',0);
			empty($_POST['reply'])&&$this->json('内容不能为空',0);
			$comment=new commentModel();
			$sign=base64_decode($_POST['_reply']);
			$rs=$comment->add($sign,$_POST['reply']);
			if($rs){
				$topic=new topicModel();
				$topic->setReply($sign,$comment->getCount(array('sign'=>$sign)));
				$this->register_use('REPLY',array('reply_id'=>$rs,'reply'=>$_POST['reply']));
				$this->json('回复成功',1,BASE_FILE.'/topic/read/sign/'.base64_decode($_POST['_reply']));
			}else{
				$this->json('发表回复失败',0);
			}
		}
	}