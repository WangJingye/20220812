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
            <h2>更新产品</h2>
        </div>

        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$detail->id}}">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">产品中文名</label>

                    <!--
                    <input type="hidden" name="custom_product_name" id="custom_product_name"
                           value="{{$detail->custom_product_name??old('custom_product_name')}}">
                     -->

                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="product_name" id="product_name"
                               lay-verify="required" data-raw="{{$detail->product_name??old('product_name')}}"
                               value="{{$detail->product_name??old('product_name')}}" placeholder="如：挚爱美礼">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">产品别名</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="product_name_en"
                               data-raw="{{$detail->product_name_en??old('product_name_en')}}"
                               value="{{$detail->product_name_en??old('product_name_en')}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">列表名</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="list_name"
                               data-raw="{{$detail->list_name??old('list_name')}}"
                               value="{{$detail->list_name??old('list_name')}}" placeholder="如：dlc">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">可搜索</label>
                    <div class="layui-input-block">
                        <input type="radio" name="can_search" value="1" title="可搜索"
                               @if($detail->can_search == 1)
                               checked
                               @endif
                        >
                        <input type="radio" name="can_search" value="0" title="不可搜索"
                               @if($detail->can_search == 0)
                               checked
                                @endif
                        >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">礼盒</label>
                    <div class="layui-input-block">
                        <input type="radio" name="is_gift_box" value="1" title="是"
                               @if($detail->is_gift_box == 1)
                               checked
                                @endif
                        >
                        <input type="radio" name="is_gift_box" value="0" title="不是"
                               @if($detail->is_gift_box == 0)
                               checked
                                @endif
                        >
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
                               value="{{$detail->custom_keyword??old('custom_keyword')}}" placeholder="关键字">
                    </div>
                </div>
                <div style="display: none" class="layui-form-item">
                    <label for="" class="layui-form-label">商品标签</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="tag"
                               value="{{$detail->tag??old('tag')}}" placeholder="多个标签,英文逗号分隔">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">规格维度</label>
                    <div class="layui-input-block">
                        @foreach($specs as $k=>$spec)
                            <input type="checkbox" name="spec_type[]" value="{{$k}}" title="{{$spec}}"
                            @if(in_array($k,$detail->spec_type))
                                checked
                            @endif
                            >
                        @endforeach
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">列表图片</label>
                    <div class="layui-input-block"
                         style="display: flex;display: -webkit-flex;flex-direction:column;width:15%;">
                        <input class="layui-input" type="hidden" id="list_img" name="list_img"
                               value="{{$detail->list_img??old('list_img')}}" >
                        <img id="list_img_src" width="100%"
                             src="{{$detail->list_img??old('list_img')}}" @if(empty($detail->list_img)) style="display:none;" @endif />
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
                               value="{{$detail->share_img??old('share_img')}}" >
                        <img id="share_img_src" width="100%"
                             src="{{$detail->share_img??old('share_img')}}" @if(empty($detail->share_img)) style="display:none;" @endif />
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
                            <input type="input" name="display_start_time" id="start_time" value=" @if(!empty($detail->display_start_time)) {{date('Y-m-d H:i:s',$detail->display_start_time)}}  @endif "
                            >
                        ~
                            <input type="input" name="display_end_time" id="end_time" value=" @if(!empty($detail->display_end_time)) {{date('Y-m-d H:i:s',$detail->display_end_time)}}  @endif "
                            >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">排序值</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="sort" value="{{$detail->sort??old('sort')}}" lay-verify="numberb" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">馥郁度</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="score" value="{{$detail->score??old('score')}}" lay-verify="numberb" >
                    </div>
                </div>
                <div style="display: none" class="layui-form-item">
                    <label class="layui-form-label">优先目录</label>
                    <div class="layui-input-block">
                        @foreach($cats as $k=>$cat)
                            <input type="radio" name="priority_cat_id" value="{{$cat['cat_id']}}" title="{{$cat['cat_name']}}"
                                   @if($detail->priority_cat_id == $cat['cat_id'])
                                   checked
                                    @endif
                            >
                        @endforeach
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">产品描述</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="product_desc"
                                  >{{$detail->product_desc??old('product_desc')}}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">推荐产品</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="rec_spu" placeholder="为您推荐商品,显示在PDP中,请填入spuid以逗号(英文)分割">{{$detail->rec_spu??old('rec_spu')}}</textarea>
                    </div>
                </div>
                <div style="display: none" class="layui-form-item">
                    <label for="" class="layui-form-label">推荐类目ID</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="rec_cat_id"
                               value="{{$detail->rec_cat_id??old('rec_cat_id')}}" placeholder="为你推荐类目ID">
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
            $.post("{{ route('backend.goods.spu.edit') }}", data.field, function (res) {
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
    })

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

    // $("#product_name").bind('change', function () {
    //     let rawPdtName = $("#product_name").data("raw");
    //     let curPdtName = $("#product_name").val();
    //     if (rawPdtName !== curPdtName) {
    //         $("#custom_product_name").val(curPdtName);
    //     }
    // });
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