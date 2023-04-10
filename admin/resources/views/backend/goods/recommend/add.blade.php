@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
<script src="{{ url('/static/admin/js/custom.js') }}"></script>

@section('content')
    <style>
        .edit-wrap-scroll{
            position: fixed;
            top:0px;
            right:20px;
        }
        .edit-wrap-scroll-menu{
            position: fixed;
            top:0px;
            left:0px;
        }
        .layui-form input[type="checkbox"], .layui-form input[type=radio], .layui-form select{
            display: none;
        }
        .layui-form-item {
            white-space: nowrap !important;
        }
    </style>
    <div class="layui-card product-edit" id="app">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>新增推荐</h2>
        </div>
        <div class="layui-card-body">

{{--            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">--}}
            <form class="layui-form" action="">
                {{csrf_field()}}
                <input type="hidden" name="id" value="">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">推荐标识</label>
                    <div class="layui-input-block">
                        <input class="layui-input" style="width: 300px;"  type="text" name="flag"
                               lay-verify="required" data-raw=""
                               value="" placeholder="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">类目ID</label>
                    <div class="layui-input-block">
                        <input class="layui-input" style="width: 300px;" type="text" name="cat_id"
                               value="" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">推荐描述</label>
                    <div class="layui-input-block">
                        <input class="layui-input" style="width: 500px;" type="text" name="rec_desc"
                               value="" >
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm">确 认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div style="display: none">
        <input type="hidden" id="J_init_products" value='' />
        <input type="hidden" id="J_search_product_index" value="" />
    </div>


    <style>

    </style>
    <script src="<?=url('/lib/app.js'); ?>"></script>
    <script src="<?=url('/lib/jquery.validate.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?=url('/lib/app/index.css'); ?>">



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
            elem: '#J_thumbnail_button' //绑定元素
            , url: "{{ route('backend.file.uploadPic') }}" //上传接口
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                var path = res.path;
                $("#J_thumbnail").val(path);
                $("#J_thumbnail_src").attr("src", path).show();
            }
            , error: function () {
                //请求异常回调
            }
        });
        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.goods.recommend.insert') }}", data.field, function (res) {
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
    });

    @endsection
</script>

