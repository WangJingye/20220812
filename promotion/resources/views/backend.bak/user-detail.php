<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>网站后台管理模版</title>
		<link rel="stylesheet" type="text/css" href="<?=url('static/admin/layui/css/layui.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?=url('static/admin/css/admin.css')?>" />
	</head>

	<body>
		<div class="page-content-wrap">
				<form class="layui-form" method="post" action="<?php echo url('admin/user/detail')?>">
					<?php echo csrf_field()?>
					<input type="hidden" name="id" value="<?php echo $user->id?>" />
					<div class="layui-tab" style="margin: 0;">
						<ul class="layui-tab-title">
							<li><a href="<?php echo url('admin/user')?>">用户列表</a></li>
							<li class="layui-this">用户详情</li>
							
						</ul>
						<div class="layui-tab-content">
							<div class="layui-tab-item"></div>
							<div class="layui-tab-item layui-show">
								<div class="layui-form-item">
									<label class="layui-form-label">用户名：</label>
									<div class="layui-input-block">
										<input value="<?php echo $user->name?>" type="text" name="name" required lay-verify="required" autocomplete="off" class="layui-input">
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">Email：</label>
									<div class="layui-input-block">
										<input value="<?php echo $user->email?>" type="text" name="email" required lay-verify="required"  autocomplete="off" class="layui-input" >
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">密码：</label>
									<div class="layui-input-block">
										<input id="password" type="text" name="password"  lay-verify=""  autocomplete="off" class="layui-input" >
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">确认密码：</label>
									<div class="layui-input-block">
										<input id="repassword" type="text" name="repassword"  lay-verify="regPwd"  autocomplete="off" class="layui-input">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="layui-form-item" style="padding-left: 10px;">
						<div class="layui-input-block">
							<button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo">立即提交</button>
							<button type="reset" class="layui-btn layui-btn-primary">重置</button>
						</div>
					</div>
				</form>
		</div>
		<script type="text/javascript">
			var SCOPE = {
				static: '/static',
				index: '/admin/category/index.html',
				add: 'add.html',
				save: '/admin/category/save.html',
				edit: 'add.html',
				updateEdit: '/admin/category/updateedit.html',
				status: '/admin/category/updatestatus.html',
				del: '/admin/category/del.html',
				delAll: '/admin/category/deleteall.html',
				listOrderAll: '/admin/category/listorderall.html'
			}
		</script>
		<script src="<?=url('static/admin/layui/layui.js')?>" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" charset="utf-8">
            layui.config({
                base: '<?php echo url("/static/admin/js/module/") . '/'; ?>'
            }).extend({
                dialog: 'dialog',

            });
        </script>
        <script src="<?=url('static/admin/js/common.js')?>" type="text/javascript" charset="utf-8"></script>
		<script>
			
			layui.use(['form', 'jquery', 'laydate', 'layer', 'laypage', 'dialog',  'element', 'upload', 'layedit'], function() {
				var form = layui.form(),
					layer = layui.layer,
					$ = layui.jquery,
					laypage = layui.laypage,
					laydate = layui.laydate,
					layedit = layui.layedit,
					element = layui.element(),
					dialog = layui.dialog;


    				form.verify({
    					regPwd: function(value) {
    						//获取密码
    						var pwd = $("#password").val();
    						if(pwd !==value) {
    							return '两次输入的密码不一致';
    						}
    					}
    				});

				//获取当前iframe的name值
				var iframeObj = $(window.frameElement).attr('name');
				//创建一个编辑器
				var editIndex = layedit.build('LAY_demo_editor', {
					tool: ['strong' //加粗
						, 'italic' //斜体
						, 'underline' //下划线
						, 'del' //删除线
						, '|' //分割线
						, 'left' //左对齐
						, 'center' //居中对齐
						, 'right' //右对齐
						, 'link' //超链接
						, 'unlink' //清除链接
						, 'image' //插入图片
					],
					height: 100
				})
				//全选
				form.on('checkbox(allChoose)', function(data) {
					var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
					child.each(function(index, item) {
						item.checked = data.elem.checked;
					});
					form.render('checkbox');
				});
				form.render();

				layui.upload({
					url: '上传接口url',
					success: function(res) {
						console.log(res); //上传成功返回值，必须为json格式
					}
				});
			});
		</script>
	</body>

</html>