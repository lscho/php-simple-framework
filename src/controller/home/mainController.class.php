<?php
	class mainController extends baseController{

		/*
		* 报名首页
		*/
		function indexAction(){
			if(empty($_SESSION['openid'])){
				$this->getToken();
			}
			//第N步
			$step=isset($_GET['step'])?$_GET['step']:1;
			$this->display('main_index'.$step);
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
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxbe73369f870a0158&redirect_uri=http%3a%2f%2fh5.jiang-mei.com%2fyiboh5%2fcms%2f&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
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
		}
	}