@extends('backend.base')
<link rel="stylesheet" type="text/css" href="<?=url('/static/admin/layui/css/layui.css'); ?>"/>
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
            <h2>邮费配置</h2>
        </div>
        <div class="layui-card-body">
            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <div class="layui-card-body">
                    @if($is_default!=1)
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">省份</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="province"
                                   lay-verify="required" data-raw="" value="{{$detail['province']??''}}" >
                        </div>
                        <div class="layui-input-block">
                            <div class="layui-form-mid layui-word-aux">注意此处填写的省份必须和OMS中的省份一致,否则不会生效!!!</div>
                        </div>
                    </div>
                    @endif
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">运费</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="ship_fee" placeholder=""
                                   lay-verify="required|number|num_limit" num-max="99" data-raw="" value="{{$detail['ship_fee']??''}}" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">满X免运费</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="free_limit"
                                   lay-verify="required|number|num_limit" num-max="10000000" data-raw="" value="{{$detail['free_limit']??''}}" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">是否免邮</label>
                        <div class="layui-input-block">
                            <input type="hidden" name="is_free" value="0" >
                            <input name="is_free" title="免邮" @if(!empty($detail['is_free'])) checked="" @endif type="checkbox" value="1">
                        </div>
                    </div>
                    <input type="hidden" name="id" value="{{$detail['id']??''}}" >
                    <input type="hidden" name="is_default" value="{{$is_default}}" >
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
            $.post("{{ route('backend.shipfee.update') }}", data.field, function (res) {
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