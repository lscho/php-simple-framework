<?php  
	class commentModel extends Model{
		//添加回复
		function add($sign,$reply=""){
			$data['sign']=$sign;
			$data['comment']=$reply;
			$data['reply_time']=time();
			$data['uid']=$_SESSION['userInfo']['uid'];
			$rs=$this->insert($data);
			return $rs;
		}
		//获取评论
		function getComment($sign,$p=1,$total=5){
			$join["[>]es_user"]= array("uid" => "uid");
		    $field=array("es_comment.comment","es_comment.reply_time","es_user.nickname");
		    $map['es_comment.sign']=$sign;
		    $count=$this->count($join,'es_comment.id',$map);
		    $page=$this->page($count,$p,$total);
		    $map['ORDER']='es_comment.id DESC';
		    $map['LIMIT']=$page['limit'];
		    $list=$this->select($join,$field,$map);
		    $data['count']=$count;
		    $data['list']=$list;
		    $data['page']=$page['data'];
		    return $data;		    		    
		}
		//获取数量
		function getCount($map=array()){
			$rs=$this->count($map);
			return $rs;
		}
	}