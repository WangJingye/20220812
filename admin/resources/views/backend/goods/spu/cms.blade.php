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
    <script src="<?=url('/static/layer/layer.js')?>"></script>
    <script src="<?=url('/lib/jquery.validate.min.js')?>"></script>
    <link rel="stylesheet" href="<?=url('/lib/app/index.css')?>">

    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/admin.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/bootstrap.min.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/swiper.min.css')?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/cms.css')?>"/>
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_1458973_v69x0axlxjf.css"/>
    <link rel="stylesheet" type="text/css" href="/static/admin/theme/default/laydate.css"/>
    <link rel="stylesheet" type="text/css" href="/static/admin/theme/default/laydate.css"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/font.css')?>"/>
    <script src="/static/admin/laydate.js"></script> <!-- 改成你的路径 -->
</head>
<body>

<div id="app" class="page-content-wrap">
    @if(session('success'))
        <div class="message success" style="margin-bottom: 5px;font-size: 12px;border: 1px solid green;">{{  session('success') }}</div>
    @endif
    <div>

        <el-form ref="form" id="cms-form"  method="post" action="{{url('admin/goods/spu/cmssave')}}" enctype="multipart/form-data" label-width="80px">
            <div style="position: absolute;left: 130px;top: 20px;">
                <span class="layui-btn layui-btn-normal"  @click="dialogVisible = true">图文JSON</span>
                <span class="layui-btn layui-btn-normal submitfm"  >保存</span>
            </div>

            <div>
                <picture-card images_list='<?php echo $kv_images?>' input_name="kv_images"/>
            </div>
            <input type="hidden" value="{{request('id')}}" name="id"/>

            <div class="layui-tab" style="margin: 0;">
                <el-tabs value="h5" type="border-card" class="type_tags" @tab-click="tabClick">

                    <el-tab-pane label="官网"  name="h5" class="type_tab">

                        <div class="layui-tab-content">
                            <div class="layui-tab-item"></div>
                            <div class="layui-tab-item layui-show">
                                <div class="content">
                                    <custom-clone v-if="true"
                                                  type="h5"
                                                  pageSource="product"
                                                  uploadUrl="{{url('admin/page/ajaxUploadProduct?').http_build_query(['spu'=>request('id')])}}"
                                                  api_domain="{{ env('APP_URL', '') }}"
                                                  oss_domain="{{ env('OSS_DOMAIN', '') }}"
                                                  :title="title"
                                                  :use_tags="['image','video','multi_image','product_detail_multi_part_text','product_detail_title_desc','product_detail_text','product_recommend','swiper_fraction']"
                                    ></custom-clone>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>

                </el-tabs>
                    <?php //内容部分?>
                    <div class="layui-tab-content" style="display: none;">
                        <div class="layui-tab-item"></div>
                        <div class="layui-tab-item layui-show">
                            <div class="content">
                                <custom-clone v-if="true"
                                              type="wechat"
                                              page_source="product"
                                              upload_url="{{url('admin/page/ajaxUploadProduct?').http_build_query(['spu'=>request('id')])}}"
                                              api_domain="{{ env('APP_URL', '') }}"
                                              oss_domain="{{ env('OSS_DOMAIN', '') }}"
                                              :title="title"
                                              :use_tags="['image','video','multi_image','product_detail_multi_part_text','product_detail_title_desc','product_detail_text','product_recommend','swiper_fraction']"
                                ></custom-clone>
                            </div>
                        </div>
                    </div>
            </div>
        </el-form>
    </div>
</div>
<script>
    var  pageId  = '';
    var  pageTitle  = '';
    var  pageName  = '';
    var  pageKey  = '';
    var  showHeaderFooter  = '';
    var  nodes  = {"wechat":<?php echo $wechat?>,"h5":<?php echo $pc?>};
    var  mediaType  = 'h5';
    var  breadcrumbs  = "";

</script>
<?php require_once './js/app.js.php';?>
<script>
    $('.edit-wrap,.element-list-wrap').css('position','unset');
    $('.submitfm').on('click',function (e){
        form = document.querySelector("form");
        var formData = new FormData(form);
        $.ajax({
            url: form.action,
            type: "POST",
            dataType:"json",
            data: formData,
            success:function (response){
                layer.msg(response.message);
            },
            processData: false,  // 不处理数据
            contentType: false   // 不设置内容类型
        });



    });


    $(window).scroll( function() {
        var  scrollTop=document.title=$(this).scrollTop();
        // console.log(scrollTop);
        if(scrollTop > 500 ){
            $('.edit-wrap').css({'position':'fixed','top':0}).addClass('edit-wrap-scroll');
        }else{
            $('.edit-wrap').css('position','unset');
            $('.edit-wrap').removeClass('edit-wrap-scroll').css('top',$('.cms-content-wrap').offset().top + 1 - scrollTop);
            $('.element-list-wrap').removeClass('edit-wrap-scroll-menu').css('top',$('.cms-content-wrap').offset().top + 1 - scrollTop);
        }

        if(scrollTop > 500 ){
            $('.element-list-wrap').css({'position':'fixed','top':0});
            $('.cms-content-wrap.h5,.cms-content-wrap.wechat').css({'marginLeft':'23%'});

        }else{
            $('.element-list-wrap').css('position','unset');
            $('.cms-content-wrap.h5,.cms-content-wrap.wechat').css({'marginLeft':0});
        }
    } );

</script>

</body>
</html>