<?php  
/**
* 模型基类
*/
class baseModel extends Model{
	/*
	* 获取详情
	*/
	function getInfo($map=array()){
		return $this->get('*',$map);	
	}

	/*
	* 获取列表
	*/
	function getList($map=array(),$pageSize=10,$p=1){
		    //分页
		$count=$this->count($map);
		$page=$this->page($count,$p,$pageSize);
		$map['ORDER']='id DESC';
		$map['LIMIT']=$page['limit'];
		$list=$this->select('*',$map);
		$data['list']=$list;
		$data['page']=$page['data'];
		return $data;		
	}
}