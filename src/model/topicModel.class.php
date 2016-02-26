<?php  
	class topicModel extends Model{
		//发表话题
		function add($data){
			$insert_id=$this->insert($data);
			return $insert_id;
		}
		//获取话题列表
		function getList($map=array(),$p=1,$total=10){
		    $join["[>]es_user"]= array("uid" => "uid");
		    $join["[>]es_tab"]= array("tab" => "id");
		    $field=array("es_topic.id","es_topic.uid","es_topic.title","es_topic.content","es_topic.view","es_topic.reply","es_topic.post_time","es_topic.sign","es_user.nickname","es_tab.name(tab_name)");
		    //分页
		    $count=$this->count($join,'es_topic.id',$map);
		    $page=$this->page($count,$p,$total);
		    $map['ORDER']='es_topic.id DESC';
		    $map['LIMIT']=$page['limit'];
		    $list=$this->select($join,$field,$map);
		    $data['list']=$list;
		    $data['page']=$page['data'];
		    return $data;
		}
		//获取话题内容
		function getInfo($sign){
		    $join["[>]es_user"]= array("uid" => "uid");
		    $join["[>]es_tab"]= array("tab" => "id");
		    $field=array("es_topic.id","es_topic.uid","es_topic.title","es_topic.content","es_topic.view","es_topic.reply","es_topic.post_time","es_topic.sign","es_user.nickname","es_tab.name(tab_name)");
		    $map['es_topic.sign']=$sign;
		    $data=$this->get($join,$field,$map);
		    return $data;			
		}
		//获取最新话题
		function getNew($num=5){
			$data=$this->select(array('sign','title'),array('LIMIT'=>$num,'ORDER'=>'post_time DESC'));
			return $data;
		}
		//阅读量+1
		function setView($sign){
			$rs=$this->update(array("view[+]" => 1),array('sign'=>$sign));
			return $rs;
		}
	}