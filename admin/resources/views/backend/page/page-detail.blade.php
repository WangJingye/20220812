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
    <svg class="icon" aria-hidden="true">
        <use xlink:href="#iconshipin"></use>
    </svg>
    @if(session('success'))
        <div class="message success" style="margin-bottom: 5px;font-size: 12px;border: 1px solid green;">{{  session('success') }}</div>
    @endif
    <div>
        <el-form ref="form"   method="post" action="" enctype="multipart/form-data" label-width="80px">
            {{  csrf_field() }}
            <input type="hidden" name="id" value="{{ $page->id }}" v-model="id"/>
            <input type="hidden" name="back_page" value="
            @if($type=='draft') admin/page @endif
            @if($type=='new' || $type=='published') admin/page/published @endif
                    "/>

            <div class="layui-tab" style="margin: 0;">
                <ul class="layui-tab-title">

                    @if($type=='draft')
                        <li><a href="{{url('admin/page')}}">草稿列表</a></li>
                    @endif
                    @if($type=='published' || $type=='new')
                        <li><a href="{{url('admin/page/published')}}">发布列表</a></li>
                    @endif

                    <li class="layui-this">页面详情:<span v-html="id"></span> </li>

                </ul>




                <div class="pay-information">
                    <div style="display:flex;justify-content: space-around;">
                        <div style="width: calc(50% - 10px);">
                            <table class="layui-table" lay-skin="nob" lay-size="">
                                <colgroup>
                                    <col width="120px">
                                </colgroup>
                                <tbody>
                                <tr>
                                    <td>文章名称：<font color="red">*</font></td>
                                    <td> <input v-model="name" value="{{$page->name}}" type="text" name="name"  required
                                                autocomplete="off" class="layui-input"  ></td>
                                </tr>
                                <tr>
                                    <td>key：<font color="red">*</font></td>
                                    <td> <input <?php if(in_array($page->key,['home','home_hair'])) echo 'readonly'?>  v-model="key" required type="text" value="{{$page->key??""}}" name="key" class="layui-input" placeholder='' /></td>
                                </tr>
                                <tr v-if="false">
                                    <td>搜索关键字：</td>
                                    <td> <input  type="text" value="{{$page->keyword??""}}" name="keyword" class="layui-input" placeholder='搜索关键字'/></td>
                                </tr>
                                <tr>
                                    <td>描述：</td>
                                    <td> <input  type="text" value="{{$page->desciption??""}}" name="desciption" class="layui-input" placeholder='描述'/></td>
                                </tr>
                                <tr v-if="false">
                                    <td>面包屑：</td>
                                    <td>
                                        <el-button size="mini" @click="newBreadcrumbs">添加</el-button>
                                        <div v-for=" (item,i) in breadcrumbs">
                                            <input v-model="item.name"  placeholder="名称"/>
                                            <input v-model="item.url"  placeholder="url"/>
                                            <el-button size="mini" @click="removeBreadcrumbs(i)" >del</el-button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input name="breadcrumbs" type="hidden" :value="JSON.stringify(breadcrumbs)" />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="width: calc(50% - 10px);">
                            <table class="layui-table" lay-skin="nob" lay-size="">
                                <colgroup>
                                    <col width="120px">
                                    <col>
                                </colgroup>
                                <tbody>
                                <tr>
                                    <td>文章标题：</td>
                                    <td> <input value="{{$page->title}}" type="text" name="title"
                                                autocomplete="off" class="layui-input" @input="changeText($event)"></td>
                                </tr>
                                <tr>
                                    <td>分享标题：</td>
                                    <td> <input value="{{$page->share_title}}" type="text" name="share_title"
                                                autocomplete="off" class="layui-input"></td>
                                </tr>
                                <tr>
                                    <td>分享图片:</td>
                                    <td> <input type="hidden" value="{{$page->share_image??""}}" name="share_image" class="share_image"/>
                                        @if($page->share_image)
                                            <img class="share_image" src="{{getImageUrl($page->share_image)}}" style="height: 28px;vertical-align: bottom;"/>
                                        @endif
                                        <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" onchange="xhrSubmitPoster(event,'share_image')">
                                    </td>

                                </tr>
                                <tr v-if="false">
                                    <td>背景大图:</td>
                                    <td> <input type="hidden" value="{{$page->bg_big_image??""}}" name="bg_big_image" class="bg_big_image"/>
                                        @if($page->bg_big_image)
                                            <img class="bg_big_image" src="{{getImageUrl($page->bg_big_image)}}" style="height: 28px;vertical-align: bottom;"/>
                                        @endif
                                        <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" onchange="xhrSubmitPoster(event,'bg_big_image')">
                                    </td>

                                </tr>
                                <tr v-if="false">
                                    <td>背景小图:</td>
                                    <td> <input type="hidden" value="{{$page->bg_small_image??""}}" name="bg_small_image" class="bg_small_image"/>
                                        @if($page->bg_small_image)
                                            <img class="bg_small_image" src="{{getImageUrl($page->bg_small_image)}}" style="height: 28px;vertical-align: bottom;"/>
                                        @endif
                                        <input style="width:200px;border:none;padding-left:0;display: inline;height: 28px;line-height: 28px;" type="file" class="layui-input" onchange="xhrSubmitPoster(event,'bg_small_image')">
                                    </td>

                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <el-tabs value="h5" type="border-card" class="type_tags" @tab-click="tabClick">

                    <el-tab-pane label="官网"  name="h5" class="type_tab">
                        <?php //字段部分?>
                        <div class="pay-information">
                            <div style="display:flex;justify-content: space-around;">
                                <div style="width: calc(50% - 10px);">
                                    <table class="layui-table" lay-skin="nob" lay-size="">
                                        <colgroup>
                                            <col width="120px">
                                        </colgroup>
                                        <tbody>

                                        <tr v-if="false">
                                            <td>加速域名：</td>
                                            <td>   @if($page->key)
                                                    <?php echo $action->getOssPath('OSS',$page->key,'os')?>
                                                @endif</td>
                                        </tr>
                                        <tr>
                                            <td>源地址：</td>
                                            <td>   @if($page->key)
                                                    <?php echo $action->getOssPath('SOURCE',$page->key,'wechat')?>
                                                @endif</td>
                                        </tr>
                                        <tr v-if="false">
                                            <td>复制数据到:</td>
                                            <td colspan="2">
                                                <el-button size="mini" @click="syncTowechat($event)">小程序</el-button>
                                            </td>
                                        </tr>
                                        <tr v-if="false">
                                            <td>终端切换：</td>
                                            <td colspan="2">
                                                <el-button size="mini" @click="switchMedia($event,'h5')">H5</el-button>
                                                <el-button size="mini" @click="switchMedia($event,'pc')">PC</el-button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="width: calc(50% - 10px);">
                                    <table class="layui-table" lay-skin="nob" lay-size="">
                                        <colgroup>
                                            <col width="120px">
                                            <tr v-if="false">
                                                <td>发布时间：</td>
                                                <td>

                                                    @if(isset($page->type) && ($page->type=='published' || $page->type=='published_and_online') || true)
                                                        <input class="layui-input" id="published_at" name="h5[published_at]" placeholder="发布时间" value="{{$items['h5']->published_at??""}}" type="text">
                                                        <input class="layui-input"  name="h5[published]"  value="{{$items['h5']->published??"0"}}" type="hidden">
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr v-if="false">
                                                <td>下线时间：</td>
                                                <td>
                                                    @if($page->type!='draft')
                                                        <input class="layui-input" id="offline_at" name="h5[offline_at]" placeholder="下线时间" value="{{$items['h5']->offline_at??""}}" type="text">
                                                        <input class="layui-input"  name="h5[offline]"  value="{{$items['h5']->offline??"0"}}" type="hidden">
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>状态：</td>
                                                <td>
                                                    @if(isset($items['wechat']->status) &&  $items['wechat']->status)  已上线 @else 已下线 @endif
                                                </td>
                                            </tr>
                                            <col>
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <td style="text-align: right" colspan="2">
                                                <span class="layui-btn layui-btn-normal"  @click="dialogVisible = true">JSON</span>
                                                <!--创建-->
                                                @if($type=='new')
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="published" title="保存在本地" data-save-type="wechat">保存</span>
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="published_and_online" title="发布到OSS上，上线" data-save-type="wechat">保存并发布</span>
                                                @endif

                                            <!--下线状态的编辑-->
                                                @if($type=='published')
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="published" title="保存在本地" data-save-type="wechat">保存</span>
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="published_and_online" title="发布到OSS上，上线" data-save-type="wechat">保存并发布</span>
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="offline" title="oss文章修改为下线" data-save-type="wechat">下线</span>
                                                @endif



                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php //内容部分?>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item"></div>
                            <div class="layui-tab-item layui-show">
                                <div class="content">
                                    <custom-clone v-if="true"
                                                  type="wechat"
                                                  api_domain="{{ env('APP_URL', '') }}"
                                                  oss_domain="{{ env('OSS_DOMAIN', '') }}"
                                                  :title="title"
                                                  :not_use_tags="['image_prod_default','image_model_default']"
                                    ></custom-clone>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane label="小程序" name="wechat">
                        <?php //字段部分?>

                        <div class="pay-information">
                            <div style="display:flex;justify-content: space-around;">
                                <div style="width: calc(50% - 10px);">
                                    <table class="layui-table" lay-skin="nob" lay-size="">
                                        <colgroup>
                                            <col width="120px">
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <td>加速域名：</td>
                                            <td> @if($page->key)
                                                    <?php echo $action->getOssPath('OSS',$page->key,'wechat')?>
                                                @endif</td>
                                        </tr>
                                        <tr>
                                            <td>OSS地址：</td>
                                            <td> @if($page->key)
                                                    <?php echo $action->getOssPath('SOURCE',$page->key,'wechat')?>
                                                @endif</td>
                                        </tr>

                                        <tr v-if="false">
                                            <td>复制数据到：</td>
                                            <td>
                                                <el-button size="mini" @click="syncToH5($event)">官网</el-button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="width: calc(50% - 10px);">
                                    <table class="layui-table" lay-skin="nob" lay-size="">
                                        <colgroup>
                                            <col width="120px">
                                            <col>
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <td>发布时间：</td>
                                            <td>
                                                @if(isset($page->type) && ($page->type=='published' || $page->type=='published_and_online') || true)
                                                    <input class="layui-input" id="published_at_wechat" name="wechat[published_at]" placeholder="发布时间" value="{{$items['wechat']->published_at??""}}" type="text">
                                                    <input class="layui-input"  name="wechat[published]" placeholder="发布时间" value="{{$items['wechat']->published??""}}" type="hidden">
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>下线时间：</td>
                                            <td>
                                                @if($page->type!='draft')
                                                    <input class="layui-input" id="offline_at_wechat" name="wechat[offline_at]" placeholder="下线时间" value="{{$items['wechat']->offline_at??""}}" type="text">
                                                    <input class="layui-input" id="offline_at_wechat" name="wechat[offline]"  value="{{$items['wechat']->offline??""}}" type="hidden">
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>状态：</td>
                                            <td>
                                                @if(isset($items['wechat']->status) &&  $items['wechat']->status)  已上线 @else 已下线 @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right" colspan="2">
                                                <span class="layui-btn layui-btn-normal"  @click="dialogVisible = true">JSON</span>
                                                <!--创建-->
                                                @if($type=='new')
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="published" title="保存在本地" data-save-type="wechat">保存</span>
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="published_and_online" title="发布到OSS上，上线" data-save-type="wechat">保存并发布</span>
                                                @endif



                                            <!--下线状态的编辑-->
                                                @if($type=='published' )
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="published" title="保存在本地" data-save-type="wechat">保存</span>
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="published_and_online" title="发布到OSS上，上线" data-save-type="wechat">保存并发布</span>
                                                    <span class="layui-btn layui-btn-normal submitFm" data-type="offline" title="oss文章修改为下线" data-save-type="wechat">下线</span>
                                                @endif





                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php //内容部分?>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item"></div>
                            <div class="layui-tab-item layui-show">
                                <div class="content">
                                    <custom-clone v-if="true"
                                                  type="wechat"
                                                  api_domain="{{ env('APP_URL', '') }}"
                                                  oss_domain="{{ env('OSS_DOMAIN', '') }}"
                                                  :title="title"
                                                  :not_use_tags="['image_prod_default','image_model_default']"
                                    ></custom-clone>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>

                </el-tabs>



            </div>
        </el-form>
    </div>
