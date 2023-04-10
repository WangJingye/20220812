<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <title>网站后台管理模版</title>
    <script src="<?=url('/lib/app.js')?>"></script>
    <script src="<?=url('/lib/jquery.validate.min.js')?>"></script>
    <link rel="stylesheet" href="<?=url('/lib/app/index.css')?>">

    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/admin.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/bootstrap.min.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/swiper.min.css')?>"/>
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_1458973_xkrtaihi4e8.css"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/cms.css')?>"/>





</head>

<body>
<div id="app" class="page-content-wrap">
    <div>
    <span  class="layui-btn json"  >获取JSON</span>

    <el-form ref="form"   method="post" action="" enctype="multipart/form-data" label-width="80px">
        {{  csrf_field() }}

        <div class="layui-tab" style="margin: 0;">
            <div class="layui-tab-content">
                <div class="layui-tab-item"></div>
                <div class="layui-tab-item layui-show">
                        <div class="content">
                            <custom-clone v-if="true"
                                          :use_tags=<?php echo $tags?>
                                          api_domain="{{ env('APP_URL', '') }}"
                                          oss_domain="{{ env('OSS_DOMAIN', '') }}"
                                          :title="title"
                            ></custom-clone>
                            <input type="hidden" name="nodes" id="nodes" v-model="JSON.stringify(nodes)" />
                        </div>
                    </div>
                </div>
            </div>
    </el-form>
    </div>
</div>
<script>
    var  pageTitle  = '';
    var  showHeaderFooter  = '';
    var nodeJson=$('input[name="{{request('name')}}"]',window.parent.document).val();
    console.log('nodeJson',nodeJson);
    if(nodeJson==''){
       var  nodes=[];
    }else{
       var  nodes  = JSON.parse($('input[name="{{request('name')}}"]',window.top.document).val());
    }

</script>
<?php require_once './js/app.js.php';?>
<script>
$('span.json').on('click',function (e){
    var target = $('input[name="{{request('name')}}"]',window.parent.document);
    $(target).val($('input[name="nodes"]').val());
});
</script>
</body>
</html>

<?php
/**
<input type="text" name="test-json" value='' />
<iframe src="{{url(URL_PREFIX.'page/iframe')}}?name=test-json&tags=image|video" width="100%" height="500px;" name="mini-cms"></iframe>
 */
?>