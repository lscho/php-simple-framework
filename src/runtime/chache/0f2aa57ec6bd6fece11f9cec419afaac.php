<br>-------------------------上面是baseController输出的<br>
<?php $this->display('head')?>
<br>-------------------------上面是head输出的<br>
<?php if($this->vars['users']) {?>
users's data
<?php } ?>
<br>-------------------------上面是判断users是否存在<br>
<?php if (count((array)$this->vars['cate'])) foreach((array)$this->vars['cate'] as $this->vars['k']=>$this->vars['v']) {?>
	<?php echo $this->vars['v']['name']?>
<?php } ?>
<br>-------------------------上面是foreach循环