<?php  
	class tabModel extends Model{
		//获取tab标签列表
		function getList(){
			$map['ORDER']='sort DESC';
			$rs=$this->select('*',$map);
			return $rs;
		}
	}