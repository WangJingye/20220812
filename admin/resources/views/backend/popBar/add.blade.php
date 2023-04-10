@extends('backend.base')

<script src="<?=url('/static/admin/js/jquery.min.js'); ?>"></script>
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
            <h2>新增弹窗</h2>
        </div>
        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">h5链接</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="pop_h5_url"
                               lay-verify="required" data-raw=""
                               value="" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <span>图片:</span>
                    <input class="layui-input layui-input-inline " name="img" type="text" style="width: 120px" />
                    <button type="button" class="layui-btn J_img">上传</button>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">h5图片</label>
                    <div class="layui-input-block">
                        <input class="layui-input layui-input-inline " name="pop_h5_img" type="text" style="width: 120px" />
                        <button type="button" class="layui-btn J_img">上传</button>

                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">app链接</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="pop_app_url"
                               lay-verify="required" data-raw=""
                               value="" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">app图片</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="pop_app_img"
                               lay-verify="required" data-raw=""
                               value="" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">tab bar 名称</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="pop_url_name"
                               lay-verify="required" data-raw=""
                               value="" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <span>状态:</span>
                    <select name="pop_status" class="layui-input layui-input-inline" data-value="">
                        <option value="0">关闭</option>
                        <option value="1">开启</option>
                    </select>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">有效期</label>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input class="layui-input" lay-verify="required" id="start_time" name="pop_start" placeholder="开始日期" value="{{$detail['start_time']??''}}" type="text">
                        </div>
                        <div class="layui-input-inline">
                            <input class="layui-input" lay-verify="required|end_time" id="end_time" name="pop_end" placeholder="结束日期" value="{{$detail['end_time']??''}}" type="text">
                        </div>
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

    <link rel="stylesheet" href="<?=url('/lib/app/index.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/css/admin.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/bootstrap.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/css/swiper.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_1458973_xkrtaihi4e8.css"/>
    <link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css'); ?>"/>


@endsection

<script>

    @section('layui_script')

    layui.config({

        base: '/static/layer/layarea/mods/'
        , version: '1.0'
    });
    layui.use(['layer', 'form','upload'], function () {

        var layer = layui.layer
            , form = layui.form
            ;

        upload.render({
            elem: '.J_img' //绑定元素
            , url: "{{ route('backend.page.ajaxUploadShareImage') }}" //上传接口
            , accept: 'file' //普通文件
            , done: function (res) {
                console.log(res)
                var item = this.item;
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                } else {
                    var path = res.path;
                    $(item).prev().val(path);
                    return layer.msg('上传成功');
                }
            }
            , error: function () {
                //请求异常回调
            }
        });

        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.popBar.popAdd') }}", data.field, function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2 //2秒关闭（如果不配置，默认是3秒）
                    });

                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
                    return false;
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
        lay('.del_product_discount').on('click',function(e){
            $(this).parent().parent().remove();
            form.render();
            --counter;
        });
        lay("input[name='start_time']").on('click', function(e){
            laydate.render({
                elem: "input[name='start_time']"
                ,show: true //直接显示
                ,closeStop: "input[name='start_time']"
            });
        });
        lay("input[name='end_time']").on('click', function(e){
            laydate.render({
                elem: "input[name='end_time']"
                ,show: true //直接显示
                ,closeStop: "input[name='end_time']"
            });
        });
    });
    var counter = {{isset($detail['condition_value'])?count($detail['condition_value']):1}};
    lay("#add_more_product_discount").on('click',function(e){
        var str = ('<div class="add_more_product"><hr style="background-color:transparent;"><div class="layui-input-inline">');
        str = str + ('<input class="layui-input" lay-verify="required|number|int_percent" name="condition_value['+ counter +']" placeholder="满X人" value="" type="text">');
        str = str + ('</div>');
        str = str + ('<div class="layui-input-inline">');
        str = str + ('<input class="layui-input" lay-verify="required|number|int_percent|css_bigger_ten" name="value_id['+ counter +']" placeholder="促销规则优惠券id" value="" type="text">');
        str = str + ('</div>');
        str = str + '<div class="layui-input-inline"><button type="button" class="layui-btn del_product_discount">删除</button></div>';
        str = str + '</div>';
        $('#rule').append(str);
        form.render();
        counter++;
        lay('.del_product_discount').on('click',function(e){
            $(this).parent().parent().remove();
            form.render();
            --counter;
        });
    });


    @endsection

</script>