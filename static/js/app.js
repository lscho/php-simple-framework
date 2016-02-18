$(function(){
	//表单异步提交
	$("#post").on('click',function(){
		var self=$(this);
		self.button('loading');
		var url=$(self.data('from')).attr('action');
		var data={};
		$(self.data('from')).find('input').each(function(){
			if($(this).attr('name')){
				data[$(this).attr('name')]=$(this).val();
			}
		});
		$(self.data('from')).find('textarea').each(function(){
			if($(this).attr('name')){
				data[$(this).attr('name')]=$(this).val();
			}			
		});
		$.post(url,data,function(rs){
			var option={placement:"center"};
			option.type=(rs.status==1)?"success":"danger";
			var msg = $.zui.messager.show(rs.msg, option);
			if(rs.status==1){
				
			}else{
				self.button('reset');
			}
		},'json')
	});
	//表单验证
	$(".validate input").blur(function(){
		var self=$(this);
		var validate=self.closest('.validate');
		if(!self.val()){
			validate.addClass('has-error');
			validate.find('.form-alert').show();
		}
	});
	$(".validate input").focus(function(){
		var self=$(this);
		var validate=self.closest('.validate');
		if(validate.hasClass('has-error')){
			validate.removeClass('has-error');
			validate.find('.form-alert').hide();
		}
	});
	//邮件发送
	$("#send").on('click',function(){
		var self=$(this);
		var value=$(self.data('for')).val();
		if(self.data('send')=='sms'){
			var reg=/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i;
			if(!reg.test(value)) {
				var msg = $.zui.messager.show("请输入有效的手机号码！", {placement:"center",type:"danger"});
				return false;
			}else{
				var data={mobile:value};
			}
		}else if(self.data('send')=='email'){
			var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
			if(!reg.test(value)) {
				var msg = $.zui.messager.show("请输入有效的邮箱地址！", {placement:"center",type:"danger"});
				return false;
			}else{
				var data={email:value};
			}
		}
		$.post(self.data('url'),data,function(rs){
			var option={placement:"center"};
			option.type=(rs.status==1)?"success":"danger";
			var msg = $.zui.messager.show(rs.msg, option);			
		},'json')
	});
});