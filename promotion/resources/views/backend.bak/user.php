<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>网站后台管理模版</title>
		<link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layuiadmin/layui/css/layui.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?=url('static/admin/css/admin.css')?>" />
	</head>

	<body>
		<div class="page-content-wrap">
			<form class="layui-form" action="">
				<div class="layui-form-item">
					<div class="layui-inline tool-btn">
						<button class="layui-btn layui-btn-small layui-btn-normal go-btn hidden-xs" data-url="<?php echo url('admin/user/detail')?>"><i class="layui-icon">&#xe654;</i></button>
					</div>
					<div class="layui-inline">
						<input type="text" name="name" placeholder="请输入名称" autocomplete="off" class="layui-input" value="<?php echo request('name')?>">
					</div>
					<button class="layui-btn layui-btn-normal" lay-submit="search">搜索</button>
				</div>
			</form>
			<?php if(session('success')):?>
			<div class="message"><?php echo session('success')?></div>
			<?php endif;?>
			<div class="layui-form" id="table-list">
				<table class="layui-table" lay-even lay-skin="nob">
					<colgroup>
						<col width="50">
						<col class="hidden-xs" width="">
						<col class="hidden-xs" width="">
						<col>
						<col class="hidden-xs" width="">
						<col width="">
						<col width="">
					</colgroup>
					<thead>
						<tr>
							<th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
							<th class="hidden-xs">ID</th>
							<th class="hidden-xs">名称</th>
							<th class="hidden-xs">EMAIL</th>
							<th>发布时间</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($list as $user):?>
						<tr>
							<td><input type="checkbox" name="" lay-skin="primary" data-id="1"></td>
							<td><?php echo $user->id?></td>
							<td><?php echo $user->name?></td>
							<td><?php echo $user->email?></td>
							<td><?php echo $user->created_at?></td>
							<td>
								<div class="layui-inline">
									<button class="layui-btn layui-btn-mini layui-btn-normal  go-btn" data-id="<?php echo $user->id?>" data-url="<?php echo url('admin/user/detail')?>"><i class="layui-icon">&#xe642;</i></button>
									<button class="layui-btn layui-btn-mini layui-btn-danger del-btn" data-id="<?php echo $user->id?>" data-url="<?php echo url('admin/user/del')?>"><i class="layui-icon">&#xe640;</i></button>
								</div>
							</td>
						</tr>
						<?php endforeach;?>
						
					</tbody>
				</table>
				 
				<div class="page-wrap">
					 <?php echo $list->appends($_GET)->links();?> 
			    </div>
			</div>
		</div>
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
			layui.use(['form', 'jquery', 'layer', 'dialog'], function() {
				var $ = layui.jquery;

			});
		</script>
	</body>

</html>