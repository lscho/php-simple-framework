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
		$data['addtime']=strtotime(date('Y-m-d'));
		$is_sign=$this->list->has(array('AND'=>$data));
		if($is_sign){
			$this->json('已经签到过了',0);
		}
		//检测是否上传图片
		if(empty($_FILES["img"]["name"])){
			$this->json('请上传图片',0);
		}
		//引入文件上传类库
		APP::load(APP_FILE.'common/class/upload.class.php');
		$upload=new upload();
		$file_path="static/upload/seven/";
		$upload -> set("path", $file_path);
		if($upload ->dos("img")){
			$data['src']=$file_path.$upload->getFileName();
		}else{
			$this->json($up->getErrorMsg(),0);
		}	
		//写入签到记录
		$res=$this->list->insert($data);
		if(!$res){
			$this->json('签到失败',0);
		}
		//增加连续签到天数
		$map['openid']=$_REQUEST['openid'];
		$rs=$this->user->get('*',$map);
		unset($data['src']);
		if(!$rs){
			//没有记录则新增数据
			$data['total']=1;
			$this->user->insert($data);
		}else{
			//有记录则更新数据
			if($data['addtime']-$rs['addtime']==86400){
				$this->user->update(array('addtime'=>$data['addtime'],'total[+]'=>1),$map);
			}else{
				$this->user->update(array('addtime'=>$data['addtime'],'total'=>1),$map);
			}
		}
		$this->json('签到成功',1);
	}
}