<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>网站后台管理模版</title>
        <script src="<?=url('/lib/app.js')?>"></script>
        <link rel="stylesheet" href="<?=url('/lib/app/index.css')?>">

		<link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/admin.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?=url('/css/bootstrap.min.css')?>" />
		<link rel="stylesheet" type="text/css" href="<?=url('/css/swiper.min.css')?>" />



    </head>

	<body>
	<style>
        .layui-form input[type="checkbox"], .layui-form input[type="radio"], .layui-form select {
            display: block;
            border: 1px solid #ccc;
            height: 40px;
            width: 100%;
        }
        h3{
            font-size:14px;
        }
        .element-list{
            width: 250px;
            display: flex;
            flex-flow:row wrap;
            justify-content: start;
            align-content: flex-start;
        }
        .element{
            width:120px;
            font-size: 12px;
            padding: .5rem 1.25rem;
            margin-bottom: 5px;
            border-radius: 5px;
            height: 36px;
            margin-right: 5px;
        }
        .element:hover,li.current{
            border:1px dashed #00a0e9;
        }
        li.current{
            box-shadow: 0px 0px 1px 1px #00a0e9 inset;
        }

        .element-list-wrap{
            overflow-y: scroll;
        }
        .cms-content-wrap{
            overflow-y: scroll;
        }
        .edit-wrap{

        }
        .cms-content{
            height: 660px;

            border: 1px dashed #eee;
        }
        .cms-content>span{
            height: 500px;
        }
        li.current img{
            padding:2px;
        }
        .last-element-list{
            height: 300px;
        }

        .el-upload-list__item-thumbnail{
            width: auto !important;
        }
        .el-carousel__arrow{
            line-height: 12px !important;
        }
        .layui-form-label{
            width:auto !important;
        }
	</style>
		<div id="app"  class="page-content-wrap">
				<?php if(session('success')):?>
				<div class="message"><?php echo session('success')?></div>
				<?php endif;?>
				<form class="layui-form" method="post" action="<?php echo url('admin/page/detail')?>" enctype="multipart/form-data" >
					<?php echo csrf_field()?>
					<input type="hidden" name="id" value="<?php echo $page->id?>" />

					<div class="layui-tab" style="margin: 0;">
						<ul class="layui-tab-title">
							<li><a href="<?php echo url('admin/page')?>">页面列表</a></li>
							<li class="layui-this">页面详情</li>

						</ul>

						<div class="layui-tab-content">
							<div class="layui-tab-item"></div>
							<div class="layui-tab-item layui-show">
								<?php if(true):?>
								<div class="layui-form-item">
									<label class="layui-form-label">文章标题：</label>
									<div class="layui-input-block">
										<input value="<?php echo $page->title?>" type="text" name="title" required lay-verify="required" autocomplete="off" class="layui-input">
									</div>
								</div>
								<div class="layui-form-item">
									<label class="layui-form-label">文章描述：</label>
									<div class="layui-input-block">
										<textarea   name="desc" placeholder=""  class="layui-textarea"  ><?php echo $page->desc?></textarea>
									</div>
								</div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">分类：</label>
                                    <div class="layui-input-block">
                                        <select name="category">
                                            <option value="" >请选择分类</option>
                                                <?php foreach($taxonomy as $k=>$v):?>
                                                    <option <?php if($page->category==$k) echo 'selected';?> value="<?php echo $k?>"><?php echo $v?></option>
                                                <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>

								<div class="layui-form-item">
									<label class="layui-form-label">作者：</label>
									<div class="layui-input-block">
										<select name="author">
											<option value="">请选择作者</option>
											<?php foreach($author as $k=>$v):?>
											<option <?php if ($page->author == $k) echo 'selected';?> value="<?php echo $k?>"><?php echo $v?></option>
											<?php endforeach;?>
										</select>
									</div>
								</div>


                                <div class="layui-form-item">
                                        <label class="layui-form-label">列表页图片：</label>
                                        <div class="layui-input-block">
                                            <input type="hidden" value="<?php echo $page->img?>" name="img"/>
                                            <?php if($page->img):?>
                                                <img src="<?php echo $page->img?>"  style="width:50px;"/>
                                            <?php endif;?>
                                            <input  style="width:200px;border:none;padding-left:0;"  type="file" name="img"   autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
								<?php endif;?>

								<div class="content">

                                    <custom-clone v-if="true" :page_content='<?php echo $pageContent;?>' api_domain="<?php echo env('APP_URL','')?>"></custom-clone>
								</div>


							</div>

						</div>
					</div>
					<div v-if="true" class="layui-form-item" style="padding-left: 10px;">
						<div class="layui-input-block">
							<button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo">立即提交</button>
						</div>
					</div>
			</form>
		</div>
    <script src="<?=url('/js/main.js')?>" ></script>
    <script src="<?=url('/js/vendor.js')?>" ></script>
	</body>
</html>