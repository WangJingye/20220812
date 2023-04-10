<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>网站后台管理模版</title>
		<link rel="stylesheet" type="text/css" href="<?php echo url('/static/admin/layui/css/layui.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo url('/static/admin/css/admin.css')?>" />
	</head>

	<body>
		<style>
            .layui-inline{
	           display:flex;
            }
            select{
	           border:1px solid #ccc;
            }
	   </style>
		<div class="page-content-wrap">
			<form class="" action="">
				<div class="layui-form-item" >
					<div class="layui-inline tool-btn" >
						<button class="layui-btn layui-btn-small layui-btn-normal go-btn hidden-xs" data-url="<?php echo url('admin/page/detail')?>"><i class="layui-icon">&#xe654;</i></button>
					</div>
					<div class="layui-inline">
						<input type="text" style="width:200px;" name="title" placeholder="请输入名称" autocomplete="off" class="layui-input" value="<?php echo request('title')?>">
					</div>
					<button class="layui-btn layui-btn-normal" lay-submit="search">搜索</button>
				</div>
			</form>
			<?php if(session('success')):?>
			<div class="message"><?php echo session('success')?></div>
			<?php endif;?>
			<div class="layui-form" id="table-list">
				<table class="layui-table" lay-even lay-skin="nob">
					<thead>
						<tr>
							<th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
							<th class="hidden-xs">ID</th>
							<th class="hidden-xs">标题</th>
							<th class="hidden-xs">图片</th>
							<th class="hidden-xs">分类</th>
							<th class="hidden-xs">状态</th>
							<th>发布时间</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($list as $page)
						<tr>
							<td><input type="checkbox" name="" lay-skin="primary" data-id="1"></td>
							<td>{{ $page->id }}</td>
							<td>{{ $page->title }}</td>
                            <td><?php if($page->img):?><img src="<?php echo $page->img ?>" style="width:100px;"/><?php endif;?></td>
                            <td>{{ $page->categoryName }}</td>
							<td><button data-id="{{ $page->id }}" class="layui-btn layui-btn-mini layui-btn-<?php echo $page->status?"normal":"warm"?> table-list-status" data-status='<?php echo $page->status?>'><?php echo $page->status?"显示":"隐藏"?></button></td>
							<td>{{ $page->created_at }}</td>
							<td>
								<div class="layui-inline">
									<button class="layui-btn layui-btn-mini layui-btn-normal go-btn" data-id="{{ $page->id }}" data-url="{{ url('admin/page/detail') }}"><i class="layui-icon">&#xe642;</i></button>

									<button class="layui-btn layui-btn-mini layui-btn-danger del-btn" data-id="{{ $page->id }}" data-url="{{ url('admin/page/del') }}"><i class="layui-icon">&#xe640;</i></button>
								
								</div>
							</td>
						</tr>
						@endforeach
						
					</tbody>
				</table>
				 
				<div class="page-wrap">
					 <?php echo $list->appends($_GET)->links();?> 
			    </div>
			</div>
		</div>
		<script src="<?php echo url('/static/admin/layui/layui.js')?>" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" charset="utf-8">
		layui.config({
			base: '<?php echo url("/static/admin/js/module/") . '/'; ?>'
		}).extend({
			dialog: 'dialog',
			
		});
		</script>
		<script src="<?php echo url('/static/admin/js/common.js')?>" type="text/javascript" charset="utf-8"></script>
		<script>
			layui.use(['form', 'jquery', 'layer', 'dialog'], function() {
				var $ = layui.jquery;
				var form = layui.form;

				//修改文章状态
				$('#table-list').on('click', '.table-list-status', function() {
					var That = $(this);
					var status = That.attr('data-status');
					That.attr('disabled','disabled').css({'background-color':'#eee'});
					if(status == 1) {
							
						$.post('/ajax/article/status', {'status':0,'id':That.data('id')},function (response){
							if(response.status == 'success'){
								That.removeClass('layui-btn-normal').addClass('layui-btn-warm').html('隐藏').attr('data-status', 0);
								That.removeAttr('disabled').css({'background-color':'#F7B824'});
							} else {
								layer.msg(response.message);
								That.removeAttr('disabled').css({'background-color':'#1E9FFF'});
							}

						},'json')
						
					} else if(status == 0) {
						$.post('/ajax/article/status', {'status':1,'id':That.data('id')},function (response){
							if(response.status=='success'){
								That.removeClass('layui-btn-warm').addClass('layui-btn-normal').html('显示').attr('data-status', 1);
								That.removeAttr('disabled').css({'background-color':'#1E9FFF'});

							}  else {
								layer.msg(response.message);
							    That.removeAttr('disabled').css({'background-color':'#F7B824'});

							}
						},'json')
					}
				})

			});
		</script>
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