<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>后台登录</title>
		<link rel="stylesheet" type="text/css" href="{{ url('/static/admin/layui/css/layui.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ url('/static/admin/css/login.css') }}" />
	</head>

	<body>
		<div class="m-login-bg" style="">
			<div class="m-login">
				<h3>后台系统登录</h3>
				<div class="m-login-warp">
					<form class="layui-form"  method="POST" action="{{ url('/admin/login') }}">
					    {{ csrf_field() }}
						<div class="layui-form-item">
							<input type="text" name="email" required lay-verify="required" placeholder="email" autocomplete="off" class="layui-input">
						</div>
						<div class="layui-form-item">
							<input type="password" name="password" required lay-verify="required" placeholder="密码" autocomplete="off" class="layui-input">
						</div>
						<div class="layui-form-item">
							<div class="layui-inline">
								<input type="text" name="captcha" required lay-verify="required" placeholder="验证码" autocomplete="off" class="layui-input">
							</div>
					
							<div class="layui-inline">
								<img src="{{captcha_src()}}" style="cursor: pointer" onclick="this.src='{{captcha_src()}}'+Math.random()">
							</div>
							<div class="message">
								@if($errors->has('captcha'))
                                    <p  class="text-danger text-left"><strong>{{$errors->first('captcha')}}</strong></p>
                            	@endif
                                @if ($errors->has('email') || $errors->has('password'))
                                    <p  class="text-danger text-left"><strong>用户名密码错误</strong></p>
                                @endif
                       
   							 </div>
						</div>
						
						
						<div class="layui-form-item m-login-btn">
							<div class="layui-inline">
								<button class="layui-btn layui-btn-normal" lay-submit lay-filter="login">登录</button>
							</div>
							<div class="layui-inline">
								<button type="reset" class="layui-btn layui-btn-primary">取消</button>
							</div>
						</div>
					</form>
				</div>
				<p class="copyright">Copyright <?php echo date('Y-m')?> by Connext</p>
			</div>
		</div>
		<script src="{{ url('/static/admin/layui/layui.js') }}" type="text/javascript" charset="utf-8"></script>
		<script>
			layui.use(['form', 'layedit', 'laydate'], function() {
				var form = layui.form(),
					layer = layui.layer;


				//自定义验证规则
				form.verify({
					email: function(value) {
						if(value.length < 5) {
							return 'email至少得5个字符啊';
						}
					},
					password: [/(.+){6,12}$/, '密码必须6到12位'],
					verity: [/(.+){6}$/, '验证码必须是6位'],
					
				});

				
				//监听提交
// 				form.on('submit(login)', function(data) {
// // 					layer.alert(JSON.stringify(data.field), {
// // 						title: '最终的提交信息'
// // 					})
// 					return true;
// 				});

			});
		</script>
	</body>

</html>