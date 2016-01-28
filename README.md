**引言**

本意是想实验一下能不能编写一个在 mvc 中使用 rest ful 编写 api 的框架，然而在实践过程中却发现 mvc 和 rest ful 并不能完美的融合到一起。所以删掉了 rest ful 部分，留下了 mvc 部分。

**实例**

    <?php
	class mainController extends baseController{
		function indexAction(){
			$user=new Model();	//实例化一个空模型
			$data=$user->table('categorys')->select('*');//通过table设置表来进行查询
			$this->assign('cate',$data);				//赋值到模版
			$users=new usersModel();					//实例化自定义模型
			$data=$users->select('*');					//此时已经自动关联users表
			$this->assign('users',$data);				//赋值到模版
			$this->display();
		}
	}
**简介**
从上面代码可以看出主要完成了路由、自动加载、模型、模版引擎等功能。[核心代码](https://github.com/eyblog/mvc/blob/master/lib/system.php)不到160行左右
