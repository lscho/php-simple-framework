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
		    $field=array("es_topic.id","es_topic.uid","es_topic.title","es_topic.content","es_topic.view","es_topic.reply","es_topic.post_time","es_user.nickname","es_tab.name");
		    //分页
		    $count=$this->count($join,'es_topic.id',$map);
		    $page['total']=$count;
		    $page['nowPage']=$p;
		    $page['totalPage']=ceil($count/$total);
		    $map['ORDER']='es_topic.id DESC';
		    $map['LIMIT']=array(($p-1)*$total,$p*$total);
		    $list=$this->select($join,$field,$map);
		    $data['list']=$list;
		    $data['page']=$page;
		    return $data;
		}
	}