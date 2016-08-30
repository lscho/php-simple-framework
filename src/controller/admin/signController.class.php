<?php
/**
* 七天打卡
*/
class signController extends baseController{

	/*
	* 打卡列表
	*/
	function sevenAction(){
		$p=empty($_GET['p'])?1:$_GET['p'];
		$model=new sevenUserModel();
		$data=$model->setOrder('total desc,id asc')->getList(array(),20,$p);
		$this->assign('list',$data['list']);
		$this->assign('page',$data['page']);
		$this->display();
	}

	/*
	* 查看签到详情
	*/
	function infoAction(){
		$model=new sevenListModel();
		$map['openid']=$_GET['id'];
		$map['ORDER']='addtime asc';
		$info=$model->select('*',$map);
		$this->assign('total',count($info));
		$this->assign('info',$info);
		$this->display();
	}

	/*
	* 签到规则
	*/
	function ruleAction(){
		$model=new model();
		$model->table('seven_set');		
		//获取起始事件
		$set=$model->get('*',array('id'=>1));
		$set['start']=date('Y-m-d',$set['start']);
		$set['end']=date('Y-m-d',$set['end']);
		$this->assign('set',$set);
		//获取签到列表
		$rule=new sevenRuleModel();
		$p=empty($_GET['p'])?1:$_GET['p'];
		$data=$rule->setOrder('times asc')->getList(array(),$this->pageSize,$p);
		$this->assign('list',$data['list']);
		$this->assign('page',$data['page']);
		$this->display();
	}

	/*
	* 添加签到规则
	*/
	function addAction(){
		if($this->isget()){
			$model=new model('seven_rule');
			$info=$model->get('*',array('id'=>$_GET['id']));
			$this->assign('info',$info);
			$this->display();
		}else{
			$model=new model();
			$model->table('seven_rule');
			$times=$_POST['times'];
			$data['rule']=$_POST['rule'];
			if(!$model->has(array('times'=>$times))){
				$data['times']=$times;
				$rs=$model->insert($data);
			}else{
				$rs=$model->update($data,array('times'=>$times));
			}
			if($rs){
				$this->json('操作成功');
			}else{
				$this->json('操作失败',0);
			}			
		}
	}

	/*
	* 设置签到时间
	*/
	function setAction(){
		$model=new model();
		$model->table('seven_set');
		$data['start']=strtotime($_POST['start']);
		$data['end']=strtotime($_POST['end']);
		$data['rule']=$_POST['rule'];
		if($model->has(array('id'=>1))){
			$rs=$model->update($data,array('id'=>1));
		}else{
			$rs=$model->insert($data);
		}
		if($rs){
			$this->json('操作成功');
		}else{
			$this->json('操作失败',0);
		}
	}
	/*
	* 批量操作
	*/
	function deleteAction(){
		$model=new model();
		$map['id']=$_POST['id'];
		$rs=$model->table('seven_list')->delete($map);
		if(!$num=$model->table('seven_list')->count(array('openid'=>$_POST['openid']))){
			$res=$model->table('seven_user')->delete(array('openid'=>$_POST['openid']));
		}else{
			$res=$model->table('seven_user')->update(array('total'=>$num),array('openid'=>$_POST['openid']));
		}
		$rs?$this->json('操作成功'):$this->json('操作失败',0);
	}	

	/*
	* 添加伪用户数据
	*/
	function addsAction(){
		$data['openid']='openid'.time();
		$data['total']=0;
		$data['addtime']=strtotime(date('ymd'));
		$model=new model('seven_user');
		$model->insert($data);
		$this->jump(__ROOT__.'/admin/sign_seven');
	}

	/*
	* 添加伪打卡记录
	*/
	function doAction(){
		
		$this->display();
	}
}