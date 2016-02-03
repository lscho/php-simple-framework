$(function(){
	//表单异步提交
	$("#post").on('click',function(){
		var self=$(this);
		self.button('loading');
		var url=$(self.data('from')).attr('action');
		$(self.data('from')).find('input').each(function(){
			alert($(this).val());
		});
		$.post(url,{
			title:$("input[name='title']").val(),
			content:$("input[name='content']").val()
		},function(rs){
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
	$("#sendmail").on('click',function(){
		var self=$(this);
		var email=$("#email").val();
		var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
		if(!reg.test(email)) {
			var msg = $.zui.messager.show("请输入有效的邮箱地址！", {placement:"center",type:"danger"});
			return false;
		}
		$.post(self.data('url'),{
			email:email
		},function(rs){
			var option={placement:"center"};
			option.type=(rs.status==1)?"success":"danger";
			var msg = $.zui.messager.show(rs.msg, option);			
		},'json')
	});
});