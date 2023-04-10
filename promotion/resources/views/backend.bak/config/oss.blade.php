<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>网站后台管理模版</title>
		<link rel="stylesheet" type="text/css" href="{{ url('static/admin/layui/css/layui.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ url('static/admin/css/admin.css') }}" />
	</head>
	<body>
		<div class="wrap-container">
				<form class="layui-form" style="width: 90%;padding-top: 20px;" action="/admin/configOss/save" method="POST">
					<div class="layui-form-item">
						<label class="layui-form-label">AccessKeyId：</label>
						<div class="layui-input-block">
							<input type="text" name="access_key_id" required lay-verify="required" placeholder="AccessKeyId" autocomplete="off" class="layui-input" value="{{ $data->access_key_id??'' }}">
						</div>
					</div>

					<div class="layui-form-item">
						<label class="layui-form-label">AccessKeySecret：</label>
						<div class="layui-input-block">
							<input type="text" name="access_key_secret" required lay-verify="required" placeholder="AccessKeySecret" autocomplete="off" class="layui-input" value="{{ $data->access_key_secret??'' }}">
						</div>
					</div>

					<div class="layui-form-item">
						<label class="layui-form-label">Endpoint：</label>
						<div class="layui-input-block">
							<input type="text" name="endpoint" required lay-verify="required" placeholder="Endpoint" autocomplete="off" class="layui-input" value="{{ $data->endpoint??'' }}">
						</div>
					</div>

					<div class="layui-form-item">
						<label class="layui-form-label">Bucket：</label>
						<div class="layui-input-block">
							<input type="text" name="bucket" required lay-verify="required" placeholder="Bucket" autocomplete="off" class="layui-input" value="{{ $data->bucket??'' }}">
						</div>
					</div>

					<div class="layui-form-item">
						<label class="layui-form-label">Url：</label>
						<div class="layui-input-block">
							<input type="text" name="url" required lay-verify="required" placeholder="Url" autocomplete="off" class="layui-input" value="{{ $data->url??'' }}">
						</div>
					</div>

					<div class="layui-form-item">
						<label class="layui-form-label">启用状态：</label>
						<div class="layui-input-block">
							<input type="checkbox" name="active" lay-skin="switch" lay-filter="switchActive" lay-text="开启|关闭" @if(!empty($data->active)) checked  value="1" @else value="0" @endif id="active">
						</div>
					</div>
					<div class="layui-form-item">
						<div class="layui-input-block">
							<button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo">立即提交</button>
							<button type="reset" class="layui-btn layui-btn-primary">重置</button>
						</div>
					</div>
				</form>
		</div>

		<script src="{{ url('static/admin/layui/layui.js') }}" type="text/javascript" charset="utf-8"></script>
		<script src="{{ url('static/admin/js/jquery.min.js') }}" type="text/javascript" charset="utf-8"></script>
		<script>
			//Demo
			layui.use(['form'], function() {
				var form = layui.form();
				form.render();
				//监听指定开关
		        form.on('switch(switchActive)', function(data){
		            if(this.checked){
		            	$("#active").attr("value",'1');
		                layer.tips('温馨提示：如果关闭将不开启OSS云服务', data.othis)
		            }else{
		            	 $("#active").attr("value",'0');

		            }
		            //do some ajax opeartiopns;
		        });
		        //监听提交
				form.on('submit(formDemo)', function(data) {
                    $.post("{{ route('backend.config.osssave') }}",data.field,function(res){
                        if(res.code!=0){
                            layer.msg(res.msg,{icon:5,anim:6});
                            return false;
                        }else{
                            layer.msg('保存成功', {icon: 6});
                        }
                    },'json');
                    return false;
				});
			});

		</script>	
	</body>

</html>