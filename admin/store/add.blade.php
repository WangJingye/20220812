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
            <h2>新增门店信息</h2>
        </div>
        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">门店名称</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="store_name"
                               lay-verify="required" data-raw=""
                               value="" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">门店id</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="store_id"
                               lay-verify="required" data-raw=""
                               value="" >
                    </div>
                </div>
               
                <div class="layui-form-item" id="area-picker">
                    <div class="layui-form-label">所属区域地址</div>
                    <div class="layui-input-block">
                        <div class="layui-input-inline">
                            <select name="province" class="province-selector" data-value="" lay-filter="province-1">
                                <option value="">请选择省</option>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select name="city" class="city-selector" data-value="" lay-filter="city-1">
                                <option value="">请选择市</option>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select name="area" class="county-selector" data-value="" lay-filter="county-1">
                                <option value="">请选择区</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">经度</label>
                    <div class="layui-input-block">
                        <input class="layui-input" lay-verify="required" type="text" name="lon"
                               value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">地址</label>
                    <div class="layui-input-block">
                        <input class="layui-input" lay-verify="required" type="text" name="address"
                               value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">纬度</label>
                    <div class="layui-input-block">
                        <input class="layui-input"lay-verify="required|number"  type="text" name="lat"
                               value="">
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
            $.post("{{ route('backend.store.update') }}", data.field, function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 3000 //2秒关闭（如果不配置，默认是3秒）
                    });
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
                } else {
                    layer.msg(res.message, {
                        icon: 2,
                        shade: 0.3,
                        offset: '300px',
                        time: 3000 //2秒关闭（如果不配置，默认是3秒）
                    });
                }
            }, 'json');
            return false;
        });
    });
    @endsection
</script>