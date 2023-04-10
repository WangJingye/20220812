@extends('backend.base')

@section('content')
    <fieldset class="layui-field-title" style="margin-top: 30px;color:red;">
        <legend>请按照顺序进行操作:</legend>
        <legend>1.通过Sftp上传图片到tb_upload/goods目录下(CAT_PICS 分类目录,PDP_SPU_PICS 商品详情目录,PLP_SPU_PICS 商品列表目录)</legend>
        <legend>2.上传分类excel(根据code 有则更新无则新增 默认上架状态)</legend>
        <legend>3.上传商品excel(根据code 有则更新无则新增 默认上架状态)</legend>
        <legend>4.上传图片excel(会删除对应商品的图文数据,只会保存jpg,jpeg,png,其他格式请自行通过后台上传)</legend>
        <legend>5.上传挂载分类excel(全量更新 会删除之前的记录)</legend>
        <legend>6.上传商品关键字excel</legend>
        <legend>7.上传商品排序值excel</legend>
        <legend>8.后台刷新商品分类的缓存</legend>
    </fieldset>

    <button class="layui-btn uploadExcel" lay-data="{url: '{{route('backend.config.data.import',['action'=>'cat'])}}', accept: 'file'}">上传分类</button>
    <button class="layui-btn uploadExcel" lay-data="{url: '{{route('backend.config.data.import',['action'=>'spu'])}}', accept: 'file'}">上传商品</button>
    <button class="layui-btn uploadExcel" lay-data="{url: '{{route('backend.config.data.import',['action'=>'img'])}}', accept: 'file'}">上传图片地址</button>
    <button class="layui-btn uploadExcel" lay-data="{url: '{{route('backend.config.data.import',['action'=>'rel'])}}', accept: 'file'}">上传挂载分类</button>
    <button class="layui-btn uploadExcel" lay-data="{url: '{{route('backend.config.data.import',['action'=>'key'])}}', accept: 'file'}">上传商品关键字</button>
    <button class="layui-btn uploadExcel" lay-data="{url: '{{route('backend.config.data.import',['action'=>'sort'])}}', accept: 'file'}">上传商品排序</button>
    <button class="layui-btn uploadExcel" lay-data="{url: '{{route('backend.config.data.import',['action'=>'price'])}}', accept: 'file'}">上传商品价格</button>
@endsection

<script>
    @section('layui_script')
    //监听提交
    layui.use(['form','upload'], function () {
        var upload = layui.upload;
        var loading;
        upload.render({
            elem: '.uploadExcel'
            ,before: function(){
                // layer.tips('接口地址：'+ this.url, this.item, {tips: 1});
                loading = layer.load(1, {shade: [0.3]});
            }
            ,done: function(res, index, upload){
                layer.close(loading);
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
            }
        })
    });


    @endsection
</script>