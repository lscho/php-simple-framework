<?php  
	class baseModel extends Model{
		//获取基础信息
		function getinfo(){
			$rs=$this->get('*');
			return $rs;
		}
	}