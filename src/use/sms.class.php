<?php
	class sms{
		//入口
		function notify($behavior,$data){
			$behavior=strtolower($behavior);
			$this->$behavior($data);
		}
		function register($data){
			$param['code']=$data['code'];
			$param['product']=$_SESSION['baseinfo']['title'];
			$rs=$this->send($data['mobile'],json_encode($param),'SMS_4940209');
		}
		function send($mobile,$data,$template){
			include APP_FILE."src/use/TopSdk.php";
			$c = new TopClient;
			$c->appkey = $appkey;
			$c->secretKey = $secret;
			$req = new AlibabaAliqinFcSmsNumSendRequest;
			$req->setSmsType("normal");
			$req->setSmsFreeSignName("注册验证");	//短信签名
			$req->setSmsParam("{\"code\":\"1234\",\"product\":\"阿里大鱼\",\"item\":\"阿里大鱼\"}");
			$req->setRecNum($mobile);
			$req->setSmsTemplateCode($template);
			$resp = $c->execute($req);
		}
	}