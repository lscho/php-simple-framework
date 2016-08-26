<?php
	class mainController extends baseController{
		/*
		* 报名首页
		*/
		function indexAction(){
			//$_SESSION['openid']='orSDHtyha_AlF4dm4NZyLId-OUps';
			if($_GET['step']!=5){
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxbe73369f870a0158&redirect_uri=http%3a%2f%2fh5.jiang-mei.com%2fyiboh5%2fcms%2f?step=0&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
				$this->getToken($url);
			}
			//第N步
			$step=isset($_GET['step'])?$_GET['step']:'0';

			$model=new model('seven_set');
			//时间
			$set=$model->get('*',array('id'=>1));
			$start=$set['start']?$set['start']:strtotime(date('Ymd'));
			//测试七天数据
			$this->assign('set',$set);
			//打卡说明
			$day=empty($_GET['day'])?date('Y-m-d'):$_GET['day'];	//测试期间允许自定义时间
			//活动开始时间
			$set=$model->table('seven_set')->get('*',array('id'=>1));
			$start=$set['start']?$set['start']:strtotime(date('Ymd'));
			//判断今天是活动第几天
			$now=(strtotime($day)-$start)/86400+1;
			$this->assign('day',$now);			
			//是否在活动日期内
			if($now>0&&$now<8){
				$rule=$model->table('seven_rule')->get('*',array('times'=>$now));
				$this->assign('rule',$rule);				
				$this->assign('date',$day);
			}else{
					$this->assign('error',1);
			}
			//获取当前用户今天打卡信息
			$map['openid']=$_SESSION['openid'];
			$map['addtime']=strtotime(date('Ymd'));
			$info=$model->table('seven_list')->get('*',array('AND'=>$map));
			$this->assign('info',$info);
			//第五步
			if($step==5){
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxbe73369f870a0158&redirect_uri=http%3a%2f%2fh5.jiang-mei.com%2fyiboh5%2fcms%2f?step=5&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
				$this->getToken($url);
				$data=$this->getList();
				$this->assign('total',$data['total']);
				$this->assign('list',$data['date']);
				$this->assign('top',$this->getTop());
			}
			$this->assign('src',"http://".$_SERVER['HTTP_HOST'].__ROOT__.'/static/home/images/fx1.jpg');
			$this->display('main_index'.$step);
		}

		/*
		* 点赞
		*/
		function likeAction(){			
			if($this->isget()){
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxbe73369f870a0158&redirect_uri=http%3a%2f%2fh5.jiang-mei.com%2fyiboh5%2fcms%2fmain%2flike%2f?id=".$_GET['id']."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
				$this->getToken($url);				
				//获取记录详情
				$map['id']=$_GET['id'];
				$model=new sevenListModel();
				$info=$model->get('*',$map);
				//获取当天任务详情
				$set=$model->table('seven_set')->get('*',array('id'=>1));
				$start=$set['start']?$set['start']:strtotime(date('Ymd'));
				$rule=$model->table('seven_rule')->get('*',array('times'=>($info['addtime']-$start)/86400+1));
				$this->assign('rule',$rule);
				//获取点赞人数
				$like=$model->table('like')->count(array('sid'=>$_GET['id']));
				$info['like']=$like;
				$this->assign('info',$info);
				//判断当前用户是否点赞
				$where['sid']=$_GET['id'];
				$where['openid']=$_SESSION['openid'];
				$is_like=$model->has(array('AND'=>$where));

				$this->assign('is_like',$is_like);
				$this->display();
			}else{
				$map['sid']=$_POST['id'];
				$map['openid']=$_SESSION['openid'];
				$model=new model('like');
				if($model->has(array('AND'=>$map))){
					$this->json('您已经点过赞了',0);
				}else{
					if($model->insert($map)){
						$this->json('点赞成功');
					}else{
						$this->json('点赞失败',0);
					}
				}
			}
		}

		/*
		* 获取排名和总人数
		*/
		function getTop(){
			//统计
			$model=new sevenUserModel();
			$total=$model->select('openid',array('GROUP'=>'openid'));
			$data['total']=count($total);
			//排名
			$sql='select openid,total,(@rowNum:=@rowNum+1) as pm from es_seven_user a,(Select (@rowNum :=0) ) b order by a.total desc,a.addtime DESC';
			$data['pm']=0;
			$rs=$model->query($sql)->fetchAll();
			foreach ($rs as $k=>$v) {
				if($v['openid']==$_SESSION['openid']){
					$data['pm']=$v['pm'];
					break;
				}
			}
			return $data;
		}

		/*
		* 获取七天记录
		*/
		function getList(){
			$date=array();
			//获取当前用户记录
			$model=new sevenListModel();
			$map['openid']=$_SESSION['openid'];
			$map['ORDER']='addtime ASC';
			$list=$model->select('*',$map);
			$data['total']=count($list);
			//获取签到开始时间
			$set=$model->table('seven_set')->get('*',array('id'=>1));
			$start=$set['start']?$set['start']:strtotime(date('Ymd'));

			foreach ($list as $v) {
				$src[$v['addtime']]=$v;
			}
			for ($i=0; $i < 7 ; $i++) {
				$d=$start+$i*86400;
				$date[$d]=$src[$d];
			}
			$data['date']=$date;
			return $data;
		}

		/*
		* 获取微信ID
		*/
		function getToken($url){
			//引入微信类库
			APP::load(APP_FILE.'common/class/wechat.class.php');
			$weObj = new Wechat(APP::$config['wechat']);
	 
			// 注意 URL 一定要动态获取，不能 hardcode.
			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			$uri = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$signPackage = $weObj->getJsSign($uri);
			if (!isset($_GET['code'])) {
				header("Location: ".$url);
				exit;
			}
			$data = $weObj->getOauthAccessToken();
			if (!$data) {
				header("Location: ".$url);
				exit;
			}
			$userinfo = $weObj->getOauthUserinfo($data['access_token'], $data['openid']);
			$this->assign('signPackage',$signPackage);
			$_SESSION['openid']=$data['openid'];
		}


	}