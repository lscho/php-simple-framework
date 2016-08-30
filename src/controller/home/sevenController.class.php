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
		//获取任务开始时间
		$model=new model('seven_set');
		$set=$model->get('*',array('id'=>1));
		$start=$set['start']?$set['start']:strtotime(date('Ymd'));	
		//检测是否有签到记录
		$data['openid']=$_SESSION['openid'];
		$data['addtime']=empty($_POST['date'])?strtotime(date('Y-m-d')):$start+86400*($_POST['date']-1);
		$is_sign=$this->list->has(array('AND'=>$data));
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
		//旋转图片
		if(!empty($_POST['deg'])){
	        //创建图像资源，以jpeg格式为例
	        $source = imagecreatefromjpeg($src);
	        //使用imagerotate()函数按指定的角度旋转
	        $rotate = imagerotate($source, $_POST['deg'], 0);
	        //旋转后的图片保存
	        imagejpeg($rotate,$src);
		}
		//写入签到记录
		if($is_sign){
			$res=$this->list->update(array('src'=>$src),array('AND'=>$data));
		}else{
			$data['src']=$src;
			$res=$this->list->insert($data);
		}
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
		$this->json($is_sign?'修改成功':'签到成功',1);
	}

	/*
	* 填写用户电话和姓名
	*/
	function saveAction(){
		//判断签到次数
		$info=$this->user->get('*',array('openid'=>$_SESSION['openid']));
		if($info['total']!=7){
			$this->json('累积打卡7天才能参与抽奖',0);
		}
		//保存用户信息
		$rs=$this->user->update($_POST,array('openid'=>$_SESSION['openid']));
		$rs?$this->json('提交成功'):$this->json('提交失败',0);
	}
}