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
            <h2>更新门店信息</h2>
        </div>
        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$detail->id}}">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">门店名称</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="store_name"
                               lay-verify="required" data-raw="{{$detail->store_name??old('store_name')}}"
                               value="{{$detail->store_name??old('store_name')}}" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">店铺id</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="store_id"
                               lay-verify="required" data-raw="{{$detail->store_id??old('store_id')}}"
                               value="{{$detail->store_id??old('store_id')}}" >
                    </div>
                </div>
                <div class="layui-form-item" id="area-picker">
                    <div class="layui-form-label">所属区域地址</div>
                    <div class="layui-input-block">
                        <div class="layui-input-inline">
                            <select name="province" class="province-selector" data-value="{{$detail->province??old('province')}}" lay-filter="province-1">
                                <option value="{{$detail->province??old('province')}}">请选择省</option>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select name="city" class="city-selector" data-value="{{$detail->city??old('city')}}" lay-filter="city-1">
                                <option value="{{$detail->city??old('city')}}">请选择市</option>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select name="area" class="county-selector" data-value="{{$detail->area??old('area')}}" lay-filter="county-1">
                                <option value="{{$detail->area??old('area')}}">请选择区</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">地址</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="address"
                               value="{{$detail->address??old('address')}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">经度</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="lon"
                               value="{{$detail->lon??old('lon')}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">纬度</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="lat"
                               value="{{$detail->lat??old('lat')}}">
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
        base: '/static/admin/layarea/mods/'
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
        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.store.update') }}", data.field, function (res) {
                console.log(res);
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    });
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
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
    });
    @endsection
</script>