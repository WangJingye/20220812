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
        
        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">裂变名称</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="name"
                               lay-verify="required" data-raw=""
                               value="{{$detail['name']}}" >
                        <input class="layui-input" type="hidden" name="id"
                               lay-verify="required" data-raw=""
                               value="{{$detail['id']}}" >
                    </div>
                </div>
                <div class="layui-form-item">
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-block">
                            <select name="type" lay-filter="type" >
                                <option {{1==$detail['type']?'selected':''}} value="1">优惠码</option>
                                <option {{2==$detail['type']?'selected':''}} value="2">优惠卷</option>
                            </select>
                        </div>
                </div>
                <div class="layui-form-item">
                        <label class="layui-form-label">优惠卷/优惠码id</label>
                        <div class="layui-input-block">
                            <input class="layui-input" type="text" name="value_id"
                               lay-verify="required" data-raw=""
                               value="{{$detail['value_id']}}" >
                        </div>
                </div>
                <div class="layui-form-item " id="condition_value" style="">
                        <label class="layui-form-label">条件值</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline" style="">
                                <input class="layui-input" lay-verify="gift" id="condition_value" name="condition_value" placeholder="人" value="{{$detail['condition_value']}}" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item" id="div_gwp_multiple" style="">
                        <label class="layui-form-label">是否送多件</label>
                        <div class="layui-input-block">
                            <div  class="layui-input-inline" >
                                <select lay-verify="gwp" name="step" >
                                     <option {{1==$detail['step']?'selected':''}} value="1">送一件</option>
                                <option {{2==$detail['step']?'selected':''}} value="2">送多件</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item" id="div_gwp_multiple" style="">
                        <label class="layui-form-label">增送上线</label>
                        <div class="layui-input-block">
                            <div class="layui-input-inline" style="">
                                <input class="layui-input" lay-verify="max_num" id="max_num" name="max_num" placeholder="人" value="{{$detail['max_num']}}" type="text">（多少人才能获得权益）
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">有效期</label>
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required" id="start_time" name="start_time" placeholder="开始日期" value="{{$detail['start_time']??''}}" type="text">
                            </div>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required|end_time" id="end_time" name="end_time" placeholder="结束日期" value="{{$detail['end_time']??''}}" type="text">
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
            // data: {
            //     province: '广东省',
            //     city: '深圳市',
            //     county: '龙岗区',
            // },
            change: function (res) {
                //选择结果
                console.log(res);
            }
        });

        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.fission.add') }}", data.field, function (res) {
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
    });


    @endsection

</script>