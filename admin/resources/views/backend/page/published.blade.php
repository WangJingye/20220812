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

			<div class="layui-inline layui-input-inline">
				<input type="text" style="width:200px;" name="name" placeholder="页面名称" autocomplete="off" class="layui-input" value="<?php echo request('name')?>">
			</div>
			<div class="layui-inline layui-input-inline" style="height: 38px;line-height: 38px;">
				状态：
				<select name="status">
					<option value="">请选择</option>
					<option value="1" @if(request('status')==='1') selected @endif;>上线</option>
					<option value="0" @if(request('status')==='0') selected @endif;>下线</option>
				</select>
			</div>
			<button class="layui-btn layui-btn-normal" lay-submit="search">搜索</button>
		</div>
		<div class="layui-inline tool-btn" >
			<button class="layui-btn layui-btn-small layui-btn-normal go-btn hidden-xs" data-url="<?php echo url('admin/page/detail/new')?>"><i class="layui-icon">&#xe654;</i>创建新页面</button>
		</div>
	</form>

	<?php if(session('success')):?>
	<div class="message success"><?php echo session('success')?></div>
	<?php endif;?>
	<?php if(session('error')):?>
	<div class="message error"><?php echo session('error')?></div>
	<?php endif;?>
	<div class="layui-form" id="table-list">
		<table class="layui-table" lay-even lay-skin="nob">
			<thead>
			<tr>
				<th class="hidden-xs">ID</th>
				<th class="hidden-xs">KEY</th>
				<th class="hidden-xs">站点</th>
				<th class="hidden-xs">名称</th>
				<th class="hidden-xs">图片</th>
				<th class="hidden-xs">時間</th>
				<th>操作</th>
			</tr>
			</thead>
			<tbody>
			@foreach ($list as $page)
				<tr>
					<td>{{ $page->id }}</td>
					<td>{{ $page->key }}</td>
					<td>{{ $page->type }}</td>
					<td>{{ $page->name }}</td>
					<td><?php if($page->share_image):?><img src="<?php echo getImageUrl($page->share_image) ?>" style="width:100px;"/><?php endif;?></td>
					<td>{{ $page->created_at }}</td>
					<td>
						<div class="layui-inline">
							<button class="layui-btn layui-btn-mini layui-btn-normal go-btn" data-id="{{ $page->id }}" data-url="{{ url('admin/page/detail/published') }}"><i class="layui-icon">&#xe642;</i></button>

							@if(!in_array($page->key,['home','home_hair']))
								<button class="layui-btn layui-btn-mini layui-btn-danger del-btn" data-id="{{ $page->id }}" data-url="{{ url('admin/page/del/published') }}"><i class="layui-icon">&#xe640;</i></button>
							@endif

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
</body>
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
	});
</script>

</html>