</div>
<script>
    var  pageId  = '<?php echo addslashes($page->id); ?>';
    var  pageTitle  = '<?php echo addslashes($page->title); ?>';
    var  pageName  = '<?php echo addslashes($page->name); ?>';
    var  pageKey  = '<?php echo addslashes($page->key); ?>';
    var  showHeaderFooter  = '{{ $page->show_header_footer }}';
    var  breadcrumbs  = <?php echo $page->breadcrumbs??'[]' ?>;
    var  nodes  = <?php echo $pageContent; ?>;
    var  mediaType  = 'wechat';

</script>
<?php require_once './js/app.js.php';?>
<script>

    $('.qrbtn').on('click',function (e){
        layer.open({
            title:'小程序预览',
            type: 1,
            area: ['320px', '360px'],
            shadeClose: true, //点击遮罩关闭
            content: '<img style="padding:18px" src="{{url('admin/page/getUnlimited',['key'=>$page->key])}}">',
            time:10000
        });
    });

    laydate.render({
        elem: '#published_at' //指定元素
        ,type: 'datetime'
    });
    laydate.render({
        elem: '#offline_at' //指定元素
        ,type: 'datetime'
    });
    laydate.render({
        elem: '#published_at_wechat' //指定元素
        ,type: 'datetime'
    });
    laydate.render({
        elem: '#offline_at_wechat' //指定元素
        ,type: 'datetime'
    });


    $('span.submitFm').on('click',function (e){
        var type = $(e.target).data('type');
        var form = $(e.target).closest('form');
        if(type=='draft'){
            var action = '{{url('admin/page/detail/draft')}}';
        }else if(type=='published'){
            var action = '{{url('admin/page/detail/published')}}';
        }else if(type=='published_and_online'){
            var action = '{{url('admin/page/detail/published_and_online')}}';
        }else if(type=='offline'){
            var action = '{{url('admin/page/detail/offline')}}';
        }
        form.attr('action',action);
        //form.submit();
        $save_type=$(this).data('save-type');
        form = document.querySelector("form");
        var formData = new FormData(form);
        formData.append('save_type',$save_type);
        if(formData.get('name')==""){
            layer.alert('文章名称必填');
            return false;
        }
        if(formData.get('key')==""){
            layer.alert('文章KEY必填');
            return false;
        }
        if(!/^\w+$/.test(formData.get('key'))){
            layer.alert('文章KEY必须是字母数字下划线');
            return false;
        }
        // var reg= /^[\u4e00-\u9fa5]+$/ug;
        // if(formData.get('keyword') &&  !reg.test(formData.get('keyword'))){
        //     layer.alert('搜索关键字必须是中文');
        //     return false;
        // }
        $.ajax({
            url: form.action,
            type: "POST",
            dataType:"json",
            data: formData,
            success:function (response){
                //console.log(response);
                layer.msg(response.message);
                if(response.pageId){
                    $('input[name="id"]').val(response.pageId);
                }

            },
            processData: false,  // 不处理数据
            contentType: false   // 不设置内容类型
        });



    });

    function xhrSubmitPoster(e,input_class){
        var files = e.target.files[0];
        if(files.size > (1024 * 512) ){
            $(e.target).val("");
            alert('图片大小为：'+Math.round(files.size/1024)+'K,不能超过512K');
            return false;
        }
        var param = new FormData();
        param.append('file',files);
        $(e.target).before('<i style="font-size: 30px;" class="loading layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop" />');
        $('span.submitFm').hide();
        $.ajax({
            url:'<?php echo url('admin/page/ajaxUploadShareImage')?>',
            type:'post',
            data:param,
            dataType:'json',
            processData: false,
            contentType:false,
            success:function(res){
                if(res.status==true){
                    if( $('input.'+input_class).val()){
                        let path = res.file;
                        if(res.domain){
                            path = res.domain+res.file
                        }
                        $('img.'+input_class).attr('src',path);
                    }
                    if($('img.'+input_class).length==0){
                        $(e.target).before('<img class="'+input_class+'" src="'+res.file+'" style="width:100px;height: 28px;vertical-align: bottom;" />');
                    }
                    $('input.'+input_class).val(res.file);
                    $('i.loading').hide();
                    $('span.submitFm').show();

                }else{
                    alert(res.message);
                }
            }
        })
    }

    $('.edit-wrap,.element-list-wrap').css('position','unset');
    $(window).scroll( function() {
        var  scrollTop=document.title=$(this).scrollTop();
        // console.log(scrollTop);
        if(scrollTop > 1000 ){
            $('.edit-wrap').css({'position':'fixed','top':0}).addClass('edit-wrap-scroll');
        }else{
            $('.edit-wrap').css('position','unset');
            $('.edit-wrap').removeClass('edit-wrap-scroll').css('top',$('.cms-content-wrap').offset().top + 1 - scrollTop);
            $('.element-list-wrap').removeClass('edit-wrap-scroll-menu').css('top',$('.cms-content-wrap').offset().top + 1 - scrollTop);
        }

        if(scrollTop > 1000 ){
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