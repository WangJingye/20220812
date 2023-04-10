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
                               lay-verify="required" data-raw=""
                               value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所属门店</label>
                    <div class="layui-input-block">
                        <input type="hidden" name="store_name" value="">
                        <select name="store_id" lay-verify="required" lay-filter="store_id">
                            <option value="">请选择所属门店</option>
                            @foreach($detail['stores'] as $k=>$v)
                                <option value="{{$k}}" >{{$v}}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">职位</label>
                    <div class="layui-input-block">
                        <input type="hidden" name="role_name" value="">

                        <select name="role_id" lay-verify="required" lay-filter="role_id" >
                                <option value="">请选择职位</option>
                            @foreach($detail['roles'] as $k=>$v)
                                <option value="{{$k}}">{{$v}}</option>
                            @endforeach

                        </select>
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


@endsection

<script>
    @section('layui_script')
    //监听提交
    layui.use(['form'], function () {
        var layer = layui.layer
            , form = layui.form
            ,laydate = layui.laydate;
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
    })



    @endsection
</script>