<?php
	class sms{
        function notify($data){
            $behavior='use_'.strtolower($data['behavior']);
            if(method_exists($this,$behavior))$this->$behavior($data['data']);
        }		
		function use_register($data){
			$param['code']=$data['code'];
			$param['product']=$_SESSION['baseinfo']['title'];
			$rs=$this->send($data['mobile'],json_encode($param),'SMS_4940209',"注册验证");
			return $rs;
		}
		function send($mobile,$data,$template,$sign){
			$a=include APP_FILE."use/alisdk/TopSdk.php";
			$c = new TopClient;
			$c->appkey = "23310798";
			$c->secretKey = "754cbdbd9aeff8f64077d522420fc3cb";
			$req = new AlibabaAliqinFcSmsNumSendRequest;
			$req->setSmsType("normal");
			$req->setSmsFreeSignName($sign);	//短信签名
			$req->setSmsParam($data);
			$req->setRecNum($mobile);
			$req->setSmsTemplateCode($template);
			$resp = (array)$c->execute($req);
			return $resp['result']['err_code']==0;
		}
	}