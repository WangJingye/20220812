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
            <h2>新增裂变</h2>
        </div>
        <div class="layui-card-body">
            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <div class="layui-card-body">
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">活动名称</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="display_name"
                                   lay-verify="required" data-raw=""
                                   value="" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">付运邮费</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="money" placeholder="只接收数字"
                                   lay-verify="required|number|num_limit" num-max="99" data-raw="" value="" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">增加产品 sku</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="add_sku"
                                   lay-verify="required" data-raw=""
                                   value="" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">有效期</label>
                        <div class="layui-input-block">
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <input class="layui-input" lay-verify="required" id="start_time" name="start_time" placeholder="开始日期" value="" type="text">
                                </div>
                                <div class="layui-input-inline">
                                    <input class="layui-input" lay-verify="required|end_time" id="end_time" name="end_time" placeholder="结束日期" value="" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button type="submit" class="layui-btn" lay-submit="" lay-filter="confirm">确 认</button>
                        </div>
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
    layui.use(['layer', 'form', 'layarea'], function () {

        var layer = layui.layer
            , form = layui.form
            , layarea = layui.layarea;

        layarea.render({
            elem: '#area-picker',
            change: function (res) {
                //选择结果
                console.log(res);
            }
        });
        form.verify({
            num_limit: function(value, item){
                var max = item.getAttribute('num-max');
                if(value*1 > max){
                    return '不能大于'+max;
                }
            }
        });
        form.on('submit(confirm)', function (data) {
            var money = data.field.money;
            if(money <=1 )
            {
                layer.msg('邮费金额必须大于1的整数'); 
                return false;
            }
            $.post("{{ route('backend.trial.edit') }}", data.field, function (res) {
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


    @endsection

</script>