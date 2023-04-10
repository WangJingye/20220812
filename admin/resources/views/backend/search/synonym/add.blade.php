@extends('backend.base')
<link rel="stylesheet" href="{{ url('/static/admin/css/form.css') }}" media="all">
@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>创建搜索跳转</h2>
        </div>
        <div class="layui-card-body">
            <form method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">同义词</label>
                    <div class="layui-input-block" style="width: 500px">
                        <input class="layui-input" type="text" name="word" lay-verify="required"
                               placeholder=""  value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">转换词</label>
                    <div class="layui-input-block" style="width: 500px">
                        <input class="layui-input" type="text" name="convert_word" lay-verify="required"
                               placeholder=""  value="">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">备注</label>
                    <div class="layui-input-block" style="width: 500px">
                        <input class="layui-input" type="text" name="remark" lay-verify="required"
                               placeholder=""  value="">
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
@endsection
<script>
    @section('layui_script')
    layui.use(['form'], function () {
        //监听提交
        form.on('submit(confirm)', function (data) {
            $.post("{{ route('backend.goods.search.addSynonym') }}", data.field, function (res) {
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
        form.on('select(hasMainMaterial)', function (data) {
            let curSelect = data.value;
            if (curSelect == '1') {
                $("#noDiamo").hide();
                $("#withDiamo").show();
            } else {
                $("#withDiamo").hide();
                $("#noDiamo").show();
            }
        });
    });
    @endsection
</script>