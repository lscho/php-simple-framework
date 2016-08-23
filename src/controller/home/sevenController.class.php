<?php
/**
* 七天签到
*/
class sevenController extends baseController{
	
	function __construct(){
		parent::__construct();
		$this->user=new sevenUserModel();
		$this->list=new sevenListModel();
	}
	
	/*
	* 进行签到
	*/
	function doAction(){
		//检测是否有签到记录
		$data['openid']=$_SESSION['openid'];
		$data['addtime']=empty($_POST['date'])?strtotime(date('Y-m-d')):strtotime($_POST['date']);
		$is_sign=$this->list->has(array('AND'=>$data));
		if($is_sign){
			$this->json('已经签到过了',0);
		}
		//检测是否上传图片
		if(empty($_POST["img"])){
			$this->json('请上传图片',0);
		}
		$file_path="static/upload/seven/";
		if (!file_exists($file_path) || !is_writable($file_path)) {
			@mkdir($file_path, 0755,true);
		}
		$img = base64_decode(str_replace('data:image/jpeg;base64,', "", $_POST['img']));
		$src=$file_path.date('Ymdhis').rand(1000,9999).'.jpg';
		file_put_contents($src, $img);
		$data['src']=$src;
		//写入签到记录
		$res=$this->list->insert($data);
		if(!$res){
			$this->json('签到失败',0);
		}
		//增加连续签到天数
		$map['openid']=$_SESSION['openid'];
		$rs=$this->user->get('*',$map);
		unset($data['src']);
		if(!$rs){
			//没有记录则新增数据
			$data['total']=1;
			$this->user->insert($data);
		}else{
			//有记录则更新数据
			$this->user->update(array('addtime'=>$data['addtime'],'total[+]'=>1),$map);
		}
		$this->json('签到成功',1);
	}
}