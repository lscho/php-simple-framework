<?php
	class mainController extends baseController{

		/*
		* 报名首页
		*/
		function indexAction(){
			//$_SESSION['openid']='orSDHtyha_AlF4dm4NZyLId-OUps';
			if(empty($_SESSION['openid'])){
				$this->getToken();
			}
			//第N步
			$step=isset($_GET['step'])?$_GET['step']:1;
			if($step==5){
				$this->getToken();
				$this->assign('list',$this->getList());
				$this->assign('top',$this->getTop());
				$this->assign('signPackage',$_SESSION['signPackage']);
			}
			$this->display('main_index'.$step);
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
			//获取当前用户记录
			$model=new sevenListModel();
			$map['openid']=$_SESSION['openid'];
			$map['ORDER']='addtime ASC';
			$list=$model->select('*',$map);
			$date=array();
			if($list){
				$num=0;
				foreach ($list as $v) {
					$src[$v['addtime']]=$v['src'];
				}
				for ($i=0; $i <7; $i++) { 
					if($num<7){
						$date[date('Y-m-d',$list[0]['addtime']+86400*$i)]=$src[$list[0]['addtime']+86400*$i];
						$num++;
					}
				}
			}
			return $date;
		}

		/*
		* 获取微信ID
		*/
		function getToken(){
			//引入微信类库
			APP::load(APP_FILE.'common/class/wechat.class.php');
			$weObj = new Wechat(APP::$config['wechat']);
	 
			// 注意 URL 一定要动态获取，不能 hardcode.
			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$signPackage = $weObj->getJsSign($url);
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxbe73369f870a0158&redirect_uri=http%3a%2f%2fh5.jiang-mei.com%2fyiboh5%2fcms%2f?step=5&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
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
			$_SESSION['openid']=$data['openid'];
			$_SESSION['signPackage']=$signPackage;
		}


	}