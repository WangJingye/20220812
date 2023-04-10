@extends('backend.base')

@section('content')
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>缓存刷新</legend>
    </fieldset>
    <div class="layui-form-item cache-wrapper">
        <button class="layui-btn cache-btn" lay-data="{{route('backend.config.cache.clear',['action'=>'product'])}}">产品&分类缓存刷新</button>
    </div>
    <div class="layui-form-item">
        <button class="layui-btn cache-btn" lay-data="{{route('backend.config.cache.clear',['action'=>'synonym'])}}">黑名单&同义词缓存刷新</button>
    </div>
    <div class="layui-form-item">
        <button class="layui-btn cache-btn" lay-data="{{route('backend.config.cache.clear',['action'=>'ad'])}}">广告缓存刷新</button>
    </div>
    <div class="layui-form-item">
        <button class="layui-btn cache-btn" lay-data="{{route('backend.config.cache.clear',['action'=>'rec'])}}">为您推荐缓存刷新</button>
    </div>

@endsection

<script>
    @section('layui_script')
    //监听提交
    layui.use(['form'], function () {
        lay(".cache-btn").on('click', function(e){
            let url = $(this).attr('lay-data');
            var loading = layer.load(1, {shade: [0.3]});
            console.log(url)
            $.get(url, function (res) {
                if (res.code === 1) {
                    layer.msg('操作成功', {
                        icon: 1,
                        shade: 0.3,
                    });
                } else {
                    layer.msg(res.message, {
                        icon: 2,
                        shade: 0.3,
                    });
                }
                layer.close(loading);
            });
        });

    });


    @endsection
</script>