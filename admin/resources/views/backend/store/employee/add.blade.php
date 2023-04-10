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
            <h2>新增导购</h2>
        </div>
        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">导购姓名</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="name"
                               lay-verify="required" data-raw=""
                               value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">导购id</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="sid"
                               lay-verify="required|number" data-raw=""
                               value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所属门店</label>
                    <div class="layui-input-block">
                        <input type="hidden" name="store_id" value="1">
                        <input class="layui-input" type="text" name="store_name"
                               lay-verify="required" data-raw="" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">职位</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="role_name"
                               lay-verify="required" data-raw="" value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">职位状态</label>
                    <div class="layui-input-block">
                        <select name="status" lay-verify="required">
                            <option value="1" >在职</option>
                            <option value="0" >离职</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">邮箱</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="email"
                               lay-verify="email" data-raw=""
                               value="">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">手机号</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="phone"
                               value="" lay-verify="required|phone">
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
    layui.use(['form'], function () {
        var layer = layui.layer
            , form = layui.form
            ,laydate = layui.laydate;

        //日期
        laydate.render({
            elem: '#dimission_at'
        });
        laydate.render({
            elem: '#probation_at'
        });

        form.on('select(store_id)', function (data) {

            var name = data.elem[data.elem.selectedIndex].text;
            $("input[name=store_name]").val(name);
        });
        form.on('select(role_id)', function (data) {
            var name = data.elem[data.elem.selectedIndex].text;
            $("input[name=role_name]").val(name);
        });

        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.store.employee.update') }}", data.field, function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    });
                    let index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
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
    })
    @endsection
</script>