@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<script src="{{ url('/static/admin/js/custom.js') }}"></script>

@section('content')
    <style>
        .layui-form-item {
            white-space: nowrap !important;
        }
    </style>

    <div class="layui-card product-edit" id="app">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>新增产品</h2>
        </div>
        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">产品ID</label>

                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="product_id" id="product_id"
                               lay-verify="required"
                               value="" placeholder="" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">产品中文名</label>

                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="product_name" id="product_name"
                               lay-verify="required|len_limit" lay-max="50" data-raw=""
                               value="" placeholder="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">产品别名</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="product_name_en"
                               value="" placeholder="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">列表名称</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="list_name"
                               value="" placeholder="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">可搜索</label>
                    <div class="layui-input-block">
                        <input type="radio" name="can_search" value="1" title="可搜索" checked>
                        <input type="radio" name="can_search" value="0" title="不可搜索">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">礼盒</label>
                    <div class="layui-input-block">
                        <input type="radio" name="is_gift_box" value="1" title="是">
                        <input type="radio" name="is_gift_box" value="0" title="不是" checked>
                    </div>
                </div>


                <div class="layui-form-item">
                    <label for="" class="layui-form-label">产品副标题</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="short_product_desc"
                               value="{{$detail->short_product_desc??old('short_product_desc')}}" >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">关键字</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="custom_keyword" id="custom_keyword"
                               value="" placeholder="关键字">
                    </div>
                </div>
                <div style="display: none" class="layui-form-item">
                    <label for="" class="layui-form-label">商品标签</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="tag"
                               value="" placeholder="多个标签,英文逗号分隔">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">规格维度</label>
                    <div class="layui-input-block">
                        @foreach($specs as $k=>$spec)
                            <input type="checkbox" name="spec_type[]" value="{{$k}}" title="{{$spec}}">
                        @endforeach
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">列表图片</label>
                    <div class="layui-input-block"
                         style="display: flex;display: -webkit-flex;flex-direction:column;width:15%;">
                        <input class="layui-input" type="hidden" id="list_img" name="list_img"
                               value="" >
                        <img id="list_img_src" width="100%" src="" style="display:none;"/>
                        <button type="button" class="layui-btn" id="list_img_button" style="margin-top:10px;">
                            <i class="layui-icon">&#xe67c;</i>上传图片
                        </button>
                        <button type="button" class="layui-btn" id="del_list_img_button" style="margin-top:10px;">
                            <i class="layui-icon">&#xe67c;</i>删除图片
                        </button>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分享图片</label>
                    <div class="layui-input-block"
                         style="display: flex;display: -webkit-flex;flex-direction:column;width:15%;">
                        <input class="layui-input" type="hidden" id="share_img" name="share_img"
                               value="" >
                        <img id="share_img_src" width="100%" src="" style="display:none;"/>
                        <button type="button" class="layui-btn" id="share_img_button" style="margin-top:10px;">
                            <i class="layui-icon">&#xe67c;</i>上传图片
                        </button>
                        <button type="button" class="layui-btn" id="del_share_img_button" style="margin-top:10px;">
                            <i class="layui-icon">&#xe67c;</i>删除图片
                        </button>
                    </div>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">注意分享图片需要5:4!</div>
                    </div>
                </div>

                <div style="display: none" class="layui-form-item">
                    <label class="layui-form-label">展示时间</label>
                    <div class="layui-input-block">
                        <input type="input" name="display_start_time" id="start_time" value=" "
                        >
                        ~
                        <input type="input" name="display_end_time" id="end_time" value=""
                        >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">排序值</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="sort" value="0" lay-verify="numberb" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">馥郁度</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="score" value="0" lay-verify="numberb" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">产品描述</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="product_desc"
                        ></textarea>
                    </div>
                </div>

                <div style="display: none" class="layui-form-item">
                    <label for="" class="layui-form-label">推荐类目ID</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="rec_cat_id"
                               value="" placeholder="为你推荐类目ID">
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm">确 认</button>
                    </div>
                </div>
            </el-form>
        </div>
    </div>

    <style>

    </style>
    <script src="<?=url('/lib/jquery.validate.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?=url('/lib/app/index.css'); ?>">

    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/admin.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/bootstrap.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/swiper.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_1458973_xkrtaihi4e8.css"/>

    <script>
        var pageTitle = '';
        var showHeaderFooter = '';
        var nodes = '';

        nodes.forEach(function (item){
          if(item.action==null || item.action==false){
              item.action={"data":[],"type":"none","route":false};
          }
        })
        console.log("nodes:",nodes);
    </script>
    <?php require_once './js/app.js.php'; ?>

    <script>
        $(window).scroll( function() {
            var  scrollTop=document.title=$(this).scrollTop();
            if(scrollTop > 910){
                $('.edit-wrap').addClass('edit-wrap-scroll').css('top',0);
                $('.element-list-wrap').addClass('edit-wrap-scroll-menu').css('top',0);
                $('.cms-content-wrap').css('marginLeft','22%');
            }else{
                $('.edit-wrap').removeClass('edit-wrap-scroll').css('top',910 - scrollTop);
                $('.element-list-wrap').removeClass('edit-wrap-scroll-menu').css('top',910 - scrollTop);
                $('.cms-content-wrap').css('marginLeft','0');
            }
            $('.cms-content-wrap').css('minHeight','800px');
        } );
    </script>
