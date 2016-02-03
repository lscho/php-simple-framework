<?php  
	class topicModel extends Model{
		//发表话题
		function add($data){
			$insert_id=$this->insert($data);
			return $insert_id;
		}
	}