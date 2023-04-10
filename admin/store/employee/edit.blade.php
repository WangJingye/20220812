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
            <h2>更新导购信息</h2>
        </div>
        <div class="layui-card-body">

            <el-form ref="form" class="layui-form" method="post" onsubmit="return check();">
                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$detail['id']}}">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">导购姓名</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="name"
                               lay-verify="required" data-raw="{{$detail['name']??old('name')}}"
                               value="{{$detail['name']??old('name')}}">
                    </div>
                </div>
                 <div class="layui-form-item">
                    <label for="" class="layui-form-label">导购id</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="sid"
                               lay-verify="required" data-raw="{{$detail['sid']??old('sid')}}"
                               value="{{$detail['sid']??old('sid')}}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">职位状态</label>
                    <div class="layui-input-block">
                        <select name="status" lay-verify="required">
                            <option value="1" @if( $detail['status']==1) selected @endif>在职</option>
                            <option value="0" @if( $detail['status']==0) selected @endif>离职</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所属门店</label>
                    <div class="layui-input-block">
                        <input type="hidden" name="store_name" value="{{$detail['store_name']}}">
                        <select name="store_id" lay-verify="required" lay-filter="store_id">
                            <option value="">直接选择</option>
                            @foreach($detail['stores'] as $k=>$v)
                                <option value="{{$k}}" @if( $detail['store_id']==$k) selected @endif>{{$v}}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">职位</label>
                    <div class="layui-input-block">
                        <select name="role_id" lay-verify="required" lay-filter="role_id">
                            <option value="">直接选择</option>
                            @foreach($detail['roles'] as $k=>$v)
                                <option value="{{$k}}" @if( $detail['role_id']==$k) selected @endif>{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">邮箱</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="email"
                               lay-verify="required" data-raw="{{$detail['email']??old('email')}}"
                               value="{{$detail['email']??old('email')}}">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">手机号</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="phone"
                               value="{{$detail['phone']??old('phone')}}">
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

    layui.use(['layer', 'form','laydate'], function () {

        var layer = layui.layer
            ,form = layui.form
            ,laydate = layui.laydate


        //日期
        laydate.render({
            elem: '#dimission_at'
        });
        laydate.render({
            elem: '#probation_at'
        });
        form.on('select(store_id)', function (data) {

            var store_name = data.elem[data.elem.selectedIndex].text;

            $("input[name=store_name]").val(store_name);
        });

        form.on('select(role_id)', function (data) {
            var role_name = data.elem[data.elem.selectedIndex].text;
            $("input[name=role_name]").val(role_name);
        });

        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.store.employee.update') }}", data.field, function (res) {
                alert(res.code);
                if (res.code == 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                        offset: '300px',
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
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