@endsection

<script>
    @section('layui_script')
    //监听提交
    layui.use(['form','upload'], function () {
        var upload = layui.upload;
        //执行实例
        upload.render({
            elem: '#J_kv_images_button' //绑定元素
            , url: "{{ route('backend.file.uploadPic') }}" //上传接口
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                var path = res.path;
                $("#J_kv_images").val(path);
                $("#share_image_src").attr("src", path).show();
            }
            , error: function () {
                //请求异常回调
            }
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#start_time' //指定元素
            ,format: 'yyyy-MM-dd HH:mm:ss' //可任意组合
        });
        //执行一个laydate实例
        laydate.render({
            elem: '#end_time' //指定元素
            ,format: 'yyyy-MM-dd HH:mm:ss' //可任意组合
        });


        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.goods.spu.insert') }}", data.field, function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    }, function () {
                        let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        parent.layer.close(index); //再执行关闭
                    });
                } else {
                    layer.msg(res.message, {
                        icon: 2,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    });
                }
            }, 'json');
            return false;
        });

        form.verify({
            numberb: [
                /^[0-9]\d*$/
                , '只允许正整数'
            ],
            len_limit: function(value, item){
                var max = item.getAttribute('lay-max');
                if(value.length > max){
                    return '不能大于'+max+'个字符的长度';
                }
            }
        });

        upload.render({
            elem: '#list_img_button' //绑定元素
            , url: "{{ route('backend.file.uploadPic') }}" //上传接口
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                var path = res.path;
                $("#list_img").val(path);
                $("#list_img_src").attr("src", path).show();
            }
            , error: function () {
                //请求异常回调
            }
        });

        upload.render({
            elem: '#share_img_button' //绑定元素
            , url: "{{ route('backend.file.uploadPic') }}" //上传接口
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                var path = res.path;
                $("#share_img").val(path);
                $("#share_img_src").attr("src", path).show();
            }
            , error: function () {
                //请求异常回调
            }
        });
    });

    if ($("#list_img").val() !== '') {
        $("#list_img_src").show();
    }
    $("#del_list_img_button").on("click",function () {
        $("#list_img").val("");
        $("#list_img_src").hide();
    });

    if ($("#share_img").val() !== '') {
        $("#share_img_src").show();
    }
    $("#del_share_img_button").on("click",function () {
        $("#share_img").val("");
        $("#share_img_src").hide();
    });

    $("#product_name").bind('change', function () {
        let rawPdtName = $("#product_name").data("raw");
        let curPdtName = $("#product_name").val();
        if (rawPdtName !== curPdtName) {
            $("#custom_product_name").val(curPdtName);
        }
    });
    $("#detail_images").bind('change', function () {
        var val = formatJson($("#detail_images").val(), false);
        if (typeof val != "undefined") {
            $("#detail_images").val(val);
        }
    });
    if ($("#detail_images").val()) {
        $("#detail_images").val(formatJson($("#detail_images").val(), false));
    }
    if ($("#custom_product_name").val()) {
        $("#product_name").val($("#custom_product_name").val());
    }
    @endsection
</script>